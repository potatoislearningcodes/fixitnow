# FixItNow — System Foundation (Laravel + MySQL)

This is the **Week 1 foundation**: database structure, models, role-based
access, and route/controller skeletons. It is not a runnable Laravel
project by itself — these files drop into a fresh Laravel install. Give
this whole folder to whoever owns Week 1 (System analyst / Database
designer / Backend dev — Accounts & Roles).

## 1. Create the base Laravel project

On a machine with Composer and PHP installed:

```bash
composer create-project laravel/laravel fixitnow
cd fixitnow
composer require laravel/breeze --dev
php artisan breeze:install blade
```

Breeze gives you login, registration, and password reset for free, so
nobody on the team has to build auth screens from scratch.

## 2. Copy in these files

Copy each file in this template into the **same path** inside your new
`fixitnow` project, overwriting `app/Models/User.php`:

```
database/migrations/2026_09_28_000001_add_role_to_users_table.php
database/migrations/2026_09_28_000002_create_technicians_table.php
database/migrations/2026_09_28_000003_create_bookings_table.php
database/migrations/2026_09_28_000004_create_payments_table.php
app/Models/User.php              (overwrite)
app/Models/Technician.php
app/Models/Booking.php
app/Models/Payment.php
app/Http/Middleware/EnsureUserHasRole.php
app/Http/Controllers/BookingController.php
app/Http/Controllers/Admin/TechnicianVerificationController.php
routes/fixitnow.php
database/seeders/FixItNowSeeder.php
```

## 3. Register the `role` middleware

**Laravel 11+** — in `bootstrap/app.php`, inside `->withMiddleware()`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\EnsureUserHasRole::class,
    ]);
})
```

**Laravel 10 or earlier** — in `app/Http/Kernel.php`, add to
`$middlewareAliases`:

```php
'role' => \App\Http\Middleware\EnsureUserHasRole::class,
```

## 4. Wire up the routes

Open `routes/web.php` and add this line near the top:

```php
require __DIR__.'/fixitnow.php';
```

## 5. Set up the database

Edit `.env` with your MySQL credentials, then:

```bash
php artisan migrate
php artisan db:seed --class=FixItNowSeeder
```

This creates three test accounts (password for all: `password`):

| Role       | Email                      |
|------------|-----------------------------|
| Admin      | admin@fixitnow.test         |
| Technician | technician@fixitnow.test (pre-approved) |
| Customer   | customer@fixitnow.test      |

## 6. Run it

```bash
php artisan serve
```

## What's intentionally left as TODO

These are marked `// TODO` in the code, and belong to specific weeks in
the task sheet — don't build them yet:

- **Email notifications** on booking creation and status change
  (Integration dev, Week 6).
- **Sandbox payment** on completed bookings (Integration dev, Week 6).
- **Blade views** (`resources/views/bookings/*`, `resources/views/admin/technicians/*`)
  — the controllers return views that don't exist yet; that's Front-end dev work
  (Weeks 4–5).
- **Map/location display** on the booking page (Integration dev, Week 5).

## Database structure at a glance

```
users            (id, name, email, password, role, phone)
  └─ technicians (id, user_id, specialty, bio, document_path,
                  verification_status, admin_notes)
       └─ bookings (id, customer_id, technician_id, service_type,
                    description, address, scheduled_at, status)
            └─ payments (id, booking_id, amount, gateway_reference, status)
```

`role` on `users` is one of `customer`, `technician`, `admin` — this is
what the `role:` middleware checks on every protected route.
