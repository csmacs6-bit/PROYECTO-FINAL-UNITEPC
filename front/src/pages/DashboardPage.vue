<template>
  <q-page class="page">
    <section class="hero-card">
      <div class="hero-copy">
        <p>Panel de control</p>
        <h1>Transportes Merida</h1>
        <span>{{ todayLabel }}</span>

        <div class="hero-metrics">
          <article>
            <strong>{{ trucks.length }}</strong>
            <span>Unidades</span>
          </article>
          <article>
            <strong>{{ activeTrips }}</strong>
            <span>Viajes activos</span>
          </article>
          <article>
            <strong>{{ maintenance.length }}</strong>
            <span>Mantenimientos</span>
          </article>
        </div>
      </div>

      <div class="hero-thumb">
        <div />
        <span>Flota Transportes Merida</span>
      </div>
    </section>

    <section class="kpi-grid">
      <kpi-card label="Camiones" :value="trucks.length" icon="local_shipping" />
      <kpi-card label="Viajes" :value="trips.length" icon="route" />
      <kpi-card label="Ingresos" :value="money(totalFreight)" icon="payments" />
      <kpi-card label="Combustible" :value="`${totalLiters} L`" icon="local_gas_station" />
    </section>

    <section class="dashboard-grid">
      <article class="panel finance-panel">
        <header>
          <h2>Resumen Financiero de Viajes</h2>
          <div class="panel-thumb" />
        </header>

        <div class="finance-grid">
          <div>
            <span>Fletes Cobrados</span>
            <strong>{{ money(totalFreight) }}</strong>
          </div>
          <div class="danger">
            <span>Total Gastos</span>
            <strong>{{ money(totalExpenses) }}</strong>
          </div>
          <div>
            <span>Ganancia Neta</span>
            <strong>{{ money(netProfit) }}</strong>
          </div>
        </div>

        <div class="progress-block">
          <div>
            <span>Margen de rentabilidad</span>
            <strong>{{ margin }}%</strong>
          </div>
          <q-linear-progress :value="margin / 100" rounded color="primary" size="10px" />
        </div>
      </article>

      <article class="panel fleet-panel">
        <header>
          <h2>Estado de Flota</h2>
        </header>

        <div class="fleet-list">
          <div v-for="item in fleetState" :key="item.label">
            <div>
              <span class="dot" :class="item.tone" />
              <strong>{{ item.label }}</strong>
              <em>{{ item.count }}</em>
            </div>
            <q-linear-progress :value="fleetProgress(item.count)" rounded :color="item.color" size="8px" />
          </div>
        </div>

        <div class="fleet-image" />
      </article>
    </section>

    <section class="panel">
      <header class="section-header">
        <div>
          <h2>Viajes Recientes</h2>
          <p>Ultimos movimientos operativos registrados.</p>
        </div>
      </header>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Descripcion</th>
              <th>Ruta</th>
              <th>Flete</th>
              <th>Ganancia</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody v-if="trips.length">
            <tr v-for="trip in trips" :key="trip.id">
              <td>{{ trip.date }}</td>
              <td>{{ trip.description }}</td>
              <td>{{ trip.origin }} -> {{ trip.destination }}</td>
              <td>{{ money(trip.freight) }}</td>
              <td>{{ money(tripProfit(trip)) }}</td>
              <td><status-badge :label="trip.status" /></td>
            </tr>
          </tbody>
        </table>
        <div v-if="!trips.length" class="empty-state">
          <q-icon name="local_shipping" />
          <strong>Sin viajes registrados</strong>
          <span>Los viajes apareceran aqui cuando sean creados.</span>
        </div>
      </div>
    </section>

    <section class="panel alert-panel">
      <header>
        <q-icon name="warning" />
        <h2>Alertas de Mantenimiento</h2>
      </header>

      <div v-if="maintenance.length" class="maintenance-list">
        <article v-for="item in maintenance" :key="item.id">
          <q-icon name="build" />
          <div>
            <strong>{{ item.description }}</strong>
            <span>{{ item.truck }} - {{ item.date }} - {{ item.workshop }}</span>
          </div>
          <status-badge :label="item.status" />
        </article>
      </div>
      <div v-else class="empty-state compact">
        <q-icon name="build" />
        <strong>Sin alertas registradas</strong>
        <span>No hay mantenimientos pendientes.</span>
      </div>
    </section>
  </q-page>
</template>

<script setup>
import { computed } from 'vue'
import KpiCard from 'components/KpiCard.vue'
import StatusBadge from 'components/StatusBadge.vue'
import { fuelRecords, maintenance, money, tripExpenses, tripProfit, trips, trucks } from 'src/data/mock'

const todayLabel = new Intl.DateTimeFormat('es-BO', {
  weekday: 'long',
  year: 'numeric',
  month: 'long',
  day: 'numeric',
}).format(new Date())

const activeTrips = computed(() => trips.filter((trip) => trip.status === 'En transito').length)
const totalFreight = computed(() => trips.reduce((sum, trip) => sum + trip.freight, 0))
const totalExpenses = computed(() => trips.reduce((sum, trip) => sum + tripExpenses(trip), 0))
const netProfit = computed(() => totalFreight.value - totalExpenses.value)
const margin = computed(() => {
  if (!totalFreight.value) return 0
  return Math.round((netProfit.value / totalFreight.value) * 100)
})
const totalLiters = computed(() => fuelRecords.reduce((sum, item) => sum + item.liters, 0))

function fleetProgress(count) {
  if (!trucks.length) return 0
  return count / trucks.length
}

const fleetState = computed(() => [
  {
    label: 'Disponibles',
    count: trucks.filter((truck) => truck.status === 'Disponible').length,
    color: 'primary',
    tone: 'primary',
  },
  {
    label: 'En Transito',
    count: trucks.filter((truck) => truck.status === 'En transito').length,
    color: 'primary',
    tone: 'primary',
  },
  {
    label: 'En Mantenimiento',
    count: trucks.filter((truck) => truck.status === 'Mantenimiento').length,
    color: 'amber',
    tone: 'warning',
  },
])
</script>

<style scoped>
.page {
  display: grid;
  gap: 22px;
}

.hero-card {
  position: relative;
  display: flex;
  justify-content: space-between;
  gap: 24px;
  min-height: 220px;
  overflow: hidden;
  padding: 28px;
  border-radius: 16px;
  background:
    linear-gradient(115deg, rgba(15, 23, 42, .94), rgba(30, 58, 138, .82), rgba(30, 41, 59, .32)),
    url('/img/truck.jpg') center / cover no-repeat,
    url('/img/truck-hero.png') center / cover no-repeat;
}

.hero-copy {
  position: relative;
  z-index: 1;
  color: #fff;
}

.hero-copy p {
  margin: 0 0 8px;
  color: #93c5fd;
  font-size: 11px;
  font-weight: 900;
  letter-spacing: .16em;
  text-transform: uppercase;
}

.hero-copy h1 {
  margin: 0;
  font-size: 26px;
  font-weight: 900;
}

.hero-copy > span {
  display: block;
  margin-top: 8px;
  color: #bfdbfe;
}

.hero-metrics {
  display: grid;
  grid-template-columns: repeat(3, minmax(120px, 1fr));
  gap: 12px;
  margin-top: 28px;
}

.hero-metrics article {
  padding: 12px 16px;
  border: 1px solid rgba(255, 255, 255, .2);
  border-radius: 12px;
  background: rgba(0, 0, 0, .3);
  backdrop-filter: blur(5px);
}

.hero-metrics strong,
.hero-metrics span {
  display: block;
}

.hero-metrics strong {
  font-size: 24px;
}

.hero-metrics span {
  color: #dbeafe;
  font-size: 12px;
}

.hero-thumb {
  position: relative;
  z-index: 1;
  align-self: center;
  width: 208px;
}

.hero-thumb div {
  height: 128px;
  border-radius: 12px;
  box-shadow: 0 0 0 1px rgba(255,255,255,.2);
  background:
    url('/img/truck.jpg') center / cover no-repeat,
    url('/img/truck-hero.png') center / cover no-repeat;
}

.hero-thumb span {
  display: block;
  margin-top: 8px;
  color: rgba(255,255,255,.55);
  font-size: 11px;
  text-align: right;
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 16px;
}

.panel {
  overflow: hidden;
  border: 1px solid #dbeafe;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0,0,0,.06);
}

.panel > header,
.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 16px 18px;
  border-bottom: 1px solid #dbeafe;
  background: #eff6ff;
}

h2 {
  margin: 0;
  color: #1e40af;
  font-size: 14px;
  font-weight: 900;
}

.section-header p {
  margin: 4px 0 0;
  color: #6b7280;
  font-size: 12px;
}

.panel-thumb {
  width: 56px;
  height: 36px;
  border-radius: 8px;
  background:
    url('/img/truck.jpg') center / cover no-repeat,
    url('/img/truck-hero.png') center / cover no-repeat;
}

.finance-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  padding: 18px;
}

.finance-grid div {
  padding: 14px;
  border-radius: 12px;
  background: #eff6ff;
}

.finance-grid .danger {
  background: #fef2f2;
}

.finance-grid span,
.progress-block span {
  display: block;
  color: #6b7280;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
}

.finance-grid strong,
.progress-block strong {
  display: block;
  margin-top: 6px;
  color: #1f2937;
  font-size: 18px;
}

.progress-block {
  display: grid;
  gap: 10px;
  padding: 0 18px 18px;
}

.progress-block > div {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.fleet-list {
  display: grid;
  gap: 16px;
  padding: 18px;
}

.fleet-list > div {
  display: grid;
  gap: 8px;
}

.fleet-list div div {
  display: flex;
  align-items: center;
  gap: 8px;
}

.fleet-list strong {
  color: #1f2937;
  font-size: 13px;
}

.fleet-list em {
  margin-left: auto;
  color: #6b7280;
  font-style: normal;
  font-size: 12px;
}

.dot {
  width: 8px;
  height: 8px;
  border-radius: 999px;
  background: #1d4ed8;
}

.dot.warning {
  background: #d97706;
}

.fleet-image {
  height: 64px;
  margin: 0 18px 18px;
  border-radius: 10px;
  background:
    linear-gradient(rgba(255,255,255,.3), rgba(255,255,255,.3)),
    url('/img/truck.jpg') center / cover no-repeat,
    url('/img/truck-hero.png') center / cover no-repeat;
  opacity: .7;
}

.table-wrap {
  overflow-x: auto;
}

.empty-state {
  display: grid;
  place-items: center;
  gap: 8px;
  min-height: 180px;
  padding: 28px;
  color: #6b7280;
  text-align: center;
}

.empty-state.compact {
  min-height: 140px;
}

.empty-state .q-icon {
  color: #94a3b8;
  font-size: 42px;
}

.empty-state strong {
  color: #1f2937;
  font-size: 14px;
}

.empty-state span {
  font-size: 12px;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  background: #eff6ff;
  color: #1e40af;
  font-size: 11px;
  text-align: left;
  text-transform: uppercase;
}

th,
td {
  padding: 13px 16px;
  border-bottom: 1px solid #e5e7eb;
  font-size: 13px;
}

tbody tr:hover {
  background: rgba(239, 246, 255, .6);
}

.alert-panel > header {
  justify-content: flex-start;
  background: #fffbeb;
}

.alert-panel header .q-icon {
  color: #d97706;
}

.maintenance-list {
  display: grid;
  gap: 10px;
  padding: 16px;
}

.maintenance-list article {
  display: grid;
  grid-template-columns: 34px 1fr auto;
  align-items: center;
  gap: 12px;
}

.maintenance-list article > .q-icon {
  display: grid;
  place-items: center;
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: #fffbeb;
  color: #d97706;
}

.maintenance-list strong,
.maintenance-list span {
  display: block;
}

.maintenance-list strong {
  color: #1f2937;
  font-size: 13px;
}

.maintenance-list span {
  color: #6b7280;
  font-size: 12px;
}

@media (max-width: 980px) {
  .kpi-grid,
  .dashboard-grid {
    grid-template-columns: 1fr 1fr;
  }

  .hero-thumb {
    display: none;
  }
}

@media (max-width: 680px) {
  .kpi-grid,
  .dashboard-grid,
  .finance-grid,
  .hero-metrics {
    grid-template-columns: 1fr;
  }
}
</style>
