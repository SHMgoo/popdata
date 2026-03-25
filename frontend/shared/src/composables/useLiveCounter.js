import { computed, onBeforeUnmount, ref } from 'vue'

/**
 * useLiveCounter
 *
 * Reusable Vue composable that acts as the timer/engine for
 * odometer-style population counters.
 *
 * It starts with a base value and base timestamp, then updates
 * the displayed count over time using a per-second growth rate.
 *
 * Intended for counters such as:
 * - U.S. population
 * - World population
 */
export function useLiveCounter(options = {}) {
  const {
    baseValue = 0,
    baseEpochMs = Date.now(),
    perSecond = 0,
    refreshMs = 250,
  } = options

  const nowMs = ref(Date.now())
  let timerId = null

 const currentValue = computed(() => {
  const elapsedSeconds = (nowMs.value - Number(baseEpochMs)) / 1000
  const liveValue = Number(baseValue) + elapsedSeconds * Number(perSecond)

  return liveValue
})

  function start() {
    if (timerId !== null) {
      return
    }

    timerId = window.setInterval(() => {
      nowMs.value = Date.now()
    }, refreshMs)
  }

  function stop() {
    if (timerId !== null) {
      window.clearInterval(timerId)
      timerId = null
    }
  }

  start()
  onBeforeUnmount(stop)

  return {
    currentValue,
    start,
    stop,
  }
}