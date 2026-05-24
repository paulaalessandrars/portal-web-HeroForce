<template>
  <!--
    Emblema oficial do HeroForce — escudo heróico com estrela interna.
    Substitui o emoji ⚡ em toda a aplicação.
  -->
  <svg
    :width="size"
    :height="size"
    viewBox="0 0 40 40"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    :aria-label="label"
    role="img"
  >
    <defs>
      <linearGradient id="hf-grad" x1="5" y1="3" x2="35" y2="37" gradientUnits="userSpaceOnUse">
        <stop offset="0%"   stop-color="#a78bfa"/>
        <stop offset="50%"  stop-color="#7c3aed"/>
        <stop offset="100%" stop-color="#6d28d9"/>
      </linearGradient>
      <linearGradient id="hf-shine" x1="5" y1="3" x2="20" y2="20" gradientUnits="userSpaceOnUse">
        <stop offset="0%"   stop-color="rgba(255,255,255,0.25)"/>
        <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
      </linearGradient>
      <filter id="hf-star-glow" x="-30%" y="-30%" width="160%" height="160%">
        <feGaussianBlur stdDeviation="1.2" result="blur"/>
        <feMerge>
          <feMergeNode in="blur"/>
          <feMergeNode in="SourceGraphic"/>
        </feMerge>
      </filter>
    </defs>

    <!-- Sombra do escudo -->
    <path
      d="M20 4.5L34 10.5V21.5C34 30 20 37 20 37C20 37 6 30 6 21.5V10.5L20 4.5Z"
      fill="rgba(0,0,0,0.3)"
      transform="translate(0.5 1)"
    />

    <!-- Corpo do escudo -->
    <path
      d="M20 4.5L34 10.5V21.5C34 30 20 37 20 37C20 37 6 30 6 21.5V10.5L20 4.5Z"
      fill="url(#hf-grad)"
    />

    <!-- Brilho interno (reflexo de luz) -->
    <path
      d="M20 4.5L34 10.5V21.5C34 30 20 37 20 37C20 37 6 30 6 21.5V10.5L20 4.5Z"
      fill="url(#hf-shine)"
    />

    <!-- Borda do escudo -->
    <path
      d="M20 4.5L34 10.5V21.5C34 30 20 37 20 37C20 37 6 30 6 21.5V10.5L20 4.5Z"
      stroke="rgba(255,255,255,0.30)"
      stroke-width="0.8"
      fill="none"
    />

    <!-- Estrela de 5 pontas centralizada -->
    <path
      d="M20 11.5L21.9 17.2H27.9L23.0 20.7L24.9 26.4L20 22.9L15.1 26.4L17.0 20.7L12.1 17.2H18.1Z"
      fill="white"
      filter="url(#hf-star-glow)"
      opacity="0.95"
    />
  </svg>
</template>

<script setup>
defineProps({
  size:  { type: Number, default: 40 },
  label: { type: String, default: 'HeroForce' },
})
</script>
