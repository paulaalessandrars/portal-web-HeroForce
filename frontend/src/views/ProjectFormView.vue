<template>
  <div class="min-h-screen bg-dark-900">
    <NavBar />

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center gap-4 mb-8">
        <router-link to="/dashboard" class="text-gray-400 hover:text-white transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </router-link>
        <h1 class="text-2xl font-bold text-white">
          {{ isEdit ? 'Editar Missão' : 'Nova Missão' }}
        </h1>
      </div>

      <div class="card">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Basic info -->
          <div class="space-y-4">
            <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wider border-b border-dark-700 pb-2">
              Informações básicas
            </h2>

            <div>
              <label class="label">Nome da missão *</label>
              <input v-model="form.name" type="text" class="input" placeholder="Operação Escudo de Ferro" required />
            </div>

            <div>
              <label class="label">Descrição</label>
              <textarea v-model="form.description" class="input resize-none" rows="3" placeholder="Descreva os objetivos desta missão..."></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="label">Status *</label>
                <select v-model="form.status" class="input" required>
                  <option value="pendente">Pendente</option>
                  <option value="em andamento">Em andamento</option>
                  <option value="concluído">Concluído</option>
                </select>
              </div>
              <div>
                <label class="label">Herói responsável *</label>
                <select v-model="form.user_id" class="input" required>
                  <option value="" disabled>Selecione um herói</option>
                  <option v-for="u in heroes" :key="u.id" :value="u.id">
                    {{ u.name }} — {{ u.character }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Goals -->
          <div class="space-y-4">
            <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wider border-b border-dark-700 pb-2">
              Metas (0–100)
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div v-for="goal in goalFields" :key="goal.key">
                <label class="label">{{ goal.label }}</label>
                <div class="flex items-center gap-3">
                  <input
                    v-model.number="form[goal.key]"
                    type="range" min="0" max="100" step="5"
                    class="flex-1 accent-hero-500"
                  />
                  <span class="text-white font-bold w-10 text-right">{{ form[goal.key] }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Error -->
          <div v-if="error" class="text-red-400 text-sm bg-red-900/30 border border-red-800 rounded-lg px-4 py-2">
            {{ error }}
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-2">
            <router-link to="/dashboard" class="btn-secondary flex-1 text-center">Cancelar</router-link>
            <button type="submit" class="btn-primary flex-1" :disabled="loading">
              {{ loading ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar missão') }}
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import NavBar from '@/components/NavBar.vue'
import api from '@/api/axios'

const route = useRoute()
const router = useRouter()

const isEdit = computed(() => !!route.params.id)
const heroes = ref([])
const loading = ref(false)
const error = ref('')

const form = ref({
  name: '',
  description: '',
  status: 'pendente',
  user_id: '',
  goal_agility: 0,
  goal_enchantment: 0,
  goal_efficiency: 0,
  goal_excellence: 0,
  goal_transparency: 0,
  goal_ambition: 0
})

const goalFields = [
  { key: 'goal_agility', label: 'Agilidade' },
  { key: 'goal_enchantment', label: 'Encantamento' },
  { key: 'goal_efficiency', label: 'Eficiência' },
  { key: 'goal_excellence', label: 'Excelência' },
  { key: 'goal_transparency', label: 'Transparência' },
  { key: 'goal_ambition', label: 'Ambição' }
]

onMounted(async () => {
  const [userRes] = await Promise.all([api.get('/users')])
  heroes.value = userRes.data.data || userRes.data

  if (isEdit.value) {
    const { data } = await api.get(`/projects/${route.params.id}`)
    const p = data.data || data
    Object.assign(form.value, {
      name: p.name,
      description: p.description || '',
      status: p.status,
      user_id: p.user_id,
      goal_agility: p.goal_agility ?? 0,
      goal_enchantment: p.goal_enchantment ?? 0,
      goal_efficiency: p.goal_efficiency ?? 0,
      goal_excellence: p.goal_excellence ?? 0,
      goal_transparency: p.goal_transparency ?? 0,
      goal_ambition: p.goal_ambition ?? 0
    })
  }
})

async function handleSubmit() {
  error.value = ''
  loading.value = true
  try {
    if (isEdit.value) {
      await api.put(`/projects/${route.params.id}`, form.value)
    } else {
      await api.post('/projects', form.value)
    }
    router.push('/dashboard')
  } catch (e) {
    const errors = e.response?.data?.errors
    error.value = errors
      ? Object.values(errors).flat().join(' ')
      : (e.response?.data?.message || 'Erro ao salvar o projeto.')
  } finally {
    loading.value = false
  }
}
</script>
