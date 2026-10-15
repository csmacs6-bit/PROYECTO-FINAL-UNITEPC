export const trucks = []

export const drivers = []

export const clients = []

export const trips = []

export const maintenance = []

export const fuelRecords = []

export const cargo = []

export const users = []

export function tripExpenses(trip) {
  return (trip.fuel || 0) + (trip.tolls || 0) + (trip.food || 0) + (trip.others || 0)
}

export function tripProfit(trip) {
  return (trip.freight || 0) - tripExpenses(trip)
}

export function money(value) {
  return `Bs ${Number(value || 0).toLocaleString('es-BO')}`
}
