<template>
  <nav class="sticky top-0 z-50 border-b backdrop-blur-2xl"
       style="background: var(--nav-bg); border-color: var(--border-color);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">

        <!-- Logo -->
        <router-link to="/dashboard" class="flex items-center gap-3 group">
          <div class="group-hover:scale-110 transition-transform duration-300 drop-shadow-lg">
            <HeroLogo :size="38" />
          </div>
          <span class="font-bold text-lg hidden sm:block tracking-wide" style="color: var(--text-primary);">
            Hero<span class="text-gradient">Force</span>
          </span>
        </router-link>

        <!-- Direita -->
        <div class="flex items-center gap-2 sm:gap-3">

          <!-- ── Seletor de tema ── -->
          <div class="flex items-center gap-2">
            <div class="flex items-center gap-0.5 p-1 rounded-xl border"
                 style="background: var(--bg-input); border-color: var(--border-color);">
              <button
                v-for="t in themes"
                :key="t.value"
                @click="theme = t.value"
                :title="t.label"
                class="w-7 h-7 rounded-lg text-sm flex items-center justify-center
                       transition-all duration-200"
                :class="theme === t.value
                  ? 'bg-hero-600 shadow-sm shadow-hero-900/50'
                  : 'hover:bg-white/10'"
              >
                {{ t.icon }}
              </button>
            </div>
          </div>

          <!-- Badge admin -->
          <span v-if="auth.isAdmin"
                class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
                       bg-hero-600/20 text-hero-300 border border-hero-500/30">
            <span class="w-1.5 h-1.5 rounded-full bg-hero-400 animate-pulse"></span>
            Admin
          </span>

          <!-- Info do usuário -->
          <div class="hidden sm:flex items-center gap-3 px-3 py-1.5 rounded-xl border"
               style="background: var(--bg-input); border-color: var(--border-color);">
            <HeroAvatar :character="user?.character" :size="34" />
            <div class="text-right">
              <p class="text-sm font-semibold leading-none" style="color: var(--text-primary);">{{ user?.name }}</p>
              <p class="text-xs mt-0.5" style="color: var(--text-muted);">{{ user?.character }}</p>
            </div>
          </div>

          <!-- Botão sair -->
          <button
            @click="handleLogout"
            class="flex items-center gap-2 px-3 py-2 rounded-xl transition-all duration-200 border"
            style="color: var(--text-muted); border-color: transparent;"
            @mouseenter="e => e.currentTarget.style.borderColor = 'var(--border-hover)'"
            @mouseleave="e => e.currentTarget.style.borderColor = 'transparent'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="hidden sm:block text-sm font-medium">Sair</span>
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useTheme } from '@/composables/useTheme'
import HeroAvatar from '@/components/HeroAvatar.vue'
import HeroLogo from '@/components/HeroLogo.vue'

const auth   = useAuthStore()
const router = useRouter()
const user   = computed(() => auth.user)

const { theme } = useTheme()

const themes = [
  { value: 'dark',  icon: '🌑', label: 'Dark Force'  },
  { value: 'light', icon: '☀️',  label: 'Arc Light'   },
  { value: 'navy',  icon: '🌊', label: 'Deep Space'  },
]

const currentTheme = computed(() => themes.find(t => t.value === theme.value))

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>
