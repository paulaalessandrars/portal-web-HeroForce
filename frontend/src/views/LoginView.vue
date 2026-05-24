<template>
  <div class="min-h-screen flex items-center justify-center p-4 bg-dark-900 relative overflow-hidden">
    <!-- Background decorative circles -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-hero-800 rounded-full -translate-x-1/2 -translate-y-1/2 opacity-20 blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-dark-600 rounded-full translate-x-1/2 translate-y-1/2 opacity-20 blur-3xl"></div>

    <div class="w-full max-w-md relative">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-hero-600 rounded-2xl mb-4 shadow-lg shadow-hero-900">
          <span class="text-4xl">⚡</span>
        </div>
        <h1 class="text-3xl font-bold text-white">HeroForce</h1>
        <p class="text-gray-400 mt-1">Portal de Projetos Heroicos</p>
      </div>

      <div class="card">
        <h2 class="text-xl font-semibold text-white mb-6">Entrar no sistema</h2>

        <form @submit.prevent="handleLogin" class="space-y-4">
          <div>
            <label class="label">E-mail</label>
            <input v-model="form.email" type="email" class="input" placeholder="heroi@heroforce.com" required />
          </div>
          <div>
            <label class="label">Senha</label>
            <input v-model="form.password" type="password" class="input" placeholder="••••••••" required />
          </div>

          <div v-if="error" class="text-red-400 text-sm bg-red-900/30 border border-red-800 rounded-lg px-4 py-2">
            {{ error }}
          </div>

          <button type="submit" class="btn-primary w-full mt-2" :disabled="loading">
            <span v-if="loading">Entrando...</span>
            <span v-else>Entrar</span>
          </button>
        </form>

        <p class="text-center text-gray-400 text-sm mt-6">
          Novo herói?
          <router-link to="/register" class="text-hero-400 hover:text-hero-300 font-medium transition-colors">
            Cadastre-se
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = ref({ email: '', password: '' })
const error = ref('')
const loading = ref(false)

async function handleLogin() {
  error.value = ''
  loading.value = true
  try {
    await auth.login(form.value.email, form.value.password)
    router.push('/dashboard')
  } catch (e) {
    error.value = e.response?.data?.message || 'Credenciais inválidas. Tente novamente.'
  } finally {
    loading.value = false
  }
}
</script>
