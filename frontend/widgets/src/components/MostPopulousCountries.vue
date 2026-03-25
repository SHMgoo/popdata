<template>
  <BaseWidget
      :title="props.title"
      :subtitle="''"
      :titleSize="props.titleSize"
  >
    <section class="most-populous" :class="sizeClass">
      <div v-if="loading" class="mpc-status">
        Loading most populous countries…
      </div>

      <div v-else-if="error" class="mpc-status mpc-status--error">
        {{ error }}
      </div>

      <div v-else class="mpc-grid">
        <div class="mpc-column" v-for="(column, columnIndex) in columns" :key="columnIndex">
          <article
            v-for="country in column"
            :key="country.rank"
            class="mpc-row"
          >
            <div class="mpc-row__top">
              <div class="mpc-row__identity">
                <span class="mpc-rank">{{ country.rank }}</span>
                <span class="mpc-name">{{ country.name }}</span>
              </div>

              <span class="mpc-population">
                {{ formatNumber(country.population) }}
              </span>
            </div>

            <div class="mpc-bar">
              <div
                class="mpc-bar__fill"
                :style="{ width: `${barWidth(country.population)}%` }"
              />
            </div>
          </article>
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
    default: 'Most Populous Countries',
  },
  subtitle: {
    type: String,
    default: 'World',
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
  limit: {
    type: Number,
    default: 10,
  },
  titleSize: {
    type: String,
  },
})

const loading = ref(true)
const error = ref('')
const countries = ref([])

const sizeClass = computed(() => `most-populous--${props.size}`)

const columns = computed(() => {
  const midpoint = Math.ceil(countries.value.length / 2)
  return [
    countries.value.slice(0, midpoint),
    countries.value.slice(midpoint),
  ]
})

const maxPopulation = computed(() => {
  if (!countries.value.length) return 0
  return Math.max(...countries.value.map((item) => Number(item.population) || 0))
})

function formatNumber(value) {
  return Number(value || 0).toLocaleString('en-US')
}

function barWidth(population) {
  const max = Number(maxPopulation.value)
  const current = Number(population)

  if (!max || !current) return 0
  return Math.max(0, Math.min(100, (current / max) * 100))
}

async function loadCountries() {
  loading.value = true
  error.value = ''

  try {
    const response = await fetch(`${apiUrl(API_ENDPOINTS.worldRankings)}?limit=${props.limit}`)
    const data = await response.json()

    if (!response.ok || !data.ok) {
      throw new Error(data.error || 'Failed to load world rankings.')
    }

    countries.value = Array.isArray(data.countries) ? data.countries : []
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Unknown error.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCountries()
})
</script>

<style scoped>
.most-populous {
  display: flex;
  flex-direction: column;
}

.mpc-status {
  padding: 0.85rem 1rem;
  border: 1px solid #dfe1e2;
  border-radius: 0.8rem;
  background: #f7f7f7;
  font-size: var(--pop-font-size-sm);
  color: var(--pop-color-text-muted);
}

.mpc-status--error {
  border-color: #f4b9b9;
  background: #fff5f5;
  color: var(--pop-color-error);
}

.mpc-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem 1.25rem;
  align-items: start;
}

.mpc-column {
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
  min-width: 0;
}

.mpc-row {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.mpc-row__top {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 1rem;
}

.mpc-row__identity {
  display: flex;
  align-items: baseline;
  gap: 0.6rem;
  min-width: 0;
}

.mpc-rank {
  flex: 0 0 auto;
  font-weight: 700;
  color: var(--pop-color-primary, #112e51);
}

.mpc-name {
  font-weight: 600;
  color: var(--pop-color-text, #1b1b1b);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.mpc-population {
  flex: 0 0 auto;
  font-variant-numeric: tabular-nums;
  color: var(--pop-color-text-muted, #5c5c5c);
}

.mpc-bar {
  height: 0.55rem;
  border-radius: 999px;
  background: #e6e6e6;
  overflow: hidden;
}

.mpc-bar__fill {
  height: 100%;
  border-radius: 999px;
  background: linear-gradient(90deg, #005ea2 0%, #2e8bc0 100%);
}

.most-populous--sm .mpc-name,
.most-populous--sm .mpc-population,
.most-populous--sm .mpc-rank {
  font-size: 0.9rem;
}

.most-populous--md .mpc-name,
.most-populous--md .mpc-population,
.most-populous--md .mpc-rank {
  font-size: 1rem;
}

.most-populous--lg .mpc-name,
.most-populous--lg .mpc-population,
.most-populous--lg .mpc-rank {
  font-size: 1.08rem;
}

@media (max-width: 640px) {
  .mpc-grid {
    grid-template-columns: 1fr;
  }

  .mpc-row__top {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.15rem;
  }
}
</style>