<template>
  <BaseWidget :title="title" :subtitle="subtitle">
    <section class="geo-ranking-tabs" :class="sizeClass">
      <div class="grt-tablist" role="tablist" aria-label="Geography tabs">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          type="button"
          class="grt-tab"
          :class="{ 'grt-tab--active': activeTab === tab.key }"
          role="tab"
          :aria-selected="activeTab === tab.key"
          @click="setActiveTab(tab.key)"
        >
          {{ tab.label }}
        </button>
      </div>

      <div class="grt-panel" role="tabpanel">
        <div v-if="!visibleItems.length" class="grt-status">
          No data available.
        </div>

        <div v-else class="grt-table">
          <div class="grt-header-row">
            <span class="grt-header-row__name">Name</span>
            <span class="grt-header-row__population">Population, 2025</span>
            <span class="grt-header-row__density">Pop. per sq. mi., 2025</span>
          </div>

          <div class="grt-list">
            <article
              v-for="item in visibleItems"
              :key="item.geoid || `${activeTab}-${item.name}`"
              class="grt-row"
            >
              <div class="grt-row__main">
                <span class="grt-name">{{ item.name }}</span>
                <span class="grt-population">{{ formatPopulation(item.population) }}</span>
                <span class="grt-density">{{ formatDensity(item.density) }}</span>
              </div>

              <div class="grt-row__bar-wrap">
                <div class="grt-bar">
                  <div
                    class="grt-bar__fill"
                    :style="{ width: `${barWidth(item)}%` }"
                  />
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>
  </BaseWidget>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import BaseWidget from './BaseWidget.vue'

const props = defineProps({
  title: {
    type: String,
    default: '',
  },
  subtitle: {
    type: String,
    default: '',
  },
  states: {
    type: Array,
    default: () => [],
  },
  counties: {
    type: Array,
    default: () => [],
  },
  cities: {
    type: Array,
    default: () => [],
  },
  initialTab: {
    type: String,
    default: 'states',
    validator: (value) => ['states', 'counties', 'cities'].includes(value),
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
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
  barMetric: {
    type: String,
    default: 'population',
    validator: (value) => ['population', 'density'].includes(value),
  },
})

const tabs = [
  { key: 'states', label: 'States' },
  { key: 'counties', label: 'Counties' },
  { key: 'cities', label: 'Cities' },
]

const activeTab = ref(props.initialTab)
let rotationTimer = null

const sizeClass = computed(() => `geo-ranking-tabs--${props.size}`)

const itemsByTab = computed(() => ({
  states: props.states,
  counties: props.counties,
  cities: props.cities,
}))

const activeItems = computed(() => itemsByTab.value[activeTab.value] || [])

const visibleItems = computed(() => {
  return activeItems.value.slice(0, props.maxItems)
})

const maxBarValue = computed(() => {
  const metric = props.barMetric
  const values = visibleItems.value.map((item) => Number(item?.[metric]) || 0)
  return values.length ? Math.max(...values) : 0
})

function setActiveTab(tabKey) {
  activeTab.value = tabKey
  restartRotation()
}

function rotateTab() {
  const currentIndex = tabs.findIndex((tab) => tab.key === activeTab.value)
  const nextIndex = currentIndex >= 0 ? (currentIndex + 1) % tabs.length : 0
  activeTab.value = tabs[nextIndex].key
}

function startRotation() {
  stopRotation()

  if (!props.autoRotate || props.rotateSeconds <= 0) return

  rotationTimer = window.setInterval(() => {
    rotateTab()
  }, props.rotateSeconds * 1000)
}

function stopRotation() {
  if (rotationTimer) {
    window.clearInterval(rotationTimer)
    rotationTimer = null
  }
}

function restartRotation() {
  if (!props.autoRotate) return
  startRotation()
}

function formatPopulation(value) {
  const number = Number(value)
  return Number.isFinite(number) ? number.toLocaleString('en-US') : '—'
}

function formatDensity(value) {
  const number = Number(value)
  return Number.isFinite(number)
    ? number.toLocaleString('en-US', { maximumFractionDigits: 1 })
    : '—'
}

function barWidth(item) {
  const metricValue = Number(item?.[props.barMetric]) || 0
  const max = Number(maxBarValue.value) || 0

  if (!metricValue || !max) return 0
  return Math.max(0, Math.min(100, (metricValue / max) * 100))
}

watch(
  () => [props.autoRotate, props.rotateSeconds],
  () => {
    startRotation()
  }
)

watch(
  () => props.initialTab,
  (newValue) => {
    activeTab.value = newValue
  }
)

onMounted(() => {
  startRotation()
})

onBeforeUnmount(() => {
  stopRotation()
})
</script>

<style scoped>
  .geo-ranking-tabs {
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
    width: 31rem;
    max-width: 100%;
    height: 25.75rem;
  }

.grt-tablist {
  display: flex;
  gap: 0;
  border-bottom: 1px solid #cfd4da;
}


.grt-tab {
    flex: 1 1 0;
    border: 1px solid #d9dde3;
    border-bottom: none;
    background: #f4f5f7;
    color: #4b5563;
    padding: 0.52rem 0.7rem;
    font-weight: 700;
    line-height: 1.15;
    cursor: pointer;
    border-radius: 0.55rem 0.55rem 0 0;
    margin-right: 0.08rem;
  }

  .grt-tab--active {
    background: #e9edf2;
    color: #112e51;
    box-shadow: inset 0.18rem 0 0 #c05600;
  }

  .grt-panel {
    display: flex;
    flex-direction: column;
    min-height: 0;
    flex: 1 1 auto;
  }

  .grt-table {
    display: flex;
    flex-direction: column;
    min-height: 0;
    flex: 1 1 auto;
  }

  .grt-header-row {
    display: grid;
    grid-template-columns: minmax(0, 1.45fr) minmax(8.2rem, 1fr) minmax(7.7rem, 1fr);
    gap: 0.65rem;
    align-items: center;
    background: linear-gradient(180deg, #1b487e 0%, #112e51 100%);
    color: #fff;
    font-size: 0.82rem;
    font-weight: 600;
    line-height: 1.15;
    padding: 0.48rem 0.7rem;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
}

  .grt-header-row__population,
  .grt-header-row__density {
    text-align: right;
  }

  .grt-list {
    display: flex;
    flex-direction: column;
    min-height: 0;
    overflow-y: auto;
  }

  .grt-row {
    display: flex;
    flex-direction: column;
    gap: 0.08rem;
    padding: 0.38rem 0.7rem 0.3rem;
    border-bottom: 1px solid #eceff2;
    background: #fff;
  }

  .grt-row__main {
    display: grid;
    grid-template-columns: minmax(0, 1.45fr) minmax(8.2rem, 1fr) minmax(7.7rem, 1fr);
    gap: 0.65rem;
    align-items: center;
    line-height: 1.1;
  }

  .grt-name {
    color: #0071bc;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .grt-population,
  .grt-density {
    text-align: right;
    color: #1f77b4;
    font-variant-numeric: tabular-nums;
  }

  .grt-row__bar-wrap {
    width: 100%;
  }

  .grt-bar {
    width: 8.2rem;
    max-width: 100%;
    height: 0.16rem;
    border-radius: 999px;
    background: #e5e7eb;
    overflow: hidden;
  }

  .grt-bar__fill {
    height: 100%;
    border-radius: 999px;
    background: linear-gradient(90deg, #005ea2 0%, #2e8bc0 100%);
  }

  .grt-status {
    padding: 0.85rem 1rem;
    border: 1px solid #dfe1e2;
    border-radius: 0.8rem;
    background: #f7f7f7;
    font-size: 0.95rem;
    color: #5c5c5c;
  }

  .geo-ranking-tabs--sm .grt-name,
  .geo-ranking-tabs--sm .grt-population,
  .geo-ranking-tabs--sm .grt-density,
  .geo-ranking-tabs--sm .grt-header-row,
  .geo-ranking-tabs--sm .grt-tab {
    font-size: 0.8rem;
  }

  .geo-ranking-tabs--md .grt-name,
  .geo-ranking-tabs--md .grt-population,
  .geo-ranking-tabs--md .grt-density,
  .geo-ranking-tabs--md .grt-header-row,
  .geo-ranking-tabs--md .grt-tab {
    font-size: 0.86rem;
  }

  .geo-ranking-tabs--lg .grt-name,
  .geo-ranking-tabs--lg .grt-population,
  .geo-ranking-tabs--lg .grt-density,
  .geo-ranking-tabs--lg .grt-header-row,
  .geo-ranking-tabs--lg .grt-tab {
    font-size: 0.92rem;
  }

  @media (max-width: 640px) {
    .geo-ranking-tabs {
      width: 100%;
      height: auto;
    }

    .grt-header-row,
    .grt-row__main {
      grid-template-columns: 1fr;
      gap: 0.2rem;
    }

    .grt-header-row__population,
    .grt-header-row__density,
    .grt-population,
    .grt-density {
      text-align: left;
    }

    .grt-bar {
      width: 100%;
    }
  }
</style>