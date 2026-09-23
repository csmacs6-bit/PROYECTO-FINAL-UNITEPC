<template>
  <q-layout view="hHh lpR fFf" class="main-layout">
    <truck-watermark />

    <header class="mobile-topbar">
      <q-btn flat round dense icon="menu" @click="sidebarOpen = true" />
      <strong>Transportes Merida</strong>
      <span />
    </header>

    <app-sidebar :open="sidebarOpen" @close="sidebarOpen = false" />

    <div
      v-if="sidebarOpen"
      class="mobile-overlay"
      @click="sidebarOpen = false"
    />

    <q-page-container class="page-container">
      <main class="content-shell">
        <router-view />
      </main>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref } from 'vue'
import AppSidebar from 'components/AppSidebar.vue'
import TruckWatermark from 'components/TruckWatermark.vue'

const sidebarOpen = ref(false)
</script>

<style scoped>
.main-layout {
  min-height: 100vh;
  background: #f9fafb;
}

.page-container {
  position: relative;
  z-index: 1;
  padding-left: 240px;
}

.content-shell {
  width: min(100%, 1280px);
  min-height: 100vh;
  margin: 0 auto;
  padding: 32px;
}

.mobile-topbar {
  position: sticky;
  top: 0;
  z-index: 30;
  display: none;
  grid-template-columns: 40px 1fr 40px;
  align-items: center;
  height: 56px;
  border-bottom: 1px solid #dbeafe;
  background: #fff;
}

.mobile-topbar strong {
  color: #1f2937;
  font-size: 14px;
  text-align: center;
}

.mobile-overlay {
  position: fixed;
  inset: 0;
  z-index: 35;
  background: rgba(0, 0, 0, .3);
}

@media (max-width: 1023px) {
  .mobile-topbar {
    display: grid;
  }

  .page-container {
    padding-left: 0;
  }

  .content-shell {
    padding: 16px;
  }
}
</style>
