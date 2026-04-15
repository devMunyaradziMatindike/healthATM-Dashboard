<script setup>
import { ref } from 'vue'
import LatestMeasurement from './components/LatestMeasurement.vue'
import LoginPage from './components/LoginPage.vue'
import MeasurementsTable from './components/MeasurementsTable.vue'

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL ?? '(not set)'
const axisLogoUrl = new URL('./assets/axis-logo.jpeg', import.meta.url).href

const authed = ref(localStorage.getItem('healthatm_authed') === '1')

function onLoginSuccess() {
  localStorage.setItem('healthatm_authed', '1')
  authed.value = true
}

function logout() {
  localStorage.removeItem('healthatm_authed')
  authed.value = false
}
</script>

<template>
  <LoginPage v-if="!authed" @success="onLoginSuccess" />
  <div v-else class="app">
    <header class="top">
      <div>
        <div class="titleRow">
          <img class="brandLogo" :src="axisLogoUrl" alt="Axis Solutions" />
          <div class="title">HealthATM Portal Axis Solutions</div>
        </div>
        <div class="subtitle">
          Live device uploads from <span class="mono">{{ apiBaseUrl }}</span>
        </div>
      </div>
      <button class="logout" type="button" @click="logout">Logout</button>
    </header>

    <main class="main">
      <LatestMeasurement />
      <MeasurementsTable />
    </main>
  </div>
</template>

<style scoped>
.app {
  min-height: 100vh;
  background: radial-gradient(900px 500px at 15% 20%, rgba(56, 189, 248, 0.35), transparent 60%),
    radial-gradient(900px 600px at 85% 25%, rgba(34, 211, 238, 0.28), transparent 55%),
    radial-gradient(1000px 700px at 50% 90%, rgba(14, 165, 233, 0.18), transparent 60%),
    linear-gradient(180deg, #eaf7ff 0%, #d6f1ff 45%, #c7ecff 100%);
  color: #0b1b2a;
}
.top {
  padding: 22px 18px 8px;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 12px;
  max-width: 1200px;
  margin: 0 auto;
}
.titleRow {
  display: flex;
  align-items: center;
  gap: 10px;
}
.brandLogo {
  width: 84px;
  height: 84px;
  object-fit: contain;
  border-radius: 14px;
  background: #fff;
  padding: 7px;
  border: 1px solid rgba(2, 132, 199, 0.18);
}
.title {
  font-size: 22px;
  font-weight: 700;
  letter-spacing: 0.2px;
}
.subtitle {
  margin-top: 6px;
  font-size: 13px;
  opacity: 0.75;
}
.logout {
  border: 1px solid rgba(2, 132, 199, 0.35);
  background: rgba(2, 132, 199, 0.12);
  color: inherit;
  padding: 8px 10px;
  border-radius: 10px;
  cursor: pointer;
}
.logout:hover {
  background: rgba(2, 132, 199, 0.18);
}
.main {
  padding: 12px 18px 42px;
  max-width: 1200px;
  margin: 0 auto;
}
.mono {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New',
    monospace;
}
</style>
