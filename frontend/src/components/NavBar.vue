<template>
  <nav class="bg-dark-800 border-b border-dark-700 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo -->
        <router-link to="/dashboard" class="flex items-center gap-3">
          <div class="w-9 h-9 bg-hero-600 rounded-lg flex items-center justify-center">
            <span class="text-xl">⚡</span>
          </div>
          <span class="text-white font-bold text-lg hidden sm:block">HeroForce</span>
        </router-link>

        <!-- User info -->
        <div class="flex items-center gap-4">
          <div class="hidden sm:flex items-center gap-3">
            <span class="text-2xl">{{ userEmoji }}</span>
            <div class="text-right">
              <p class="text-white text-sm font-medium leading-none">{{ user?.name }}</p>
              <p class="text-gray-400 text-xs mt-0.5">{{ user?.character }}</p>
            </div>
          </div>

          <span v-if="auth.isAdmin" class="hidden sm:inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-hero-900 text-hero-300 border border-hero-700">
            Admin
          </span>

          <button @click="handleLogout" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors px-3 py-2 rounded-lg hover:bg-dark-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="hidden sm:block text-sm">Sair</span>
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

const auth = useAuthStore()
const router = useRouter()
const user = computed(() => auth.user)

const EMOJIS = {
  'Iron Man': '🤖', 'Spider-Man': '🕷️', 'Thor': '⚡', 'Captain America': '🛡️',
  'Black Widow': '🕸️', 'Hulk': '💚', 'Black Panther': '🐾', 'Doctor Strange': '✨',
  'Scarlet Witch': '🔮', 'Deadpool': '⚔️', 'Batman': '🦇', 'Superman': '💫',
  'Wonder Woman': '👑', 'The Flash': '⚡', 'Aquaman': '🌊', 'Green Lantern': '💚',
  'Cyborg': '🤖', 'Spawn': '🔥', 'Hellboy': '😈', 'Invincible': '💪'
}
const userEmoji = computed(() => EMOJIS[user.value?.character] || '🦸')

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>
