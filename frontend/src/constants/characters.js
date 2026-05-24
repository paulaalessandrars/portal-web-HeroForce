/**
 * Lista canônica de personagens disponíveis no HeroForce.
 * Usada no dropdown de cadastro e como referência para avatares.
 */
export const CHARACTERS = [
  // Marvel
  { value: 'Iron Man',        group: 'Marvel' },
  { value: 'Spider-Man',      group: 'Marvel' },
  { value: 'Thor',            group: 'Marvel' },
  { value: 'Captain America', group: 'Marvel' },
  { value: 'Black Widow',     group: 'Marvel' },
  { value: 'Hulk',            group: 'Marvel' },
  { value: 'Black Panther',   group: 'Marvel' },
  { value: 'Doctor Strange',  group: 'Marvel' },
  { value: 'Scarlet Witch',   group: 'Marvel' },
  { value: 'Deadpool',        group: 'Marvel' },
  // DC
  { value: 'Batman',          group: 'DC' },
  { value: 'Superman',        group: 'DC' },
  { value: 'Wonder Woman',    group: 'DC' },
  { value: 'The Flash',       group: 'DC' },
  { value: 'Aquaman',         group: 'DC' },
  { value: 'Green Lantern',   group: 'DC' },
  { value: 'Cyborg',          group: 'DC' },
  // Outros
  { value: 'Spawn',           group: 'Outros' },
  { value: 'Hellboy',         group: 'Outros' },
  { value: 'Invincible',      group: 'Outros' },
]

/** Grupos únicos para renderizar os <optgroup> */
export const CHARACTER_GROUPS = [...new Set(CHARACTERS.map(c => c.group))]
