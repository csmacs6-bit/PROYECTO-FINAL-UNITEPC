<template>
  <span class="status-badge" :class="tone">
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: {
    type: String,
    required: true,
  },
})

const tone = computed(() => {
  const value = props.label.toLowerCase()

  if (value.includes('activo') || value.includes('disponible') || value.includes('completado') || value.includes('entregado')) {
    return 'success'
  }

  if (value.includes('transito') || value.includes('progreso')) {
    return 'primary'
  }

  if (value.includes('pendiente') || value.includes('programado') || value.includes('permiso')) {
    return 'warning'
  }

  if (value.includes('mantenimiento') || value.includes('inactivo') || value.includes('correctivo')) {
    return 'danger'
  }

  return 'neutral'
})
</script>

<style scoped>
.status-badge {
  display: inline-flex;
  align-items: center;
  width: fit-content;
  min-height: 24px;
  padding: 4px 9px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 800;
  line-height: 1;
}

.success {
  background: #d1fae5;
  color: #059669;
}

.primary {
  background: #dbeafe;
  color: #1d4ed8;
}

.warning {
  background: #fef3c7;
  color: #92400e;
}

.danger {
  background: #fee2e2;
  color: #ef4444;
}

.neutral {
  background: #f1f5f9;
  color: #475569;
}
</style>
