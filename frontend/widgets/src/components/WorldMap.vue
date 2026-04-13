<template>
  <BaseWidget :title="title" :subtitle="subtitle">
    <section class="world-map-widget">
      <svg
        class="world-map"
        :viewBox="`0 0 ${svgWidth} ${svgHeight}`"
        role="img"
        aria-label="Clickable world map"
      >
        <g class="countries">
          <path
            v-for="feature in mappedFeatures"
            :key="feature.id"
            :d="feature.path"
            class="country"
            :class="{ 'is-selected': selectedCountryId === feature.id }"
            @click="$emit('country-selected', feature)"
          />
        </g>
      </svg>

      <div class="world-map-readout">
        <template v-if="selectedFeature">
          <strong>{{ selectedFeature.name }}</strong>
        </template>
        <template v-else>
          <span>Click a country</span>
        </template>
      </div>
    </section>
  </BaseWidget>
</template>

<script setup>
import { computed } from 'vue'
import { geoNaturalEarth1, geoPath } from 'd3-geo'
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
  geojson: {
    type: Object,
    required: true,
  },
  selectedCountryId: {
    type: String,
    default: '',
  },
})

defineEmits(['country-selected'])

const svgWidth = 980
const svgHeight = 520

const mapInsetLeft = 2
const mapInsetTop = 18
const mapInsetRight = 2
const mapInsetBottom = 6

const filteredGeojson = computed(() => {
  const features = (props.geojson?.features || []).filter((feature) => {
    const id = feature.id || feature.properties?.id || ''
    const name = String(feature.properties?.name || '').toLowerCase()

    return id !== 'AQ' && id !== 'AY' && name !== 'antarctica'
  })

  return {
    type: 'FeatureCollection',
    features,
  }
})

const projection = computed(() => {
  const proj = geoNaturalEarth1().fitExtent(
    [
      [mapInsetLeft, mapInsetTop],
      [svgWidth - mapInsetRight, svgHeight - mapInsetBottom],
    ],
    filteredGeojson.value
  )

  const scale = proj.scale()
  proj.scale(scale * 1.22)

  // move a little right and noticeably down
  proj.translate([svgWidth / 2 + -20, svgHeight / 2 + 44])

  return proj
})

const mappedFeatures = computed(() => {
  const pathGenerator = geoPath(projection.value)

  return filteredGeojson.value.features
    .map((feature) => {
      const id = feature.id || feature.properties?.id || feature.properties?.name
      const name = feature.properties?.name || 'Unknown'
      const path = pathGenerator(feature)

      if (!path) return null

      return {
        id,
        name,
        population: feature.properties?.population ?? null,
        path,
        raw: feature,
      }
    })
    .filter(Boolean)
})

const selectedFeature = computed(() => {
  return (
    mappedFeatures.value.find(
      (feature) => feature.id === props.selectedCountryId
    ) || null
  )
})
</script>

<style scoped>
.world-map-widget {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  /*width: 40rem;*/
  max-width: 100%;
}

.world-map {
  width: 100%;
  height: auto;
  display: block;
  background: #f8fafc;
  border: 1px solid #dfe5ea;
  border-radius: 0.5rem;
}

.country {
  fill: #9ec5d1;
  stroke: #ffffff;
  stroke-width: 0.6;
  cursor: pointer;
  transition:
    fill 0.15s ease,
    stroke 0.15s ease,
    stroke-width 0.15s ease;
}

.country:hover {
  fill: #5da8ba;
}

.country.is-selected {
  fill: #005ea2;
  stroke: #112e51;
  stroke-width: 1.2;
}

.world-map-readout {
  min-height: 1.5rem;
  font-size: 0.9rem;
  color: #112e51;
}
</style>