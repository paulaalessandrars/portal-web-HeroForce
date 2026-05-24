<template>
  <div class="min-h-screen">
    <NavBar />

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Cabeçalho da página -->
      <div class="flex items-center gap-4 mb-8 animate-slide-up">
        <router-link to="/dashboard"
                     class="w-10 h-10 rounded-xl bg-white/[0.06] border border-white/10
                            flex items-center justify-center text-gray-400 hover:text-white
                            hover:bg-white/[0.10] transition-all duration-200">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </router-link>
        <div>
          <h1 class="text-2xl font-black text-white">
            {{ isEdit ? 'Editar Missão' : 'Nova Missão' }}
          </h1>
          <p class="text-gray-500 text-sm mt-0.5">
            {{ isEdit ? 'Atualize os detalhes desta missão' : 'Configure uma nova missão para o time' }}
          </p>
        </div>
      </div>

      <div class="card animate-slide-up" style="animation-delay: 0.1s">
        <form @submit.prevent="handleSubmit" class="space-y-8">

          <!-- ── Seção: Informações básicas ── -->
          <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-white/[0.06]">
              <div class="w-8 h-8 rounded-lg bg-hero-600/20 flex items-center justify-center">
                <span class="text-sm">📋</span>
              </div>
              <h2 class="text-sm font-bold text-gray-300 uppercase tracking-widest">
                Informações básicas
              </h2>
            </div>

            <div>
              <label class="label">Nome da missão *</label>
              <input v-model="form.name" type="text" class="input"
                     placeholder="Operação Escudo de Ferro" required />
            </div>

            <div>
              <label class="label">Descrição</label>
              <textarea v-model="form.description" class="input resize-none" rows="3"
                        placeholder="Descreva os objetivos desta missão..."></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="label">Status *</label>
                <SelectInput
                  v-model="form.status"
                  :options="[
                    { value: 'pendente',     label: '⏳ Pendente'     },
                    { value: 'em andamento', label: '🔄 Em andamento' },
                    { value: 'concluído',    label: '✅ Concluído'     },
                  ]"
                  placeholder="Selecione o status"
                />
              </div>
              <div>
                <label class="label">Herói responsável *</label>
                <SelectInput
                  v-model="form.user_id"
                  :options="heroes.map(u => ({ value: u.id, label: `${u.name} — ${u.character}` }))"
                  placeholder="Selecione um herói"
                />
              </div>
            </div>
          </div>

          <!-- ── Seção: Metas ── -->
          <div class="space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-white/[0.06]">
              <div class="w-8 h-8 rounded-lg bg-hero-600/20 flex items-center justify-center">
                <span class="text-sm">🎯</span>
              </div>
              <h2 class="text-sm font-bold text-gray-300 uppercase tracking-widest">
                Metas (0 – 100)
              </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div v-for="goal in goalFields" :key="goal.key">
                <div class="flex items-center justify-between mb-2">
                  <label class="label !mb-0">{{ goal.label }}</label>
                  <span class="text-sm font-black px-2 py-0.5 rounded-lg bg-white/[0.06]"
                        :class="goalValueColor(form[goal.key])">
                    {{ form[goal.key] }}
                  </span>
                </div>
                <input
                  v-model.number="form[goal.key]"
                  type="range" min="0" max="100" step="5"
                  class="w-full h-2 rounded-full appearance-none cursor-pointer
                         bg-white/10 accent-hero-500"
                />
              </div>
            </div>
          </div>

          <!-- ── Erro ── -->
          <div v-if="error"
               class="text-red-400 text-sm bg-red-500/10 border border-red-500/30 rounded-xl px-4 py-3">
            {{ error }}
          </div>

          <!-- ── Ações ── -->
          <div class="flex gap-3 pt-2">
            <router-link to="/dashboard" class="btn-secondary flex-1 text-center">
              Cancelar
            </router-link>
            <button type="submit" class="btn-primary flex-1" :disabled="loading">
              {{ loading ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar missão ⚡') }}
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
import SelectInput from '@/components/SelectInput.vue'
import { useGoalColor } from '@/composables/useGoalColor'
import api from '@/api/axios'

const route  = useRoute()
const router = useRouter()

const { textColor: goalValueColor } = useGoalColor()

const isEdit  = computed(() => !!route.params.id)
const heroes  = ref([])
const loading = ref(false)
const error   = ref('')

const form = ref({
  name:               '',
  description:        '',
  status:             'pendente',
  user_id:            '',
  goal_agility:       0,
  goal_enchantment:   0,
  goal_efficiency:    0,
  goal_excellence:    0,
  goal_transparency:  0,
  goal_ambition:      0,
})

const goalFields = [
  { key: 'goal_agility',       label: 'Agilidade'     },
  { key: 'goal_enchantment',   label: 'Encantamento'  },
  { key: 'goal_efficiency',    label: 'Eficiência'    },
  { key: 'goal_excellence',    label: 'Excelência'    },
  { key: 'goal_transparency',  label: 'Transparência' },
  { key: 'goal_ambition',      label: 'Ambição'       },
]

onMounted(async () => {
  const [userRes] = await Promise.all([api.get('/users')])
  heroes.value = userRes.data.data || userRes.data

  if (isEdit.value) {
    const { data } = await api.get(`/projects/${route.params.id}`)
    const p = data.data || data
    Object.assign(form.value, {
      name:               p.name,
      description:        p.description  || '',
      status:             p.status,
      user_id:            p.user_id,
      goal_agility:       p.goal_agility       ?? 0,
      goal_enchantment:   p.goal_enchantment   ?? 0,
      goal_efficiency:    p.goal_efficiency    ?? 0,
      goal_excellence:    p.goal_excellence    ?? 0,
      goal_transparency:  p.goal_transparency  ?? 0,
      goal_ambition:      p.goal_ambition      ?? 0,
    })
  }
})

async function handleSubmit() {
  error.value   = ''
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
