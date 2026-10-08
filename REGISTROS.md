# Registros con Laravel

**Configuración actual:** el proyecto utiliza Apache y MySQL de XAMPP. Sigue [XAMPP.md](XAMPP.md). La opción SQLite y `artisan serve` descrita abajo corresponde al modo de desarrollo anterior; para volver a ella también debes cambiar el proxy de Quasar a `http://127.0.0.1:8000`.

Se conectaron los botones de creación de Usuarios, Camiones, Conductores, Clientes, Cargas, Viajes, Combustible y Mantenimiento. Los formularios validan datos, guardan en la base de datos y actualizan el listado. Los registros se conservan al recargar la página. Editar, eliminar, filtros, reportes y los paneles de resumen quedan para otra etapa.

## Iniciar en Windows

Requisitos: PHP 8.2 o superior, Composer y Node.js 18 o superior. Se usa Laravel 12 para ser compatible con el PHP 8.2 de XAMPP instalado.

En una terminal, desde la carpeta del proyecto:

```powershell
cd backend
composer install
Copy-Item .env.example .env # Solo si .env todavía no existe
php artisan key:generate # Solo al configurar una instalación nueva
php artisan migrate
php artisan transport:admin
php artisan serve --host=127.0.0.1 --port=8000
```

`transport:admin` pide nombre, usuario y contraseña para crear el administrador inicial. No modifica cuentas existentes. El registro público crea cuentas Usuario o Chofer; el administrador crea los otros roles desde Usuarios. Las antiguas cuentas de demostración del navegador ya no se utilizan.

En una segunda terminal, desde la carpeta del proyecto:

```powershell
npm install
npm run dev
```

Abre la dirección que muestra Quasar. Si Quasar ya estaba iniciado, reinícialo para que active el proxy `/api` hacia Laravel. La sesión dura 12 horas.

## Base de datos

Para probar inmediatamente se usa SQLite, en `backend/database/database.sqlite`, sin iniciar MySQL. Laravel también está configurado para MySQL/MariaDB. Para usar MySQL, crea una base vacía y configura en `backend/.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=transportes
DB_USERNAME=root
DB_PASSWORD=
```

Después ejecuta `php artisan config:clear` y `php artisan migrate` desde `backend`. Crearás tablas nuevas en esa base; los datos de SQLite no se transfieren automáticamente.

## Orden recomendado

1. Crear el administrador e iniciar sesión.
2. Registrar clientes y camiones.
3. Registrar conductores y cargas.
4. Registrar viajes con sus referencias.
5. Registrar combustible y mantenimiento.

Las relaciones se validan con claves foráneas. Una carga de un viaje debe pertenecer al cliente seleccionado. Placas, usuarios, licencias, carnets y NIT no se duplican. Las contraseñas se guardan con hash y no aparecen en las respuestas.

## Comprobaciones

```powershell
npm run build
cd backend
php artisan test
```

Las pruebas usan SQLite en memoria y no modifican los registros reales. La definición compartida de los formularios está en `shared/registrations.json`; los endpoints activos están en `backend/routes/api.php`. Conserva `shared` junto a `backend` al desplegar. Para producción, sirve el frontend compilado y dirige `/api` al directorio público de Laravel; el proxy de Quasar solo funciona durante desarrollo.
