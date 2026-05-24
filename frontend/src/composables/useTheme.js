/**
 * useTheme — gerencia tema (dark/light/navy) e efeitos visuais por herói.
 *
 * Estado compartilhado no nível de módulo (singleton) — uma única instância
 * para toda a aplicação, sem Pinia. Persiste em localStorage.
 */
import { ref, watch } from 'vue'

// ─── Cores dos orbes por personagem ──────────────────────────────────────────
const HERO_EFFECTS = {
  'Iron Man':        { o1: 'rgba(239,68,68,0.30)',   o2: 'rgba(245,158,11,0.22)', o3: 'rgba(220,38,38,0.20)' },
  'Spider-Man':      { o1: 'rgba(220,38,38,0.28)',   o2: 'rgba(59,130,246,0.22)', o3: 'rgba(30,64,175,0.18)' },
  'Thor':            { o1: 'rgba(251,191,36,0.28)',   o2: 'rgba(59,130,246,0.22)', o3: 'rgba(99,102,241,0.18)' },
  'Captain America': { o1: 'rgba(59,130,246,0.28)',   o2: 'rgba(220,38,38,0.20)', o3: 'rgba(255,255,255,0.06)' },
  'Black Widow':     { o1: 'rgba(220,38,38,0.25)',   o2: 'rgba(15,15,15,0.50)',   o3: 'rgba(127,29,29,0.22)' },
  'Hulk':            { o1: 'rgba(74,222,128,0.25)',   o2: 'rgba(34,197,94,0.20)',  o3: 'rgba(22,163,74,0.20)' },
  'Black Panther':   { o1: 'rgba(124,58,237,0.28)',   o2: 'rgba(15,15,15,0.45)',   o3: 'rgba(109,40,217,0.20)' },
  'Doctor Strange':  { o1: 'rgba(220,38,38,0.25)',   o2: 'rgba(124,58,237,0.22)', o3: 'rgba(251,191,36,0.18)' },
  'Scarlet Witch':   { o1: 'rgba(220,38,38,0.32)',   o2: 'rgba(168,85,247,0.26)', o3: 'rgba(244,63,94,0.24)' },
  'Deadpool':        { o1: 'rgba(220,38,38,0.30)',   o2: 'rgba(20,20,20,0.35)',   o3: 'rgba(127,29,29,0.22)' },
  'Batman':          { o1: 'rgba(15,15,15,0.55)',     o2: 'rgba(251,191,36,0.14)', o3: 'rgba(40,40,40,0.45)' },
  'Superman':        { o1: 'rgba(59,130,246,0.28)',   o2: 'rgba(220,38,38,0.22)', o3: 'rgba(251,191,36,0.18)' },
  'Wonder Woman':    { o1: 'rgba(220,38,38,0.28)',   o2: 'rgba(251,191,36,0.24)', o3: 'rgba(59,130,246,0.18)' },
  'The Flash':       { o1: 'rgba(251,191,36,0.30)',   o2: 'rgba(220,38,38,0.20)', o3: 'rgba(245,158,11,0.24)' },
  'Aquaman':         { o1: 'rgba(14,165,233,0.30)',   o2: 'rgba(34,211,238,0.24)', o3: 'rgba(6,182,212,0.20)' },
  'Green Lantern':   { o1: 'rgba(74,222,128,0.28)',   o2: 'rgba(34,197,94,0.24)', o3: 'rgba(22,163,74,0.20)' },
  'Cyborg':          { o1: 'rgba(34,211,238,0.28)',   o2: 'rgba(59,130,246,0.22)', o3: 'rgba(6,182,212,0.20)' },
  'Spawn':           { o1: 'rgba(5,5,5,0.65)',         o2: 'rgba(220,38,38,0.18)', o3: 'rgba(30,30,30,0.55)' },
  'Hellboy':         { o1: 'rgba(220,38,38,0.32)',   o2: 'rgba(251,191,36,0.20)', o3: 'rgba(127,29,29,0.28)' },
  'Invincible':      { o1: 'rgba(37,99,235,0.30)',   o2: 'rgba(251,191,36,0.24)', o3: 'rgba(59,130,246,0.20)' },
}

const DEFAULT_EFFECTS = {
  o1: 'rgba(109,40,217,0.25)',
  o2: 'rgba(14,165,233,0.15)',
  o3: 'rgba(217,70,239,0.20)',
}

// ─── Estado singleton ─────────────────────────────────────────────────────────
const theme = ref(localStorage.getItem('hf-theme') || 'dark')

function applyTheme(t) {
  document.documentElement.setAttribute('data-theme', t)
  localStorage.setItem('hf-theme', t)
}

function applyHeroColors(character) {
  const e   = HERO_EFFECTS[character] || DEFAULT_EFFECTS
  const root = document.documentElement
  root.style.setProperty('--orb1', e.o1)
  root.style.setProperty('--orb2', e.o2)
  root.style.setProperty('--orb3', e.o3)
}

// Aplica imediatamente na carga do módulo
applyTheme(theme.value)

watch(theme, applyTheme)

// ─── Composable público ───────────────────────────────────────────────────────
export function useTheme() {
  return { theme, applyHeroColors }
}
