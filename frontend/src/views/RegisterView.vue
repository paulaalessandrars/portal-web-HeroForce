<template>
  <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Personagens decorativos -->
    <div class="absolute inset-0 flex pointer-events-none select-none">
      <img src="https://cdn.jsdelivr.net/gh/akabab/superhero-api@0.3.0/api/images/lg/579-scarlet-witch.jpg"
           class="absolute right-0 bottom-0 h-[90vh] object-cover object-top opacity-10 blur-sm"
           style="mask-image: linear-gradient(to left, rgba(0,0,0,0.5), transparent);
                  -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,0.5), transparent);"
           alt="" />
      <img src="https://cdn.jsdelivr.net/gh/akabab/superhero-api@0.3.0/api/images/lg/720-wonder-woman.jpg"
           class="absolute left-0 bottom-0 h-[90vh] object-cover object-top opacity-10 blur-sm"
           style="mask-image: linear-gradient(to right, rgba(0,0,0,0.5), transparent);
                  -webkit-mask-image: linear-gradient(to right, rgba(0,0,0,0.5), transparent);"
           alt="" />
    </div>

    <div class="w-full max-w-lg relative animate-slide-up">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 mb-5 drop-shadow-lg">
          <HeroLogo :size="80" />
        </div>
        <h1 class="text-4xl font-black text-white tracking-tight">
          Hero<span class="text-gradient">Force</span>
        </h1>
        <p class="text-gray-500 mt-2 text-sm tracking-widest uppercase">Escolha seu personagem e entre para o time</p>
      </div>

      <div class="card">
        <h2 class="text-xl font-bold text-white mb-6">Cadastro de Herói</h2>

        <form @submit.prevent="handleRegister" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="label">Nome completo</label>
              <input v-model="form.name" type="text" class="input"
                     placeholder="Tony Stark" required />
            </div>
            <div>
              <label class="label">E-mail</label>
              <input v-model="form.email" type="email" class="input"
                     placeholder="tony@stark.com" required />
            </div>
          </div>

          <div>
            <label class="label">Personagem</label>
            <SelectInput
              v-model="form.character"
              :options="CHARACTERS.map(c => ({ value: c.value, label: c.value, group: c.group }))"
              placeholder="Escolha seu super-herói..."
            />
          </div>

          <!-- Preview do personagem -->
          <div v-if="form.character"
               class="flex items-center gap-4 bg-white/[0.04] border border-white/[0.08]
                      rounded-xl px-4 py-3 animate-fade-in">
            <HeroAvatar :character="form.character" :size="60" />
            <div>
              <p class="text-white font-bold text-lg">{{ form.character }}</p>
              <p class="text-gray-500 text-sm">Seu personagem escolhido</p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="label">Senha</label>
              <input v-model="form.password" type="password" class="input"
                     placeholder="••••••••" required minlength="8" />
            </div>
            <div>
              <label class="label">Confirmar senha</label>
              <input v-model="form.password_confirmation" type="password" class="input"
                     placeholder="••••••••" required />
            </div>
          </div>

          <div v-if="error"
               class="text-red-400 text-sm bg-red-500/10 border border-red-500/30 rounded-xl px-4 py-3">
            {{ error }}
          </div>

          <button type="submit" class="btn-primary w-full mt-2" :disabled="loading">
            <span v-if="loading">Cadastrando herói...</span>
            <span v-else>Entrar para o time ⚡</span>
          </button>
        </form>

        <div class="divider mt-6 pt-5">
          <p class="text-center text-gray-500 text-sm">
            Já tem conta?
            <router-link to="/login" class="text-hero-400 hover:text-hero-300 font-semibold transition-colors ml-1">
              Fazer login
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { CHARACTERS, CHARACTER_GROUPS } from '@/constants/characters'
import HeroAvatar from '@/components/HeroAvatar.vue'
import SelectInput from '@/components/SelectInput.vue'
import HeroLogo from '@/components/HeroLogo.vue'

const router = useRouter()
const auth   = useAuthStore()

const form = ref({
  name:                  '',
  email:                 '',
  character:             '',
  password:              '',
  password_confirmation: '',
})
const error   = ref('')
const loading = ref(false)

function charactersByGroup(group) {
  return CHARACTERS.filter(c => c.group === group)
}

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
