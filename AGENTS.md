# AGENTS.md

## Objetivo

Este repositorio es una aplicacion Laravel 13 para gestion unificada de usuarios y eventos.
El objetivo de este documento es dar a cualquier agente el contexto minimo necesario para trabajar bien, mantener consistencia y evitar regresiones funcionales o visuales.

## Stack actual

- Backend: Laravel 13, PHP 8.3+
- Frontend: Blade + Alpine.js + Tailwind CSS + Vite
- Testing: Pest
- Base de datos objetivo: MySQL
- Auth UI base: Laravel Breeze

## Comandos utiles

### Instalacion inicial

```bash
composer run setup
```

### Desarrollo local

```bash
composer run dev
```

Esto levanta:

- `php artisan serve`
- `php artisan queue:listen --tries=1`
- `npm run dev`

### Build frontend

```bash
npm run build
```

### Tests

```bash
composer test
php artisan test
php artisan test --filter=NombreDelTest
```

Nota: en este entorno puede aparecer una advertencia de Pest intentando escribir cache en `vendor/pestphp/pest/.temp/test-results`. Esa advertencia no invalida el resultado de los tests si estos pasan.

## Estructura importante

- `routes/web.php`: separacion de rutas entre `admin` y `portal`
- `app/Http/Controllers/`: logica de dashboard, eventos, perfil e inscripciones
- `app/Models/`: `User`, `Event`, `EventRegistration`, `Role`
- `app/Auth/`: mapa central de capacidades (`Capability`, `RoleCapabilities`)
- `app/Policies/`: reglas de autorizacion de eventos e inscripciones
- `app/Http/Middleware/EnsureUserHasActiveRole.php`: invalida la sesion si el usuario no tiene rol activo
- `app/Http/Middleware/EnsureUserHasBackofficeAccess.php`: restringe el area administrativa usando la capacidad `backoffice.access`
- `resources/views/layouts/`: shell autenticado del panel (`app`, `sidebar`, `topbar`)
- `resources/views/components/`: componentes Blade reutilizables
- `resources/views/events/`: vistas del modulo de eventos
- `resources/views/profile/`: perfil del usuario
- `resources/css/app.css`: clases utilitarias del panel
- `tests/Feature/`: pruebas funcionales principales

## Dominio actual

### Roles

Los roles sembrados actualmente son:

- `admin`
- `coordinator`
- `user`

Reglas relevantes:

- La fuente de verdad de permisos ya no debe dispersarse en arrays de roles dentro de policies o vistas
- Las capacidades por rol viven en `app/Auth/RoleCapabilities.php`
- Los identificadores de capacidad viven en `app/Auth/Capability.php`
- `User::hasCapability()` es la forma preferida de consultar permisos
- `admin` tiene `system.manage_all` y acceso total al backoffice
- `coordinator` tiene acceso a backoffice y gestion operativa, pero no borrado de eventos
- `user` solo consume portal y acciones propias
- cualquier usuario sin rol activo es expulsado por el middleware `active.role`

Capacidades actualmente definidas:

- `backoffice.access`
- `events.view`
- `events.create`
- `events.update`
- `events.delete`
- `registrations.view_any`
- `registrations.view_own`
- `registrations.create`
- `registrations.update_any`
- `registrations.delete_any`
- `registrations.delete_own`
- `users.view`
- `users.create`
- `users.update`
- `users.assign_role`

### Eventos

Entidad principal: `App\Models\Event`

Campos relevantes:

- `name`
- `description`
- `date`
- `time`
- `location`
- `status`
- `capacity`
- `has_parking`
- `parking_slots`
- `user_id`

Estados canonicos del sistema:

- `activo`
- `abierto`
- `cerrado`
- `cancelado`

Importante:

- El modelo `Event` normaliza estados legacy con `EventStatus::normalize()`
- Valores antiguos como `programado`, `open`, `publicado` o `confirmado` no deben romper render ni persistencia
- Si una nueva funcionalidad toca estados, debe usar `App\Enums\EventStatus`

### Inscripciones

Entidad principal: `App\Models\EventRegistration`

Estados actuales:

- `registered`
- `waitlist`
- `cancelled`
- `attended`

Uso actual:

- `registered` y `attended` consumen capacidad
- `cancelled` no debe aparecer en "Mis eventos"

## Arquitectura de rutas

La aplicacion ya no debe mezclar backoffice y portal bajo un mismo espacio de URLs.

### Area administrativa

Usar prefijo y nombres de ruta `admin.*`

Ejemplos:

- `/admin/dashboard`
- `/admin/events`
- `/admin/events/create`
- `/admin/events/{event}`
- `/admin/events/{event}/edit`

Destino:

- administracion
- gestion operativa
- CRUD y supervision

Acceso:

- solo `admin` y `coordinator`

### Portal de usuario

Usar prefijo y nombres de ruta `portal.*`

Ejemplos:

- `/portal/dashboard`
- `/portal/profile`
- `/portal/events`
- `/portal/events/mis-eventos`
- `/portal/events/{event}`

Destino:

- autoservicio del usuario
- consulta de eventos
- perfil
- inscripciones propias

Acceso:

- cualquier usuario con rol activo

### Regla de diseño

Si una funcionalidad es de gestion interna, debe vivir en `admin.*`.
Si una funcionalidad es de consumo, consulta o accion personal del usuario, debe vivir en `portal.*`.

No agregar nuevas secciones autenticadas en rutas planas como `/events` o `/profile` si pertenecen claramente a una de las dos areas.

## Estandar visual actual

Las vistas autenticadas ya no deben inventar su propio layout por pagina. Reutilizar el patron actual.

### Shell del panel

El layout autenticado vive en:

- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/sidebar.blade.php`
- `resources/views/layouts/topbar.blade.php`

### Componentes de pagina

Usar preferentemente:

- `x-breadcrumbs`
- `x-page-header`
- `x-panel`
- `x-events.form`

### Clases utilitarias del panel

Definidas en `resources/css/app.css`:

- `app-page-header`
- `app-panel`
- `app-panel-muted`
- `app-alert-success`
- `app-alert-danger`
- `app-alert-info`
- `app-link`
- `app-link-muted`
- `app-select`
- `app-textarea`
- `app-checkbox`
- `app-table-*`
- `app-badge`

### Regla practica de UI

Si se crea una nueva seccion autenticada, debe seguir este esquema:

1. `x-app-layout`
2. `x-slot name="header"` con `x-page-header`
3. breadcrumbs definidos en `x-page-header` con la ruta actual
4. contenedor principal con `max-w-*` y `space-y-6`
5. contenido dentro de `x-panel`
6. inputs y tablas usando clases/componentes compartidos

No reintroducir:

- cabeceras sueltas con estilos distintos por vista
- bloques `dark:*` inconsistentes con el panel actual
- formularios o tablas con estilos aislados si ya existe una clase del sistema

## Convenciones funcionales

- Los textos visibles al usuario deben mantenerse en espanol
- Mantener consistencia con rutas nombradas existentes y su area (`admin.*` o `portal.*`)
- Preferir componentes Blade antes que duplicar markup
- Si se modifica una policy, revisar las rutas que usan `->can(...)`
- Si se modifica autorizacion, primero ajustar `Capability` y `RoleCapabilities`, luego consumir esos cambios desde policies o middleware
- Si se modifica la estructura de formularios de eventos, revisar `EventRequest`
- Si se modifica capacidad o estados de inscripcion, revisar `Event::confirmedRegistrationsCount()` y `EventRegistration::usesCapacity()`
- Si se toca una vista con header, revisar tambien sus breadcrumbs

## Seeds y datos de prueba

Seeder principal:

- `DatabaseSeeder`

Actualmente ejecuta:

- `RoleSeeder`
- `UserSeeder`
- `EventSeeder`
- `EventRegistrationSeeder`

Cuando un cambio depende de roles o datos base, asegurar que los seeders sigan siendo coherentes.

## Tests que conviene correr segun el cambio

### Dashboard y layout autenticado

```bash
php artisan test --filter=DashboardTest
php artisan test --filter=ProfileTest
```

### Modulo de eventos

```bash
php artisan test --filter=EventAuthorizationTest
php artisan test --filter=UserEventsTest
php artisan test --filter=EventRegistrationTest
php artisan test --filter=EventValidationTest
```

## Checklist antes de cerrar una tarea

- La vista nueva o modificada usa el estandar visual del panel
- La vista nueva o modificada usa breadcrumbs
- No se rompen roles ni policies
- La nueva ruta queda en el espacio correcto: `admin` o `portal`
- Los estados del evento siguen usando `EventStatus`
- Los textos de interfaz estan en espanol
- Se ejecutaron tests acordes al area tocada
- No se duplicaron componentes o estilos ya existentes

## Cambios sensibles

Tener especial cuidado al tocar:

- `app/Models/Event.php`
- `app/Http/Requests/EventRequest.php`
- `app/Policies/EventPolicy.php`
- `app/Policies/EventRegistrationPolicy.php`
- `app/Auth/Capability.php`
- `app/Auth/RoleCapabilities.php`
- `routes/web.php`
- `resources/css/app.css`
- `resources/views/layouts/*`
- `resources/views/components/breadcrumbs.blade.php`
- `resources/views/components/page-header.blade.php`

## Expectativa para futuros agentes

Trabajar de forma incremental y consistente con la base actual:

- primero entender rutas, policy y modelo afectados
- despues reutilizar componentes existentes
- luego validar con Pest

Si hay duda entre "hacer rapido" y "mantener el estandar del panel", priorizar el estandar.
