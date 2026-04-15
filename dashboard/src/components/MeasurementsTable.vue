<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { api } from '../api'

const loading = ref(false)
const error = ref(null)
const page = ref(1)
const perPage = ref(25)
const q = ref('')
const equipId = ref('')
const from = ref('')
const to = ref('')

const response = ref(null)
const rows = computed(() => response.value?.data ?? [])

const selected = ref(null)

async function load() {
  loading.value = true
  error.value = null
  try {
    const { data } = await api.get('/measurements', {
      params: {
        page: page.value,
        per_page: perPage.value,
        q: q.value || undefined,
        equip_id: equipId.value || undefined,
        from: from.value || undefined,
        to: to.value || undefined,
      },
    })
    response.value = data
  } catch (e) {
    error.value = e?.message ?? String(e)
  } finally {
    loading.value = false
  }
}

function openRow(row) {
  selected.value = row
}

function close() {
  selected.value = null
}

function scaled(val) {
  if (val == null) return null
  const n = Number(val)
  if (!Number.isFinite(n)) return null
  return (n / 10).toFixed(1)
}

const lastPage = computed(() => response.value?.last_page ?? 1)

watch([perPage], () => {
  page.value = 1
  load()
})

onMounted(load)
</script>

<template>
  <section class="card">
    <div class="card__head">
      <h2>Measurements</h2>
      <button class="btn" :disabled="loading" @click="load">Refresh</button>
    </div>

    <div class="filters">
      <input v-model="q" class="input" placeholder="Search name/card/qr/phone/device…" />
      <input v-model="equipId" class="input" placeholder="Equip ID (optional)" />
      <input v-model="from" class="input" type="date" />
      <input v-model="to" class="input" type="date" />
      <button class="btn" :disabled="loading" @click="page = 1; load()">Apply</button>
    </div>

    <p v-if="loading" class="muted">Loading…</p>
    <p v-else-if="error" class="error">{{ error }}</p>

    <div v-else class="tableWrap">
      <table class="table">
        <thead>
          <tr>
            <th>Time</th>
            <th>Patient</th>
            <th>Device</th>
            <th>Height</th>
            <th>Weight</th>
            <th>Temp</th>
            <th>BP</th>
            <th>Pulse</th>
            <th>SpO₂</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in rows" :key="row.id" class="row" @click="openRow(row)">
            <td class="mono">{{ row.end_time || row.created_at }}</td>
            <td>{{ row.name || row.card_id || row.qr_code || '—' }}</td>
            <td class="mono">{{ row.equip_id }}</td>
            <td class="mono">{{ row.height != null ? `${scaled(row.height)} cm` : '—' }}</td>
            <td class="mono">{{ row.weight != null ? `${scaled(row.weight)} kg` : '—' }}</td>
            <td class="mono">{{ row.body_temperature != null ? `${scaled(row.body_temperature)} °C` : '—' }}</td>
            <td class="mono">
              {{
                row.systolic_bp != null && row.diastolic_bp != null
                  ? `${row.systolic_bp}/${row.diastolic_bp}`
                  : '—'
              }}
            </td>
            <td class="mono">{{ row.pulse_per_minute != null ? row.pulse_per_minute : '—' }}</td>
            <td class="mono">
              {{ row.blood_oxygen_saturation != null ? `${row.blood_oxygen_saturation}%` : '—' }}
            </td>
          </tr>
        </tbody>
      </table>

      <div class="pager">
        <button class="btn" :disabled="loading || page <= 1" @click="page -= 1; load()">Prev</button>
        <div class="muted">Page {{ page }} / {{ lastPage }}</div>
        <button class="btn" :disabled="loading || page >= lastPage" @click="page += 1; load()">Next</button>
      </div>
    </div>
  </section>

  <div v-if="selected" class="modal" @click.self="close">
    <div class="modal__panel">
      <div class="modal__head">
        <div>
          <div class="modal__title">Measurement #{{ selected.id }}</div>
          <div class="muted mono">{{ selected.end_time || selected.created_at }}</div>
        </div>
        <button class="btn" @click="close">Close</button>
      </div>
      <pre class="json">{{ JSON.stringify(selected, null, 2) }}</pre>
    </div>
  </div>
</template>

<style scoped>
.card {
  margin-top: 16px;
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
.filters {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr auto;
  gap: 10px;
  margin-bottom: 12px;
}
.input {
  width: 100%;
  border: 1px solid rgba(2, 132, 199, 0.25);
  background: rgba(255, 255, 255, 0.9);
  color: inherit;
  padding: 10px 12px;
  border-radius: 12px;
}
.tableWrap {
  overflow: auto;
  border: 1px solid rgba(2, 132, 199, 0.16);
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.65);
}
.table {
  width: 100%;
  border-collapse: collapse;
  min-width: 920px;
}
th,
td {
  padding: 10px 12px;
  border-bottom: 1px solid rgba(2, 132, 199, 0.14);
  text-align: left;
}
th {
  font-size: 12px;
  opacity: 0.7;
  position: sticky;
  top: 0;
  background: rgba(234, 247, 255, 0.95);
  backdrop-filter: blur(8px);
}
.row {
  cursor: pointer;
}
.row:hover {
  background: rgba(2, 132, 199, 0.06);
}
.mono {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New',
    monospace;
}
.pager {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 12px;
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
.modal {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: grid;
  place-items: center;
  padding: 18px;
}
.modal__panel {
  width: min(980px, 100%);
  max-height: 80vh;
  overflow: auto;
  border-radius: 16px;
  border: 1px solid rgba(2, 132, 199, 0.18);
  background: rgba(255, 255, 255, 0.92);
  padding: 14px;
}
.modal__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 12px;
}
.modal__title {
  font-size: 16px;
}
.json {
  font-size: 12px;
  line-height: 1.4;
  white-space: pre-wrap;
  word-break: break-word;
  margin: 0;
  padding: 12px;
  border-radius: 12px;
  background: rgba(234, 247, 255, 0.85);
  border: 1px solid rgba(2, 132, 199, 0.16);
}
@media (max-width: 1000px) {
  .filters {
    grid-template-columns: 1fr 1fr;
  }
}
</style>

