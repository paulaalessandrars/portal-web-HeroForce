import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
})

// ── Request: injeta o Bearer token em toda requisição ──────────────────────
api.interceptors.request.use(config => {
  const token = localStorage.getItem('heroforce_token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

// ── Response: tenta renovar o JWT automaticamente quando expira (401) ──────
let isRefreshing = false
let failedQueue  = []

function processQueue(error, token = null) {
  failedQueue.forEach(({ resolve, reject }) =>
    error ? reject(error) : resolve(token)
  )
  failedQueue = []
}

api.interceptors.response.use(
  res => res,
  async err => {
    const original = err.config

    // Ignora 401 da rota de refresh (loop infinito) e rotas de auth
    const isAuthRoute = original.url?.includes('/auth/')
    if (err.response?.status !== 401 || original._retry || isAuthRoute) {
      return Promise.reject(err)
    }

    if (isRefreshing) {
      // Enfileira requisições enquanto o refresh está em andamento
      return new Promise((resolve, reject) => {
        failedQueue.push({ resolve, reject })
      }).then(token => {
        original.headers.Authorization = `Bearer ${token}`
        return api(original)
      }).catch(e => Promise.reject(e))
    }

    original._retry  = true
    isRefreshing     = true

    try {
      const { data } = await api.post('/auth/refresh')
      const newToken = data.access_token

      localStorage.setItem('heroforce_token', newToken)
      api.defaults.headers.common.Authorization = `Bearer ${newToken}`
      original.headers.Authorization = `Bearer ${newToken}`

      processQueue(null, newToken)
      return api(original)
    } catch (refreshError) {
      processQueue(refreshError, null)
      // Refresh falhou — desloga o usuário
      localStorage.removeItem('heroforce_token')
      localStorage.removeItem('heroforce_user')
      window.location.href = '/login'
      return Promise.reject(refreshError)
    } finally {
      isRefreshing = false
    }
  }
)

export default api
