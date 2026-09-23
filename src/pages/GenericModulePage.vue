<template>
  <q-page class="module-page">
    <header class="module-header">
      <div>
        <p>{{ module.iconLabel || 'Modulo' }}</p>
        <h1>{{ module.title }}</h1>
        <span>{{ module.subtitle }}</span>
      </div>

      <q-btn
        v-if="module.action && !isDriver"
        unelevated
        no-caps
        color="primary"
        icon="add"
        :label="module.action"
      />
    </header>

    <q-banner v-if="module.alert" rounded class="alert-banner">
      <q-icon name="warning" />
      {{ module.alert }}
    </q-banner>

    <section v-if="module.kpis" class="kpi-grid">
      <kpi-card
        v-for="item in module.kpis"
        :key="item.label"
        :label="item.label"
        :value="item.value"
        :icon="item.icon"
      />
    </section>

    <section v-if="module.filters" class="filter-card">
      <q-input
        v-for="filter in module.filters"
        :key="filter"
        outlined
        dense
        :label="filter"
      >
        <template #prepend>
          <q-icon name="search" />
        </template>
      </q-input>
    </section>

    <section v-if="module.map" class="map-panel">
      <q-icon name="map" />
      <strong>Mapa GPS - integracion pendiente</strong>
      <span>Panel preparado para integrar mapas reales en una fase posterior.</span>
    </section>

    <section v-if="module.cards" class="cards-grid">
      <article v-for="item in module.cards" :key="item.id" class="data-card">
        <div class="card-top">
          <div>
            <status-badge v-if="item.status" :label="item.status" />
            <h2>{{ item.title }}</h2>
            <p>{{ item.subtitle }}</p>
          </div>

          <div v-if="!isDriver" class="actions">
            <q-btn flat round dense icon="edit" />
            <q-btn flat round dense icon="delete" class="delete-btn" />
          </div>
        </div>

        <dl class="meta-grid">
          <div v-for="meta in item.meta" :key="meta[0]">
            <dt>{{ meta[0] }}</dt>
            <dd>{{ meta[1] }}</dd>
          </div>
        </dl>

        <div v-if="item.finance" class="finance-row">
          <div v-for="entry in item.finance" :key="entry[0]" :class="entry[2]">
            <span>{{ entry[0] }}</span>
            <strong>{{ entry[1] }}</strong>
          </div>
        </div>
      </article>

      <div v-if="!module.cards.length" class="empty-state">
        <q-icon :name="module.icon || 'inbox'" />
        <strong>Sin registros</strong>
        <span>Este modulo esta listo para registrar informacion.</span>
      </div>
    </section>

    <section v-if="module.table" class="table-card">
      <div class="table-wrap">
        <table v-if="module.table.rows.length">
          <thead>
            <tr>
              <th v-for="column in module.table.columns" :key="column">{{ column }}</th>
              <th v-if="!isDriver">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, rowIndex) in module.table.rows" :key="rowIndex">
              <td v-for="(cell, cellIndex) in row" :key="`${rowIndex}-${cellIndex}`">
                <status-badge
                  v-if="module.table.columns[cellIndex] === 'Estado'"
                  :label="cell"
                />
                <span v-else>{{ cell }}</span>
              </td>
              <td v-if="!isDriver" class="table-actions">
                <q-btn flat round dense icon="edit" />
                <q-btn flat round dense icon="delete" class="delete-btn" />
              </td>
            </tr>
          </tbody>
        </table>
        <div v-else class="empty-state">
          <q-icon :name="module.icon || 'inbox'" />
          <strong>Sin registros</strong>
          <span>Este modulo esta listo para registrar informacion.</span>
        </div>
      </div>
    </section>
  </q-page>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import KpiCard from 'components/KpiCard.vue'
import StatusBadge from 'components/StatusBadge.vue'
import { modules } from 'src/data/modules'
import { getSession } from 'src/services/auth'

const props = defineProps({
  moduleKey: {
    type: String,
    default: '',
  },
})

const route = useRoute()
const session = getSession()
const module = computed(() => modules[props.moduleKey || route.meta.moduleKey] || modules.viajes)
const isDriver = computed(() => session?.role === 'Chofer')
</script>

<style scoped>
.module-page {
  display: grid;
  gap: 18px;
}

.module-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.module-header p {
  margin: 0 0 6px;
  color: #1d4ed8;
  font-size: 11px;
  font-weight: 900;
  letter-spacing: .14em;
  text-transform: uppercase;
}

.module-header h1 {
  margin: 0;
  color: #1f2937;
  font-size: 24px;
  font-weight: 900;
}

.module-header span {
  display: block;
  margin-top: 6px;
  color: #6b7280;
  font-size: 14px;
}

.alert-banner {
  border: 1px solid #fde68a;
  background: #fffbeb;
  color: #92400e;
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.filter-card {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 12px;
  padding: 16px;
  border: 1px solid #dbeafe;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0,0,0,.06);
}

.map-panel {
  display: grid;
  place-items: center;
  min-height: 360px;
  border: 1px solid #dbeafe;
  border-radius: 12px;
  background: #e5e7eb;
  color: #6b7280;
  text-align: center;
}

.map-panel .q-icon {
  color: #94a3b8;
  font-size: 46px;
}

.map-panel strong,
.map-panel span {
  display: block;
}

.map-panel strong {
  color: #475569;
  font-size: 16px;
}

.cards-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.cards-grid > .empty-state {
  grid-column: 1 / -1;
}

.data-card,
.table-card {
  border: 1px solid #dbeafe;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0,0,0,.06);
}

.data-card {
  display: grid;
  gap: 16px;
  padding: 18px;
}

.card-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
}

h2 {
  margin: 10px 0 4px;
  color: #1f2937;
  font-size: 18px;
  font-weight: 900;
}

.card-top p {
  margin: 0;
  color: #6b7280;
  font-size: 13px;
}

.actions,
.table-actions {
  display: flex;
  gap: 2px;
}

.delete-btn {
  color: #ef4444;
}

.meta-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin: 0;
}

dt {
  color: #6b7280;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
}

dd {
  margin: 4px 0 0;
  color: #1f2937;
  font-size: 13px;
  font-weight: 700;
}

.finance-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  padding: 12px;
  border-radius: 10px;
  background: #f8fafc;
}

.finance-row span,
.finance-row strong {
  display: block;
}

.finance-row span {
  color: #6b7280;
  font-size: 11px;
  font-weight: 800;
}

.finance-row strong {
  margin-top: 4px;
  color: #1d4ed8;
  font-size: 13px;
}

.finance-row .danger strong {
  color: #ef4444;
}

.table-wrap {
  overflow-x: auto;
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

.empty-state {
  display: grid;
  place-items: center;
  gap: 8px;
  min-height: 180px;
  padding: 28px;
  border: 1px dashed #bfdbfe;
  border-radius: 12px;
  background: #fff;
  color: #6b7280;
  text-align: center;
}

.table-card .empty-state {
  border: 0;
  border-radius: 0;
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

@media (max-width: 1100px) {
  .cards-grid,
  .kpi-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .filter-card {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 680px) {
  .module-header {
    align-items: flex-start;
    flex-direction: column;
  }

  .cards-grid,
  .kpi-grid,
  .filter-card,
  .meta-grid,
  .finance-row {
    grid-template-columns: 1fr;
  }
}
</style>
