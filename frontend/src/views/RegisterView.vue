<template>
  <div class="min-h-screen flex items-center justify-center p-4 bg-dark-900 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-hero-800 rounded-full translate-x-1/2 -translate-y-1/2 opacity-20 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-dark-600 rounded-full -translate-x-1/2 translate-y-1/2 opacity-20 blur-3xl"></div>

    <div class="w-full max-w-lg relative">
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-hero-600 rounded-2xl mb-4 shadow-lg shadow-hero-900">
          <span class="text-4xl">⚡</span>
        </div>
        <h1 class="text-3xl font-bold text-white">HeroForce</h1>
        <p class="text-gray-400 mt-1">Escolha seu personagem e entre para o time</p>
      </div>

      <div class="card">
        <h2 class="text-xl font-semibold text-white mb-6">Cadastro de Herói</h2>

        <form @submit.prevent="handleRegister" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="label">Nome completo</label>
              <input v-model="form.name" type="text" class="input" placeholder="Tony Stark" required />
            </div>
            <div>
              <label class="label">E-mail</label>
              <input v-model="form.email" type="email" class="input" placeholder="tony@stark.com" required />
            </div>
          </div>

          <div>
            <label class="label">Personagem</label>
            <select v-model="form.character" class="input" required>
              <option value="" disabled>Escolha seu super-herói...</option>
              <optgroup label="Marvel">
                <option value="Iron Man">Iron Man</option>
                <option value="Spider-Man">Spider-Man</option>
                <option value="Thor">Thor</option>
                <option value="Captain America">Captain America</option>
                <option value="Black Widow">Black Widow</option>
                <option value="Hulk">Hulk</option>
                <option value="Black Panther">Black Panther</option>
                <option value="Doctor Strange">Doctor Strange</option>
                <option value="Scarlet Witch">Scarlet Witch</option>
                <option value="Deadpool">Deadpool</option>
              </optgroup>
              <optgroup label="DC">
                <option value="Batman">Batman</option>
                <option value="Superman">Superman</option>
                <option value="Wonder Woman">Wonder Woman</option>
                <option value="The Flash">The Flash</option>
                <option value="Aquaman">Aquaman</option>
                <option value="Green Lantern">Green Lantern</option>
                <option value="Cyborg">Cyborg</option>
              </optgroup>
              <optgroup label="Outros">
                <option value="Spawn">Spawn</option>
                <option value="Hellboy">Hellboy</option>
                <option value="Invincible">Invincible</option>
              </optgroup>
            </select>
          </div>

          <!-- Avatar preview -->
          <div v-if="form.character" class="flex items-center gap-3 bg-dark-700 rounded-lg px-4 py-3">
            <span class="text-3xl">{{ characterEmoji }}</span>
            <div>
              <p class="text-white font-medium">{{ form.character }}</p>
              <p class="text-gray-400 text-sm">Seu personagem escolhido</p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="label">Senha</label>
              <input v-model="form.password" type="password" class="input" placeholder="••••••••" required minlength="8" />
            </div>
            <div>
              <label class="label">Confirmar senha</label>
              <input v-model="form.password_confirmation" type="password" class="input" placeholder="••••••••" required />
            </div>
          </div>

          <div v-if="error" class="text-red-400 text-sm bg-red-900/30 border border-red-800 rounded-lg px-4 py-2">
            {{ error }}
          </div>

          <button type="submit" class="btn-primary w-full mt-2" :disabled="loading">
            <span v-if="loading">Cadastrando...</span>
            <span v-else>Entrar para o time ⚡</span>
          </button>
        </form>

        <p class="text-center text-gray-400 text-sm mt-6">
          Já tem conta?
          <router-link to="/login" class="text-hero-400 hover:text-hero-300 font-medium transition-colors">
            Fazer login
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = ref({
  name: '',
  email: '',
  character: '',
  password: '',
  password_confirmation: ''
})
const error = ref('')
const loading = ref(false)

const EMOJIS = {
  'Iron Man': '🤖', 'Spider-Man': '🕷️', 'Thor': '⚡', 'Captain America': '🛡️',
  'Black Widow': '🕸️', 'Hulk': '💚', 'Black Panther': '🐾', 'Doctor Strange': '✨',
  'Scarlet Witch': '🔮', 'Deadpool': '⚔️', 'Batman': '🦇', 'Superman': '💫',
  'Wonder Woman': '👑', 'The Flash': '⚡', 'Aquaman': '🌊', 'Green Lantern': '💚',
  'Cyborg': '🤖', 'Spawn': '🔥', 'Hellboy': '😈', 'Invincible': '💪'
}

const characterEmoji = computed(() => EMOJIS[form.value.character] || '🦸')

async function handleRegister() {
  error.value = ''
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'As senhas não coincidem.'
    return
  }
  loading.value = true
  try {
    await auth.register(form.value)
    router.push('/dashboard')
  } catch (e) {
    const errors = e.response?.data?.errors
    if (errors) {
      error.value = Object.values(errors).flat().join(' ')
    } else {
      error.value = e.response?.data?.message || 'Erro ao cadastrar. Tente novamente.'
    }
  } finally {
    loading.value = false
  }
}
</script>
