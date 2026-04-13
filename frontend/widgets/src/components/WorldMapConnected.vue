<template>
  <section class="world-map-connected">
    <div class="world-map-toolbar">
      <label class="world-map-toolbar__label" for="country-select">
        Select a country:
      </label>

      <select
        id="country-select"
        class="world-map-toolbar__select"
        :value="selectedCountryId"
        @change="handleSelectChange"
      >
        <option value="">Choose a country</option>
        <option
          v-for="country in countryOptions"
          :key="country.id"
          :value="country.id"
        >
          {{ country.name }}
        </option>
      </select>
    </div>

    <WorldMap
      :geojson="worldGeojson"
      :selected-country-id="selectedCountryId"
      @country-selected="handleCountrySelected"
    />
  </section>
</template>

<script setup>
import { computed, ref } from 'vue'
import WorldMap from '@widgets/components/WorldMap.vue'
import worldGeojson from '@shared/data/world.json'

const selectedCountryId = ref('')

const countryOptions = computed(() => {
  return (worldGeojson.features || [])
    .map((feature) => {
      const id = feature.id || feature.properties?.id || ''
      const name = feature.properties?.name || 'Unknown'

      return {
        id,
        name,
      }
    })
    .filter((country) => {
      const lowerName = String(country.name).toLowerCase()
      return country.id && lowerName !== 'antarctica'
    })
    .sort((a, b) => a.name.localeCompare(b.name))
})

function handleCountrySelected(country) {
  selectedCountryId.value = country.id
  console.log('Selected country:', country)
}

function handleSelectChange(event) {
  selectedCountryId.value = event.target.value
}
</script>

<style scoped>
.world-map-connected {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.world-map-toolbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem;
}

.world-map-toolbar__label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #112e51;
}

.world-map-toolbar__select {
  min-width: 15rem;
  max-width: 100%;
  padding: 0.45rem 0.65rem;
  border: 1px solid #cbd5e1;
  border-radius: 0.4rem;
  background: #ffffff;
  font-size: 0.9rem;
  color: #1f2937;
}
</style>