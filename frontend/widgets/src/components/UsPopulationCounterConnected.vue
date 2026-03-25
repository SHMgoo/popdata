<template>
  <div v-if="loading" class="counter-status">Loading population…</div>
  <div v-else-if="error" class="counter-status counter-status--error">
    {{ error }}
  </div>
  <PopulationCounter
    v-else
    title="U.S. Population"
    subtitle=""
    label=""
    meta=""
    :size="props.size"
    :base-population="summary.basePopulation"
    :base-epoch-ms="summary.baseEpochMs"
    :per-second="summary.perSecond"
    :icon-src="usIcon"
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

const usIcon = `${import.meta.env.BASE_URL}images/us-population-icon.png`

const loading = ref(true)
const error = ref('')
const summary = ref(null)

onMounted(async () => {
  try {
    const response = await fetch(apiUrl(API_ENDPOINTS.usPopulationSummary))
    const data = await response.json()

    if (!response.ok || !data.ok) {
      throw new Error(data.error || 'Failed to load U.S. population summary.')
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

<style scoped>
.counter-status {
  padding: 1rem;
  border: 1px solid #dfe1e2;
  border-radius: 0.75rem;
  background: #f7f7f7;
  color: #3d4551;
}

.counter-status--error {
  border-color: #f4b9b9;
  background: #fff5f5;
  color: #b50909;
}
</style>