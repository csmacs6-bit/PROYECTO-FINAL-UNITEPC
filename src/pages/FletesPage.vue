<template>
  <q-page class="freights-page">
    <header>
      <h1>Fletes</h1>
      <p>Control de cobros de cargas entregadas. Cada carga aparece una sola vez con sus viajes asociados.</p>
    </header>
    <q-linear-progress v-if="loading" indeterminate color="primary" />
    <q-banner v-if="loadError" class="bg-red-1 text-negative" role="alert">
      {{ loadError }}
      <template #action><q-btn flat label="Reintentar" @click="load" /></template>
    </q-banner>
    <section class="summary">
      <kpi-card label="Total de fletes" :value="money(total)" icon="payments" />
      <kpi-card label="Pendiente de cobro" :value="money(pending)" icon="schedule" />
      <kpi-card label="Cobrado" :value="money(total - pending)" icon="check_circle" />
    </section>
    <section class="filters">
      <q-input v-model="search" outlined dense clearable label="Buscar carga, cliente o camión" />
      <q-select v-model="status" outlined dense :options="['Todos', 'Pendiente', 'Cobrado']" label="Estado del cobro" />
      <q-input v-model="month" outlined dense clearable type="month" label="Mes de salida del viaje o carga" />
    </section>
    <q-table :rows="filteredRows" :columns="columns" row-key="id" :loading="loading" :pagination="{ rowsPerPage: 10 }" no-data-label="No hay fletes para mostrar. Marca una carga como Entregada para verla aquí." flat bordered wrap-cells>
      <template #body-cell-paymentStatus="props">
        <q-td :props="props"><q-badge :color="props.row.paymentStatus === 'Cobrado' ? 'positive' : 'orange-9'" :label="props.row.paymentStatus" /></q-td>
      </template>
      <template #body-cell-actions="props">
        <q-td :props="props">
          <q-btn flat no-caps color="primary" :label="props.row.paymentStatus === 'Cobrado' ? 'Marcar pendiente' : 'Registrar cobro'" :disable="saving || loading || !!loadError" @click="openPayment(props.row)" />
        </q-td>
      </template>
    </q-table>
    <q-dialog v-model="dialogOpen" persistent>
      <q-card class="payment-dialog">
        <q-form @submit="savePayment">
          <q-card-section>
            <h2>{{ selected?.paymentStatus === 'Cobrado' ? 'Marcar como pendiente' : 'Registrar cobro' }}</h2>
            <p>{{ selected?.description }} · {{ money(Number(selected?.freight || 0)) }}</p>
            <q-input v-if="selected?.paymentStatus !== 'Cobrado'" v-model="paidAt" outlined type="date" label="Fecha de cobro" :rules="[value => !!value || 'Indica la fecha de cobro']" />
            <p v-else>El flete volverá a quedar pendiente y se borrará su fecha de cobro.</p>
          </q-card-section>
          <q-card-actions align="right">
            <q-btn flat no-caps label="Cancelar" :disable="saving" v-close-popup />
            <q-btn unelevated no-caps color="primary" label="Guardar" type="submit" :loading="saving" />
          </q-card-actions>
        </q-form>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useQuasar } from 'quasar'
import KpiCard from 'components/KpiCard.vue'
import { money } from 'src/data/mock'
import { loadRecords, records, updateFreightPayment } from 'src/services/registrations'

const $q = useQuasar()
const loading = ref(false)
const loadError = ref('')
const search = ref('')
const status = ref('Todos')
const month = ref('')
const selected = ref(null)
const dialogOpen = ref(false)
const paidAt = ref('')
const saving = ref(false)
const rows = computed(() => records.cargas.filter(cargo => cargo.status === 'Entregada').map(cargo => {
  const trips = records.viajes.filter(trip => trip.cargo_id === cargo.id)
  return {
    ...cargo,
    paymentStatus: cargo.paymentStatus || 'Pendiente',
    client: records.clientes.find(client => client.id === cargo.client_id)?.name || 'Sin asignar',
    trucks: [...new Set(trips.map(trip => records.camiones.find(truck => truck.id === trip.truck_id)?.plate || 'Sin asignar'))].join(', ') || 'Sin viaje asignado',
    tripDates: trips.map(trip => `#${trip.id}: ${trip.date} / ${trip.arrivalDate || cargo.arrivalDate || 'Sin fecha de llegada'}`).join('; ') || `${cargo.departureDate} / ${cargo.arrivalDate || 'Sin fecha de llegada'}`,
    dates: trips.length ? trips.map(trip => trip.date) : [cargo.departureDate],
    route: `${cargo.origin} → ${cargo.destination}`,
  }
}))
const total = computed(() => rows.value.reduce((sum, row) => sum + Number(row.freight), 0))
const pending = computed(() => rows.value.filter(row => row.paymentStatus !== 'Cobrado').reduce((sum, row) => sum + Number(row.freight), 0))
const filteredRows = computed(() => rows.value.filter(row => {
  const query = (search.value || '').trim().toLocaleLowerCase()
  return (!query || [row.description, row.client, row.trucks, row.route].join(' ').toLocaleLowerCase().includes(query)) &&
    (status.value === 'Todos' || row.paymentStatus === status.value) &&
    (!month.value || row.dates.some(date => date?.startsWith(month.value)))
}))
const columns = [
  { name: 'description', label: 'Carga', field: 'description' },
  { name: 'client', label: 'Cliente', field: 'client' },
  { name: 'trucks', label: 'Camiones', field: 'trucks' },
  { name: 'route', label: 'Ruta', field: 'route' },
  { name: 'tripDates', label: 'Viajes: salida / llegada', field: 'tripDates' },
  { name: 'freight', label: 'Flete (Bs)', field: row => Number(row.freight), format: money, sortable: true },
  { name: 'paymentStatus', label: 'Estado del cobro', field: 'paymentStatus' },
  { name: 'paidAt', label: 'Fecha de cobro', field: row => row.paidAt || '—' },
  { name: 'actions', label: 'Acciones', field: 'id' },
].map(column => ({ align: 'left', ...column }))

async function load() {
  loading.value = true
  loadError.value = ''
  try { await loadRecords('cargas') } catch (error) { loadError.value = error.message }
  finally { loading.value = false }
}
function openPayment(row) {
  selected.value = row
  paidAt.value = new Intl.DateTimeFormat('en-CA', { timeZone: 'America/La_Paz', year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date())
  dialogOpen.value = true
}
async function savePayment() {
  if (saving.value) return
  saving.value = true
  try {
    const nextStatus = selected.value.paymentStatus === 'Cobrado' ? 'Pendiente' : 'Cobrado'
    await updateFreightPayment(selected.value.id, { paymentStatus: nextStatus, paidAt: nextStatus === 'Cobrado' ? paidAt.value : null })
    dialogOpen.value = false
    $q.notify({ type: 'positive', message: 'Estado del cobro guardado.' })
  } catch (error) { $q.notify({ type: 'negative', message: error.message }) }
  finally { saving.value = false }
}
onMounted(load)
</script>

<style scoped>
.freights-page { display: grid; gap: 18px; }
h1 { margin: 0; color: #1f2937; font-size: 24px; font-weight: 900; }
h2 { margin: 0 0 12px; font-size: 20px; }
p { color: #6b7280; }
.summary, .filters { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
.filters { padding: 16px; border: 1px solid #dbeafe; border-radius: 12px; background: white; }
.payment-dialog { width: 420px; max-width: 95vw; }
@media (max-width: 700px) { .summary, .filters { grid-template-columns: 1fr; } }
</style>
