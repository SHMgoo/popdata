<template>
  <BaseWidget :title="title" :subtitle="subtitle">
    <section class="age-sex-widget">
      <div class="as-header">
        <div class="as-legend">
          <span class="as-legend__item">
            <span class="as-legend__swatch as-legend__swatch--male" />
            {{ maleLabel }}
          </span>

          <span class="as-legend__item">
            <span class="as-legend__swatch as-legend__swatch--female" />
            {{ femaleLabel }}
          </span>
        </div>

        <div class="as-hover-readout">
          <template v-if="hoveredRow">
            <span class="as-hover-readout__age">Age {{ hoveredRow.ageLabel }}</span>
            <span>{{ maleLabel }}: {{ formatPercent(hoveredRow.malePercent) }}</span>
            <span>{{ femaleLabel }}: {{ formatPercent(hoveredRow.femalePercent) }}</span>
          </template>

          <template v-else>
            <span class="as-hover-readout__hint">Hover over a bar to compare values</span>
          </template>
        </div>
      </div>

      <div class="as-chart-shell">
        <div class="as-chart-wrap">
          <svg
            class="as-chart"
            :viewBox="`0 0 ${svgWidth} ${svgHeight}`"
            role="img"
            aria-label="United States Population by Age and Sex"
            preserveAspectRatio="xMidYMid meet"
          >
            <text
              class="as-side-label"
              :x="leftBarMinX + maxHalfBarWidth / 2"
              :y="26"
              text-anchor="middle"
            >
              {{ maleLabel }}
            </text>

            <text
              class="as-side-label"
              :x="centerX + centerGap / 2 + maxHalfBarWidth / 2"
              :y="26"
              text-anchor="middle"
            >
              {{ femaleLabel }}
            </text>

            <rect
              class="as-center-gutter"
              :x="centerX - centerGap / 2"
              :y="chartTop - 2"
              :width="centerGap"
              :height="chartBottom - chartTop + 4"
            />

            <line
              class="as-guide-line"
              :x1="leftGuide1X"
              :x2="leftGuide1X"
              :y1="chartTop"
              :y2="chartBottom"
            />
            <line
              class="as-guide-line"
              :x1="leftGuide2X"
              :x2="leftGuide2X"
              :y1="chartTop"
              :y2="chartBottom"
            />
            <line
              class="as-guide-line"
              :x1="rightGuide1X"
              :x2="rightGuide1X"
              :y1="chartTop"
              :y2="chartBottom"
            />
            <line
              class="as-guide-line"
              :x1="rightGuide2X"
              :x2="rightGuide2X"
              :y1="chartTop"
              :y2="chartBottom"
            />

            <line
              class="as-center-line"
              :x1="centerX"
              :x2="centerX"
              :y1="chartTop - 4"
              :y2="chartBottom + 4"
            />

            <line
              class="as-axis-line"
              :x1="leftBarMinX"
              :x2="rightBarMaxX"
              :y1="chartBottom + 10"
              :y2="chartBottom + 10"
            />

            <text class="as-axis-label" :x="leftBarMinX" :y="chartBottom + 26" text-anchor="start">
              {{ percentAxisLabel }}
            </text>
            <text class="as-axis-label" :x="centerX" :y="chartBottom + 26" text-anchor="middle">
              0%
            </text>
            <text class="as-axis-label" :x="rightBarMaxX" :y="chartBottom + 26" text-anchor="end">
              {{ percentAxisLabel }}
            </text>

            <g
              v-for="row in rows"
              :key="row.age"
              class="as-row-group"
              @mouseenter="hoveredAge = row.age"
              @mouseleave="hoveredAge = null"
            >
              <rect
                v-if="isHovered(row)"
                class="as-row-highlight"
                :x="leftBarMinX - 8"
                :y="row.y - rowBandHeight / 2"
                :width="rightBarMaxX - leftBarMinX + 16"
                :height="rowBandHeight"
                rx="3"
              />

              <rect
                class="as-bar as-bar--male"
                :class="{ 'is-hovered': isHovered(row) }"
                :x="centerX - centerGap / 2 - row.maleWidth"
                :y="row.y - barHeight / 2"
                :width="row.maleWidth"
                :height="barHeight"
                rx="0"
              />

              <rect
                class="as-bar as-bar--female"
                :class="{ 'is-hovered': isHovered(row) }"
                :x="centerX + centerGap / 2"
                :y="row.y - barHeight / 2"
                :width="row.femaleWidth"
                :height="barHeight"
                rx="0"
              />

              <text
                v-if="shouldShowAgeLabel(row)"
                class="as-age-label"
                :class="{ 'is-hovered': isHovered(row) }"
                :x="centerX"
                :y="row.y + 2"
                text-anchor="middle"
              >
                {{ row.ageLabel }}
              </text>
            </g>
          </svg>
        </div>
      </div>

      <div class="as-controls">
        <button
          class="as-play"
          type="button"
          :aria-pressed="isPlaying ? 'true' : 'false'"
          :aria-label="isPlaying ? 'Pause animation' : 'Play animation'"
          @click="$emit('toggle-play')"
        >
          <span v-if="isPlaying">❚❚</span>
          <span v-else>▶</span>
        </button>

        <input
          class="as-slider"
          type="range"
          :min="0"
          :max="Math.max(years.length - 1, 0)"
          :value="selectedIndex"
          step="1"
          @input="$emit('update:selected-index', Number($event.target.value))"
        />

        <div class="as-year">{{ selectedYearLabel }}</div>
      </div>

      <div class="as-year-ticks">
        <span
          v-for="(year, index) in years"
          :key="year"
          class="as-year-ticks__item"
          :class="{ 'is-active': index === selectedIndex }"
        >
          {{ year }}
        </span>
      </div>
    </section>
  </BaseWidget>
</template>

<script setup>
import { computed, ref } from 'vue'
import BaseWidget from './BaseWidget.vue'

const props = defineProps({
  title: {
    type: String,
    default: 'United States Population by Age and Sex',
  },
  subtitle: {
    type: String,
    default: '',
  },
  years: {
    type: Array,
    default: () => [],
  },
  selectedIndex: {
    type: Number,
    default: 0,
  },
  maxPercent: {
    type: Number,
    default: 0.015,
  },
  maleLabel: {
    type: String,
    default: 'Male',
  },
  femaleLabel: {
    type: String,
    default: 'Female',
  },
  maleGroups: {
    type: Array,
    default: () => [],
  },
  femaleGroups: {
    type: Array,
    default: () => [],
  },
  isPlaying: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['update:selected-index', 'toggle-play'])

const hoveredAge = ref(null)

const svgWidth = 450
const svgHeight = 360

const chartTop = 18
const chartBottom = 312
const chartHeight = chartBottom - chartTop

const centerX = svgWidth / 2
const chartPaddingX = 12
const centerGap = 46
const maxHalfBarWidth = (svgWidth - chartPaddingX * 2 - centerGap) / 2

const leftBarMinX = centerX - centerGap / 2 - maxHalfBarWidth
const rightBarMaxX = centerX + centerGap / 2 + maxHalfBarWidth

const leftGuide1X = leftBarMinX + maxHalfBarWidth * 0.33
const leftGuide2X = leftBarMinX + maxHalfBarWidth * 0.66
const rightGuide1X = centerX + centerGap / 2 + maxHalfBarWidth * 0.33
const rightGuide2X = centerX + centerGap / 2 + maxHalfBarWidth * 0.66

const totalRows = 101
const rowStep = chartHeight / totalRows
const rowBandHeight = Math.max(rowStep, 2.8)
const barHeight = Math.max(rowStep - 1.4, 1.7)

const safeMaxPercent = computed(() => {
  return props.maxPercent > 0 ? props.maxPercent : 0.015
})

const rows = computed(() => {
  const byAge = []

  for (let index = 0; index < totalRows; index += 1) {
    const male = props.maleGroups[index] || {}
    const female = props.femaleGroups[index] || {}

    const age = String(male.age ?? female.age ?? index).padStart(2, '0')
    const ageNumber = Number(age)
    const ageLabel = ageNumber >= 100 ? '100+' : age

    const malePercent = Number(male.total_percentage) || 0
    const femalePercent = Number(female.total_percentage) || 0

    const y = chartTop + rowStep * (totalRows - 1 - index) + rowStep / 2

    byAge.push({
      index,
      age,
      ageNumber,
      ageLabel,
      malePercent,
      femalePercent,
      maleWidth: Math.max(0, (malePercent / safeMaxPercent.value) * maxHalfBarWidth),
      femaleWidth: Math.max(0, (femalePercent / safeMaxPercent.value) * maxHalfBarWidth),
      y,
    })
  }

  return byAge
})

const selectedYearLabel = computed(() => props.years[props.selectedIndex] ?? '')

const hoveredRow = computed(() => {
  if (hoveredAge.value === null) return null
  return rows.value.find((row) => row.age === hoveredAge.value) || null
})

const percentAxisLabel = computed(() => {
  return `${(safeMaxPercent.value * 100).toFixed(1)}%`
})

function formatPercent(value) {
  return `${(Number(value) * 100).toFixed(2)}%`
}

function isHovered(row) {
  return hoveredAge.value === row.age
}

function shouldShowAgeLabel(row) {
  return isHovered(row) || row.ageNumber % 10 === 0 || row.ageNumber === 100
}
</script>

<style scoped>
.age-sex-widget {
  display: flex;
  flex-direction: column;
  gap: 0.55rem;
  width: 31rem;
  max-width: 100%;
  min-height: 25.75rem;
}

.as-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 0.75rem;
  min-height: 3.25rem;
}

.as-legend {
  display: flex;
  gap: 0.9rem;
  flex-wrap: wrap;
  font-size: 0.78rem;
  color: #3d4551;
}

.as-legend__item {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-weight: 600;
}

.as-legend__swatch {
  width: 0.72rem;
  height: 0.72rem;
  border-radius: 999px;
  display: inline-block;
}

.as-legend__swatch--male {
  background: #7f93a3;
}

.as-legend__swatch--female {
  background: #00a7c8;
}

.as-hover-readout {
  width: 220px;
  min-height: 3.25rem;
  height: 3.25rem;
  flex: 0 0 220px;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: flex-start;
  font-size: 0.76rem;
  line-height: 1.2;
  color: #3d4551;
  overflow: hidden;
}

.as-hover-readout__hint {
  color: #6b7280;
  white-space: nowrap;
}

.as-hover-readout span {
  white-space: nowrap;
}

.as-hover-readout__age {
  font-weight: 700;
  color: #112e51;
}

.as-hover-readout__hint {
  color: #6b7280;
}

.as-chart-shell {
  width: 100%;
}

.as-chart-wrap {
  width: 100%;
  height: 21rem;
}

.as-chart {
  width: 100%;
  height: 100%;
  display: block;
}

.as-side-label {
  font-size: 12px;
  font-weight: 700;
  fill: #4b5563;
}

.as-center-gutter {
  fill: #ffffff;
}

.as-guide-line {
  stroke: #ffffff;
  stroke-width: 1;
}

.as-center-line {
  stroke: #d1d5db;
  stroke-width: 1;
}

.as-axis-line {
  stroke: #4b5563;
  stroke-width: 1;
}

.as-axis-label {
  font-size: 10px;
  fill: #4b5563;
}

.as-row-highlight {
  fill: #eef5fb;
}

.as-bar {
  transition:
    opacity 0.15s ease,
    filter 0.15s ease,
    fill 0.15s ease;
}

.as-bar--male {
  fill: #7f93a3;
}

.as-bar--female {
  fill: #00a7c8;
}

.as-bar.is-hovered {
  opacity: 1;
  stroke: #112e51;
  stroke-width: 0.4;
}

.as-age-label {
  font-size: 6.8px;
  font-weight: 700;
  fill: #a3a3a3;
  pointer-events: none;
}

.as-age-label.is-hovered {
  fill: #374151;
}

.as-controls {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 0.7rem;
  margin-top: 0.1rem;
}

.as-play {
  width: 1.95rem;
  height: 1.95rem;
  border: 1px solid #c8d1db;
  border-radius: 999px;
  background: #ffffff;
  color: #005ea2;
  font-size: 0.9rem;
  line-height: 1;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.as-play:hover {
  background: #f8fbff;
}

.as-slider {
  width: 100%;
  accent-color: #0f8fa1;
}

.as-year {
  min-width: 3rem;
  text-align: right;
  font-size: 0.84rem;
  font-weight: 700;
  color: #112e51;
}

.as-year-ticks {
  display: flex;
  justify-content: space-between;
  gap: 0.35rem;
  font-size: 0.68rem;
  color: #6b7280;
  margin-left: 2.55rem;
}

.as-year-ticks__item.is-active {
  color: #112e51;
  font-weight: 700;
}

@media (max-width: 720px) {
  .age-sex-widget {
    width: 100%;
    min-height: auto;
  }

  .as-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .as-hover-readout {
    align-items: flex-start;
  }

  .as-chart-wrap {
    height: 18rem;
  }

  .as-year-ticks {
    margin-left: 0;
  }
}
</style>