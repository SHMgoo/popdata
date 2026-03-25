<template>
  <div v-if="loading">Loading population…</div>
  <div v-else-if="error">{{ error }}</div>
  <PopulationCounter
    v-else
    title="World Population"
    subtitle=""
    label=""
    meta=""
    :size="props.size"
    :base-population="summary.basePopulation"
    :base-epoch-ms="summary.baseEpochMs"
    :per-second="summary.perSecond"
    :icon-src="worldIcon"
    icon-alt=""
  />
</template>

<script setup>
import { onMounted, ref } from 'vue'
import PopulationCounter from './PopulationCounter.vue'
import { apiUrl, API_ENDPOINTS } from '../../../shared/src/config/api.js'

const props = defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
})

const worldIcon = `${import.meta.env.BASE_URL}images/world-population-icon.png`

const loading = ref(true)
const error = ref('')
const summary = ref(null)

onMounted(async () => {
  try {
    const response = await fetch(apiUrl(API_ENDPOINTS.worldCurrent))
    const data = await response.json()

    if (!response.ok || !data.ok) {
      throw new Error(data.error || 'Failed to load world population summary.')
    }

    summary.value = {
      basePopulation: Number(data.basePopulation),
      baseEpochMs: Number(data.baseEpochMs),
      perSecond: Number(data.perSecond),
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Unknown error.'
  } finally {
    loading.value = false
  }
})
</script>