import { api } from './api'
const STORAGE_KEY = 'tm_auth_session'
export function getSession() {
  try { const session = JSON.parse(localStorage.getItem(STORAGE_KEY)); return session?.token ? session : null } catch { return null }
}
export async function login(username, password) {
  try {
    const result = await api('/login', { method: 'POST', body: { username, password } })
    const session = { ...result.user, token: result.token, loggedAt: new Date().toISOString() }
    localStorage.setItem(STORAGE_KEY, JSON.stringify(session))
    return { ok: true, user: session }
  } catch (error) { return { ok: false, message: Object.values(error.errors || {}).flat()[0] || error.message } }
}
export async function registerUser(payload) {
  try {
    const result = await api('/register', { method: 'POST', body: { ...payload, password_confirmation: payload.passwordConfirm } })
    return { ok: true, user: result.data }
  } catch (error) { return { ok: false, message: Object.values(error.errors || {}).flat()[0] || error.message } }
}
export function logout() {
  const request = api('/logout', { method: 'POST' })
  localStorage.removeItem(STORAGE_KEY)
  request.catch(() => {})
}
