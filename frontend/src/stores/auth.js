import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('heroforce_token') || null)
  const user = ref(JSON.parse(localStorage.getItem('heroforce_user') || 'null'))

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  async function login(email, password) {
    const { data } = await api.post('/auth/login', { email, password })
    token.value = data.access_token
    user.value = data.user
    localStorage.setItem('heroforce_token', data.access_token)
    localStorage.setItem('heroforce_user', JSON.stringify(data.user))
  }

  async function register(payload) {
    const { data } = await api.post('/auth/register', payload)
    token.value = data.access_token
    user.value = data.user
    localStorage.setItem('heroforce_token', data.access_token)
    localStorage.setItem('heroforce_user', JSON.stringify(data.user))
  }

  async function logout() {
    try { await api.post('/auth/logout') } catch {}
    token.value = null
    user.value = null
    localStorage.removeItem('heroforce_token')
    localStorage.removeItem('heroforce_user')
  }

  async function fetchMe() {
    const { data } = await api.get('/auth/me')
    user.value = data
    localStorage.setItem('heroforce_user', JSON.stringify(data))
  }

  return { token, user, isAuthenticated, isAdmin, login, register, logout, fetchMe }
})
