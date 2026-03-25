<template>
  <BaseWidget
    class="popdata-widget--centered"
    :title="props.title"
    :subtitle="props.subtitle"
    :titleSize="props.titleSize"
  >
    <section class="population-counter" :class="sizeClass">
      <p v-if="props.label" class="label">{{ props.label }}</p>

      <div class="counter-shell">
        <div class="counter-panel">
          <div class="counter-panel-inner">
            <div v-if="props.iconSrc" class="counter-icon" aria-hidden="true">
              <img :src="props.iconSrc" :alt="props.iconAlt" />
            </div>

            <p class="value" :class="{ 'is-ticking': tick }">
              {{ formattedPopulation }}
            </p>
          </div>
        </div>      
      </div>

      <p class="timing" v-if="props.showTiming">
        Next +1 in ~{{ formattedSecondsToNext }}s
        <span class="timing-sep">•</span>
        1 person every ~{{ formattedSecondsPerPerson }}s
      </p>

      <p v-if="props.meta" class="meta">{{ props.meta }}</p>
    </section>
  </BaseWidget>
</template>mornin

<script setup>
import { computed, ref, watch } from 'vue'
import BaseWidget from './BaseWidget.vue'
import { useLiveCounter } from '../../../shared/src/composables/useLiveCounter.js'

const props = defineProps({
  title: {
    type: String,
    default: 'Population',
  },
   titleSize: {
    type: String,   
    default: '2rem',
  },
  subtitle: {
    type: String,
    default: '',
  },
  label: {
    type: String,
    default: 'Current estimate',
  },
  meta: {
    type: String,
    default: '',
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
  showProgress: {
    type: Boolean,
    default: true,
  },
  showTiming: {
    type: Boolean,
    default: true,
  },
  basePopulation: {
    type: Number,
    required: true,
  },
  baseEpochMs: {
    type: Number,
    required: true,
  },
  perSecond: {
    type: Number,
    required: true,
  },
    iconSrc: {
    type: String,
    default: '',
  },
  iconAlt: {
    type: String,
    default: '',
  },
})

const { currentValue } = useLiveCounter({
  baseValue: props.basePopulation,
  baseEpochMs: props.baseEpochMs,
  perSecond: props.perSecond,
  refreshMs: 100,
})

const populationInt = computed(() => Math.floor(currentValue.value))
const populationFraction = computed(() => currentValue.value - populationInt.value)

const formattedPopulation = computed(() => {
  return populationInt.value.toLocaleString()
})

const secondsPerPerson = computed(() => {
  if (props.perSecond <= 0) return 0
  return 1 / props.perSecond
})

const secondsToNext = computed(() => {
  if (props.perSecond <= 0) return 0
  return (1 - populationFraction.value) / props.perSecond
})

const formattedSecondsPerPerson = computed(() => {
  return secondsPerPerson.value.toFixed(1)
})

const formattedSecondsToNext = computed(() => {
  return secondsToNext.value.toFixed(1)
})

const tick = ref(false)

watch(populationInt, (newValue, oldValue) => {
  if (oldValue == null || newValue === oldValue) return

  tick.value = true
  window.setTimeout(() => {
    tick.value = false
  }, 180)
})

const sizeClass = computed(() => {
  return `population-counter--${props.size}`
})
</script>

<style scoped>
.population-counter {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.3rem;
  width: 100%;
  text-align: center;
}

.label {
  margin: 0;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #5c5c5c;
}

.counter-shell {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.counter-panel {
  width: 100%;
  padding: 1rem 1rem 0.95rem;
  border: 1px solid #274863;
  border-radius: 0.9rem;
  background: linear-gradient(180deg, #1b487e 0%, #112e51 100%);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 2px 8px rgba(17, 46, 81, 0.12);
  overflow: hidden;
  text-align: center;
}

.counter-panel-inner {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.85rem;
  margin: 0 auto;
  max-width: 100%;
}

.counter-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  flex: 0 0 auto;
}

.counter-icon img {
  width: 54px;
  height: 54px;
  object-fit: contain;
  opacity: 1;
}

.value {
  margin: 0;
  width: auto;
  color: #ffffff;
  font-weight: 700;
  line-height: 1;
  font-variant-numeric: tabular-nums lining-nums;
  letter-spacing: 0.02em;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: clip;
  text-align: center;
  transition:
    transform 180ms ease,
    opacity 180ms ease;
}

.value.is-ticking {
  transform: scale(1.018);
  opacity: 0.96;
}

.timing {
  margin: 0;
  color: #5c5c5c;
  font-variant-numeric: tabular-nums;
  line-height: 1.4;
}

.timing-sep {
  display: inline-block;
  margin: 0 0.35rem;
}

.meta {
  margin: 0;
  color: #3d4551;
  line-height: 1.5;
}

/* small */
.population-counter--sm .counter-icon img {
  width: 42px;
  height: 42px;
}

.population-counter--sm .value {
  font-size: clamp(1.35rem, 4.5vw, 1.8rem);
}

.population-counter--sm .timing {
  font-size: 0.78rem;
}

.population-counter--sm .meta {
  font-size: 0.82rem;
}

/* medium */
.population-counter--md .counter-icon img {
  width: 54px;
  height: 54px;
}

.population-counter--md .value {
  font-size: clamp(1.9rem, 5.5vw, 2.8rem);
}

.population-counter--md .timing {
  font-size: 0.9rem;
}

.population-counter--md .meta {
  font-size: 0.95rem;
}

/* large */
.population-counter--lg .counter-icon img {
  width: 64px;
  height: 64px;
}

.population-counter--lg .value {
  font-size: clamp(2.3rem, 6.5vw, 3.6rem);
}

.population-counter--lg .timing {
  font-size: 1rem;
}

.population-counter--lg .meta {
  font-size: 1rem;
}

@media (max-width: 480px) {
  .counter-panel {
    padding: 0.85rem 0.85rem 0.8rem;
  }

  .counter-panel-inner {
    gap: 0.65rem;
  }

  .population-counter--sm .counter-icon img,
  .population-counter--md .counter-icon img,
  .population-counter--lg .counter-icon img {
    width: 40px;
    height: 40px;
  }

  .timing {
    line-height: 1.55;
  }

  .timing-sep {
    margin: 0 0.25rem;
  }
}
</style>