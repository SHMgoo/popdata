<template>
  <BaseWidget
     :title="''"
     :subtitle="''"
     :titleSize="''"
  >
    <section class="pop-on-date" :class="sizeClass">
      <div v-if="loading" class="pod-status">
        <div class="pod-status__dot"></div>
        <span>Loading population estimate...</span>
      </div>

      <div v-else-if="error" class="pod-status pod-status--error">
        <strong>Unable to load data.</strong>
        <span>{{ error }}</span>
      </div>

      <div v-else-if="result" class="pod-result">
        <div class="pod-select">
          <label class="pod-select__label" for="pod-date">
            <span class="pod-select__icon" aria-hidden="true">📅</span>
            <span class="pod-select__text">Select a date</span>
          </label>

          <div class="pod-input-wrap">
            <input
              id="pod-date"
              v-model="selectedDate"
              class="pod-input"
              type="date"
              :max="maxDate"
              @change="loadPopulation"
            />
          </div>
        </div>

        <div class="pod-main">
          <p class="pod-sentence">
            The United States population on
            <strong>{{ result.label }}</strong>
            was:
          </p>

          <div class="pod-number">
            {{ result.formattedPopulation }}
          </div>
        </div>

        <div class="pod-side">
          <div class="pod-side__item">
            <span class="pod-side__label">Source</span>
            <span class="pod-side__value">
              {{ result.source || 'U.S. Census Bureau PEP Daily' }}
            </span>
          </div>

          <div v-if="result.timezoneAtMidnight" class="pod-side__item">
            <span class="pod-side__label">Time Basis</span>
            <span class="pod-side__value">
              Midnight {{ result.timezoneAtMidnight }}
            </span>
          </div>
        </div>
      </div>
    </section>
  </BaseWidget>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import BaseWidget from './BaseWidget.vue'
import { apiUrl, API_ENDPOINTS } from '../../../shared/src/config/api.js'

const props = defineProps({
  title: {
    type: String,
    default: 'Select a past date to see what the United States population was',
  },
  subtitle: {
    type: String,
    default: '',
  },
  titleSize: {
    type: String,
    default: '1.15rem',
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
})

const loading = ref(true)
const error = ref('')
const result = ref(null)
const selectedDate = ref(yesterdayString())

const sizeClass = computed(() => `pop-on-date--${props.size}`)
const maxDate = computed(() => yesterdayString())

function formatDateInput(date) {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

function yesterdayString() {
  const date = new Date()
  date.setDate(date.getDate() - 1)
  return formatDateInput(date)
}

async function loadPopulation() {
  loading.value = true
  error.value = ''

  try {
    const url = `${apiUrl(API_ENDPOINTS.usPopulationOnDate)}?date=${selectedDate.value}`
    console.log('UsPopulationOnDate fetch URL:', url)

    const response = await fetch(url)
    const raw = await response.text()

    console.log('UsPopulationOnDate status:', response.status)
    console.log('UsPopulationOnDate raw response:', raw)

    const data = JSON.parse(raw)

    if (!response.ok || !data.ok) {
      throw new Error(data.error || 'Failed to load population for selected date.')
    }

    result.value = data.data
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Unknown error.'
    result.value = null
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadPopulation()
})
</script>

<style scoped>
.pop-on-date {
  display: flex;
  flex-direction: column;
}

.pod-status {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.9rem 1rem;
  border: 1px solid #dfe1e2;
  border-radius: 0.85rem;
  background: #f0f6fb;
  color: #1a4480;
  font-size: 0.9rem;
}

.pod-status__dot {
  width: 0.7rem;
  height: 0.7rem;
  border-radius: 999px;
  background: #005ea2;
  flex: 0 0 auto;
}

.pod-status--error {
  flex-direction: column;
  align-items: flex-start;
  gap: 0.35rem;
  border-color: #f4b9b9;
  background: #fff5f5;
  color: #b50909;
}

.pod-result {
  display: grid;
  grid-template-columns: minmax(200px, 240px) minmax(0, 1fr) minmax(190px, 240px);
  align-items: center;
  gap: 1.35rem;
  padding: 0.75rem 1.2rem;
  border: 1px solid #cfe1f3;
  border-left: 0.45rem solid #005ea2;
  border-radius: 0.9rem;
  background: linear-gradient(to bottom right, #f8fbff, #eef5fb);
}

.pod-select {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  min-width: 0;
}

.pod-select__label {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: #005ea2;
}

.pod-select__icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 1.85rem;
  height: 1.85rem;
  border-radius: 999px;
  background: #e8f1f8;
  font-size: 0.95rem;
  line-height: 1;
}

.pod-input-wrap {
  position: relative;
  padding-left: 2.3rem;
}

.pod-input {
  width: 120px;
  min-height: 2rem;
  padding: 0.25rem 0.45rem;
  border: 1px solid #a9aeb1;
  border-radius: 0.35rem;
  font: inherit;
  font-size: 0.82rem;
  background: #f3f4f6;
  color: #1b1b1b;
  box-shadow: none;
}

.pod-input:hover {
  border-color: #4f5b66;
}

.pod-input:focus {
  outline: 3px solid rgba(0, 94, 162, 0.22);
  outline-offset: 1px;
  border-color: #005ea2;
}

.pod-main {
  min-width: 0;
  text-align: center;
}

.pod-sentence {
  margin: 0 0 0.45rem;
  color: #3d4551;
  line-height: 1.45;
}

.pod-number {
  font-weight: 800;
  line-height: 1;
  letter-spacing: -0.02em;
  color: #c05600;
  font-variant-numeric: tabular-nums;
}

.pod-side {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
  min-width: 0;
  padding-left: 4.5rem;
}

.pod-side__item {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.pod-side__label {
  font-size: 0.72rem;
  font-weight: 800;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: #3d4551;
}

.pod-side__value {
  font-size: 0.72rem;
  line-height: 1.4;
  color: #1b1b1b;
}

.pop-on-date--sm .pod-number {
  font-size: 1.7rem;
}

.pop-on-date--sm .pod-sentence {
  font-size: 0.9rem;
}

.pop-on-date--md .pod-number {
  font-size: 2.45rem;
}

.pop-on-date--md .pod-sentence {
  font-size: 1.05rem;
}

.pop-on-date--lg .pod-number {
  font-size: 3rem;
}

.pop-on-date--lg .pod-sentence {
  font-size: 1.1rem;
}

@media (max-width: 700px) {
  .pod-result {
    grid-template-columns: 1fr;
    gap: 0.95rem;
  }

  .pod-main {
    text-align: left;
  }

  .pod-side {
    border-top: 1px solid #dfe1e2;
    padding-top: 0.8rem;
    padding-left: 0;
  }
}
</style>