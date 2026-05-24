<template>
  <div
    class="flex-shrink-0 rounded-full overflow-hidden"
    :style="{
      width: sizePx,
      height: sizePx,
      boxShadow: `0 0 0 2px ${config.ring}, 0 0 14px ${config.ring}66`
    }"
  >
    <img
      :src="imgSrc"
      :alt="character"
      class="w-full h-full object-cover object-top"
      loading="lazy"
      @error="onError"
    />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  character: { type: String, default: 'Iron Man' },
  size:      { type: Number, default: 40 }
})

const sizePx = computed(() => props.size + 'px')

/* ─── Imagens reais dos quadrinhos via jsdelivr CDN (akabab/superhero-api) ────
   Cada personagem aponta para o retrato oficial do comic/HQ.
   ring → cor do anel brilhante ao redor do avatar
   ─────────────────────────────────────────────────────────────────────────── */
const BASE = 'https://cdn.jsdelivr.net/gh/akabab/superhero-api@0.3.0/api/images/lg/'

const CONFIGS = {
  'Iron Man':        { img: BASE + '346-iron-man.jpg',        ring: '#ef5350' },
  'Spider-Man':      { img: BASE + '620-spider-man.jpg',      ring: '#e53935' },
  'Thor':            { img: BASE + '659-thor.jpg',            ring: '#ffd600' },
  'Captain America': { img: BASE + '149-captain-america.jpg', ring: '#e53935' },
  'Black Widow':     { img: BASE + '107-black-widow.jpg',     ring: '#e53935' },
  'Hulk':            { img: BASE + '332-hulk.jpg',            ring: '#76ff03' },
  'Black Panther':   { img: BASE + '106-black-panther.jpg',   ring: '#7c4dff' },
  'Doctor Strange':  { img: BASE + '226-doctor-strange.jpg',  ring: '#ffd600' },
  'Scarlet Witch':   { img: BASE + '579-scarlet-witch.jpg',   ring: '#e91e63' },
  'Deadpool':        { img: BASE + '213-deadpool.jpg',        ring: '#e53935' },
  'Batman':          { img: BASE + '70-batman.jpg',           ring: '#ffd600' },
  'Superman':        { img: BASE + '644-superman.jpg',        ring: '#e53935' },
  'Wonder Woman':    { img: BASE + '720-wonder-woman.jpg',    ring: '#ffd600' },
  'The Flash':       { img: BASE + '263-flash.jpg',           ring: '#ffd600' },
  'Aquaman':         { img: BASE + '38-aquaman.jpg',          ring: '#ffa726' },
  'Green Lantern':   { img: BASE + '306-hal-jordan.jpg',      ring: '#76ff03' },
  'Cyborg':          { img: BASE + '194-cyborg.jpg',          ring: '#00e5ff' },
  'Spawn':           { img: BASE + '612-spawn.jpg',           ring: '#b71c1c' },
  'Hellboy':         { img: BASE + '322-hellboy.jpg',         ring: '#ffd600' },
  'Invincible':      { img: 'https://api.dicebear.com/9.x/adventurer/svg?seed=mark-grayson-invincible&backgroundColor=1565c0&backgroundType=gradientLinear&radius=50&skinColor=f2d3b1&hairColor=2c1b18&hair=short02&eyes=variant04&eyebrows=variant04&mouth=variant07&featuresProbability=0', ring: '#fdd835' },
}

/* Fallback para personagens não mapeados */
const DEFAULT = {
  img: 'https://api.dicebear.com/9.x/adventurer/svg?seed=hero-default&backgroundColor=424242&radius=50',
  ring: '#9e9e9e'
}

const config   = computed(() => CONFIGS[props.character] || DEFAULT)
const errored  = ref(false)
const imgSrc   = computed(() =>
  errored.value
    ? `https://api.dicebear.com/9.x/adventurer/svg?seed=${encodeURIComponent(props.character)}&backgroundColor=424242&radius=50`
    : config.value.img
)

function onError() {
  errored.value = true
}
</script>
