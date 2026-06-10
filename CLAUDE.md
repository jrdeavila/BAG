# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

BAG is a Laravel 12 (PHP 8.2+) internal activity-management app for Cámara de Comercio de Valledupar. It tracks work **activities** assigned to **employees/users**, with role-based access control, email notifications, and Excel report exports. The UI is built on AdminLTE (jeroennoten/laravel-adminlte) Blade components. The codebase and UI strings are in Spanish.

## Commands

```bash
# Run the full dev stack (server + queue worker + log tailing + vite) concurrently
composer run dev

# Tests (clears config first, then runs PHPUnit)
composer test
php artisan test --filter=SomeTest      # single test / class
php artisan test tests/Feature/Foo.php  # single file

# Lint / format (Laravel Pint)
./vendor/bin/pint              # fix
./vendor/bin/pint --test       # check only

# Frontend
npm run dev      # vite dev server
npm run build    # production assets

# Queues — listeners send mail via the queue; a worker must be running
php artisan queue:listen        # or queue:work
php artisan horizon             # Redis-backed queue dashboard/supervisor

php artisan migrate             # runs only against the default (mysql) connection — see below
```

Tests run against an in-memory SQLite DB (see `phpunit.xml`), with `sync` queue and `array` mail. Note: most domain models are bound to specific MySQL connections (below), so feature tests that touch them need those connections available or overridden.

## Architecture

### Multi-database design (critical)

The app spans **three MySQL databases**, and models are pinned to named connections via `protected $connection`. This is the single most important thing to understand before touching any model or query:

- **`timeit`** — external/legacy HR database (read-mostly). Holds `usuarios` (users), `empleados` (employees), `cargos` (jobs), and CV/`hojas_de_vida` data. `User`, `Employee`, `Job`, `Curriculum` all use `$connection = 'timeit'`.
- **`auth`** — authorization database. Holds Spatie `roles`/`permissions` tables. `Role` and `Permission` extend the Spatie models and override `$connection = 'auth'`. `config/permission.php` qualifies table names with the DB name from `DB_AUTHORIZATION_DATABASE`.
- **`mysql`** (default) — this app's own database. Holds the `activities` table. `Activity` uses `$connection = 'mysql'`. **Migrations in `database/migrations/` only target this connection** — do not add migrations for `timeit` or `auth` tables; those are managed externally.

Connections are configured in `config/database.php` and fed by `DB_*`, `DB_TIMEIT_*`, and `DB_AUTHORIZATION_*` env vars.

### Legacy-schema attribute mapping

`User` and `Employee` map a legacy Spanish schema to clean English API names. The real columns (`clave`, `correo`, `rol`, `estado`, `Empleados_id`, `noDocumento`, `nombres`, `Cargos_id`, …) are `$hidden`, and English accessors/mutators (`email`, `role`, `status`, `employee_id`, `document_number`, `full_name`, `job_id`, …) are exposed via `$appends` + get/set attribute methods. When querying raw columns use the Spanish names; when reading model attributes prefer the English ones. `User::scopeWithActiveEmployee()` filters to employees with `estado = 'Activo'`.

### Custom authentication

Login is **not** standard Laravel. Auth uses a custom `plaintext` provider:

- `PlainTextUserProvider` (registered in `AppServiceProvider::boot` via `Auth::provider('plaintext', …)`, wired in `config/auth.php`).
- Credentials: username field is `usuario` (mapped to `correo`/email) and the "password" is the employee's **document number** (`empleados.noDocumento`), matched via the `employee` relation. `getAuthPassword()` returns the `clave` column. Validation is a plain string comparison — no hashing. See `LoginController::credentials()` and `username()`.

### Authorization

Spatie laravel-permission drives gates. In `AppServiceProvider::boot`:
- `Gate::before` grants **everything** to users with the `superadmin` role.
- `manage-user-roles` gate also requires `superadmin`.

Controllers gate individual actions in their constructors via `can:` middleware (e.g. `ActivityController` uses `list-activities`, `view-activity`, `create-activity`, `edit-activity`, `delete-activity`, `show-activity-owner`). The superadmin-only role-management UI lives under `/admin/user-roles` (`Admin\UserRoleController`).

### Activity lifecycle & notifications

`Activity` is decorated with `#[ObservedBy(ActivityObserver::class)]`. On `created`/`updated`, the observer fires `ActivityCreatedEvent` / `ActivityUpdatedEvent`. Their listeners (`ActivityCreated/UpdatedListener`) send paired Mailables — one to the activity creator, one to the assigned user (`app/Mail/Activity*Notification.php`). Because mail goes through the queue, a queue worker (or Horizon) must run for notifications to send. Activity state is modeled by the `ActivityStatus` and `ActivityPriority` enums (string-backed) in `app/Enums/`.

### Reporting / exports

`ReportController` (invokable) + `app/Exports/ActivityExport.php` use maatwebsite/excel to export activity reports.

### Routes

All app routes are in `routes/web.php` behind `auth`. `Auth::routes()` provides the login scaffolding. Activities use a resource controller (`activities.*`) plus extra `finish`, `show`, and `show-user-details` routes. Admin role management is nested under the `admin.` name prefix and `can:manage-user-roles`.
