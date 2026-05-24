<template>
  <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- ── Personagens decorativos laterais ── -->
    <div class="absolute inset-0 pointer-events-none select-none">
      <img src="https://cdn.jsdelivr.net/gh/akabab/superhero-api@0.3.0/api/images/lg/620-spider-man.jpg"
           class="absolute left-0 bottom-0 h-[90vh] object-cover object-top opacity-10 blur-sm"
           style="mask-image: linear-gradient(to right, rgba(0,0,0,0.6), transparent);
                  -webkit-mask-image: linear-gradient(to right, rgba(0,0,0,0.6), transparent);"
           alt="" />
      <img src="https://cdn.jsdelivr.net/gh/akabab/superhero-api@0.3.0/api/images/lg/346-iron-man.jpg"
           class="absolute right-0 bottom-0 h-[90vh] object-cover object-top opacity-10 blur-sm"
           style="mask-image: linear-gradient(to left, rgba(0,0,0,0.6), transparent);
                  -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,0.6), transparent);"
           alt="" />
    </div>

    <!-- ── Efeitos de raios no fundo ── -->
    <svg class="absolute inset-0 w-full h-full pointer-events-none select-none"
         preserveAspectRatio="xMidYMid slice" aria-hidden="true">

      <!-- Raio superior direito -->
      <polyline points="78%,0 72%,12% 77%,12% 65%,35% 69%,35% 55%,62%"
                stroke="rgba(139,92,246,0.55)" stroke-width="1.5"
                stroke-linecap="round" stroke-linejoin="round" fill="none"
                class="bolt-1"/>
      <!-- Brilho do raio 1 -->
      <polyline points="78%,0 72%,12% 77%,12% 65%,35% 69%,35% 55%,62%"
                stroke="rgba(200,170,255,0.30)" stroke-width="4"
                stroke-linecap="round" stroke-linejoin="round" fill="none"
                class="bolt-1" style="filter: blur(3px)"/>

      <!-- Raio lateral esquerdo -->
      <polyline points="18%,0 24%,14% 19%,14% 30%,38% 26%,38% 35%,60%"
                stroke="rgba(167,139,250,0.45)" stroke-width="1.2"
                stroke-linecap="round" stroke-linejoin="round" fill="none"
                class="bolt-2"/>
      <polyline points="18%,0 24%,14% 19%,14% 30%,38% 26%,38% 35%,60%"
                stroke="rgba(200,170,255,0.20)" stroke-width="4"
                stroke-linecap="round" stroke-linejoin="round" fill="none"
                class="bolt-2" style="filter: blur(3px)"/>

      <!-- Raio pequeno canto inferior direito -->
      <polyline points="90%,60% 85%,72% 89%,72% 80%,90%"
                stroke="rgba(124,58,237,0.50)" stroke-width="1"
                stroke-linecap="round" stroke-linejoin="round" fill="none"
                class="bolt-3"/>

      <!-- Flash de energia central (halo) -->
      <circle cx="50%" cy="42%" r="180" fill="none"
              stroke="rgba(139,92,246,0.08)" stroke-width="80"
              class="energy-ring"/>
      <circle cx="50%" cy="42%" r="90" fill="none"
              stroke="rgba(167,139,250,0.06)" stroke-width="40"
              class="energy-ring-2"/>
    </svg>

    <!-- ── Card de login ── -->
    <div class="w-full max-w-md relative animate-slide-up">

      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20
                    rounded-2xl mb-5 shadow-glow-purple relative">
          <HeroLogo :size="80" />
        </div>
        <h1 class="text-4xl font-black tracking-tight" style="color: var(--text-primary);">
          Hero<span class="text-gradient">Force</span>
        </h1>
        <p class="mt-2 text-sm tracking-widest uppercase" style="color: var(--text-muted);">
          Portal de Projetos Heroicos
        </p>
      </div>

      <div class="card">
        <h2 class="text-xl font-bold mb-6" style="color: var(--text-primary);">Entrar no sistema</h2>

        <form @submit.prevent="handleLogin" class="space-y-4">
          <div>
            <label class="label">E-mail</label>
            <input v-model="form.email" type="email" class="input"
                   placeholder="heroi@heroforce.com" required />
          </div>
          <div>
            <label class="label">Senha</label>
            <input v-model="form.password" type="password" class="input"
                   placeholder="••••••••" required />
          </div>

          <div v-if="error"
               class="text-red-400 text-sm bg-red-500/10 border border-red-500/30 rounded-xl px-4 py-3">
            {{ error }}
          </div>

          <button type="submit" class="btn-primary w-full mt-2" :disabled="loading">
            <span v-if="loading">Verificando identidade...</span>
            <span v-else>Acessar o sistema</span>
          </button>
        </form>

        <div class="divider mt-6 pt-5">
          <p class="text-center text-sm" style="color: var(--text-muted);">
            Novo herói?
            <router-link to="/register" class="text-hero-400 hover:text-hero-300 font-semibold transition-colors ml-1">
              Cadastre-se
            </router-link>
          </p>
        </div>
      </div>

      <!-- Demo rápido -->
      <div class="mt-4 card !py-4 !px-5">
        <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--text-muted);">
          Demo rápido
        </p>
        <div class="flex flex-wrap gap-2">
          <button v-for="cred in demoCredentials" :key="cred.email"
                  @click="fillDemo(cred)"
                  class="text-xs px-3 py-1.5 rounded-lg transition-all border"
                  style="background: var(--bg-btn-sec); border-color: var(--border-color); color: var(--text-muted);"
                  @mouseenter="e => e.currentTarget.style.color = 'var(--text-primary)'"
                  @mouseleave="e => e.currentTarget.style.color = 'var(--text-muted)'">
            {{ cred.label }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import HeroLogo from '@/components/HeroLogo.vue'

const router = useRouter()
const auth   = useAuthStore()

const form    = ref({ email: '', password: '' })
const error   = ref('')
const loading = ref(false)

const demoCredentials = [
  { label: '⚙️ Admin (Iron Man)',  email: 'admin@heroforce.com', password: 'password' },
  { label: '🕷️ Spider-Man',        email: 'peter@heroforce.com', password: 'password' },
  { label: '🦸‍♀️ Wonder Woman',   email: 'diana@heroforce.com', password: 'password' },
  { label: '🦇 Batman',            email: 'bruce@heroforce.com', password: 'password' },
]

function fillDemo(cred) {
  form.value.email    = cred.email
  form.value.password = cred.password
}

async function handleLogin() {
  error.value   = ''
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

<style scoped>
/* ── Raios: flash realista — quase invisível, pisca rápido ── */
.bolt-1 {
  opacity: 0;
  animation: bolt-flash 5s infinite 0.3s;
}
.bolt-2 {
  opacity: 0;
  animation: bolt-flash 7s infinite 2.1s;
}
.bolt-3 {
  opacity: 0;
  animation: bolt-flash 9s infinite 4.5s;
}

@keyframes bolt-flash {
  0%, 80%, 100%  { opacity: 0; }
  82%            { opacity: 1; }
  84%            { opacity: 0.15; }
  86%            { opacity: 0.9; }
  88%            { opacity: 0; }
}

/* ── Anéis de energia pulsando ── */
.energy-ring {
  animation: energy-pulse 6s ease-in-out infinite;
}
.energy-ring-2 {
  animation: energy-pulse 6s ease-in-out infinite 1s;
}

@keyframes energy-pulse {
  0%, 100% { opacity: 0.5; r: 180; }
  50%      { opacity: 1;   r: 200; }
}
</style>
