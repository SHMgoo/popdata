<template>
  <div v-if="loading" class="widget-status">Loading most populous data…</div>
  <div v-else-if="error" class="widget-status widget-status--error">
    {{ error }}
  </div>
  <GeoRankingTabs
    v-else
    title="Most Populous"
    :subtitle="subtitle"
    :states="payload.states"
    :counties="payload.counties"
    :cities="payload.cities"
    :initial-tab="initialTab"
    :auto-rotate="autoRotate"
    :rotate-seconds="rotateSeconds"
    :max-items="maxItems"
    :size="size"
    bar-metric="population"
  />
</template>

<script setup>
import { onMounted, ref } from 'vue'
import GeoRankingTabs from './GeoRankingTabs.vue'
import { apiUrl, API_ENDPOINTS } from '../../../shared/src/config/api.js'

const props = defineProps({
  subtitle: {
    type: String,
    default: '',
  },
  initialTab: {
    type: String,
    default: 'states',
  },
  autoRotate: {
    type: Boolean,
    default: false,
  },
  rotateSeconds: {
    type: Number,
    default: 5,
  },
  maxItems: {
    type: Number,
    default: 10,
  },
  size: {
    type: String,
    default: 'md',
  },
})

const loading = ref(true)
const error = ref('')
const payload = ref({
  states: [],
  counties: [],
  cities: [],
})

onMounted(async () => {
  try {
    const response = await fetch(apiUrl(API_ENDPOINTS.usPopulous))
    const data = await response.json()

    if (!response.ok || !data.ok) {
      throw new Error(data.error || 'Failed to load most populous data.')
    }

    payload.value = {
      states: Array.isArray(data.states) ? data.states : [],
      counties: Array.isArray(data.counties) ? data.counties : [],
      cities: Array.isArray(data.cities) ? data.cities : [],
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Unknown error.'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.widget-status {
  padding: 1rem;
  border: 1px solid #dfe1e2;
  border-radius: 0.75rem;
  background: #f7f7f7;
  color: #3d4551;
}

.widget-status--error {
  border-color: #f4b9b9;
  background: #fff5f5;
  color: #b50909;
}
</style>