<template>
  <div class="group relative card !p-0 overflow-hidden cursor-pointer
              hover:border-white/20 hover:scale-[1.02] hover:shadow-glow-purple
              transition-all duration-300 flex flex-col">

    <!-- Barra lateral colorida por status -->
    <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-2xl" :class="statusBar"></div>

    <div class="flex flex-col gap-4 p-6 flex-1">
      <!-- ── Cabeçalho ── -->
      <div class="flex items-start justify-between gap-2">
        <div class="flex-1 min-w-0">
          <h3 class="text-white font-bold text-lg leading-snug truncate">{{ project.name }}</h3>
          <p class="text-gray-500 text-sm mt-1 line-clamp-2 leading-relaxed">{{ project.description }}</p>
        </div>
        <span :class="statusBadge" class="shrink-0">{{ statusLabel }}</span>
      </div>

      <!-- ── Herói responsável ── -->
      <div class="flex items-center gap-3 bg-white/[0.03] rounded-xl px-3 py-2.5
                  border border-white/[0.05]">
        <HeroAvatar :character="project.user?.character" :size="36" />
        <div>
          <p class="text-gray-200 text-sm font-semibold leading-none">{{ project.user?.name }}</p>
          <p class="text-gray-500 text-xs mt-0.5">{{ project.user?.character }}</p>
        </div>
      </div>

      <!-- ── Progresso geral ── -->
      <div>
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Progresso geral</span>
          <span class="text-sm font-black" :class="textColor(progress)">{{ progress }}%</span>
        </div>
        <div class="w-full bg-white/5 rounded-full h-2 overflow-hidden">
          <div
            class="h-2 rounded-full transition-all duration-700"
            :class="barColor(progress)"
            :style="{ width: progress + '%' }"
          ></div>
        </div>
      </div>

      <!-- ── Grid de metas ── -->
      <div class="grid grid-cols-3 gap-2">
        <div v-for="goal in goals" :key="goal.key"
             class="bg-white/[0.03] border border-white/[0.05] rounded-xl p-2 text-center
                    hover:bg-white/[0.06] transition-colors">
          <p class="text-base font-black" :class="textColor(goal.value)">{{ goal.value }}</p>
          <p class="text-gray-600 text-[10px] leading-tight mt-0.5 uppercase tracking-wide">{{ goal.label }}</p>
        </div>
      </div>
    </div>

    <!-- ── Ações (admin) ── -->
    <div v-if="isAdmin"
         class="flex gap-2 px-6 py-4 bg-white/[0.02] border-t border-white/[0.06]"
         @click.stop>
      <router-link :to="`/projects/${project.id}/edit`"
                   class="btn-secondary text-sm flex-1 text-center !py-2">
        Editar
      </router-link>
      <button @click="$emit('delete', project.id)"
              class="btn-danger text-sm !py-2 px-4">
        Excluir
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useGoalColor } from '@/composables/useGoalColor'
import HeroAvatar from '@/components/HeroAvatar.vue'

const props = defineProps({ project: { type: Object, required: true } })
defineEmits(['delete'])

const auth    = useAuthStore()
const isAdmin = computed(() => auth.isAdmin)

const { textColor, barColor } = useGoalColor()

const statusBadge = computed(() => ({
  'pendente':     'badge-pending',
  'em andamento': 'badge-progress',
  'concluído':    'badge-done',
}[props.project.status] || 'badge-pending'))

const statusLabel = computed(() => ({
  'pendente':     'Pendente',
  'em andamento': 'Em andamento',
  'concluído':    'Concluído',
}[props.project.status] || props.project.status))

// Barra lateral colorida por status
const statusBar = computed(() => ({
  'pendente':     'bg-gradient-to-b from-amber-400 to-amber-600',
  'em andamento': 'bg-gradient-to-b from-sky-400 to-sky-600',
  'concluído':    'bg-gradient-to-b from-emerald-400 to-emerald-600',
}[props.project.status] || 'bg-gray-600'))

const goals = computed(() => [
  { key: 'agility',      label: 'Agilidade',    value: props.project.goal_agility      ?? 0 },
  { key: 'enchantment',  label: 'Encantamento', value: props.project.goal_enchantment  ?? 0 },
  { key: 'efficiency',   label: 'Eficiência',   value: props.project.goal_efficiency   ?? 0 },
  { key: 'excellence',   label: 'Excelência',   value: props.project.goal_excellence   ?? 0 },
  { key: 'transparency', label: 'Transparência',value: props.project.goal_transparency ?? 0 },
  { key: 'ambition',     label: 'Ambição',      value: props.project.goal_ambition     ?? 0 },
])

const progress = computed(() => {
  const vals = goals.value.map(g => g.value).filter(v => v > 0)
  if (!vals.length) return 0
  return Math.round(vals.reduce((a, b) => a + b, 0) / (goals.value.length * 100) * 100)
})
</script>
