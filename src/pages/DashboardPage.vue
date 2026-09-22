<template>
  <q-page class="dashboard-page">
    <div class="dashboard-shell">
      <header class="topbar">
        <div class="brand-lockup">
          <div class="brand-badge">
            <q-icon name="local_shipping" />
          </div>
          <div>
            <p class="eyebrow">Panel principal</p>
            <h1>Transportes Merida</h1>
          </div>
        </div>

        <div class="user-actions">
          <div class="user-pill">
            <span>{{ session?.role }}</span>
            <strong>@{{ session?.username }}</strong>
          </div>
          <q-btn
            outline
            no-caps
            color="negative"
            icon="logout"
            label="Cerrar sesion"
            @click="closeSession"
          />
        </div>
      </header>

      <section class="operations-hero">
        <div class="hero-copy">
          <p class="eyebrow">Proyecto de transporte</p>
          <h2>Control visual de camiones, viajes y conductores</h2>
          <p class="hero-text">
            Bienvenido, {{ session?.fullName }}. Esta es la primera vista visual del sistema para presentar el modulo inicial del proyecto.
          </p>

          <div class="hero-actions">
            <q-btn unelevated no-caps icon="add_road" label="Nuevo viaje" class="primary-action" />
            <q-btn flat no-caps icon="map" label="Ver rutas" class="secondary-action" />
          </div>
        </div>

        <div class="hero-visual">
          <img src="/img/truck-hero.png" alt="Camion de carga pesada en ruta" />
          <div class="tracking-card">
            <span>Unidad destacada</span>
            <strong>CBB-2341</strong>
            <p>Cochabamba - Santa Cruz</p>
          </div>
        </div>
      </section>

      <section class="summary-grid">
        <article v-for="item in stats" :key="item.label" class="summary-card" :class="item.tone">
          <q-icon :name="item.icon" />
          <span>{{ item.label }}</span>
          <strong>{{ item.value }}</strong>
        </article>
      </section>

      <section class="workspace-grid">
        <article class="truck-board panel">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">Camiones</p>
              <h3>Estado de camiones</h3>
            </div>
            <q-badge color="green-7" label="Activo" />
          </div>

          <div class="truck-list">
            <div v-for="truck in trucks" :key="truck.plate" class="truck-row">
              <div class="truck-photo" aria-hidden="true"></div>
              <div class="truck-info">
                <div class="truck-title">
                  <strong>{{ truck.plate }}</strong>
                  <span :class="['status-dot', truck.tone]">{{ truck.status }}</span>
                </div>
                <p>{{ truck.driver }}</p>
                <small>{{ truck.route }}</small>
                <q-linear-progress rounded size="6px" :value="truck.progress" :color="truck.color" />
              </div>
            </div>
          </div>
        </article>

        <article class="route-panel panel">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">Rutas</p>
              <h3>Viajes de hoy</h3>
            </div>
            <q-icon name="route" />
          </div>

          <div class="route-map">
            <div class="route-line"></div>
            <div v-for="point in routePoints" :key="point.city" class="route-point" :style="point.style">
              <span></span>
              <strong>{{ point.city }}</strong>
            </div>
          </div>

          <div class="route-summary">
            <div>
              <span>Salida</span>
              <strong>07:30</strong>
            </div>
            <div>
              <span>Carga</span>
              <strong>22 tn</strong>
            </div>
            <div>
              <span>ETA</span>
              <strong>18:40</strong>
            </div>
          </div>
        </article>
      </section>

      <section class="bottom-grid">
        <article class="panel module-panel">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">Modulos</p>
              <h3>Primer alcance visual</h3>
            </div>
          </div>

          <div class="module-grid">
            <div v-for="module in modules" :key="module.label" class="module-card">
              <q-icon :name="module.icon" />
              <span>{{ module.label }}</span>
              <strong>{{ module.value }}</strong>
            </div>
          </div>
        </article>

        <article class="panel activity-panel">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">Actividad</p>
              <h3>Registro reciente</h3>
            </div>
          </div>

          <div class="activity-list">
            <div v-for="event in events" :key="event.title" class="activity-item">
              <q-icon :name="event.icon" />
              <div>
                <strong>{{ event.title }}</strong>
                <span>{{ event.detail }}</span>
              </div>
            </div>
          </div>
        </article>
      </section>
    </div>
  </q-page>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { getSession, logout } from 'src/services/auth'

const router = useRouter()
const session = getSession()

const stats = [
  { label: 'Camiones activos', value: '18', icon: 'local_shipping', tone: 'blue' },
  { label: 'Viajes programados', value: '7', icon: 'route', tone: 'green' },
  { label: 'Conductores', value: '24', icon: 'badge', tone: 'amber' },
  { label: 'Alertas', value: '2', icon: 'warning', tone: 'red' },
]

const trucks = [
  {
    plate: '2341-CBB',
    driver: 'Juan Mamani Condori',
    route: 'Cochabamba - Santa Cruz',
    status: 'En ruta',
    tone: 'moving',
    color: 'blue-7',
    progress: 0.72,
  },
  {
    plate: '5678-CBA',
    driver: 'Maria Flores Herrera',
    route: 'Base Cercado - Sacaba',
    status: 'Cargando',
    tone: 'loading',
    color: 'amber-7',
    progress: 0.38,
  },
  {
    plate: '9012-CBN',
    driver: 'Carlos Quispe Vargas',
    route: 'Cochabamba - La Paz',
    status: 'Mantenimiento',
    tone: 'service',
    color: 'red-7',
    progress: 0.16,
  },
]

const routePoints = [
  { city: 'Cercado', style: { left: '8%', top: '58%' } },
  { city: 'Montero', style: { left: '48%', top: '35%' } },
  { city: 'Santa Cruz', style: { left: '78%', top: '62%' } },
]

const modules = [
  { label: 'Usuarios', value: 'Mock auth', icon: 'manage_accounts' },
  { label: 'Camiones', value: 'Vista inicial', icon: 'local_shipping' },
  { label: 'Rutas', value: 'Plan diario', icon: 'alt_route' },
  { label: 'Reportes', value: 'Pendiente', icon: 'bar_chart' },
]

const events = [
  { title: 'CBB-2341 salio a ruta', detail: 'Salida registrada a las 07:30', icon: 'departure_board' },
  { title: 'Revision preventiva', detail: 'Unidad 9012-CBN en taller', icon: 'build' },
  { title: 'Conductor aprobado', detail: 'Perfil listo para asignacion', icon: 'verified' },
]

function closeSession() {
  logout()
  router.push('/login')
}
</script>

<style scoped>
.dashboard-page {
  min-height: 100vh;
  padding: 24px;
  color: #1f2937;
  font-family: Inter, Roboto, sans-serif;
  background:
    linear-gradient(rgba(248, 250, 252, .96), rgba(248, 250, 252, .96)),
    url('/img/truck-hero.png') center bottom / cover no-repeat,
    #f8fafc;
}

.dashboard-shell {
  max-width: 1180px;
  margin: 0 auto;
}

.topbar,
.operations-hero,
.panel,
.summary-card {
  border: 1px solid #dbeafe;
  border-radius: 8px;
  background: rgba(255, 255, 255, .94);
  box-shadow: 0 1px 3px rgba(15, 23, 42, .06);
}

.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  padding: 16px;
}

.brand-lockup,
.user-actions,
.hero-actions,
.panel-heading,
.truck-title,
.activity-item {
  display: flex;
  align-items: center;
}

.brand-lockup {
  gap: 12px;
}

.brand-badge {
  width: 44px;
  height: 44px;
  display: grid;
  place-items: center;
  border-radius: 8px;
  background: #1d4ed8;
  color: #fff;
}

.brand-badge .q-icon {
  font-size: 25px;
}

.eyebrow {
  margin: 0 0 6px;
  color: #2563eb;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: .12em;
  text-transform: uppercase;
}

h1,
h2,
h3,
p {
  margin: 0;
}

h1 {
  color: #0f172a;
  font-size: 24px;
  font-weight: 900;
}

.user-actions {
  justify-content: flex-end;
  gap: 12px;
}

.user-pill {
  display: grid;
  gap: 2px;
  min-width: 132px;
  padding: 8px 10px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #f8fafc;
}

.user-pill span {
  color: #64748b;
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
}

.user-pill strong {
  color: #111827;
  font-size: 13px;
}

.operations-hero {
  display: grid;
  grid-template-columns: minmax(0, .92fr) minmax(360px, 1.08fr);
  gap: 22px;
  margin-top: 18px;
  padding: 22px;
  overflow: hidden;
}

.hero-copy {
  align-self: center;
  padding: 8px 0;
}

.hero-copy h2 {
  max-width: 540px;
  color: #0f172a;
  font-size: clamp(1.8rem, 3vw, 3rem);
  font-weight: 900;
  line-height: 1.05;
  letter-spacing: 0;
}

.hero-text {
  max-width: 540px;
  margin-top: 14px;
  color: #475569;
  font-size: 15px;
  line-height: 1.6;
}

.hero-actions {
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 22px;
}

.primary-action {
  background: #2563eb;
  color: #fff;
}

.secondary-action {
  color: #1d4ed8;
}

.hero-visual {
  position: relative;
  min-height: 292px;
  overflow: hidden;
  border-radius: 8px;
  background: #0f172a;
}

.hero-visual img {
  width: 100%;
  height: 100%;
  min-height: 292px;
  display: block;
  object-fit: cover;
  object-position: 62% center;
}

.tracking-card {
  position: absolute;
  left: 14px;
  bottom: 14px;
  display: grid;
  gap: 3px;
  min-width: 190px;
  padding: 12px;
  border: 1px solid rgba(219, 234, 254, .38);
  border-radius: 8px;
  background: rgba(15, 23, 42, .76);
  color: #fff;
  backdrop-filter: blur(8px);
}

.tracking-card span {
  color: #bfdbfe;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: .08em;
  text-transform: uppercase;
}

.tracking-card strong {
  font-size: 22px;
}

.tracking-card p {
  color: #e0f2fe;
  font-size: 12px;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
  margin-top: 18px;
}

.summary-card {
  display: grid;
  gap: 7px;
  min-height: 122px;
  padding: 18px;
}

.summary-card .q-icon {
  font-size: 25px;
}

.summary-card span {
  color: #64748b;
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
}

.summary-card strong {
  color: #111827;
  font-size: 28px;
  font-weight: 900;
}

.summary-card.blue .q-icon {
  color: #2563eb;
}

.summary-card.green .q-icon {
  color: #16a34a;
}

.summary-card.amber .q-icon {
  color: #d97706;
}

.summary-card.red .q-icon {
  color: #dc2626;
}

.workspace-grid,
.bottom-grid {
  display: grid;
  gap: 18px;
  margin-top: 18px;
}

.workspace-grid {
  grid-template-columns: minmax(0, 1.08fr) minmax(340px, .92fr);
}

.bottom-grid {
  grid-template-columns: minmax(0, 1fr) minmax(340px, .72fr);
}

.panel {
  padding: 18px;
}

.panel-heading {
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
}

.panel-heading h3 {
  color: #0f172a;
  font-size: 18px;
  font-weight: 900;
}

.panel-heading > .q-icon {
  color: #2563eb;
  font-size: 28px;
}

.truck-list {
  display: grid;
  gap: 12px;
}

.truck-row {
  display: grid;
  grid-template-columns: 96px 1fr;
  gap: 12px;
  padding: 10px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #f8fafc;
}

.truck-photo {
  min-height: 74px;
  border-radius: 6px;
  background: url('/img/truck-hero.png') 66% center / cover no-repeat;
}

.truck-info {
  display: grid;
  align-content: center;
  gap: 7px;
  min-width: 0;
}

.truck-title {
  justify-content: space-between;
  gap: 10px;
}

.truck-title strong {
  color: #0f172a;
  font-size: 16px;
}

.truck-info p {
  color: #475569;
  font-size: 13px;
}

.truck-info small {
  color: #64748b;
  font-size: 12px;
}

.status-dot {
  flex: 0 0 auto;
  padding: 4px 8px;
  border-radius: 999px;
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
}

.status-dot.moving {
  background: #dbeafe;
  color: #1d4ed8;
}

.status-dot.loading {
  background: #fef3c7;
  color: #92400e;
}

.status-dot.service {
  background: #fee2e2;
  color: #b91c1c;
}

.route-map {
  position: relative;
  min-height: 255px;
  overflow: hidden;
  border-radius: 8px;
  background:
    linear-gradient(135deg, rgba(37, 99, 235, .12), rgba(22, 163, 74, .1)),
    repeating-linear-gradient(0deg, transparent 0 42px, rgba(148, 163, 184, .18) 42px 43px),
    repeating-linear-gradient(90deg, transparent 0 42px, rgba(148, 163, 184, .18) 42px 43px),
    #f8fafc;
}

.route-line {
  position: absolute;
  left: 14%;
  right: 15%;
  top: 53%;
  height: 4px;
  border-radius: 999px;
  background: linear-gradient(90deg, #2563eb, #16a34a, #f59e0b);
  transform: rotate(-8deg);
}

.route-point {
  position: absolute;
  display: grid;
  justify-items: center;
  gap: 7px;
  transform: translate(-50%, -50%);
}

.route-point span {
  width: 18px;
  height: 18px;
  display: block;
  border: 4px solid #fff;
  border-radius: 999px;
  background: #2563eb;
  box-shadow: 0 0 0 4px rgba(37, 99, 235, .18);
}

.route-point strong {
  padding: 5px 8px;
  border: 1px solid #dbeafe;
  border-radius: 8px;
  background: #fff;
  color: #0f172a;
  font-size: 11px;
}

.route-summary {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  margin-top: 12px;
}

.route-summary div {
  display: grid;
  gap: 4px;
  padding: 10px;
  border-radius: 8px;
  background: #f8fafc;
}

.route-summary span {
  color: #64748b;
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
}

.route-summary strong {
  color: #0f172a;
  font-size: 16px;
}

.module-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
}

.module-card {
  display: grid;
  gap: 8px;
  min-height: 124px;
  padding: 14px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #f8fafc;
}

.module-card .q-icon {
  color: #2563eb;
  font-size: 24px;
}

.module-card span {
  color: #475569;
  font-size: 12px;
  font-weight: 900;
  text-transform: uppercase;
}

.module-card strong {
  color: #0f172a;
  font-size: 15px;
}

.activity-list {
  display: grid;
  gap: 10px;
}

.activity-item {
  gap: 12px;
  padding: 11px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #f8fafc;
}

.activity-item .q-icon {
  width: 34px;
  height: 34px;
  display: grid;
  place-items: center;
  flex: 0 0 34px;
  border-radius: 8px;
  background: #eff6ff;
  color: #2563eb;
  font-size: 20px;
}

.activity-item div {
  display: grid;
  gap: 3px;
}

.activity-item strong {
  color: #111827;
  font-size: 13px;
}

.activity-item span {
  color: #64748b;
  font-size: 12px;
}

@media (max-width: 960px) {
  .operations-hero,
  .workspace-grid,
  .bottom-grid {
    grid-template-columns: 1fr;
  }

  .summary-grid,
  .module-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .dashboard-page {
    padding: 14px;
  }

  .topbar,
  .user-actions {
    align-items: stretch;
    flex-direction: column;
  }

  .user-actions {
    width: 100%;
  }

  .operations-hero {
    padding: 16px;
  }

  .hero-visual,
  .hero-visual img {
    min-height: 220px;
  }

  .summary-grid,
  .module-grid,
  .route-summary {
    grid-template-columns: 1fr;
  }

  .truck-row {
    grid-template-columns: 1fr;
  }

  .truck-photo {
    min-height: 130px;
  }
}
</style>
