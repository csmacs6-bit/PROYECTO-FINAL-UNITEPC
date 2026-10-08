# Iniciar con XAMPP

El backend utiliza Apache y MySQL/MariaDB de XAMPP. En este equipo MySQL utiliza el puerto **3303**. El frontend Quasar utiliza Node.js.

La configuración de Apache apunta a `backend/public` mediante un alias local, por lo que puedes conservar el proyecto en el escritorio. El instalador conserva los otros ajustes de Apache y verifica su sintaxis.

## Primera vez

1. La configuración de Apache se instala desde la carpeta principal con `powershell -ExecutionPolicy Bypass -File scripts/install-xampp.ps1`. Si ya fue instalada, no necesitas repetirlo. Si falta permiso de escritura en XAMPP, ejecuta este comando desde una terminal como administrador.
2. Abre el panel de XAMPP y pulsa **Start** en **Apache** y **MySQL**. Si Apache ya estaba iniciado durante la configuración, pulsa **Stop** y luego **Start**.
3. En una terminal de VS Code, desde la carpeta principal:

```powershell
cd backend
php artisan config:clear
php artisan transport:database
php artisan migrate
php artisan transport:admin
```

`transport:database` crea `transportes` si no existe, sin borrar otra base ni sus datos. `transport:admin` pide tus credenciales y se ejecuta solo al crear el administrador inicial. Los registros anteriores de SQLite permanecen en su archivo; no se trasladan a MySQL automáticamente.

4. En una segunda terminal, desde la carpeta principal:

```powershell
npm run dev
```

Abre la dirección que muestra Quasar e inicia sesión. Reinicia Quasar si ya estaba ejecutándose cuando se cambió la configuración.

## En las siguientes sesiones

Inicia **Apache** y **MySQL** en XAMPP y ejecuta `npm run dev` desde la carpeta principal. No hace falta `php artisan serve`.

## Verificar

Abre `http://localhost/transportes/up`: debe responder correctamente cuando Apache y Laravel estén disponibles. La interfaz se abre en la dirección de Quasar, no en esa dirección del backend. Puedes ver las tablas en `http://localhost/phpmyadmin`, dentro de `transportes`.

Si falla la conexión a MySQL, verifica que esté iniciado y que `backend/.env` coincida con su puerto y credenciales. Aquí está configurado con `127.0.0.1:3303`, usuario `root` y contraseña vacía, según la configuración local de XAMPP.
