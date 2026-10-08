import { reactive } from 'vue'
import schema from '../../shared/registrations.json'
import { api } from './api'

export { schema }
export const records = reactive(Object.fromEntries(Object.keys(schema).map((key) => [key, []])))

export async function loadRecords(key) {
  const keys = key === 'usuarios' ? ['usuarios'] : Object.keys(schema).filter((name) => name !== 'usuarios')
  const results = await Promise.all(keys.map(async (name) => [name, (await api(`/${name}`)).data]))
  for (const [name, rows] of results) records[name] = rows
}

export async function createRecord(key, payload) {
  const result = await api(`/${key}`, { method: 'POST', body: payload })
  records[key].unshift(result.data)
  return result.data
}

export function moduleData() {
  const find = (key, id, field) => records[key].find((row) => row.id === Number(id))?.[field] || 'Sin asignar'
  return {
    trucks: records.camiones.map((row) => ({ ...row, driver: records.conductores.find((driver) => driver.truck_id === row.id)?.name || 'Sin asignar', gps: row.gps || 'Sin GPS' })),
    drivers: records.conductores.map((row) => ({ ...row, truck: find('camiones', row.truck_id, 'plate') })),
    clients: records.clientes,
    cargo: records.cargas.map((row) => ({ ...row, weight: `${row.weight} t`, dates: `${row.departureDate} / ${row.arrivalDate || 'Pendiente'}` })),
    trips: records.viajes.map((row) => ({ ...row, freight: Number(row.freight), truck: find('camiones', row.truck_id, 'plate'), driver: find('conductores', row.driver_id, 'name'), client: find('clientes', row.client_id, 'name') })),
    fuelRecords: records.combustible.map((row) => ({ ...row, liters: Number(row.liters), price: Number(row.price), truck: find('camiones', row.truck_id, 'plate') })),
    maintenance: records.mantenimiento.map((row) => ({ ...row, cost: Number(row.cost), truck: find('camiones', row.truck_id, 'plate'), nextDate: row.nextDate || 'Sin programar' })),
    users: records.usuarios.map((row) => ({ ...row, registeredAt: row.created_at?.slice(0, 10) })),
  }
}
