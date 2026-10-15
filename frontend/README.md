# Frontend del sistema de transportes

Esta carpeta contiene los avances de la interfaz: inicio de sesión, paneles, camiones, conductores, clientes, cargas, viajes, fletes, combustible, mantenimiento, GPS y reportes.

## Iniciar

Desde la raíz del repositorio:

```powershell
cd frontend
npm install
npm run dev
```

Para compilar: `npm run build`. El resultado se genera en `frontend/dist/spa`.

El código Vue está en `src`, las imágenes en `public` y la configuración en `quasar.config.js`. `resources` y `routes` conservan las vistas y rutas de la primera interfaz Laravel.

Los formularios utilizan `../shared/registrations.json`, compartido con el servidor. El backend se conserva en `../backend`; las funciones de autenticación y registros siguen utilizando su API. Mover los archivos no elimina esa conexión.
