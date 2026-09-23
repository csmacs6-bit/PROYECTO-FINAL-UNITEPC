const STORAGE_KEY = 'tm_auth_session'
const USERS_KEY = 'tm_registered_users'

export const demoUsers = [
  {
    fullName: 'Administrador',
    username: 'admin',
    password: 'admin123',
    role: 'Administrador',
    status: 'Activo',
  },
  {
    fullName: 'Operador',
    username: 'operador',
    password: 'op2026',
    role: 'Operador',
    status: 'Activo',
  },
  {
    fullName: 'Chofer',
    username: 'chofer',
    password: 'chofer123',
    role: 'Chofer',
    status: 'Activo',
  },
]

export function getRegisteredUsers() {
  try {
    return JSON.parse(localStorage.getItem(USERS_KEY)) || []
  } catch {
    return []
  }
}

export function getUsers() {
  return [...demoUsers, ...getRegisteredUsers()]
}

export function getSession() {
  try {
    return JSON.parse(localStorage.getItem(STORAGE_KEY))
  } catch {
    return null
  }
}

export function login(username, password) {
  const normalizedUsername = username.trim().toLowerCase()
  const user = getUsers().find((item) => (
    item.username.toLowerCase() === normalizedUsername &&
    item.password === password
  ))

  if (!user) {
    return {
      ok: false,
      message: 'Usuario o contrasena incorrectos.',
    }
  }

  const session = {
    fullName: user.fullName,
    username: user.username,
    role: user.role,
    driver: user.driver || null,
    loggedAt: new Date().toISOString(),
  }

  localStorage.setItem(STORAGE_KEY, JSON.stringify(session))

  return {
    ok: true,
    user: session,
  }
}

export function registerUser(payload) {
  const username = payload.username.trim().toLowerCase()
  const userExists = getUsers().some((user) => user.username.toLowerCase() === username)

  if (userExists) {
    return {
      ok: false,
      message: 'El nombre de usuario ya esta registrado.',
    }
  }

  const newUser = {
    fullName: payload.fullName.trim(),
    username,
    password: payload.password,
    role: payload.role || 'Usuario',
    status: 'Pendiente',
    driver: null,
    registeredAt: new Date().toISOString(),
  }

  const users = getRegisteredUsers()
  users.push(newUser)
  localStorage.setItem(USERS_KEY, JSON.stringify(users))

  return {
    ok: true,
    user: newUser,
  }
}

export function logout() {
  localStorage.removeItem(STORAGE_KEY)
}
