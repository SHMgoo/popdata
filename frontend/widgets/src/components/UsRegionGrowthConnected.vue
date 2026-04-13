<template>
  <div v-if="loading" class="widget-status">
    Loading region growth data…
  </div>

  <div v-else-if="error" class="widget-status widget-status--error">
    {{ error }}
  </div>

  <UsRegionGrowth
    v-else
    :title="title"
    :subtitle="subtitle"
    :years="years"
    :series="series"
  />
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import UsRegionGrowth from './UsRegionGrowth.vue'
import { apiUrl, API_ENDPOINTS } from '../../../shared/src/config/api.js'

const props = defineProps({
  title: {
    type: String,
    default: 'United States Population Growth by Region',
  },
  subtitle: {
    type: String,
    default: '',
  },
})

const loading = ref(true)
const error = ref('')
const payload = ref(null)

const years = computed(() => {
  const south = payload.value?.south?.values || []
  return south.map((item) => item.year)
})

const series = computed(() => {
  if (!payload.value) return []

  return [
    {
      key: 'northeast',
      label: payload.value.northeast?.label || 'Northeast',
      values: (payload.value.northeast?.values || []).map(
        (item) => Number(item.population) / 1000000
      ),
    },
    {
      key: 'midwest',
      label: payload.value.midwest?.label || 'Midwest',
      values: (payload.value.midwest?.values || []).map(
        (item) => Number(item.population) / 1000000
      ),
    },
    {
      key: 'west',
      label: payload.value.west?.label || 'West',
      values: (payload.value.west?.values || []).map(
        (item) => Number(item.population) / 1000000
      ),
    },
    {
      key: 'south',
      label: payload.value.south?.label || 'South',
      values: (payload.value.south?.values || []).map(
        (item) => Number(item.population) / 1000000
      ),
    },
  ]
})

onMounted(async () => {
  try {
    const response = await fetch(apiUrl(API_ENDPOINTS.usRegions))
    const data = await response.json()

    if (!response.ok || !data.ok) {
      throw new Error(data.error || 'Failed to load region growth data.')
    }

    payload.value = data
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
