<template>
  <q-page class="auth-page register-page">
    <section class="form-panel">
      <div class="auth-card-wrap">
        <q-btn
          flat
          dense
          no-caps
          icon="arrow_back"
          label="Volver al inicio de sesion"
          class="back-link"
          to="/login"
        />

        <h2 class="page-title">Crear cuenta</h2>
        <p class="page-subtitle">Completa tus datos y selecciona el rol para acceder luego desde login.</p>

        <q-card flat class="auth-card">
          <q-card-section class="q-pa-none">
            <q-form class="field-grid" @submit.prevent="submitRegister">
              <q-input
                v-model.trim="form.fullName"
                outlined
                label="Nombre completo"
                placeholder="Ej. Juan Mamani Condori"
                class="tm-input"
                :error="submitted && !form.fullName"
                error-message="Ingresa tu nombre completo."
              >
                <template #prepend>
                  <q-icon name="person" />
                </template>
              </q-input>

              <q-input
                v-model.trim="form.username"
                outlined
                label="Nombre de usuario"
                placeholder="jmamani"
                class="tm-input"
                :error="submitted && !form.username"
                error-message="Ingresa un nombre de usuario."
              >
                <template #prepend>
                  <span class="at-prefix">@</span>
                </template>
              </q-input>

              <q-select
                v-model="form.role"
                outlined
                label="Rol de acceso"
                :options="roleOptions"
                class="tm-input"
                :error="submitted && !form.role"
                error-message="Selecciona administrador, usuario o chofer."
              >
                <template #prepend>
                  <q-icon name="admin_panel_settings" />
                </template>
              </q-select>

              <q-input
                v-model="form.password"
                outlined
                label="Contrasena"
                placeholder="Minimo 8 caracteres"
                :type="showPassword ? 'text' : 'password'"
                class="tm-input"
                :error="submitted && !isValidPassword"
                error-message="La contrasena debe tener al menos 8 caracteres."
              >
                <template #prepend>
                  <q-icon name="lock" />
                </template>
                <template #append>
                  <q-btn
                    flat
                    round
                    dense
                    :icon="showPassword ? 'visibility_off' : 'visibility'"
                    @click="showPassword = !showPassword"
                  />
                </template>
              </q-input>

              <q-input
                v-model="form.passwordConfirm"
                outlined
                label="Confirmar contrasena"
                placeholder="Repite tu contrasena"
                :type="showConfirm ? 'text' : 'password'"
                class="tm-input"
                :error="submitted && !passwordsMatch"
                error-message="Las contrasenas no coinciden."
              >
                <template #prepend>
                  <q-icon name="lock" />
                </template>
                <template #append>
                  <q-btn
                    flat
                    round
                    dense
                    :icon="showConfirm ? 'visibility_off' : 'visibility'"
                    @click="showConfirm = !showConfirm"
                  />
                </template>
              </q-input>

              <q-banner v-if="errorMessage" rounded class="error-banner">
                {{ errorMessage }}
              </q-banner>

              <q-btn
                unelevated
                no-caps
                icon-right="arrow_forward"
                label="Crear mi cuenta"
                class="submit-btn"
                type="submit"
              />
            </q-form>

          </q-card-section>
        </q-card>
      </div>
    </section>
  </q-page>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { registerUser } from 'src/services/auth'

const router = useRouter()
const $q = useQuasar()

const showPassword = ref(false)
const showConfirm = ref(false)
const submitted = ref(false)
const errorMessage = ref('')

const form = reactive({
  fullName: '',
  username: '',
  role: null,
  password: '',
  passwordConfirm: '',
})

const roleOptions = [
  'Administrador',
  'Usuario',
  'Chofer',
]

const isValidPassword = computed(() => form.password.length >= 8)
const passwordsMatch = computed(() => form.password && form.password === form.passwordConfirm)
const isValidForm = computed(() => (
  form.fullName &&
  form.username &&
  form.role &&
  isValidPassword.value &&
  passwordsMatch.value
))

function submitRegister() {
  submitted.value = true
  errorMessage.value = ''

  if (!isValidForm.value) {
    errorMessage.value = 'Revisa los datos marcados antes de continuar.'
    return
  }

  const result = registerUser(form)

  if (!result.ok) {
    errorMessage.value = result.message
    return
  }

  $q.notify({
    type: 'positive',
    message: 'Cuenta creada correctamente. Ahora puedes iniciar sesion.',
  })

  router.push({
    path: '/login',
    query: { registered: form.username },
  })
}
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1fr;
  background: #f9fafb;
  color: #1f2937;
  font-family: Inter, Roboto, sans-serif;
}

.form-panel {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 36px 24px;
  overflow: hidden;
}

.form-panel::after {
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  height: 220px;
  opacity: .05;
  background:
    url('/img/truck-hero.png') center bottom / cover no-repeat,
    linear-gradient(90deg, transparent, #1e3a8a 45%, transparent);
  pointer-events: none;
}

.auth-card-wrap {
  position: relative;
  z-index: 1;
  width: min(100%, 520px);
}

.back-link {
  margin-bottom: 22px;
  color: #9ca3af;
  font-size: 12px;
  font-weight: 600;
}

.page-title {
  margin: 0;
  color: #111827;
  font-size: 24px;
  font-weight: 800;
  letter-spacing: 0;
}

.page-subtitle {
  margin: 8px 0 22px;
  color: #6b7280;
  font-size: 14px;
  line-height: 1.5;
}

.auth-card {
  padding: 30px;
  border: 1px solid #dbeafe;
  border-radius: 8px;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
}

.field-grid {
  display: grid;
  gap: 18px;
}

.tm-input :deep(.q-field__control) {
  min-height: 46px;
  border-radius: 8px;
}

.tm-input :deep(.q-field__label) {
  color: #4b5563;
  font-size: 12px;
  font-weight: 700;
}

.at-prefix {
  color: #94a3b8;
  font-size: 14px;
  font-weight: 700;
}

.error-banner {
  border: 1px solid #fecaca;
  background: #fef2f2;
  color: #dc2626;
  font-size: 13px;
}

.submit-btn {
  height: 46px;
  border-radius: 8px;
  background: #2563eb;
  color: #fff;
  font-size: 14px;
  font-weight: 800;
  box-shadow: 0 10px 24px rgba(37, 99, 235, .2);
}

.submit-btn:hover {
  background: #1d4ed8;
}

@media (max-width: 880px) {
  .auth-page {
    grid-template-columns: 1fr;
  }

  .form-panel {
    min-height: auto;
    padding: 28px 16px 38px;
  }

  .auth-card {
    padding: 22px;
  }
}
</style>
