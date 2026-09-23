<template>
  <article class="kpi-card">
    <div>
      <p>{{ label }}</p>
      <strong>{{ value }}</strong>
      <span v-if="hint">{{ hint }}</span>
    </div>
    <q-icon :name="icon" class="kpi-icon" />
  </article>
</template>

<script setup>
defineProps({
  label: {
    type: String,
    required: true,
  },
  value: {
    type: [String, Number],
    required: true,
  },
  hint: {
    type: String,
    default: '',
  },
  icon: {
    type: String,
    default: 'analytics',
  },
})
</script>

<style scoped>
.kpi-card {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  min-height: 112px;
  padding: 20px;
  border: 1px solid #dbeafe;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
}

p {
  margin: 0 0 8px;
  color: #6b7280;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
}

strong {
  display: block;
  color: #1f2937;
  font-size: 24px;
  line-height: 1.1;
}

span {
  display: block;
  margin-top: 8px;
  color: #6b7280;
  font-size: 12px;
}

.kpi-icon {
  display: grid;
  place-items: center;
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: #eff6ff;
  color: #1d4ed8;
  font-size: 22px;
}
</style>
