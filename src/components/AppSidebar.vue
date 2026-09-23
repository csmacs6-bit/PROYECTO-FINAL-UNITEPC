<template>
  <aside class="sidebar" :class="{ open }">
    <div class="sidebar-head">
      <div class="head-row">
        <div>
          <h1>Transportes Merida</h1>
          <p>Sistema de Gestion</p>
        </div>

        <q-btn
          flat
          round
          dense
          icon="close"
          class="mobile-close"
          @click="$emit('close')"
        />
      </div>

      <div class="truck-strip" />
    </div>

    <nav class="nav-list">
      <router-link
        v-for="item in visibleItems"
        :key="item.to"
        :to="item.to"
        class="nav-item"
        active-class="active"
        @click="$emit('close')"
      >
        <q-icon :name="item.icon" />
        <span>{{ item.label }}</span>
        <q-icon v-if="item.adminOnly" name="shield" class="admin-mark" />
        <q-icon name="chevron_right" class="active-chevron" />
      </router-link>
    </nav>

    <footer class="sidebar-foot">
      <div class="user-row">
        <div class="avatar" :class="roleClass">{{ initials }}</div>
        <div class="user-copy">
          <strong>{{ session?.fullName }}</strong>
          <span>{{ session?.role }}</span>
        </div>
      </div>

      <button class="logout-btn" type="button" @click="handleLogout">
        <q-icon name="logout" />
        Cerrar Sesion
      </button>
    </footer>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { getSession, logout } from 'src/services/auth'

defineProps({
  open: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['close'])

const router = useRouter()
const session = getSession()

const items = [
  { label: 'Dashboard', icon: 'dashboard', to: '/dashboard' },
  { label: 'Viajes', icon: 'route', to: '/viajes' },
  { label: 'Camiones', icon: 'local_shipping', to: '/camiones' },
  { label: 'Conductores', icon: 'engineering', to: '/conductores' },
  { label: 'Clientes', icon: 'domain', to: '/clientes' },
  { label: 'Cargas', icon: 'inventory_2', to: '/cargas' },
  { label: 'Combustible', icon: 'local_gas_station', to: '/combustible' },
  { label: 'Mantenimiento', icon: 'build', to: '/mantenimiento' },
  { label: 'GPS en Vivo', icon: 'location_on', to: '/gps' },
  { label: 'Reportes', icon: 'bar_chart', to: '/reportes' },
  { label: 'Usuarios', icon: 'groups', to: '/usuarios', adminOnly: true },
]

const visibleItems = computed(() => {
  if (session?.role === 'Chofer') {
    return [
      { label: 'Mi Panel', icon: 'dashboard', to: '/chofer/dashboard' },
    ]
  }

  if (session?.role !== 'Administrador') {
    return items.filter((item) => !item.adminOnly)
  }

  return items
})

const initials = computed(() => {
  const name = session?.fullName || 'Usuario'
  return name
    .split(' ')
    .slice(0, 2)
    .map((part) => part.charAt(0))
    .join('')
    .toUpperCase()
})

const roleClass = computed(() => {
  if (session?.role === 'Administrador') return 'admin'
  if (session?.role === 'Chofer') return 'driver'
  return 'operator'
})

function handleLogout() {
  logout()
  router.push('/login')
}
</script>

<style scoped>
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 40;
  display: flex;
  flex-direction: column;
  width: 240px;
  height: 100vh;
  border-right: 1px solid #dbeafe;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
}

.sidebar-head {
  padding: 20px 20px 12px;
  background:
    radial-gradient(circle at 8px 8px, rgba(255,255,255,.12) 2px, transparent 2px) 0 0 / 18px 18px,
    #1e3a8a;
}

.head-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 10px;
}

h1 {
  margin: 0;
  color: #fff;
  font-size: 14px;
  font-weight: 800;
}

p {
  margin: 3px 0 0;
  color: #93c5fd;
  font-size: 11px;
}

.truck-strip {
  position: relative;
  height: 56px;
  margin-top: 14px;
  overflow: hidden;
  border-radius: 10px;
  background:
    linear-gradient(rgba(30, 58, 138, .25), rgba(30, 58, 138, .25)),
    url('/img/truck.jpg') center / cover no-repeat,
    url('/img/truck-hero.png') center / cover no-repeat;
}

.mobile-close {
  display: none;
  color: #93c5fd;
}

.nav-list {
  display: grid;
  gap: 2px;
  padding: 16px 12px;
  overflow-y: auto;
}

.nav-item {
  display: grid;
  grid-template-columns: 20px 1fr auto auto;
  align-items: center;
  gap: 10px;
  min-height: 40px;
  padding: 10px 12px;
  border-radius: 8px;
  color: #6b7280;
  font-size: 13px;
  text-decoration: none;
}

.nav-item:hover {
  background: #f3f4f6;
}

.nav-item.active {
  background: #dbeafe;
  color: #1e40af;
  font-weight: 800;
}

.admin-mark {
  color: #1d4ed8;
  font-size: 15px;
}

.active-chevron {
  display: none;
  color: #1e40af;
}

.active .active-chevron {
  display: inline-flex;
}

.sidebar-foot {
  margin-top: auto;
  padding: 16px;
  border-top: 1px solid #dbeafe;
}

.user-row {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

.avatar {
  display: grid;
  place-items: center;
  width: 32px;
  height: 32px;
  flex: 0 0 32px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 900;
}

.avatar.admin {
  background: #dbeafe;
  color: #1d4ed8;
}

.avatar.driver {
  background: #fef3c7;
  color: #92400e;
}

.avatar.operator {
  background: #f1f5f9;
  color: #475569;
}

.user-copy {
  min-width: 0;
}

.user-copy strong,
.user-copy span {
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.user-copy strong {
  color: #1f2937;
  font-size: 12px;
}

.user-copy span {
  color: #9ca3af;
  font-size: 11px;
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  margin-top: 12px;
  padding: 9px 10px;
  border: 0;
  border-radius: 8px;
  background: transparent;
  color: #6b7280;
  cursor: pointer;
  font: inherit;
  font-size: 12px;
  font-weight: 700;
}

.logout-btn:hover {
  background: #fef2f2;
  color: #ef4444;
}

@media (max-width: 1023px) {
  .sidebar {
    transform: translateX(-100%);
    transition: transform .2s ease;
  }

  .sidebar.open {
    transform: translateX(0);
  }

  .mobile-close {
    display: inline-flex;
  }
}
</style>
