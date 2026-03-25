<template>
  <main class="popclock-page">
    <section class="hero">
      <div class="popclock-shell">
        <div class="hero__grid">
          <div class="hero__copy">
            <p class="page-eyebrow">Population at a glance</p>
            <h1 class="page-title">U.S. and World Population Clock</h1>
            <p class="page-summary">
              Shared population widgets showing current U.S. and world estimates,
              change over time, and recent-date lookup in a cleaner Popclock-style layout.
            </p>
          </div>

          <aside class="hero__note">
            <p class="hero__note-label">On this page</p>
            <ul class="hero__note-list">
              <li>Current U.S. and world population estimates</li>
              <li>How the U.S. population changes over time</li>
              <li>Recent-date lookup and global comparison</li>
            </ul>
          </aside>
        </div>

        <div class="hero-counters">
          <div class="panel-card panel-card--hero">
            <UsPopulationCounterConnected size="md" />
          </div>

          <div class="panel-card panel-card--hero">
            <WorldPopulationCounterConnected size="md" />
          </div>
        </div>
      </div>
    </section>

    <section class="popclock-band">
      <div class="popclock-shell">
        <div class="content-grid content-grid--wide-left">
          <div class="band-heading">
            <h2 class="band-heading__title">How population changes</h2>
            <p class="band-heading__text">
              Births, deaths, and international migration all contribute to how
              the U.S. population changes over time.
            </p>
          </div>

          <div class="panel-card">
            <UsComponentsOfChange size="md" />
          </div>
        </div>
      </div>
    </section>

    <section class="popclock-band">
      <div class="popclock-shell">
        <div class="content-grid content-grid--wide-left">
          <div class="band-heading">
            <h2 class="band-heading__title">Population on a past date</h2>
            <p class="band-heading__text">
              Choose a recent date to view the estimated U.S. population for that day.
            </p>
          </div>

          <div class="panel-card">
            <UsPopulationOnDate size="md" />
          </div>
        </div>
      </div>
    </section>

     <section class="popclock-band">
      <div class="popclock-shell">
        <div class="band-heading">
          <h2 class="band-heading__title">Population distribution</h2>
          <p class="band-heading__text">
            Explore the largest and most densely populated states, counties, and cities.
          </p>
        </div>

        <div class="widget-grid widget-grid--two-up">
          <div class="panel-card">
           <UsMostPopulousConnected size="sm" :auto-rotate="true" :rotate-seconds="3" />
          </div>

          <div class="panel-card">
            <UsHighestDensityConnected size="sm" />
          </div>
        </div>
      </div>
    </section>

    <section class="popclock-band popclock-band--muted">
      <div class="popclock-shell">
        <div class="content-grid content-grid--wide-left">
          <div class="band-heading">
            <h2 class="band-heading__title">Global context</h2>
            <p class="band-heading__text">
              Compare the world’s most populous countries and place the U.S.
              population in a broader international view.
            </p>
          </div>

          <div class="panel-card">
            <MostPopulousCountries size="md" />
          </div>
        </div>
      </div>
    </section>
  </main>
</template>

<script setup>
import UsPopulationCounterConnected from '@widgets/components/UsPopulationCounterConnected.vue'
import UsComponentsOfChange from '@widgets/components/UsComponentsOfChange.vue'
import WorldPopulationCounterConnected from '@widgets/components/WorldPopulationCounterConnected.vue'
import MostPopulousCountries from '@widgets/components/MostPopulousCountries.vue'
import UsPopulationOnDate from '@widgets/components/UsPopulationOnDate.vue'
import UsMostPopulousConnected from '@widgets/components/UsMostPopulousConnected.vue'
import UsHighestDensityConnected from '@widgets/components/UsHighestDensityConnected.vue'
</script>

<style scoped>
.popclock-page {
  min-height: 100vh;
  background: #f6f8fb;
  color: #1b1b1b;
}

.popclock-shell {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 1.25rem;
}

.hero {
  padding: 2.6rem 0 2.1rem;
  background: linear-gradient(180deg, #eef5fb 0%, #f6f8fb 100%);
  border-bottom: 1px solid #d9e2ec;
}

.hero__grid {
  display: grid;
  grid-template-columns: minmax(0, 1.5fr) minmax(250px, 340px);
  gap: 1.4rem;
  align-items: start;
  margin-bottom: 1.5rem;
}

.hero__copy {
  max-width: 760px;
}

.page-eyebrow {
  margin: 0 0 0.55rem;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #005ea2;
}

.page-title {
  margin: 0 0 0.7rem;
  font-size: clamp(2rem, 3.3vw, 2.9rem) !important;
  line-height: 1.08;
  color: #112e51;
  letter-spacing: -0.02em;
}

.page-summary {
  margin: 0;
  max-width: 58ch;
  font-size: 1rem;
  line-height: 1.65;
  color: #3d4551;
}

.hero__note {
  padding: 0.95rem 1rem;
  border: 1px solid #d9e2ec;
  border-radius: 0.9rem;
  background: rgba(255, 255, 255, 0.72);
}

.hero__note-label {
  margin: 0 0 0.55rem;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #5b6f86;
}

.hero__note-list {
  margin: 0;
  padding-left: 1.1rem;
  color: #4b5563;
  line-height: 1.5;
}

.hero-counters {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.25rem;
  align-items: stretch;
}

.popclock-band {
  padding: 2.25rem 0;
}

.popclock-band--muted {
  background: #fbfcfe;
  border-top: 1px solid #e5e7eb;
  border-bottom: 1px solid #e5e7eb;
}

.band-heading {
  max-width: 580px;
  margin-bottom: 1rem;
}

.band-heading__title {
  margin: 0 0 0.25rem;
  font-size: clamp(1.05rem, 2vw, 1.9rem) !important;
  line-height: 1.2 !important;
  color: #4f6b8a !important;
  font-weight: 650 !important;
}

.band-heading__text {
  margin: 0;
  color: #6b7280;
  line-height: 1.5;
  font-size: 0.95rem;
}

.content-grid {
  display: grid;
  grid-template-columns: 280px minmax(0, 1fr);
  gap: 1.35rem;
  align-items: start;
}

.content-grid--wide-left {
  grid-template-columns: 300px minmax(0, 1fr);
}

.panel-card {
  padding: 1.1rem;
  border: 1px solid #dfe1e2;
  border-radius: 1rem;
  background: #ffffff;
  box-shadow: 0 2px 10px rgba(17, 46, 81, 0.04);
}

.panel-card--hero {
  background: rgba(255, 255, 255, 0.96);
}

.widget-grid {
  display: grid;
  gap: 1.25rem;
  align-items: start;
}

.widget-grid--two-up {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

@media (max-width: 960px) {
  .widget-grid--two-up {
    grid-template-columns: 1fr;
  }
}


@media (max-width: 960px) {
  .hero__grid,
  .content-grid,
  .content-grid--wide-left {
    grid-template-columns: 1fr;
  }

  .hero-counters {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .popclock-shell {
    padding: 0 1rem;
  }

  .hero {
    padding: 2.2rem 0 1.8rem;
  }

  .popclock-band {
    padding: 1.9rem 0;
  }

  .page-title {
    font-size: 1.9rem;
  }

  .panel-card {
    padding: 0.95rem;
  }
}
</style>