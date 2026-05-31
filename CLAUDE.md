# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Development
```bash
composer run dev       # Start all services: PHP server, queue, pail log viewer, Vite
```

### Build
```bash
composer run setup     # Full setup: install deps, generate key, migrate, npm install & build
npm run build          # Build frontend assets
```

### Testing
```bash
composer run test      # Clears config, checks PHP lint (pint), runs PHPUnit
php artisan test --filter=TestName  # Run a single test
```

### Linting & Formatting
```bash
composer run lint              # Fix PHP code style with Pint
composer run lint:check        # Check PHP style without fixing
npm run lint                   # Fix JS/TS with ESLint
npm run lint:check             # Check JS/TS without fixing
npm run format                 # Format with Prettier
npm run format:check           # Check formatting
npm run types:check            # TypeScript type checking (vue-tsc)
composer run ci:check          # Full CI check: JS lint, format, types, PHP lint + tests
```

### Database
```bash
php artisan migrate
php artisan db:seed
```

## Architecture

This is a **Laravel 13 + Vue 3 + Inertia.js** application for club table reservations.

### Backend (PHP)

**Stack:** Laravel 13, Laravel Fortify (auth + 2FA), Laravel Sanctum (API tokens), Inertia.js server-side adapter, SQLite (default).

**Structure:**
- `app/Http/Controllers/` — thin controllers that delegate to services
- `app/Services/` — business logic (static methods pattern): `ReservationService`, `ReservationRequestService`, `TableService`, `UserService`, `InviteService`
- `app/Models/` — Eloquent models with `SoftDeletes` on all domain models
- `app/Enums/` — `TableStatus` (ready/not_ready), `InviteStatus` (pending/accepted/revoked/expired)
- `app/Concerns/` — shared validation rule traits (`ProfileValidationRules`, `PasswordValidationRules`)

**Routes split:**
- `routes/web.php` — Inertia pages (Welcome, Dashboard), includes `settings.php`
- `routes/settings.php` — Profile, Security, API token, Appearance settings pages
- `routes/api.php` — REST API protected by `auth:sanctum`: full CRUD for Reservations, ReservationRequests, Tables, Invites, plus relationship management (attach/detach users, tables)

**Domain model relationships:**
- `User` ↔ `Reservation` (many-to-many via `reservation_user`), `User` ↔ `ReservationRequest` (many-to-many)
- `Table` → `Reservation` (one-to-many), `Table` → `ReservationRequest` (one-to-many)
- `ReservationRequest` has an `author` (User) and optional `table`; can have `invites`
- `Invite` links an `author` User to a `target` User for a `Reservation`; has `accept()`/`revoke()` methods that manage the `reservation.users` pivot

**Intended workflow:** `ReservationRequest` (pending, table optional, invites can be sent) → converts into `Reservation` (confirmed, table required). Two separate models reflect this proposal-then-confirmation flow.

**Authentication:** Laravel Fortify handles registration, login, password reset, email verification, and 2FA (TOTP). Sanctum provides API token auth for the REST API. Both session-based (web) and token-based (API) auth are active simultaneously.

**Inertia shared data** (via `HandleInertiaRequests`): `auth.user`, `name` (app name), `sidebarOpen`.

**Flash toasts:** Controllers call `initializeFlashToast()` to pass toast messages to the frontend; the client reads them from the Inertia shared data on page transitions.

### Frontend (Vue 3 + TypeScript)

**Stack:** Vue 3, TypeScript, Inertia.js, Tailwind CSS v4, Reka UI (headless components), Lucide icons, vue-sonner (toasts).

**Structure:**
- `resources/js/pages/` — Inertia page components (map 1:1 to routes)
- `resources/js/components/ui/` — low-level UI primitives (built on Reka UI), each with an `index.ts` barrel export
- `resources/js/composables/` — `useAppearance`, `useCurrentUrl`, `useInitials`, `useTwoFactorAuth`
- `resources/js/types/` — TypeScript types (`Auth`, `User`, `TwoFactorConfigContent`, navigation/UI types)

**Layout system:** Page components declare their layout via `defineOptions({ layout: ... })`:
- Public/auth pages (`auth/*`) → `AuthLayout` (variants: Simple, Split, Card)
- Settings pages → `[AppLayout, SettingsLayout]` (nested array)
- All other authenticated pages → `AppLayout` → `AppSidebarLayout` or `AppHeaderLayout`
- Welcome page uses no layout

**Routing:** Laravel Wayfinder (`@laravel/vite-plugin-wayfinder`) auto-generates typed route helpers from PHP route definitions — use these instead of hardcoded strings. Re-runs on Vite dev server restart.

**Composables:**
- `useAppearance()` — theme management (light/dark/system), persists via cookie
- `useInitials()` — derives avatar fallback text from user name
- `useTwoFactorAuth()` — TOTP setup/teardown flow
- `useCurrentUrl()` — tracks active page for nav highlighting
