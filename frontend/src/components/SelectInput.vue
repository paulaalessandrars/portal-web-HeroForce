<template>
  <div class="relative" ref="wrapper">

    <!-- ── Botão trigger ── -->
    <button
      ref="triggerRef"
      type="button"
      @click="toggle"
      class="input flex items-center justify-between gap-2 cursor-pointer text-left"
      :class="{ 'ring-2 ring-hero-500/40 border-hero-500/50': open }"
    >
      <span :class="hasValue ? 'text-[var(--text-primary)]' : 'text-[var(--text-placeholder)]'">
        {{ selectedLabel }}
      </span>
      <svg
        class="w-4 h-4 flex-shrink-0 transition-transform duration-200"
        style="color: var(--text-muted);"
        :class="{ 'rotate-180': open }"
        fill="none" stroke="currentColor" viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- ── Dropdown via Teleport → sempre acima de tudo ── -->
    <Teleport to="body">
      <Transition name="sel">
        <div
          v-if="open"
          :style="dropdownStyle"
          class="backdrop-blur-2xl border rounded-xl shadow-2xl overflow-hidden"
          style="border-color: var(--border-color); background: var(--bg-dropdown);"
        >
          <div class="max-h-64 overflow-y-auto py-1">

            <!-- Opção "todos" opcional -->
            <button
              v-if="allLabel !== undefined"
              type="button"
              @click="select('')"
              class="sel-option"
              :class="{ 'sel-option-active': !hasValue }"
            >
              {{ allLabel || placeholder }}
            </button>

            <!-- Opções simples -->
            <template v-if="!hasGroups">
              <button
                v-for="opt in options"
                :key="opt.value"
                type="button"
                @click="select(opt.value)"
                class="sel-option"
                :class="{ 'sel-option-active': String(modelValue) === String(opt.value) }"
              >
                {{ opt.label }}
              </button>
            </template>

            <!-- Opções agrupadas -->
            <template v-else>
              <div v-for="group in uniqueGroups" :key="group">
                <p class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-widest"
                   style="color: var(--text-muted); background: rgba(255,255,255,0.02);">
                  {{ group }}
                </p>
                <button
                  v-for="opt in optionsByGroup(group)"
                  :key="opt.value"
                  type="button"
                  @click="select(opt.value)"
                  class="sel-option"
                  :class="{ 'sel-option-active': String(modelValue) === String(opt.value) }"
                >
                  {{ opt.label }}
                </button>
              </div>
            </template>

          </div>
        </div>
      </Transition>
    </Teleport>

  </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue:  { default: '' },
  options:     { type: Array, default: () => [] },
  placeholder: { type: String, default: 'Selecione...' },
  allLabel:    { type: String, default: undefined },
})

const emit      = defineEmits(['update:modelValue'])
const wrapper   = ref(null)
const triggerRef = ref(null)
const open        = ref(false)
const dropdownStyle = ref({})

// ─── Computeds ───────────────────────────────────────────────────
const hasValue = computed(() =>
  props.modelValue !== '' && props.modelValue !== null && props.modelValue !== undefined
)

const hasGroups = computed(() => props.options.some(o => o.group))

const uniqueGroups = computed(() =>
  [...new Set(props.options.map(o => o.group).filter(Boolean))]
)

const selectedLabel = computed(() => {
  if (!hasValue.value) return props.placeholder
  const found = props.options.find(o => String(o.value) === String(props.modelValue))
  return found?.label || props.modelValue
})

// ─── Posicionamento do dropdown (fixo, relativo ao trigger) ───────
function updateDropdownPosition() {
  if (!triggerRef.value) return
  const rect = triggerRef.value.getBoundingClientRect()
  dropdownStyle.value = {
    position:  'fixed',
    top:       `${rect.bottom + 6}px`,
    left:      `${rect.left}px`,
    width:     `${rect.width}px`,
    minWidth:  '180px',
    zIndex:    9999,
  }
}

// ─── Ações ───────────────────────────────────────────────────────
function toggle() {
  open.value = !open.value
  if (open.value) nextTick(updateDropdownPosition)
}

function select(val) {
  emit('update:modelValue', val)
  open.value = false
}

function optionsByGroup(group) {
  return props.options.filter(o => o.group === group)
}

// ─── Fecha ao clicar fora ou pressionar Escape ────────────────────
function onClickOutside(e) {
  if (!open.value) return
  if (wrapper.value?.contains(e.target)) return
  // Verifica se clicou dentro do dropdown teleportado
  const dropdown = document.querySelector('[data-sel-dropdown]')
  if (dropdown?.contains(e.target)) return
  open.value = false
}

function onEscape(e) {
  if (e.key === 'Escape') open.value = false
}

// Reposiciona ao scrollar ou redimensionar
function onScroll() {
  if (open.value) updateDropdownPosition()
}

onMounted(() => {
  document.addEventListener('mousedown', onClickOutside)
  document.addEventListener('keydown',   onEscape)
  window.addEventListener('scroll',     onScroll, true)
  window.addEventListener('resize',     onScroll)
})

onUnmounted(() => {
  document.removeEventListener('mousedown', onClickOutside)
  document.removeEventListener('keydown',   onEscape)
  window.removeEventListener('scroll',     onScroll, true)
  window.removeEventListener('resize',     onScroll)
})
</script>

<style>
.sel-option {
  display: block;
  width: 100%;
  text-align: left;
  padding: 8px 14px;
  font-size: 0.875rem;
  color: var(--text-primary);
  transition: background 0.12s;
  cursor: pointer;
  background: transparent;
  border: none;
}
.sel-option:hover        { background: rgba(139,92,246,0.12); }
.sel-option-active       { background: rgba(139,92,246,0.22) !important; color: #a78bfa !important; font-weight: 600; }

.sel-enter-active { transition: opacity 0.14s ease, transform 0.14s ease; }
.sel-leave-active { transition: opacity 0.10s ease, transform 0.10s ease; }
.sel-enter-from   { opacity: 0; transform: translateY(-6px); }
.sel-leave-to     { opacity: 0; transform: translateY(-4px); }
</style>
