# Módulo de Viajes

## Demostración para la entrega

1. Inicia sesión como administrador u operador.
2. Registra un cliente, un camión con capacidad suficiente, un conductor activo con licencia vigente y una carga del cliente.
3. En Viajes, pulsa Nuevo Viaje y selecciona el cliente y su carga. Al crear el viaje, se copian origen, destino, salida, llegada y flete de la carga; puedes revisarlos antes de guardar.
4. Selecciona camión y conductor, revisa los campos y guarda como Programado.
5. Comprueba la tarjeta: carga, cliente, camión, conductor, ruta, salida, llegada, flete y estado.
6. Usa Editar para cambiar a En transito. Para completar, registra una llegada igual o posterior a la salida y selecciona Completado. También puedes usar Completar viaje; si falta la llegada, se abre el formulario.
7. Usa los filtros para buscar por viaje, estado, camión, conductor, cliente o mes.

## Validaciones

- Las cuatro entidades deben existir y la carga debe pertenecer al cliente.
- El peso de la carga no puede superar la capacidad del camión.
- Para viajes programados o en tránsito, el camión no puede estar en mantenimiento; el conductor debe estar activo y su licencia debe cubrir la fecha de salida.
- El flete no puede ser negativo. La llegada no puede ser anterior a la salida y es obligatoria para completar.
- Las validaciones se aplican al crear y editar desde la API. La interfaz muestra los errores en los campos correspondientes.
- La llegada es opcional mientras el viaje está abierto. El estado se sigue mediante el registro actual y su edición; no se incluye un historial de cambios de estado.

## Verificación técnica

Desde backend: `php artisan test` (base SQLite en memoria, sin modificar la base de trabajo).

Desde frontend: `npm run build`.

Desde la raíz: `git ls-files '*node_modules*'` debe quedar sin resultados. Las dependencias locales se conservan; para instalarlas en otra computadora, ejecuta `npm ci` desde frontend y `composer install` desde backend.

La corrección de Git elimina node_modules de los commits nuevos. Los commits anteriores conservan su contenido histórico.
