<template>
  <BaseWidget
    :title="props.title"
    :subtitle="''"
  >
    <section class="components-of-change" :class="sizeClass">
      <div v-if="loading" class="coc-status">
        Loading components of change…
      </div>

      <div v-else-if="error" class="coc-status coc-status--error">
        {{ error }}
      </div>

      <div v-else class="coc-list">
        <article class="coc-row">
          <div class="coc-copy">
            <span class="coc-kicker">Births</span>
            <span class="coc-value">
              One birth every {{ Math.round(birthsEverySeconds) }} seconds
            </span>
          </div>
          <div class="coc-bar">
            <div
              class="coc-bar-fill"
              :style="{ width: `${progress(birthsProgress)}%` }"
            />
          </div>
        </article>

        <article class="coc-row">
          <div class="coc-copy">
            <span class="coc-kicker">Deaths</span>
            <span class="coc-value">
              One death every {{ Math.round(deathsEverySeconds) }} seconds
            </span>
          </div>
          <div class="coc-bar">
            <div
              class="coc-bar-fill coc-bar-fill--drain"
              :style="{ width: `${reverseProgress(deathsProgress)}%` }"
            />
          </div>
        </article>

        <article class="coc-row">
          <div class="coc-copy">
            <span class="coc-kicker">International Migration</span>
            <span class="coc-value">
              One international migrant (net) every {{ Math.round(netMigrationEverySeconds) }} seconds
            </span>
          </div>
          <div class="coc-bar">
            <div
              class="coc-bar-fill"
              :style="{ width: `${progress(netMigrationProgress)}%` }"
            />
          </div>
        </article>

        <article class="coc-row coc-row--net">
          <div class="coc-copy">
            <span class="coc-kicker">Net Gain</span>
            <span class="coc-value">
              Net gain of one person every {{ Math.round(netGainEverySeconds) }} seconds
            </span>
          </div>
          <div class="coc-bar">
            <div
              class="coc-bar-fill"
              :style="{ width: `${progress(netGainProgress)}%` }"
            />
          </div>
        </article>
      </div>
    </section>
  </BaseWidget>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import BaseWidget from './BaseWidget.vue'
import { apiUrl, API_ENDPOINTS } from '../../../shared/src/config/api.js'

const props = defineProps({
  title: {
    type: String,
    default: 'Components of Population Change',
  },
  subtitle: {
    type: String,
    default: 'United States',
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
})

const loading = ref(true)
const error = ref('')
const summary = ref(null)
const nowMs = ref(Date.now())

let timerId = null

const sizeClass = computed(() => `components-of-change--${props.size}`)

const netGainEverySeconds = computed(() => {
  return Number(summary.value?.secondsPerPersonNetGain ?? 0)
})

const birthsEverySeconds = computed(() => {
  return Number(summary.value?.components?.birthEverySeconds ?? 0)
})

const deathsEverySeconds = computed(() => {
  return Number(summary.value?.components?.deathEverySeconds ?? 0)
})

const netMigrationEverySeconds = computed(() => {
  return Number(summary.value?.components?.netMigEverySeconds ?? 0)
})

const secondsSinceBase = computed(() => {
  const baseEpochMs = Number(summary.value?.baseEpochMs ?? 0)

  if (!baseEpochMs) return 0

  const elapsed = (nowMs.value - baseEpochMs) / 1000
  return Math.max(0, elapsed)
})

function cycleProgress(intervalSeconds) {
  const interval = Number(intervalSeconds)

  if (!interval || interval <= 0) return 0

  return (secondsSinceBase.value % interval) / interval
}

function reverseProgress(value) {
  const percent = 100 - (Number(value) * 100)
  return Math.max(0, Math.min(100, percent))
}

const birthsProgress = computed(() => cycleProgress(birthsEverySeconds.value))
const deathsProgress = computed(() => cycleProgress(deathsEverySeconds.value))
const netMigrationProgress = computed(() => cycleProgress(netMigrationEverySeconds.value))
const netGainProgress = computed(() => cycleProgress(netGainEverySeconds.value))

function progress(value) {
  const percent = Number(value) * 100
  return Math.max(0, Math.min(100, percent))
}

async function loadSummary() {
  loading.value = true
  error.value = ''

  try {
    const response = await fetch(apiUrl(API_ENDPOINTS.usPopulationSummary))
    const data = await response.json()

    if (!response.ok || !data.ok) {
      throw new Error(data.error || 'Failed to load U.S. population summary.')
    }

    summary.value = data
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Unknown error.'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await loadSummary()

  timerId = window.setInterval(() => {
    nowMs.value = Date.now()
  }, 250)
})

onBeforeUnmount(() => {
  if (timerId) {
    window.clearInterval(timerId)
  }
})
</script>

<style scoped>
.components-of-change {
  display: flex;
  flex-direction: column;
  padding-top: .3rem;
}

.coc-status {
  padding: 0.7rem 0.85rem;
  border: 1px solid #dfe1e2;
  border-radius: 0.8rem;
  background: #f7f7f7;
  font-size: var(--pop-font-size-sm);
  color: var(--pop-color-text-muted);
}

.coc-status--error {
  border-color: #f4b9b9;
  background: #fff5f5;
  color: var(--pop-color-error);
}

.coc-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.coc-row {
  display: grid;
  grid-template-columns: minmax(13rem, 18rem) minmax(8rem, 1fr);
  align-items: center;
  gap: 0.65rem;
  padding: 0.55rem 0.7rem;
  border: 1px solid #d9e8f6;
  border-radius: 0.75rem;
  background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
  box-shadow: 0 1px 3px rgba(17, 46, 81, 0.05);
}

.coc-copy {
  display: flex;
  flex-direction: column;
  gap: 0.08rem;
  text-align: left;
}

.coc-kicker {
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #5c5c5c;
  line-height: 1.1;
}

.coc-value {
  color: var(--pop-color-text);
  font-weight: var(--pop-font-weight-semibold);
  line-height: 1.15;
  font-size: 0.82rem;
}

.coc-bar {
  width: 100%;
  height: 0.4rem;
  overflow: hidden;
  border-radius: 999px;
  background: #d9e8f6;
  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.06);
}

.coc-bar-fill {
  height: 100%;
  border-radius: 999px;
  background: linear-gradient(90deg, #00bde3 0%, #005ea2 100%);
  transition: width 200ms linear;
}

.coc-bar-fill--drain {
  margin-right: auto;
  background: linear-gradient(90deg, #b50909 0%, #d54309 100%);
}

.coc-row--net {
  border-color: rgba(0, 94, 162, 0.22);
  background: linear-gradient(180deg, #eef7ff 0%, #f8fbff 100%);
}

.coc-row--net .coc-kicker {
  color: #005ea2;
}

.coc-row--net .coc-value {
  color: var(--pop-color-accent);
}

.coc-row--net .coc-bar-fill {
  background: linear-gradient(90deg, #00a91c 0%, #2e8540 100%);
}

.components-of-change--sm .coc-kicker {
  font-size: 0.58rem;
}

.components-of-change--sm .coc-value {
  font-size: 0.75rem;
}

.components-of-change--sm .coc-row {
  padding: 0.5rem 0.65rem;
}

.components-of-change--md .coc-kicker {
  font-size: 0.62rem;
}

.components-of-change--md .coc-value {
  font-size: 0.82rem;
}

.components-of-change--lg .coc-kicker {
  font-size: 0.68rem;
}

.components-of-change--lg .coc-value {
  font-size: 0.9rem;
}

.components-of-change--lg .coc-row {
  padding: 0.7rem 0.85rem;
}

@media (max-width: 840px) {
  .coc-row {
    grid-template-columns: 1fr;
    gap: 0.45rem;
    align-items: start;
  }
}
</style>