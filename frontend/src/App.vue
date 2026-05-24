<template>
  <div class="min-h-screen relative">

    <!-- ── Fundo cinematográfico com orbes por herói ── -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none" aria-hidden="true">
      <div class="absolute inset-0" style="background-color: var(--bg-body); transition: background-color 0.4s;"></div>

      <!-- Orbe 1 — cor do herói (canto sup. esquerdo) -->
      <div class="absolute -top-48 -left-48 w-[700px] h-[700px] rounded-full blur-[140px] animate-float"
           style="background-color: var(--orb1); transition: background-color 1s ease;"></div>

      <!-- Orbe 2 — cor secundária (centro-direita) -->
      <div class="absolute top-1/2 -right-64 w-[550px] h-[550px] rounded-full blur-[120px] animate-float-slow"
           style="background-color: var(--orb2); transition: background-color 1s ease;"></div>

      <!-- Orbe 3 — cor terciária (fundo esquerdo) -->
      <div class="absolute -bottom-48 left-1/4 w-[500px] h-[500px] rounded-full blur-[100px] animate-float-alt"
           style="background-color: var(--orb3); transition: background-color 1s ease;"></div>

      <!-- Orbe 4 — sutil no topo -->
      <div class="absolute -top-20 left-1/2 -translate-x-1/2 w-[400px] h-[300px]
                  rounded-full blur-[100px] animate-pulse-glow"
           style="background-color: var(--orb1); opacity: 0.4; transition: background-color 1s ease;"></div>

      <!-- Vinheta radial -->
      <div class="absolute inset-0"
           style="background: radial-gradient(ellipse at center, transparent 40%, rgba(0,0,0,0.55) 100%);"></div>
    </div>

    <!-- ── Conteúdo ── -->
    <div class="relative z-10 min-h-screen">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useTheme } from '@/composables/useTheme'

const auth = useAuthStore()
const { applyHeroColors } = useTheme()

// Aplica as cores do herói imediatamente se já estiver logado
if (auth.user?.character) {
  applyHeroColors(auth.user.character)
}

// Atualiza quando o usuário mudar (login/logout/troca de conta)
watch(() => auth.user?.character, (character) => {
  applyHeroColors(character || null)
})
</script>
