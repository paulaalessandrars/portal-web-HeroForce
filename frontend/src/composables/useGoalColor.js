/**
 * Retorna classes CSS de cor baseadas no valor percentual de uma meta.
 * Regra: ≥70 verde | ≥40 amarelo | <40 vermelho
 */
export function useGoalColor() {
  function textColor(value) {
    if (value >= 70) return 'text-green-400'
    if (value >= 40) return 'text-yellow-400'
    return 'text-red-400'
  }

  function barColor(value) {
    if (value >= 70) return 'bg-green-500'
    if (value >= 40) return 'bg-yellow-500'
    return 'bg-red-500'
  }

  return { textColor, barColor }
}
