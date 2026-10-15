<template>
  <q-page class="driver-page">
    <section class="hero">
      <p>Portal del conductor</p>
      <h1>Bienvenido, {{ session?.fullName }}</h1>
      <span>Resumen visual de viajes asignados y mantenimiento pendiente.</span>

      <div class="driver-kpis">
        <kpi-card label="Viajes asignados" :value="myTrips.length" icon="route" />
        <kpi-card label="En transito" :value="activeTrips" icon="local_shipping" />
        <kpi-card label="Pendientes" :value="pendingTrips" icon="schedule" />
        <kpi-card label="Mantenimiento" :value="assignedMaintenance.length" icon="build" />
      </div>
    </section>

    <section class="panel-grid">
      <article class="panel">
        <header>
          <q-icon name="route" />
          <h2>Mis viajes</h2>
        </header>

        <div v-if="myTrips.length" class="list">
          <article v-for="trip in myTrips" :key="trip.id">
            <div>
              <strong>{{ trip.description }}</strong>
              <span>{{ trip.origin }} -> {{ trip.destination }} - {{ trip.date }}</span>
            </div>
            <status-badge :label="trip.status" />
          </article>
        </div>

        <div v-else class="empty-state">
          <q-icon name="route" />
          <strong>Sin viajes asignados</strong>
          <span>Cuando administracion asigne un viaje, aparecera aqui.</span>
        </div>
      </article>

      <article class="panel">
        <header>
          <q-icon name="build" />
          <h2>Mantenimiento que toca</h2>
        </header>

        <div v-if="assignedMaintenance.length" class="list">
          <article v-for="item in assignedMaintenance" :key="item.id">
            <div>
              <strong>{{ item.description }}</strong>
              <span>{{ item.date }} - {{ item.workshop }}</span>
            </div>
            <status-badge :label="item.status" />
          </article>
        </div>

        <div v-else class="empty-state">
          <q-icon name="build" />
          <strong>Sin mantenimiento pendiente</strong>
          <span>Las revisiones asignadas se mostraran en esta seccion.</span>
        </div>
      </article>
    </section>

    <section class="panel wide-panel">
      <header>
        <q-icon name="notifications" />
        <h2>Avisos importantes</h2>
      </header>

      <div class="notice">
        <q-icon name="info" />
        <div>
          <strong>Panel solo informativo</strong>
          <span>El chofer puede revisar cantidades, viajes asignados y mantenimientos pendientes. No administra registros.</span>
        </div>
      </div>
    </section>
  </q-page>
</template>

<script setup>
import { computed } from 'vue'
import KpiCard from 'components/KpiCard.vue'
import StatusBadge from 'components/StatusBadge.vue'
import { maintenance, trips } from 'src/data/mock'
import { getSession } from 'src/services/auth'

const session = getSession()
const myTrips = computed(() => trips.filter((trip) => trip.driver === session?.fullName))
const activeTrips = computed(() => myTrips.value.filter((trip) => trip.status === 'En transito').length)
const pendingTrips = computed(() => myTrips.value.filter((trip) => trip.status === 'Pendiente').length)
const assignedMaintenance = computed(() => maintenance.filter((item) => item.driver === session?.fullName))
</script>

<style scoped>
.driver-page {
  display: grid;
  gap: 18px;
}

.hero {
  overflow: hidden;
  padding: 28px;
  border-radius: 16px;
  background:
    linear-gradient(115deg, rgba(15, 23, 42, .94), rgba(30, 58, 138, .82), rgba(30, 41, 59, .35)),
    url('/img/truck.jpg') center / cover no-repeat,
    url('/img/truck-hero.png') center / cover no-repeat;
}

.hero p {
  margin: 0 0 8px;
  color: #93c5fd;
  font-size: 11px;
  font-weight: 900;
  letter-spacing: .16em;
  text-transform: uppercase;
}

.hero h1 {
  margin: 0;
  color: #fff;
  font-size: 26px;
  font-weight: 900;
}

.hero > span {
  display: block;
  margin-top: 8px;
  color: #dbeafe;
  font-size: 14px;
}

.driver-kpis {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-top: 24px;
}

.panel-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.panel {
  overflow: hidden;
  border: 1px solid #dbeafe;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0,0,0,.06);
}

.panel header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 16px 18px;
  border-bottom: 1px solid #dbeafe;
  background: #eff6ff;
}

.panel header .q-icon {
  color: #1d4ed8;
  font-size: 20px;
}

h2 {
  margin: 0;
  color: #1e40af;
  font-size: 14px;
  font-weight: 900;
}

.list {
  display: grid;
}

.list article {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 14px 18px;
  border-bottom: 1px solid #e5e7eb;
}

.list strong,
.list span,
.notice strong,
.notice span {
  display: block;
}

.list strong,
.notice strong {
  color: #1f2937;
  font-size: 13px;
}

.list span,
.notice span {
  margin-top: 4px;
  color: #6b7280;
  font-size: 12px;
}

.empty-state {
  display: grid;
  place-items: center;
  gap: 8px;
  min-height: 190px;
  padding: 28px;
  color: #6b7280;
  text-align: center;
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

.notice {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 18px;
}

.notice > .q-icon {
  display: grid;
  place-items: center;
  width: 36px;
  height: 36px;
  flex: 0 0 36px;
  border-radius: 10px;
  background: #eff6ff;
  color: #1d4ed8;
  font-size: 20px;
}

@media (max-width: 900px) {
  .driver-kpis,
  .panel-grid {
    grid-template-columns: 1fr;
  }
}
</style>
