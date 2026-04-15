<script setup>
import { computed, onMounted, ref } from 'vue'
import { api } from '../api'

const loading = ref(false)
const error = ref(null)
const measurement = ref(null)

async function load() {
  loading.value = true
  error.value = null
  try {
    const { data } = await api.get('/measurements/latest')
    measurement.value = data
  } catch (e) {
    error.value = e?.message ?? String(e)
  } finally {
    loading.value = false
  }
}

onMounted(load)

const heightCm = computed(() =>
  measurement.value?.height != null ? (Number(measurement.value.height) / 10).toFixed(1) : null,
)
const weightKg = computed(() =>
  measurement.value?.weight != null ? (Number(measurement.value.weight) / 10).toFixed(1) : null,
)
const tempC = computed(() =>
  measurement.value?.body_temperature != null
    ? (Number(measurement.value.body_temperature) / 10).toFixed(1)
    : null,
)
</script>

<template>
  <section class="card">
    <div class="card__head">
      <h2>Latest measurement</h2>
      <button class="btn" :disabled="loading" @click="load">Refresh</button>
    </div>

    <p v-if="loading" class="muted">Loading…</p>
    <p v-else-if="error" class="error">{{ error }}</p>
    <p v-else-if="!measurement" class="muted">No data yet.</p>

    <div v-else class="grid">
      <div class="kpi">
        <div class="kpi__label">Patient</div>
        <div class="kpi__value">{{ measurement.name || measurement.card_id || '—' }}</div>
      </div>
      <div class="kpi">
        <div class="kpi__label">Device</div>
        <div class="kpi__value">{{ measurement.equip_id }}</div>
      </div>
      <div class="kpi">
        <div class="kpi__label">Time</div>
        <div class="kpi__value">{{ measurement.end_time || '—' }}</div>
      </div>

      <div class="kpi">
        <div class="kpi__label">Height</div>
        <div class="kpi__value">{{ heightCm ? `${heightCm} cm` : '—' }}</div>
      </div>
      <div class="kpi">
        <div class="kpi__label">Weight</div>
        <div class="kpi__value">{{ weightKg ? `${weightKg} kg` : '—' }}</div>
      </div>
      <div class="kpi">
        <div class="kpi__label">Temperature</div>
        <div class="kpi__value">{{ tempC ? `${tempC} °C` : '—' }}</div>
      </div>

      <div class="kpi">
        <div class="kpi__label">BP</div>
        <div class="kpi__value">
          {{
            measurement.systolic_bp != null && measurement.diastolic_bp != null
              ? `${measurement.systolic_bp}/${measurement.diastolic_bp}`
              : '—'
          }}
        </div>
      </div>
      <div class="kpi">
        <div class="kpi__label">Pulse</div>
        <div class="kpi__value">
          {{ measurement.pulse_per_minute != null ? `${measurement.pulse_per_minute} /min` : '—' }}
        </div>
      </div>
      <div class="kpi">
        <div class="kpi__label">SpO₂</div>
        <div class="kpi__value">
          {{
            measurement.blood_oxygen_saturation != null
              ? `${measurement.blood_oxygen_saturation}%`
              : '—'
          }}
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.card {
  border: 1px solid rgba(2, 132, 199, 0.18);
  background: rgba(255, 255, 255, 0.75);
  border-radius: 16px;
  padding: 16px;
  box-shadow:
    0 10px 30px rgba(2, 132, 199, 0.10),
    0 1px 0 rgba(255, 255, 255, 0.6) inset;
}
.card__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 12px;
}
.grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 12px;
}
.kpi {
  border: 1px solid rgba(2, 132, 199, 0.16);
  border-radius: 14px;
  padding: 12px;
  background: rgba(255, 255, 255, 0.7);
}
.kpi__label {
  font-size: 12px;
  opacity: 0.7;
}
.kpi__value {
  font-size: 18px;
  margin-top: 6px;
}
.btn {
  border: 1px solid rgba(2, 132, 199, 0.35);
  background: rgba(2, 132, 199, 0.12);
  color: inherit;
  padding: 8px 10px;
  border-radius: 10px;
  cursor: pointer;
}
.btn:hover {
  background: rgba(2, 132, 199, 0.18);
}
.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.muted {
  opacity: 0.7;
}
.error {
  color: #b42318;
}
@media (max-width: 900px) {
  .grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
</style>

