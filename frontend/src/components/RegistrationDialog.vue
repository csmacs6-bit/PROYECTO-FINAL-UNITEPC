<template>
  <q-dialog v-model="visible" persistent>
    <q-card style="width: 680px; max-width: 95vw">
      <q-card-section class="row items-center">
        <div class="text-h6">{{ title }}</div>
        <q-space />
        <q-btn flat round icon="close" aria-label="Cerrar" :disable="saving" @click="visible = false" />
      </q-card-section>
      <q-form @submit="save">
        <q-card-section class="q-gutter-md" style="max-height: 65vh; overflow-y: auto">
          <q-banner v-if="message" rounded class="bg-red-1 text-negative" role="alert">{{ message }}</q-banner>
          <template v-for="field in fields" :key="field.name">
            <q-select
              v-if="['select', 'relation'].includes(field.type)"
              v-model="form[field.name]" outlined :label="field.label + (field.required && !(record && field.type === 'password') ? ' *' : '')"
              :options="options(field)" :emit-value="field.type === 'relation'" :map-options="field.type === 'relation'"
              :clearable="!field.required" :disable="saving" :rules="rules(field)"
              :error="!!errors[field.name]" :error-message="errors[field.name]?.[0]"
              @update:model-value="clearError(field.name)"
            >
              <template #no-option><q-item><q-item-section>Primero registra {{ field.label.toLowerCase() }} en su módulo.</q-item-section></q-item></template>
            </q-select>
            <q-input
              v-else v-model="form[field.name]" outlined :label="field.label + (field.required && !(record && field.type === 'password') ? ' *' : '')"
              :type="field.type === 'number' ? 'number' : field.type === 'textarea' ? 'textarea' : field.type"
              :min="field.min" :max="field.max" :step="field.integer || field.name === 'year' ? 1 : 'any'"
              :hint="record && field.type === 'password' ? 'Deja vacío para conservar la contraseña actual.' : undefined"
              :disable="saving" :rules="rules(field)" :error="!!errors[field.name]" :error-message="errors[field.name]?.[0]"
              @update:model-value="clearError(field.name)"
            />
          </template>
          <q-input v-if="moduleKey === 'usuarios'" v-model="form.password_confirmation" outlined type="password" label="Confirmar contraseña" :disable="saving" :rules="[value => (record && !form.password) || value === form.password || 'Las contraseñas no coinciden.']" />
        </q-card-section>
        <q-card-actions align="right" class="q-pa-md">
          <q-btn flat no-caps label="Cancelar" :disable="saving" @click="visible = false" />
          <q-btn unelevated no-caps color="primary" label="Guardar registro" type="submit" :loading="saving" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { useQuasar } from 'quasar'
import { schema, records, createRecord, updateRecord } from 'src/services/registrations'

const props = defineProps({ modelValue: Boolean, moduleKey: { type: String, required: true }, title: { type: String, default: 'Nuevo registro' }, record: { type: Object, default: null } })
const emit = defineEmits(['update:modelValue'])
const $q = useQuasar()
const visible = computed({ get: () => props.modelValue, set: (value) => emit('update:modelValue', value) })
const fields = computed(() => schema[props.moduleKey] || [])
const form = reactive({})
const errors = ref({})
const message = ref('')
const saving = ref(false)

watch(() => props.modelValue, (open) => {
  if (!open) return
  for (const name of Object.keys(form)) delete form[name]
  for (const field of fields.value) form[field.name] = field.type === 'password' ? '' : props.record?.[field.name] ?? field.default ?? (field.type === 'relation' ? null : '')
  if (props.moduleKey === 'usuarios') form.password_confirmation = ''
  errors.value = {}
  message.value = ''
})

function options(field) {
  if (field.type === 'select') return field.options
  let rows = records[field.resource]
  if (props.moduleKey === 'viajes' && field.name === 'cargo_id' && form.client_id) rows = rows.filter((row) => row.client_id === form.client_id)
  return rows.map((row) => ({ label: row[field.display], value: row.id }))
}

watch(() => form.client_id, () => {
  if (props.moduleKey === 'viajes' && form.cargo_id && !records.cargas.some(row => row.id === form.cargo_id && row.client_id === form.client_id)) form.cargo_id = null
})

function rules(field) {
  return [(value) => {
    if (value === '' || value === null || value === undefined || (typeof value === 'string' && !value.trim())) return !field.required || (props.record && field.type === 'password') || 'Este campo es obligatorio.'
    if (field.type === 'password' && value.length < 8) return 'Usa al menos 8 caracteres.'
    if (field.type === 'number' && (!Number.isFinite(Number(value)) || Number(value) < (field.min ?? 0))) return `El mínimo es ${field.min ?? 0}.`
    if (field.type === 'number' && field.max && Number(value) > field.max) return `El máximo es ${field.max}.`
    if ((field.integer || field.name === 'year') && !Number.isInteger(Number(value))) return 'Ingresa un número entero.'
    if (field.after && form[field.after] && value < form[field.after]) return 'La fecha debe ser igual o posterior a la fecha inicial.'
    return true
  }]
}

function clearError(name) { delete errors.value[name] }

async function save() {
  if (saving.value) return
  saving.value = true
  errors.value = {}
  message.value = ''
  try {
    const payload = Object.fromEntries(Object.entries(form).map(([key, value]) => [key, typeof value === 'string' && key !== 'password' && key !== 'password_confirmation' ? value.trim() || null : value]))
    if (props.record) await updateRecord(props.moduleKey, props.record.id, payload)
    else await createRecord(props.moduleKey, payload)
    visible.value = false
    $q.notify({ type: 'positive', message: 'Registro guardado correctamente.' })
  } catch (error) {
    errors.value = error.errors || {}
    message.value = Object.keys(errors.value).length ? 'Revisa los campos marcados.' : error.message
  } finally { saving.value = false }
}
</script>
