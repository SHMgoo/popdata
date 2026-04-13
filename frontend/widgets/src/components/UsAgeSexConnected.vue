<template>
  <div v-if="loading" class="widget-status">
    Loading age and sex data…
  </div>

  <div v-else-if="error" class="widget-status widget-status--error">
    {{ error }}
  </div>

  <UsAgeSex
    v-else
    :title="title"
    :subtitle="subtitle"
    :years="years"
    :selected-index="selectedIndex"
    :max-percent="maxPercent"
    :male-label="maleLabel"
    :female-label="femaleLabel"
    :male-groups="selectedMaleGroups"
    :female-groups="selectedFemaleGroups"
    :is-playing="isPlaying"
    @update:selected-index="handleSelectedIndexUpdate"
    @toggle-play="togglePlay"
  />
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import UsAgeSex from './UsAgeSex.vue'
import { apiUrl, API_ENDPOINTS } from '../../../shared/src/config/api.js'

const props = defineProps({
  title: {
    type: String,
    default: 'United States Population by Age and Sex',
  },
  subtitle: {
    type: String,
    default: '',
  },
  playIntervalMs: {
    type: Number,
    default: 1400,
  },
})

const loading = ref(true)
const error = ref('')
const payload = ref(null)
const selectedIndex = ref(0)
const isPlaying = ref(false)
let timerId = null

const normalizedYears = computed(() => {
  const maleValues = payload.value?.male?.values || []
  const femaleValues = payload.value?.female?.values || []

  return maleValues.map((maleYear, index) => {
    const femaleYear = femaleValues[index] || {}
    const year = new Date(Number(maleYear.date) * 1000).getUTCFullYear()

    return {
      year,
      maleGroups: maleYear.age_groups || [],
      femaleGroups: femaleYear.age_groups || [],
    }
  })
})

const years = computed(() => normalizedYears.value.map((item) => item.year))

const maxPercent = computed(() => Number(payload.value?.max_percent) || 0.015)

const maleLabel = computed(() => payload.value?.male?.label || 'Male')
const femaleLabel = computed(() => payload.value?.female?.label || 'Female')

const selectedMaleGroups = computed(() => {
  return normalizedYears.value[selectedIndex.value]?.maleGroups || []
})

const selectedFemaleGroups = computed(() => {
  return normalizedYears.value[selectedIndex.value]?.femaleGroups || []
})

function stopPlaying() {
  if (timerId) {
    clearInterval(timerId)
    timerId = null
  }
  isPlaying.value = false
}

function startPlaying() {
  if (!years.value.length) return

  stopPlaying()
  isPlaying.value = true

  timerId = setInterval(() => {
    if (selectedIndex.value >= years.value.length - 1) {
      stopPlaying()
      return
    }

    selectedIndex.value += 1
  }, props.playIntervalMs)
}

function togglePlay() {
  if (isPlaying.value) {
    stopPlaying()
    return
  }

  if (selectedIndex.value >= years.value.length - 1) {
    selectedIndex.value = 0
  }

  startPlaying()
}

function handleSelectedIndexUpdate(index) {
  selectedIndex.value = Number(index) || 0
  stopPlaying()
}

watch(
  normalizedYears,
  (value) => {
    if (!value.length) return
    selectedIndex.value = value.length - 1
  },
  { immediate: true }
)

onMounted(async () => {
  try {
    const response = await fetch(apiUrl(API_ENDPOINTS.usAgeSex))
    const data = await response.json()

    if (!response.ok || !data.ok) {
      throw new Error(data.error || 'Failed to load age and sex data.')
    }

    payload.value = data
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Unknown error.'
  } finally {
    loading.value = false
  }
})

onBeforeUnmount(() => {
  stopPlaying()
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
