/**
 * formatNumber
 *
 * Simple helper to format numbers with U.S. commas.
 * Example: 341234567 -> 341,234,567
 */
export function formatNumber(value) {
  const number = Number(value ?? 0)

  if (!Number.isFinite(number)) {
    return '0'
  }

  return new Intl.NumberFormat('en-US', {
    maximumFractionDigits: 0,
  }).format(Math.floor(number))
}
