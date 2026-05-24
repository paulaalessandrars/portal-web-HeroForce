<template>
  <div class="card hover:border-hero-700 transition-all duration-200 flex flex-col gap-4">
    <!-- Header -->
    <div class="flex items-start justify-between gap-2">
      <div class="flex-1 min-w-0">
        <h3 class="text-white font-semibold text-lg truncate">{{ project.name }}</h3>
        <p class="text-gray-400 text-sm mt-1 line-clamp-2">{{ project.description }}</p>
      </div>
      <span :class="statusBadge">{{ statusLabel }}</span>
    </div>

    <!-- Responsible -->
    <div class="flex items-center gap-2">
      <span class="text-xl">{{ ownerEmoji }}</span>
      <div>
        <p class="text-gray-300 text-sm font-medium">{{ project.user?.name }}</p>
        <p class="text-gray-500 text-xs">{{ project.user?.character }}</p>
      </div>
    </div>

    <!-- Progress bar -->
    <div>
      <div class="flex items-center justify-between mb-1.5">
        <span class="text-xs text-gray-400 font-medium">Progresso geral</span>
        <span class="text-xs font-semibold" :class="progressColor">{{ progress }}%</span>
      </div>
      <div class="w-full bg-dark-700 rounded-full h-2">
        <div
          class="h-2 rounded-full transition-all duration-500"
          :class="progressBarColor"
          :style="{ width: progress + '%' }"
        ></div>
      </div>
    </div>

    <!-- Goals grid -->
    <div class="grid grid-cols-3 gap-2">
      <div v-for="goal in goals" :key="goal.key" class="bg-dark-700 rounded-lg p-2 text-center">
        <p class="text-lg font-bold" :class="goal.value >= 70 ? 'text-green-400' : goal.value >= 40 ? 'text-yellow-400' : 'text-red-400'">
          {{ goal.value }}
        </p>
        <p class="text-gray-500 text-xs leading-tight">{{ goal.label }}</p>
      </div>
    </div>

    <!-- Actions -->
    <div v-if="isAdmin" class="flex gap-2 pt-2 border-t border-dark-700">
      <router-link :to="`/projects/${project.id}/edit`" class="btn-secondary text-sm flex-1 text-center">
        Editar
      </router-link>
      <button @click="$emit('delete', project.id)" class="btn-danger text-sm">
        Excluir
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({ project: { type: Object, required: true } })
defineEmits(['delete'])

const auth = useAuthStore()
const isAdmin = computed(() => auth.isAdmin)

const EMOJIS = {
  'Iron Man': '🤖', 'Spider-Man': '🕷️', 'Thor': '⚡', 'Captain America': '🛡️',
  'Black Widow': '🕸️', 'Hulk': '💚', 'Black Panther': '🐾', 'Doctor Strange': '✨',
  'Scarlet Witch': '🔮', 'Deadpool': '⚔️', 'Batman': '🦇', 'Superman': '💫',
  'Wonder Woman': '👑', 'The Flash': '⚡', 'Aquaman': '🌊', 'Green Lantern': '💚',
  'Cyborg': '🤖', 'Spawn': '🔥', 'Hellboy': '😈', 'Invincible': '💪'
}
const ownerEmoji = computed(() => EMOJIS[props.project.user?.character] || '🦸')

const statusBadge = computed(() => ({
  'pendente': 'badge-pending',
  'em andamento': 'badge-progress',
  'concluído': 'badge-done'
}[props.project.status] || 'badge-pending'))

const statusLabel = computed(() => ({
  'pendente': 'Pendente',
  'em andamento': 'Em andamento',
  'concluído': 'Concluído'
}[props.project.status] || props.project.status))

const goals = computed(() => [
  { key: 'agility', label: 'Agilidade', value: props.project.goal_agility ?? 0 },
  { key: 'enchantment', label: 'Encantamento', value: props.project.goal_enchantment ?? 0 },
  { key: 'efficiency', label: 'Eficiência', value: props.project.goal_efficiency ?? 0 },
  { key: 'excellence', label: 'Excelência', value: props.project.goal_excellence ?? 0 },
  { key: 'transparency', label: 'Transparência', value: props.project.goal_transparency ?? 0 },
  { key: 'ambition', label: 'Ambição', value: props.project.goal_ambition ?? 0 },
])

const progress = computed(() => {
  const vals = goals.value.map(g => g.value).filter(v => v > 0)
  if (!vals.length) return 0
  return Math.round(vals.reduce((a, b) => a + b, 0) / (goals.value.length * 100) * 100)
})

const progressColor = computed(() =>
  progress.value >= 70 ? 'text-green-400' : progress.value >= 40 ? 'text-yellow-400' : 'text-red-400'
)
const progressBarColor = computed(() =>
  progress.value >= 70 ? 'bg-green-500' : progress.value >= 40 ? 'bg-yellow-500' : 'bg-red-500'
)
</script>
