# Proyecto de Gestion Unificado

Aplicacion web desarrollada con Laravel para administrar eventos, usuarios e inscripciones desde una experiencia separada por contexto:

- `admin`: backoffice para gestion operativa
- `portal`: autoservicio para usuarios autenticados

El proyecto esta construido sobre Blade, Alpine.js y Tailwind CSS, con autenticacion basada en Laravel Breeze y una capa de autorizacion centralizada por capacidades.

## Caracteristicas principales

- Dashboard administrativo con metricas de usuarios, eventos e inscripciones
- Portal de usuario con resumen de inscripciones y proximos eventos
- CRUD de eventos en el area administrativa
- Inscripciones, cancelaciones y lista de espera
- Navegacion separada entre `admin` y `portal`
- Layout Blade reutilizable con sidebar, topbar y breadcrumbs
- Autorizacion por capacidades centralizadas

## Stack tecnico

- PHP 8.3+
- Laravel 13
- Blade
- Alpine.js
- Tailwind CSS
- Vite
- MySQL
- Pest

## Arquitectura funcional

### Areas de la aplicacion

#### Admin

Espacio orientado a gestion interna.

Rutas principales:

- `/admin/dashboard`
- `/admin/events`
- `/admin/events/create`
- `/admin/events/{event}`
- `/admin/events/{event}/edit`

#### Portal

Espacio orientado al usuario final.

Rutas principales:

- `/portal/dashboard`
- `/portal/profile`
- `/portal/events`
- `/portal/events/mis-eventos`
- `/portal/events/{event}`

### Regla de separacion

- Todo lo que sea gestion interna debe vivir en `admin.*`
- Todo lo que sea autoservicio o accion personal debe vivir en `portal.*`

## Roles y autorizacion

El sistema usa roles persistidos en base de datos y capacidades definidas en codigo.

Roles base:

- `admin`
- `coordinator`
- `user`

La fuente de verdad de permisos vive en:

- [app/Auth/Capability.php](app/Auth/Capability.php)
- [app/Auth/RoleCapabilities.php](app/Auth/RoleCapabilities.php)

Ejemplo de enfoque:

- `admin` accede a backoffice y tiene control total sobre eventos
- `coordinator` accede a backoffice y gestiona eventos e inscripciones
- `user` navega el portal y gestiona sus propias inscripciones

Las policies y middleware consumen capacidades a traves de `User::hasCapability()` y `User::canAccessBackoffice()`.

## Modulo de eventos

El proyecto gira alrededor de `Event` y `EventRegistration`.

Estados canonicos del evento:

- `activo`
- `abierto`
- `cerrado`
- `cancelado`

Notas relevantes:

- Los estados legacy se normalizan con `App\Enums\EventStatus`
- Solo eventos `activo` y `abierto` aceptan inscripciones
- Si no hay cupos disponibles, la inscripcion entra en `waitlist`
- Las cancelaciones pueden promover automaticamente al primer usuario en lista de espera

## Estandar de interfaz

Las vistas autenticadas siguen un patron comun:

- `x-app-layout`
- sidebar y topbar reutilizables
- `x-page-header`
- `x-breadcrumbs`
- `x-panel`

Esto evita que cada modulo tenga una navegacion o presentacion distinta.

## Requisitos

- PHP 8.3 o superior
- Composer
- Node.js 20+ recomendado
- npm
- MySQL

## Instalacion

1. Clonar el repositorio
2. Instalar dependencias
3. Configurar entorno
4. Ejecutar migraciones
5. Compilar frontend

Comando rapido:

```bash
composer run setup
```

Este script ejecuta:

- `composer install`
- copia de `.env` si no existe
- `php artisan key:generate`
- `php artisan migrate --force`
- `npm install --ignore-scripts`
- `npm run build`

## Configuracion local

El proyecto usa MySQL por defecto.

Variables relevantes en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion-app
DB_USERNAME=root
DB_PASSWORD=root
```

## Ejecucion en desarrollo

```bash
composer run dev
```

Este comando levanta en paralelo:

- servidor Laravel
- listener de cola
- Vite en modo desarrollo

Tambien puedes ejecutar servicios por separado si lo prefieres.

## Seeds y accesos iniciales

Seeder principal:

```bash
php artisan db:seed
```

Usuarios de referencia creados por `UserSeeder`:

| Rol | Email | Password | Estado |
|---|---|---|---|
| Admin | `admin@example.com` | `password` | Activo |
| Coordinator | `coordinacion@example.com` | `password` | Activo |
| User | `usuario@example.com` | `password` | Activo |
| User | `inactivo@example.com` | `password` | Inactivo |

## Pruebas

Suite completa:

```bash
composer test
```

Ejecucion directa:

```bash
php artisan test
```

Pruebas utiles por modulo:

```bash
php artisan test --filter=DashboardTest
php artisan test --filter=ProfileTest
php artisan test --filter=EventAuthorizationTest
php artisan test --filter=EventRegistrationTest
php artisan test --filter=EventValidationTest
php artisan test --filter=UserEventsTest
php artisan test --filter=CapabilityAuthorizationTest
```

Nota: en este entorno puede aparecer una advertencia de Pest al intentar escribir cache en `vendor/pestphp/pest/.temp/test-results`. Si los tests pasan, esa advertencia no invalida el resultado.

## Estructura relevante

```text
app/
  Auth/                   # Capacidades y mapa de permisos por rol
  Http/
    Controllers/          # Dashboards, eventos, perfil e inscripciones
    Middleware/           # Restricciones por rol activo y acceso backoffice
    Requests/             # Validaciones de formularios
  Models/                 # User, Role, Event, EventRegistration
  Policies/               # Reglas de autorizacion por recurso
database/
  migrations/             # Esquema de base de datos
  seeders/                # Roles, usuarios, eventos e inscripciones
resources/
  views/
    components/           # Componentes Blade reutilizables
    layouts/              # Shell autenticado del panel
    events/               # Vistas del modulo de eventos
    profile/              # Vistas del perfil
routes/
  web.php                 # Definicion de rutas admin y portal
tests/
  Feature/                # Pruebas funcionales principales
```

## Convenciones del proyecto

- Mantener textos de interfaz en espanol
- Reutilizar componentes Blade antes de duplicar markup
- No mezclar nuevas funcionalidades autenticadas fuera de `admin.*` o `portal.*`
- Si cambia la autorizacion, ajustar primero la capa de capacidades
- Si cambia el flujo de eventos, revisar policies, requests y tests del modulo

## Documentacion interna

Para contexto operativo del repositorio y estandares de trabajo del proyecto, revisar:

- [AGENTS.md](AGENTS.md)

## Estado actual

Base funcional implementada:

- autenticacion
- roles base
- dashboards
- modulo de eventos
- inscripciones
- menu administrativo y portal
- breadcrumbs
- autorizacion centralizada por capacidades

## Licencia

Proyecto de uso interno. Definir licencia publica antes de distribucion externa.
