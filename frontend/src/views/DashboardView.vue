<template>
  <div class="min-h-screen">

    <!-- ── Imagem do personagem logado como fundo da página ── -->
    <div v-if="characterImgUrl" class="fixed inset-0 z-0 pointer-events-none select-none overflow-hidden">
      <img :src="characterImgUrl"
           class="absolute right-0 bottom-0 h-[95vh] object-cover object-top transition-opacity duration-700"
           :style="{
             opacity: 'var(--char-opacity)',
             maskImage: 'linear-gradient(to left, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.4) 40%, transparent 70%)',
             WebkitMaskImage: 'linear-gradient(to left, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.4) 40%, transparent 70%)',
           }"
           alt="" />
    </div>

    <NavBar />

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- ── Banner de boas-vindas ── -->
      <div class="relative overflow-hidden card !p-0 mb-8 animate-slide-up">
        <!-- Imagem do personagem -->
        <div class="absolute right-0 top-0 h-full w-64 pointer-events-none select-none hidden md:block">
          <img :src="characterImgUrl"
               class="h-full w-full object-cover object-top opacity-25"
               style="mask-image: linear-gradient(to left, rgba(0,0,0,0.6), transparent);
                      -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,0.6), transparent);"
               alt="" />
        </div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 pr-8">
          <div class="flex items-center gap-4">
            <HeroAvatar :character="auth.user?.character" :size="56" />
            <div>
              <p class="text-gray-400 text-sm">Bem-vindo de volta,</p>
              <h1 class="text-2xl font-black text-white">{{ auth.user?.name }}</h1>
              <p class="text-hero-400 text-sm font-medium">{{ auth.user?.character }}</p>
            </div>
          </div>
          <router-link v-if="auth.isAdmin" to="/projects/new"
                       class="btn-primary inline-flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Nova Missão
          </router-link>
        </div>
      </div>

      <!-- ── Contadores ── -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div v-for="stat in stats" :key="stat.label"
             class="card text-center group hover:scale-[1.03] transition-transform duration-200 cursor-default">
          <div class="w-10 h-10 rounded-xl mx-auto mb-3 flex items-center justify-center"
               :class="stat.iconBg">
            <span class="text-xl">{{ stat.icon }}</span>
          </div>
          <p class="text-3xl font-black" :class="stat.color">{{ stat.value }}</p>
          <p class="text-gray-500 text-xs mt-1 font-medium uppercase tracking-wider">{{ stat.label }}</p>
        </div>
      </div>

      <!-- ── Filtros ── -->
      <div class="card mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
          <div class="flex-1">
            <label class="label">Buscar missão</label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
                   style="color: var(--text-muted);"
                   fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
              </svg>
              <input v-model="filters.search" type="text"
                     class="input !pl-10" placeholder="Nome da missão..." />
            </div>
          </div>
          <div class="sm:w-48">
            <label class="label">Status</label>
            <SelectInput
              v-model="filters.status"
              :options="statusOptions"
              placeholder="Todos"
              all-label="Todos"
            />
          </div>
          <div v-if="auth.isAdmin" class="sm:w-56">
            <label class="label">Herói responsável</label>
            <SelectInput
              v-model="filters.user_id"
              :options="heroOptions"
              placeholder="Todos os heróis"
              all-label="Todos os heróis"
            />
          </div>
          <div class="sm:w-32 flex items-end">
            <button @click="clearFilters" class="btn-secondary w-full">Limpar</button>
          </div>
        </div>
      </div>

      <!-- Contador de resultados -->
      <p class="text-gray-500 text-sm mb-4">
        {{ totalProjects }} missão(ões) encontrada(s)
      </p>

      <!-- ── Erro ── -->
      <div v-if="loadError" class="card text-center py-10">
        <span class="text-4xl">⚠️</span>
        <p class="text-red-400 mt-3">{{ loadError }}</p>
      </div>

      <!-- ── Skeleton loading ── -->
      <div v-else-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="i in 6" :key="i" class="card animate-pulse">
          <div class="h-5 bg-white/5 rounded-lg w-3/4 mb-3"></div>
          <div class="h-3 bg-white/5 rounded w-full mb-2"></div>
          <div class="h-3 bg-white/5 rounded w-2/3 mb-6"></div>
          <div class="grid grid-cols-3 gap-2">
            <div v-for="j in 6" :key="j" class="h-12 bg-white/5 rounded-xl"></div>
          </div>
        </div>
      </div>

      <!-- ── Grid de projetos ── -->
      <div v-else-if="filteredProjects.length"
           class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <ProjectCard
          v-for="project in filteredProjects"
          :key="project.id"
          :project="project"
          class="animate-slide-up"
          @click="openDetail(project)"
          @delete="confirmDelete"
        />
      </div>

      <!-- ── Estado vazio ── -->
      <div v-else class="card text-center py-20">
        <span class="text-7xl">🦸</span>
        <h3 class="text-2xl font-black text-white mt-5">Nenhuma missão encontrada</h3>
        <p class="text-gray-500 mt-2 max-w-sm mx-auto">
          {{ auth.isAdmin
            ? 'Crie a primeira missão clicando em "Nova Missão".'
            : 'Aguarde um administrador criar projetos.' }}
        </p>
      </div>
    </main>

    <!-- ── Modal de detalhes do projeto ── -->
    <ProjectDetailModal
      v-if="detailModal.project"
      :show="detailModal.show"
      :project="detailModal.project"
      @close="detailModal.show = false"
      @delete="id => { detailModal.show = false; confirmDelete(id) }"
      @status-updated="onStatusUpdated"
    />

    <!-- ── Modal de confirmação de exclusão ── -->
    <Transition name="modal">
      <div v-if="deleteModal.show"
           class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
           @click.self="deleteModal.show = false">
        <div class="card w-full max-w-sm animate-slide-up">
          <div class="w-12 h-12 rounded-2xl bg-red-500/10 border border-red-500/20
                      flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-white mb-1">Excluir missão?</h3>
          <p class="text-gray-500 text-sm mb-5">Esta ação não pode ser desfeita.</p>
          <p v-if="deleteModal.error" class="text-red-400 text-sm mb-4">{{ deleteModal.error }}</p>
          <div class="flex gap-3">
            <button @click="deleteModal.show = false" class="btn-secondary flex-1">Cancelar</button>
            <button @click="deleteProject" class="btn-danger flex-1" :disabled="deleteModal.loading">
              {{ deleteModal.loading ? 'Excluindo...' : 'Excluir' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import NavBar from '@/components/NavBar.vue'
import ProjectCard from '@/components/ProjectCard.vue'
import ProjectDetailModal from '@/components/ProjectDetailModal.vue'
import HeroAvatar from '@/components/HeroAvatar.vue'
import SelectInput from '@/components/SelectInput.vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'

const auth     = useAuthStore()
const projects = ref([])
const heroes   = ref([])
const loading  = ref(true)
const loadError = ref('')
const filters  = ref({ search: '', status: '', user_id: '' })
const deleteModal = ref({ show: false, id: null, loading: false, error: '' })
const detailModal = ref({ show: false, project: null })

// URL da imagem do personagem para o banner
const CHARACTER_IMGS = {
  'Iron Man':        '346-iron-man',
  'Spider-Man':      '620-spider-man',
  'Thor':            '659-thor',
  'Captain America': '149-captain-america',
  'Black Widow':     '107-black-widow',
  'Hulk':            '332-hulk',
  'Black Panther':   '106-black-panther',
  'Doctor Strange':  '226-doctor-strange',
  'Scarlet Witch':   '579-scarlet-witch',
  'Deadpool':        '213-deadpool',
  'Batman':          '70-batman',
  'Superman':        '644-superman',
  'Wonder Woman':    '720-wonder-woman',
  'The Flash':       '263-flash',
  'Aquaman':         '38-aquaman',
  'Green Lantern':   '306-hal-jordan',
  'Cyborg':          '194-cyborg',
  'Spawn':           '612-spawn',
  'Hellboy':         '322-hellboy',
}

const BASE = 'https://cdn.jsdelivr.net/gh/akabab/superhero-api@0.3.0/api/images/lg/'
const characterImgUrl = computed(() => {
  const slug = CHARACTER_IMGS[auth.user?.character]
  return slug ? `${BASE}${slug}.jpg` : ''
})

const filteredProjects = computed(() => {
  return projects.value.filter(p => {
    const matchSearch = !filters.value.search ||
      p.name.toLowerCase().includes(filters.value.search.toLowerCase())
    const matchStatus = !filters.value.status || p.status === filters.value.status
    const matchUser   = !filters.value.user_id || p.user_id === Number(filters.value.user_id)
    return matchSearch && matchStatus && matchUser
  })
})

const totalProjects = computed(() => filteredProjects.value.length)

const stats = computed(() => [
  {
    label:  'Total',
    value:  projects.value.length,
    color:  'text-white',
    icon:   '⚡',
    iconBg: 'bg-hero-600/20',
  },
  {
    label:  'Pendentes',
    value:  projects.value.filter(p => p.status === 'pendente').length,
    color:  'text-amber-400',
    icon:   '⏳',
    iconBg: 'bg-amber-500/10',
  },
  {
    label:  'Em andamento',
    value:  projects.value.filter(p => p.status === 'em andamento').length,
    color:  'text-sky-400',
    icon:   '🔄',
    iconBg: 'bg-sky-500/10',
  },
  {
    label:  'Concluídos',
    value:  projects.value.filter(p => p.status === 'concluído').length,
    color:  'text-emerald-400',
    icon:   '✅',
    iconBg: 'bg-emerald-500/10',
  },
])

const statusOptions = [
  { value: 'pendente',     label: '⏳ Pendente'      },
  { value: 'em andamento', label: '🔄 Em andamento'  },
  { value: 'concluído',    label: '✅ Concluído'      },
]

const heroOptions = computed(() =>
  heroes.value.map(u => ({ value: u.id, label: `${u.name} — ${u.character}` }))
)

function clearFilters() {
  filters.value = { search: '', status: '', user_id: '' }
}

function openDetail(project) {
  detailModal.value = { show: true, project }
}

function onStatusUpdated({ id, status }) {
  const p = projects.value.find(x => x.id === id)
  if (p) p.status = status
  if (detailModal.value.project?.id === id) {
    detailModal.value.project = { ...detailModal.value.project, status }
  }
}

function confirmDelete(id) {
  deleteModal.value = { show: true, id, loading: false, error: '' }
}

async function deleteProject() {
  deleteModal.value.loading = true
  deleteModal.value.error   = ''
  try {
    await api.delete(`/projects/${deleteModal.value.id}`)
    projects.value = projects.value.filter(p => p.id !== deleteModal.value.id)
    deleteModal.value.show = false
  } catch (e) {
    deleteModal.value.error = e.response?.data?.message || 'Erro ao excluir. Tente novamente.'
  } finally {
    deleteModal.value.loading = false
  }
}

onMounted(async () => {
  try {
    const [projRes, userRes] = await Promise.all([
      api.get('/projects'),
      api.get('/users'),
    ])
    projects.value = projRes.data.data || projRes.data
    heroes.value   = userRes.data.data || userRes.data
  } catch (e) {
    loadError.value = 'Não foi possível carregar os projetos. Tente recarregar a página.'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to       { opacity: 0; }
</style>
