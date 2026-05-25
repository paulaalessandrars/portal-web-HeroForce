<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show"
           class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
           @click.self="$emit('close')">

        <div class="relative w-full max-w-xl animate-slide-up card !p-0 overflow-hidden max-h-[90vh] flex flex-col">

          <!-- Barra de status colorida no topo -->
          <div class="h-1 w-full shrink-0" :class="statusBar"></div>

          <!-- Imagem de fundo do herói (decorativa) -->
          <div class="absolute inset-0 pointer-events-none select-none overflow-hidden">
            <img v-if="heroImgUrl" :src="heroImgUrl"
                 class="absolute right-0 bottom-0 h-full object-cover object-top opacity-[0.06]"
                 style="mask-image: linear-gradient(to left, rgba(0,0,0,0.8), transparent);
                        -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,0.8), transparent);"
                 alt="" />
          </div>

          <!-- ── Cabeçalho ── -->
          <div class="relative flex items-start justify-between gap-4 px-6 pt-5 pb-4 shrink-0">
            <div class="flex-1 min-w-0">
              <span :class="statusBadge" class="mb-2 inline-block">{{ statusLabel }}</span>
              <h2 class="text-xl font-black leading-snug" style="color: var(--text-primary);">
                {{ project.name }}
              </h2>
            </div>
            <button @click="$emit('close')"
                    class="shrink-0 w-8 h-8 rounded-xl flex items-center justify-center transition-colors"
                    style="background: var(--bg-input); color: var(--text-muted);"
                    @mouseenter="e => e.currentTarget.style.color = 'var(--text-primary)'"
                    @mouseleave="e => e.currentTarget.style.color = 'var(--text-muted)'">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- ── Conteúdo scrollável ── -->
          <div class="relative overflow-y-auto flex-1 px-6 pb-6 space-y-5">

            <!-- Descrição completa -->
            <div class="rounded-xl p-4 border" style="background: var(--bg-input); border-color: var(--border-color);">
              <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color: var(--text-muted);">
                Descrição da missão
              </p>
              <p class="leading-relaxed text-sm" style="color: var(--text-primary);">
                {{ project.description || 'Sem descrição disponível.' }}
              </p>
            </div>

            <!-- Herói responsável -->
            <div class="flex items-center gap-3 rounded-xl px-4 py-3 border"
                 style="background: var(--bg-input); border-color: var(--border-color);">
              <HeroAvatar :character="project.user?.character" :size="44" />
              <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-0.5" style="color: var(--text-muted);">
                  Herói responsável
                </p>
                <p class="font-semibold" style="color: var(--text-primary);">{{ project.user?.name }}</p>
                <p class="text-sm" style="color: var(--text-muted);">{{ project.user?.character }}</p>
              </div>
            </div>

            <!-- Progresso geral -->
            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-widest" style="color: var(--text-muted);">
                  Progresso geral
                </span>
                <span class="text-sm font-black" :class="textColor(progress)">{{ progress }}%</span>
              </div>
              <div class="w-full rounded-full h-2.5 overflow-hidden" style="background: var(--bg-input);">
                <div class="h-2.5 rounded-full transition-all duration-700"
                     :class="barColor(progress)"
                     :style="{ width: progress + '%' }"></div>
              </div>
            </div>

            <!-- Grid de metas -->
            <div>
              <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color: var(--text-muted);">
                Metas heroicas
              </p>
              <div class="grid grid-cols-3 gap-2">
                <div v-for="goal in goals" :key="goal.key"
                     class="rounded-xl p-3 text-center border"
                     style="background: var(--bg-input); border-color: var(--border-color);">
                  <p class="text-xl font-black" :class="textColor(goal.value)">{{ goal.value }}</p>
                  <p class="text-[10px] mt-0.5 uppercase tracking-wide" style="color: var(--text-muted);">
                    {{ goal.label }}
                  </p>
                </div>
              </div>
            </div>

            <!-- ── Alterar status (todos os usuários) ── -->
            <div class="rounded-xl p-4 border" style="background: var(--bg-input); border-color: var(--border-color);">
              <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color: var(--text-muted);">
                Atualizar status da missão
              </p>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="opt in statusOptions"
                  :key="opt.value"
                  @click="changeStatus(opt.value)"
                  :disabled="savingStatus"
                  class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                         border transition-all duration-200"
                  :class="localStatus === opt.value
                    ? opt.activeClass
                    : 'opacity-50 hover:opacity-80'"
                  :style="localStatus !== opt.value
                    ? `background: var(--bg-btn-sec); border-color: var(--border-color); color: var(--text-muted);`
                    : ''"
                >
                  <span>{{ opt.icon }}</span>
                  <span>{{ opt.label }}</span>
                  <svg v-if="savingStatus && pendingStatus === opt.value"
                       class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                  </svg>
                </button>
              </div>
              <p v-if="statusError" class="text-red-400 text-xs mt-2">{{ statusError }}</p>
            </div>

          </div>

          <!-- ── Rodapé (admin) ── -->
          <div v-if="isAdmin"
               class="relative shrink-0 flex gap-3 px-6 py-4 border-t"
               style="background: var(--bg-card); border-color: var(--border-color);">
            <router-link :to="`/projects/${project.id}/edit`"
                         class="btn-secondary flex-1 text-center text-sm"
                         @click="$emit('close')">
              Editar missão
            </router-link>
            <button @click="$emit('delete', project.id)"
                    class="btn-danger text-sm px-5">
              Excluir
            </button>
          </div>
        </div>

      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useGoalColor } from '@/composables/useGoalColor'
import HeroAvatar from '@/components/HeroAvatar.vue'
import api from '@/api/axios'

const props = defineProps({
  show:    { type: Boolean, required: true },
  project: { type: Object,  required: true },
})

const emit = defineEmits(['close', 'delete', 'status-updated'])

const auth    = useAuthStore()
const isAdmin = computed(() => auth.isAdmin)
const { textColor, barColor } = useGoalColor()

// Status local — espelha o projeto mas pode ser alterado antes de salvar
const localStatus  = ref(props.project.status)
const savingStatus = ref(false)
const pendingStatus = ref('')
const statusError  = ref('')

watch(() => props.project.status, v => { localStatus.value = v })

const CHARACTER_IMGS = {
  'Iron Man':        '346-iron-man',      'Spider-Man':      '620-spider-man',
  'Thor':            '659-thor',          'Captain America': '149-captain-america',
  'Black Widow':     '107-black-widow',   'Hulk':            '332-hulk',
  'Black Panther':   '106-black-panther', 'Doctor Strange':  '226-doctor-strange',
  'Scarlet Witch':   '579-scarlet-witch', 'Deadpool':        '213-deadpool',
  'Batman':          '70-batman',         'Superman':        '644-superman',
  'Wonder Woman':    '720-wonder-woman',  'The Flash':       '263-flash',
  'Aquaman':         '38-aquaman',        'Green Lantern':   '306-hal-jordan',
  'Cyborg':          '194-cyborg',        'Spawn':           '612-spawn',
  'Hellboy':         '322-hellboy',
}

const BASE = 'https://cdn.jsdelivr.net/gh/akabab/superhero-api@0.3.0/api/images/lg/'
const heroImgUrl = computed(() => {
  const slug = CHARACTER_IMGS[props.project.user?.character]
  return slug ? `${BASE}${slug}.jpg` : ''
})

const statusOptions = [
  {
    value: 'pendente',
    label: 'Pendente',
    icon: '⏳',
    activeClass: 'bg-amber-500/20 border-amber-500/50 text-amber-300',
  },
  {
    value: 'em andamento',
    label: 'Em andamento',
    icon: '🔄',
    activeClass: 'bg-sky-500/20 border-sky-500/50 text-sky-300',
  },
  {
    value: 'concluído',
    label: 'Concluído',
    icon: '✅',
    activeClass: 'bg-emerald-500/20 border-emerald-500/50 text-emerald-300',
  },
]

const statusBadge = computed(() => ({
  'pendente':     'badge-pending',
  'em andamento': 'badge-progress',
  'concluído':    'badge-done',
}[localStatus.value] || 'badge-pending'))

const statusLabel = computed(() => ({
  'pendente':     'Pendente',
  'em andamento': 'Em andamento',
  'concluído':    'Concluído',
}[localStatus.value] || localStatus.value))

const statusBar = computed(() => ({
  'pendente':     'bg-gradient-to-r from-amber-400 to-amber-600',
  'em andamento': 'bg-gradient-to-r from-sky-400 to-sky-600',
  'concluído':    'bg-gradient-to-r from-emerald-400 to-emerald-600',
}[localStatus.value] || 'bg-gray-600'))

const goals = computed(() => [
  { key: 'agility',      label: 'Agilidade',     value: props.project.goal_agility      ?? 0 },
  { key: 'enchantment',  label: 'Encantamento',  value: props.project.goal_enchantment  ?? 0 },
  { key: 'efficiency',   label: 'Eficiência',    value: props.project.goal_efficiency   ?? 0 },
  { key: 'excellence',   label: 'Excelência',    value: props.project.goal_excellence   ?? 0 },
  { key: 'transparency', label: 'Transparência', value: props.project.goal_transparency ?? 0 },
  { key: 'ambition',     label: 'Ambição',       value: props.project.goal_ambition     ?? 0 },
])

const progress = computed(() => {
  const vals = goals.value.map(g => g.value).filter(v => v > 0)
  if (!vals.length) return 0
  return Math.round(vals.reduce((a, b) => a + b, 0) / (goals.value.length * 100) * 100)
})

async function changeStatus(status) {
  if (status === localStatus.value || savingStatus.value) return
  savingStatus.value  = true
  pendingStatus.value = status
  statusError.value   = ''
  try {
    await api.patch(`/projects/${props.project.id}/status`, { status })
    localStatus.value = status
    emit('status-updated', { id: props.project.id, status })
  } catch (e) {
    statusError.value = e.response?.data?.message || 'Erro ao atualizar status.'
  } finally {
    savingStatus.value  = false
    pendingStatus.value = ''
  }
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to       { opacity: 0; }
</style>
