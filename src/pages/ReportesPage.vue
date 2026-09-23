<template>
  <q-page class="reports-page">
    <header class="reports-header">
      <div>
        <p>Analitica</p>
        <h1>Reportes</h1>
        <span>Resumen visual de gastos, viajes, mantenimiento y estado general.</span>
      </div>
    </header>

    <q-tabs v-model="tab" dense active-color="primary" indicator-color="primary" align="left" class="tabs">
      <q-tab name="gastos" label="Gastos Mensuales" />
      <q-tab name="viajes" label="Busqueda de Viajes" />
      <q-tab name="mantenimiento" label="Mantenimiento" />
      <q-tab name="resumen" label="Resumen General" />
    </q-tabs>

    <q-tab-panels v-model="tab" animated class="panels">
      <q-tab-panel name="gastos">
        <section class="kpi-grid">
          <kpi-card label="Ingresos" :value="money(totalFreight)" icon="payments" />
          <kpi-card label="Gastos" :value="money(totalExpenses)" icon="receipt_long" />
          <kpi-card label="Ganancia" :value="money(totalProfit)" icon="trending_up" />
          <kpi-card label="Viajes" :value="trips.length" icon="route" />
        </section>
        <div class="chart-placeholder">Grafico de barras apiladas - combustible, peajes, viaticos y otros</div>
      </q-tab-panel>

      <q-tab-panel name="viajes">
        <div class="chart-placeholder">Panel de busqueda avanzada de viajes con filtros por fechas, camion, conductor y cliente</div>
      </q-tab-panel>

      <q-tab-panel name="mantenimiento">
        <div class="chart-placeholder">Grafico de costo por camion y distribucion por tipo de mantenimiento</div>
      </q-tab-panel>

      <q-tab-panel name="resumen">
        <section class="kpi-grid">
          <kpi-card label="Fletes Totales" :value="money(totalFreight)" icon="payments" />
          <kpi-card label="Gastos Totales" :value="money(totalExpenses)" icon="receipt_long" />
          <kpi-card label="Ganancia" :value="money(totalProfit)" icon="trending_up" />
          <kpi-card label="Margen" :value="`${margin}%`" icon="percent" />
        </section>
      </q-tab-panel>
    </q-tab-panels>
  </q-page>
</template>

<script setup>
import { computed, ref } from 'vue'
import KpiCard from 'components/KpiCard.vue'
import { money, tripExpenses, tripProfit, trips } from 'src/data/mock'

const tab = ref('gastos')
const totalFreight = computed(() => trips.reduce((sum, trip) => sum + trip.freight, 0))
const totalExpenses = computed(() => trips.reduce((sum, trip) => sum + tripExpenses(trip), 0))
const totalProfit = computed(() => trips.reduce((sum, trip) => sum + tripProfit(trip), 0))
const margin = computed(() => {
  if (!totalFreight.value) return 0
  return Math.round((totalProfit.value / totalFreight.value) * 100)
})
</script>

<style scoped>
.reports-page {
  display: grid;
  gap: 18px;
}

.reports-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.reports-header p {
  margin: 0 0 6px;
  color: #1d4ed8;
  font-size: 11px;
  font-weight: 900;
  letter-spacing: .14em;
  text-transform: uppercase;
}

.reports-header h1 {
  margin: 0;
  color: #1f2937;
  font-size: 24px;
  font-weight: 900;
}

.reports-header span {
  display: block;
  margin-top: 6px;
  color: #6b7280;
}

.tabs,
.panels {
  border: 1px solid #dbeafe;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0,0,0,.06);
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.chart-placeholder {
  display: grid;
  place-items: center;
  min-height: 320px;
  margin-top: 18px;
  border: 1px dashed #bfdbfe;
  border-radius: 12px;
  background: #eff6ff;
  color: #1e40af;
  font-weight: 800;
  text-align: center;
}

@media (max-width: 900px) {
  .kpi-grid {
    grid-template-columns: 1fr;
  }
}
</style>
