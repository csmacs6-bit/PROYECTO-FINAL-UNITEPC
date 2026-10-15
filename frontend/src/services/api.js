export async function api(path, options = {}) {
  let session
  try { session = JSON.parse(localStorage.getItem('tm_auth_session')) } catch { session = null }
  let response
  try {
    response = await fetch(`/api${path}`, {
      ...options,
      headers: { Accept: 'application/json', 'Content-Type': 'application/json', ...(session?.token ? { Authorization: `Bearer ${session.token}` } : {}), ...options.headers },
      ...(options.body ? { body: JSON.stringify(options.body) } : {}),
    })
  } catch { throw new Error('No se pudo conectar con Laravel. Comprueba que el backend esté iniciado.') }
  if (response.status === 204) return null
  const data = await response.json().catch(() => null)
  if (!response.ok || !data) {
    if (response.status === 401) {
      localStorage.removeItem('tm_auth_session')
      if (window.location.pathname !== '/login') window.location.assign('/login')
    }
    const error = new Error(data?.message || 'No se pudo completar la solicitud al backend.')
    error.errors = data?.errors || {}
    error.status = response.status
    throw error
  }
  return data
}
