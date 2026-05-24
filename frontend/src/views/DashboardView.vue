<template>
  <div class="min-h-screen bg-dark-900">
    <NavBar />

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
          <h1 class="text-2xl font-bold text-white">Painel de Missões</h1>
          <p class="text-gray-400 mt-1">{{ totalProjects }} projeto(s) encontrado(s)</p>
        </div>
        <router-link v-if="auth.isAdmin" to="/projects/new" class="btn-primary inline-flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Nova Missão
        </router-link>
      </div>

      <!-- Stats cards -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="card text-center">
          <p class="text-3xl font-bold text-white">{{ counts.total }}</p>
          <p class="text-gray-400 text-sm mt-1">Total</p>
        </div>
        <div class="card text-center">
          <p class="text-3xl font-bold text-yellow-400">{{ counts.pending }}</p>
          <p class="text-gray-400 text-sm mt-1">Pendentes</p>
        </div>
        <div class="card text-center">
          <p class="text-3xl font-bold text-blue-400">{{ counts.inProgress }}</p>
          <p class="text-gray-400 text-sm mt-1">Em andamento</p>
        </div>
        <div class="card text-center">
          <p class="text-3xl font-bold text-green-400">{{ counts.done }}</p>
          <p class="text-gray-400 text-sm mt-1">Concluídos</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="card mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
          <div class="flex-1">
            <label class="label">Buscar projeto</label>
            <input v-model="filters.search" type="text" class="input" placeholder="Nome do projeto..." />
          </div>
          <div class="sm:w-48">
            <label class="label">Status</label>
            <select v-model="filters.status" class="input">
              <option value="">Todos</option>
              <option value="pendente">Pendente</option>
              <option value="em andamento">Em andamento</option>
              <option value="concluído">Concluído</option>
            </select>
          </div>
          <div class="sm:w-56">
            <label class="label">Herói responsável</label>
            <select v-model="filters.user_id" class="input">
              <option value="">Todos os heróis</option>
              <option v-for="u in heroes" :key="u.id" :value="u.id">{{ u.name }}</option>
            </select>
          </div>
          <div class="sm:w-32 flex items-end">
            <button @click="clearFilters" class="btn-secondary w-full">Limpar</button>
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="i in 6" :key="i" class="card animate-pulse">
          <div class="h-5 bg-dark-700 rounded w-3/4 mb-3"></div>
          <div class="h-3 bg-dark-700 rounded w-full mb-2"></div>
          <div class="h-3 bg-dark-700 rounded w-2/3"></div>
        </div>
      </div>

      <!-- Projects grid -->
      <div v-else-if="filteredProjects.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <ProjectCard
          v-for="project in filteredProjects"
          :key="project.id"
          :project="project"
          @delete="confirmDelete"
        />
      </div>

      <!-- Empty state -->
      <div v-else class="card text-center py-16">
        <span class="text-6xl">🦸</span>
        <h3 class="text-xl font-semibold text-white mt-4">Nenhuma missão encontrada</h3>
        <p class="text-gray-400 mt-2">
          {{ auth.isAdmin ? 'Crie a primeira missão clicando em "Nova Missão".' : 'Aguarde um administrador criar projetos.' }}
        </p>
      </div>
    </main>

    <!-- Delete confirmation modal -->
    <div v-if="deleteModal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
      <div class="card w-full max-w-sm">
        <h3 class="text-lg font-semibold text-white mb-2">Excluir missão?</h3>
        <p class="text-gray-400 text-sm mb-6">Esta ação não pode ser desfeita.</p>
        <div class="flex gap-3">
          <button @click="deleteModal.show = false" class="btn-secondary flex-1">Cancelar</button>
          <button @click="deleteProject" class="btn-danger flex-1" :disabled="deleteModal.loading">
            {{ deleteModal.loading ? 'Excluindo...' : 'Excluir' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import NavBar from '@/components/NavBar.vue'
import ProjectCard from '@/components/ProjectCard.vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'

const auth = useAuthStore()
const projects = ref([])
const heroes = ref([])
const loading = ref(true)
const filters = ref({ search: '', status: '', user_id: '' })
const deleteModal = ref({ show: false, id: null, loading: false })

const totalProjects = computed(() => filteredProjects.value.length)

const filteredProjects = computed(() => {
  return projects.value.filter(p => {
    const matchSearch = !filters.value.search || p.name.toLowerCase().includes(filters.value.search.toLowerCase())
    const matchStatus = !filters.value.status || p.status === filters.value.status
    const matchUser = !filters.value.user_id || p.user_id === Number(filters.value.user_id)
    return matchSearch && matchStatus && matchUser
  })
})

const counts = computed(() => ({
  total: projects.value.length,
  pending: projects.value.filter(p => p.status === 'pendente').length,
  inProgress: projects.value.filter(p => p.status === 'em andamento').length,
  done: projects.value.filter(p => p.status === 'concluído').length
}))

function clearFilters() {
  filters.value = { search: '', status: '', user_id: '' }
}

function confirmDelete(id) {
  deleteModal.value = { show: true, id, loading: false }
}

async function deleteProject() {
  deleteModal.value.loading = true
  try {
    await api.delete(`/projects/${deleteModal.value.id}`)
    projects.value = projects.value.filter(p => p.id !== deleteModal.value.id)
    deleteModal.value.show = false
  } catch (e) {
    console.error(e)
  } finally {
    deleteModal.value.loading = false
  }
}

onMounted(async () => {
  try {
    const [projRes, userRes] = await Promise.all([
      api.get('/projects'),
      api.get('/users')
    ])
    projects.value = projRes.data.data || projRes.data
    heroes.value = userRes.data.data || userRes.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>
