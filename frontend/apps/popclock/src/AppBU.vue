<template>
  <main class="popclock-page">
    <section class="page-intro">
      <div class="popclock-shell">
        <div class="page-intro__grid">
          <div class="page-intro__copy">
            <p class="page-eyebrow">Popdata Framework Demo</p>
            <h1 class="page-title">Population at a glance</h1>
            <p class="page-summary">
              A cleaner prototype of the Popclock experience using shared Popdata
              widgets. This page focuses on current population, change over time,
              and broader context without letting the page headings overpower the data.
            </p>
          </div>

          <aside class="page-intro__note">
            <p class="page-note__label">What this page shows</p>
            <ul class="page-note__list">
              <li>Current U.S. and world population estimates</li>
              <li>How the U.S. population changes</li>
              <li>Global comparison and recent-date lookup</li>
            </ul>
          </aside>
        </div>
      </div>
    </section>

    <section class="popclock-band popclock-band--highlight">
      <div class="popclock-shell">
        <div class="band-heading band-heading--tight">
          <h2>Current population</h2>
          <p>Live estimates for the United States and the world.</p>
        </div>

        <div class="hero-counters">
          <div class="panel-card">
            <UsPopulationCounterConnected size="md" />
          </div>

          <div class="panel-card">
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
            <p>
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
            <p>
              Choose a recent date to view the estimated U.S. population for that day.
            </p>
          </div>

          <div class="panel-card">
            <UsPopulationOnDate size="md" />
          </div>
        </div>
      </div>
    </section>

    <section class="popclock-band popclock-band--muted">
      <div class="popclock-shell">
        <div class="content-grid content-grid--wide-left">
          <div class="band-heading">
            <h2 class="band-heading__title">Global context</h2>
            <p>
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

.page-intro {
  padding: 2.75rem 0 2rem;
  background: linear-gradient(180deg, #eef5fb 0%, #f6f8fb 100%);
  border-bottom: 1px solid #d9e2ec;
}

.page-intro__grid {
  display: grid;
  grid-template-columns: minmax(0, 1.5fr) minmax(260px, 360px);
  gap: 1.5rem;
  align-items: start;
}

.page-intro__copy {
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
  margin: 0 0 0.75rem;
  font-size: clamp(2rem, 3.3vw, 2.9rem);
  line-height: 1.08;
  color: #112e51;
  letter-spacing: -0.02em;
}

.page-summary {
  margin: 0;
  max-width: 62ch;
  font-size: 1rem;
  line-height: 1.65;
  color: #3d4551;
}

.page-intro__note {
  padding: 1rem 1.05rem;
  border: 1px solid #d9e2ec;
  border-radius: 0.9rem;
  background: rgba(255, 255, 255, 0.72);
}

.page-note__label {
  margin: 0 0 0.65rem;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #112e51;
}

.page-note__list {
  margin: 0;
  padding-left: 1.1rem;
  color: #3d4551;
  line-height: 1.55;
}

.popclock-band {
  padding: 2.25rem 0;
}

.popclock-band--highlight {
  background: #f6f8fb;
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
  font-size: clamp(1.05rem, 2.0vw, 1.9rem) !important;
  line-height: 1.2 !important;
  color: #4f6b8a !important;
  font-weight: 650 !important;
}

.band-heading--tight {
  margin-bottom: 1.15rem;
}

.band-heading h2 {
  margin: 0 0 0.25rem;
  font-size: 2rem;
  line-height: 1.2;
  color: #4f6b8a;
  font-weight: 650;
}

.band-heading p {
  margin: 0;
  color: #6b7280;
  line-height: 1.5;
  font-size: 0.95rem;
}

.hero-counters {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.25rem;
  align-items: stretch;
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

@media (max-width: 960px) {
  .page-intro__grid,
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

  .page-intro {
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