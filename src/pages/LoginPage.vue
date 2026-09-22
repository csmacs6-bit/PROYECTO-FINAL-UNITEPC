<template>
  <q-page class="auth-page login-page">
    <auth-brand-panel
      title="servicio de transportes merida"
      description="Servicio de transporte nacional"
    />

    <section class="form-panel">
      <div class="auth-card-wrap">
        <h1 class="page-title">Iniciar sesion</h1>
        <p class="page-subtitle">Ingresa con tus credenciales para acceder al sistema.</p>

        <q-card flat class="auth-card">
          <q-card-section class="q-pa-none">
            <q-form class="field-grid" @submit.prevent="submitLogin">
              <q-input
                v-model.trim="form.username"
                outlined
                label="Usuario"
                placeholder="admin"
                class="tm-input"
                :error="submitted && !form.username"
                error-message="Ingresa tu usuario."
              >
                <template #prepend>
                  <q-icon name="person" />
                </template>
              </q-input>

              <q-input
                v-model="form.password"
                outlined
                label="Contrasena"
                placeholder="Ingresa tu contrasena"
                :type="showPassword ? 'text' : 'password'"
                class="tm-input"
                :error="submitted && !form.password"
                error-message="Ingresa tu contrasena."
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

              <q-banner v-if="errorMessage" rounded class="error-banner">
                {{ errorMessage }}
              </q-banner>

              <q-btn
                unelevated
                no-caps
                icon-right="login"
                label="Ingresar al sistema"
                class="submit-btn"
                type="submit"
              />
            </q-form>
          </q-card-section>
        </q-card>

        <p class="register-link">
          Eres conductor?
          <router-link to="/register">Registrarse</router-link>
        </p>
      </div>
    </section>
  </q-page>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AuthBrandPanel from 'components/AuthBrandPanel.vue'
import { login } from 'src/services/auth'

const router = useRouter()
const route = useRoute()

const showPassword = ref(false)
const submitted = ref(false)
const errorMessage = ref('')

const form = reactive({
  username: route.query.registered?.toString() || '',
  password: '',
})

function submitLogin() {
  submitted.value = true
  errorMessage.value = ''

  if (!form.username || !form.password) {
    errorMessage.value = 'Completa usuario y contrasena para continuar.'
    return
  }

  const result = login(form.username, form.password)

  if (!result.ok) {
    errorMessage.value = result.message
    return
  }

  router.push('/dashboard')
}
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 3fr 2fr;
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
  width: min(100%, 420px);
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

.register-link {
  margin: 16px 0 0;
  color: #9ca3af;
  font-size: 12px;
  text-align: center;
}

.register-link a {
  color: #2563eb;
  font-weight: 800;
  text-decoration: none;
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
