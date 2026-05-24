/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js,ts}'],
  theme: {
    extend: {
      colors: {
        hero: {
          50:  '#f5f3ff',
          100: '#ede9fe',
          200: '#ddd6fe',
          300: '#c4b5fd',
          400: '#a78bfa',
          500: '#8b5cf6',
          600: '#7c3aed',
          700: '#6d28d9',
          800: '#5b21b6',
          900: '#4c1d95',
        },
        dark: {
          950: '#020208',
          900: '#07070f',
          800: '#0e0e1c',
          700: '#151528',
          600: '#1c1c35',
          500: '#252545',
        },
        neon: {
          purple: '#a78bfa',
          blue:   '#38bdf8',
          pink:   '#f472b6',
          green:  '#4ade80',
          yellow: '#fbbf24',
          cyan:   '#22d3ee',
        },
      },
      fontFamily: {
        hero: ['"Exo 2"', 'sans-serif'],
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translate(0px, 0px) scale(1)' },
          '33%':      { transform: 'translate(50px, -70px) scale(1.05)' },
          '66%':      { transform: 'translate(-40px, 40px) scale(0.97)' },
        },
        'float-reverse': {
          '0%, 100%': { transform: 'translate(0px, 0px) scale(1)' },
          '33%':      { transform: 'translate(-60px, 70px) scale(1.04)' },
          '66%':      { transform: 'translate(50px, -50px) scale(0.98)' },
        },
        'float-alt': {
          '0%, 100%': { transform: 'translate(0px, 0px) scale(1)' },
          '50%':      { transform: 'translate(30px, -30px) scale(1.06)' },
        },
        'pulse-glow': {
          '0%, 100%': { opacity: '0.5' },
          '50%':      { opacity: '0.9' },
        },
        'slide-up': {
          '0%':   { transform: 'translateY(28px)', opacity: '0' },
          '100%': { transform: 'translateY(0)',    opacity: '1' },
        },
        'fade-in': {
          '0%':   { opacity: '0' },
          '100%': { opacity: '1' },
        },
        'scan': {
          '0%':   { transform: 'translateY(-100%)' },
          '100%': { transform: 'translateY(100vh)' },
        },
      },
      animation: {
        'float':         'float 24s ease-in-out infinite',
        'float-slow':    'float-reverse 32s ease-in-out infinite',
        'float-alt':     'float-alt 18s ease-in-out infinite 6s',
        'pulse-glow':    'pulse-glow 4s ease-in-out infinite',
        'slide-up':      'slide-up 0.5s ease-out both',
        'fade-in':       'fade-in 0.4s ease-out both',
        'scan':          'scan 8s linear infinite',
      },
      boxShadow: {
        'glow-purple': '0 0 30px rgba(124, 58, 237, 0.4)',
        'glow-blue':   '0 0 30px rgba(56, 189, 248, 0.4)',
        'glow-pink':   '0 0 30px rgba(244, 114, 182, 0.4)',
        'neon':        '0 0 20px rgba(139, 92, 246, 0.6), 0 0 40px rgba(139, 92, 246, 0.3)',
      },
    },
  },
  plugins: [],
}
