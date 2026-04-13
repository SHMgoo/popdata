<template>
  <BaseWidget :title="title" :subtitle="subtitle">
    <section class="region-growth">
      <div class="rg-chart-wrap">
        <div class="rg-y-label">Population (in millions)</div>

        <svg
          class="rg-chart"
          :viewBox="`0 0 ${svgWidth} ${svgHeight}`"
          role="img"
          aria-label="United States Population Growth by Region"
          preserveAspectRatio="xMidYMid meet"
        >
          <!-- gridlines -->
          <g class="rg-grid">
            <line
              v-for="tick in yTicks"
              :key="`grid-${tick.value}`"
              :x1="chartLeft"
              :x2="chartRight"
              :y1="tick.y"
              :y2="tick.y"
            />
          </g>

          <!-- stacked area layers -->
          <path
            v-for="layer in stackedLayers"
            :key="layer.key"
            class="rg-area"
            :d="layer.path"
            :style="{ fill: layer.color }"
          />

          <!-- axes -->
          <line
            class="rg-axis"
            :x1="chartLeft"
            :x2="chartLeft"
            :y1="chartTop"
            :y2="chartBottom"
          />
          <line
            class="rg-axis"
            :x1="chartLeft"
            :x2="chartRight"
            :y1="chartBottom"
            :y2="chartBottom"
          />

          <!-- y tick labels -->
          <g class="rg-y-ticks">
            <text
              v-for="tick in yTicks"
              :key="`ylabel-${tick.value}`"
              :x="chartLeft - 10"
              :y="tick.y + 4"
              text-anchor="end"
            >
              {{ tick.value }}
            </text>
          </g>

          <!-- x tick labels -->
          <g class="rg-x-ticks">
            <text
              v-for="point in xPoints"
              :key="`xlabel-${point.year}`"
              :x="point.x"
              :y="chartBottom + 22"
              text-anchor="middle"
            >
              {{ point.year }}
            </text>
          </g>
        </svg>
      </div>

      <div class="rg-legend">
        <div
          v-for="item in legendItems"
          :key="item.key"
          class="rg-legend__item"
        >
          <span
            class="rg-legend__swatch"
            :style="{ background: item.color }"
          />
          <span>{{ item.label }}</span>
        </div>
      </div>
    </section>
  </BaseWidget>
</template>

<script setup>
import { computed } from 'vue'
import BaseWidget from './BaseWidget.vue'

const props = defineProps({
  title: {
    type: String,
    default: 'United States Population Growth by Region',
  },
  subtitle: {
    type: String,
    default: '',
  },
  years: {
    type: Array,
    default: () => [],
  },
  series: {
    type: Array,
    default: () => [],
  },
})

const svgWidth = 450
const svgHeight = 360

const chartLeft = 52
const chartRight = 430
const chartTop = 8
const chartBottom = 320
const chartWidth = chartRight - chartLeft
const chartHeight = chartBottom - chartTop

const regionColors = {
  northeast: '#9fdbe5',
  midwest: '#6fbecb',
  west: '#3ea5b5',
  south: '#1f7a84',
}

const legendItems = computed(() =>
  props.series.map((item) => ({
    key: item.key,
    label: item.label,
    color: regionColors[item.key] || '#999999',
  }))
)

const stackedTotals = computed(() => {
  const count = props.years.length
  const totals = Array.from({ length: count }, () => 0)

  for (const series of props.series) {
    series.values.forEach((value, index) => {
      totals[index] += Number(value) || 0
    })
  }

  return totals
})

const maxY = computed(() => {
  const max = Math.max(...stackedTotals.value, 0)
  if (!max) return 400
  return Math.ceil(max / 50) * 50
})

const yTicks = computed(() => {
  const segments = 6
  return Array.from({ length: segments + 1 }, (_, index) => {
    const value = (maxY.value / segments) * index
    return {
      value: Math.round(value),
      y: chartBottom - (value / maxY.value) * chartHeight,
    }
  })
})

const xPoints = computed(() => {
  const count = props.years.length
  return props.years.map((year, index) => ({
    year,
    x: chartLeft + (count <= 1 ? 0 : (index / (count - 1)) * chartWidth),
  }))
})

function toY(value) {
  return chartBottom - (value / maxY.value) * chartHeight
}

const stackedLayers = computed(() => {
  const running = Array.from({ length: props.years.length }, () => 0)

  return props.series.map((series) => {
    const topPoints = []
    const bottomPoints = []

    series.values.forEach((value, index) => {
      const numeric = Number(value) || 0
      const x = xPoints.value[index]?.x ?? chartLeft

      const bottom = running[index]
      const top = bottom + numeric

      topPoints.push([x, toY(top)])
      bottomPoints.push([x, toY(bottom)])

      running[index] = top
    })

    if (!topPoints.length) {
      return {
        key: series.key,
        color: regionColors[series.key] || '#999999',
        path: '',
      }
    }

    const topPath = topPoints
      .map((point, index) => `${index === 0 ? 'M' : 'L'} ${point[0]} ${point[1]}`)
      .join(' ')

    const bottomPath = bottomPoints
      .slice()
      .reverse()
      .map((point) => `L ${point[0]} ${point[1]}`)
      .join(' ')

    return {
      key: series.key,
      color: regionColors[series.key] || '#999999',
      path: `${topPath} ${bottomPath} Z`,
    }
  })
})
</script>

<style scoped>
.region-growth {
  display: flex;
  flex-direction: column;
  gap: 0.45rem;
  width: 31rem;
  max-width: 100%;
  min-height: 25.75rem;
}

.rg-chart-wrap {
  display: grid;
  grid-template-columns: 1.25rem 1fr;
  gap: 0.35rem;
  align-items: start;
  flex: 1 1 auto;
  min-height: 0;
}

.rg-y-label {
  writing-mode: vertical-rl;
  transform: rotate(180deg);
  font-size: 0.72rem;
  line-height: 1;
  color: #4b5563;
  padding-top: 3.65rem;
}

.rg-area {
  opacity: 0.95;
  stroke: #ffffff;
  stroke-width: 0.8;
}

.rg-chart {
  width: 100%;
  height: auto;
  display: block;
}

.rg-grid line {
  stroke: #cfd8df;
  stroke-width: 1;
}

.rg-axis {
  stroke: #1f2937;
  stroke-width: 1.15;
}

.rg-area {
  opacity: 0.98;
}

.rg-y-ticks text,
.rg-x-ticks text {
    fill: #374151;
  font-size: 11px;
}

.rg-legend {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 0.35rem 0.75rem;
  padding-top: 0.05rem;
}

.rg-legend__item {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  color: #274c6b;
  font-size: 0.84rem;
  font-weight: 500;
}

.rg-legend__swatch {
  width: 2.2rem;
  height: 0.85rem;
  border-radius: 0.2rem;
  display: inline-block;
}

@media (max-width: 640px) {
  .rg-chart-wrap {
    grid-template-columns: 1fr;
  }

  .rg-y-label {
    writing-mode: horizontal-tb;
    transform: none;
    padding-top: 0;
  }
}
</style>