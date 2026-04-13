export const API_BASE = import.meta.env.VITE_API_BASE || '/popdata'

export const API_ENDPOINTS = {
  health: '/health.php',
  manifest: '/manifest.php',
  usConfig: '/us-config.php',
  usPopulationSummary: '/us-population-summary.php',
  usPopulationOnDate: '/us-population-on-date.php',
  usComponentsOfChange: '/us-components-of-change.php',
  usRankings: '/us-rankings.php',
  worldCurrent: '/world-current.php',
  worldRankings: '/world-rankings.php',
  usPopulous: '/us-populous.php',
  usDensity: '/us-density.php',
  usRegions: '/us-regions.php',
  usAgeSex: '/us-age-sex.php',
}

export function apiUrl(endpoint) {
  return `${API_BASE}${endpoint}`
}