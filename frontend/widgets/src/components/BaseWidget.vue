<template>
  <section
    class="base-widget"
    :style="{ '--widget-title-size': props.titleSize }"
  >
    <header
      v-if="title || subtitle"
      class="base-widget__header"
      :class="{ 'base-widget__header--centered': title && !subtitle }"
    >
      <h2
        v-if="title"
        class="base-widget__title"
        :style="{ '--widget-title-size': titleSize }"
      >
        {{ title }}
      </h2>

      <p v-if="subtitle" class="base-widget__subtitle">
        {{ subtitle }}
      </p>
    </header>

    <div class="base-widget__body">
      <slot />
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    default: '',
  },
  titleSize: {
    type: String,
    default: '1.1rem',
  },
  subtitle: {
    type: String,
    default: '',
  },
})
</script>

<style scoped>
:global(.popdata-widget--centered) .base-widget__header {
  align-items: center;
  text-align: center;
}

:global(.popdata-widget--centered) .base-widget__title {
  text-align: center;
}

:global(.popdata-widget--centered) .base-widget__subtitle {
  text-align: center;
}

.base-widget {
  display: flex;
  flex-direction: column;
  gap: 0.55rem;
}

.base-widget__header {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 0;
}

.base-widget__header--centered {
  align-items: center;
  text-align: center;
}

h2.base-widget__title {
  margin: 0;
  font-size: var(--widget-title-size, 1.1rem) !important;
  line-height: 1.35 !important;
  font-weight: 700;
  color: #112e51;
  text-align: inherit;
}

.base-widget__subtitle {
  margin: 0.35rem 0 0;
  font-size: 0.9rem;
  color: #5c5c5c;
  text-align: inherit;
}

.base-widget__body {
  display: flex;
  flex-direction: column;
  gap: var(--pop-space-3);
}
</style>