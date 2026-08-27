# PROYECTO-FINAL-UNITEPC
# Sistema de Gestión para Empresa de Transporte de Carga Pesada

## Descripción del proyecto

El presente proyecto consiste en el desarrollo de un sistema de gestión para una empresa dedicada al transporte de carga pesada mediante camiones.

El sistema tiene como finalidad mejorar la organización, control y administración de las principales operaciones de la empresa, centralizando la información relacionada con clientes, camiones, conductores, cargas, viajes, gastos y mantenimiento.

Actualmente, la empresa presenta dificultades para organizar y consultar la información de sus operaciones, debido a que diferentes registros pueden encontrarse dispersos o realizarse de manera manual. Esto dificulta el seguimiento de los viajes, el control de los gastos, la administración de los vehículos y la obtención de información para la toma de decisiones.

Por medio del sistema se busca facilitar la gestión de las actividades de la empresa y mantener un registro organizado de las operaciones realizadas.

---

## Problema

La empresa de transporte de carga pesada presenta dificultades en la organización y control de sus operaciones.

Entre las principales problemáticas identificadas se encuentran:

- Falta de organización de la información de los viajes.
- Dificultad para llevar un control adecuado de los camiones.
- Dificultad para conocer la disponibilidad de los camiones.
- Falta de un registro organizado de los conductores.
- Dificultad para controlar las cargas transportadas.
- Falta de control detallado de los gastos de cada viaje.
- Dificultad para controlar combustible, peajes, viáticos y otros gastos.
- Falta de información organizada sobre los ingresos y fletes.
- Dificultad para consultar el historial de viajes.
- Falta de control sobre los mantenimientos realizados a los camiones.
- Dificultad para conocer los próximos mantenimientos.
- Demora en la búsqueda y consulta de información.
- Dificultad para generar reportes de las operaciones.

---

## Objetivo general

Mejorar la gestión, organización y control de las operaciones de una empresa de transporte de carga pesada mediante el desarrollo de un sistema de gestión que permita centralizar y administrar la información generada por sus diferentes actividades.

---

## Objetivos específicos

- Analizar los procesos actuales relacionados con la gestión y control de las operaciones de la empresa.
- Identificar las principales necesidades y dificultades existentes en el manejo de la información.
- Organizar la información correspondiente a los viajes, camiones y cargas transportadas.
- Mejorar el control de los gastos generados durante cada viaje.
- Facilitar el registro y consulta de los costos, fletes e ingresos.
- Mantener un historial de los viajes realizados por cada camión.
- Mejorar el control de los mantenimientos realizados a los camiones.
- Facilitar la consulta de información histórica.
- Generar reportes de viajes, gastos, ingresos y operaciones.

---

# Funcionalidades del sistema

## Gestión de usuarios

El sistema permitirá administrar los usuarios que tendrán acceso a las diferentes funciones.

- Inicio de sesión.
- Registro de usuarios.
- Modificación de usuarios.
- Gestión de roles y permisos.
- Control de acceso a las funciones del sistema.

## Gestión de clientes

Permitirá administrar la información de los clientes que utilizan los servicios de transporte.

- Registro de clientes.
- Modificación de información.
- Consulta de clientes.
- Historial de servicios realizados.

## Gestión de camiones

Permitirá registrar y controlar los vehículos pertenecientes a la empresa.

- Registro de camiones.
- Información de placa.
- Marca y modelo.
- Tipo de vehículo.
- Capacidad de carga.
- Consulta de información.
- Historial de viajes.
- Historial de mantenimiento.

## Gestión de conductores

Permitirá administrar la información de los conductores de la empresa.

- Registro de conductores.
- Datos personales.
- Número de licencia.
- Categoría de licencia.
- Fecha de vencimiento.
- Consulta de información.
- Historial de viajes realizados.

## Gestión de cargas

Permitirá registrar la información correspondiente a las cargas transportadas.

- Registro de cargas.
- Descripción de la carga.
- Tipo de carga.
- Peso.
- Origen.
- Destino.
- Cliente relacionado.
- Observaciones.

## Gestión de viajes

Este módulo permitirá registrar y controlar los viajes realizados por la empresa.

- Registro de viajes.
- Selección del cliente.
- Registro de la carga.
- Asignación del camión.
- Asignación del conductor.
- Registro del origen y destino.
- Fecha de salida.
- Fecha de llegada.
- Registro del flete.
- Control del estado del viaje.
- Consulta del historial de viajes.

## Gestión de gastos

Permitirá registrar los gastos generados durante cada viaje.

- Gastos de combustible.
- Gastos de peajes.
- Viáticos.
- Reparaciones.
- Otros gastos.
- Cálculo del total de gastos.
- Consulta de gastos por viaje.

## Gestión de mantenimiento

Permitirá llevar un control de los mantenimientos realizados a los camiones.

- Registro de mantenimientos.
- Tipo de mantenimiento.
- Fecha.
- Descripción.
- Kilometraje.
- Costo.
- Próximo mantenimiento.
- Historial de mantenimiento por camión.

## Reportes

El sistema permitirá obtener información organizada de las operaciones de la empresa.

- Reportes de viajes.
- Reportes de gastos.
- Reportes de ingresos.
- Reportes de camiones.
- Reportes de conductores.
- Reportes de clientes.
- Reportes de mantenimiento.
- Reportes por períodos de tiempo.

---

# Tecnologías utilizadas

## Frontend

El frontend será desarrollado utilizando:

- **Quasar Framework**
- **Vue.js**
- **JavaScript**
- **Pinia**
- **Axios**

El frontend será responsable de la interfaz y de la interacción del usuario con las diferentes funciones del sistema.

## Backend

El backend será desarrollado utilizando:

- **Laravel**
- **PHP**
- **API REST**

El backend será responsable de la lógica del sistema, procesamiento de información, validaciones y comunicación con la base de datos.

## Base de datos

Para el almacenamiento de información se utilizará:

- **MySQL / MariaDB**

La base de datos almacenará la información relacionada con usuarios, clientes, camiones, conductores, cargas, viajes, gastos y mantenimientos.

## Herramientas de desarrollo

- Visual Studio Code
- Git
- GitHub
- XAMPP
- Composer
- Node.js
- npm

---

# Arquitectura del proyecto

El sistema estará organizado en tres partes principales:

```text
                 SISTEMA DE TRANSPORTE
                         │
          ┌──────────────┴──────────────┐
          │                             │
      FRONTEND                       BACKEND
      Quasar                         Laravel
      Vue.js                         PHP
          │                             │
          └──────────────┬──────────────┘
                         │
                      API REST
                         │
                         ▼
                    BASE DE DATOS
                    MySQL/MariaDB
