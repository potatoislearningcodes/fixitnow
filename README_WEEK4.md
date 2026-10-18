# FixItNow — Week 4 (Payments + Service Booking System feature merge)

This folds in the uploaded "Service Booking System" guide (building type,
services, utility-man availability, status tracking, rooms, payment
section) on top of what's already built, without removing anything.

**Payment decision:** manual recording, not a live payment gateway. The
guide's own GCash/Online Banking methods are described as "reference
number is recorded" — that's proof-of-payment logging, not a checkout
flow. This matches how real repair services usually get paid (the
technician is paid directly, the system just confirms it) and is far
simpler to build and defend than integrating a real sandbox checkout.

## 1. Copy these files in (overwrite where named)

```
database/migrations/2026_10_18_000001_add_service_details_to_bookings_table.php  (new)
database/migrations/2026_10_18_000002_update_payments_table_for_manual_recording.php  (new)
app/Models/Technician.php                                  (overwrite)
app/Models/Booking.php                                     (overwrite)
app/Models/Payment.php                                     (overwrite)
app/Http/Controllers/BookingController.php                 (overwrite)
app/Http/Controllers/Admin/BookingAssignmentController.php (new)
routes/fixitnow.php                                        (overwrite)
resources/views/bookings/create.blade.php                  (overwrite)
resources/views/bookings/show.blade.php                    (overwrite)
resources/views/admin/bookings/unassigned.blade.php        (new)
```

## 2. Run the new migrations

```bash
php artisan migrate
```

If this errors about renaming a column (`renameColumn` needs a package
called `doctrine/dbal` on some Laravel versions), run:

```bash
composer require doctrine/dbal
php artisan migrate
```

then continue — this is a one-time setup step, not a bug in the migration.

## 3. Add an admin nav link (optional but recommended)

So the admin can actually find the new "Unassigned Bookings" screen, add
this inside the same desktop nav-links block as your other admin link, in
`resources/views/layouts/navigation.blade.php`:

```blade
@if (auth()->user()->isAdmin())
    <x-nav-link :href="route('admin.bookings.unassigned')" :active="request()->routeIs('admin.bookings.unassigned')">
        {{ __('Unassigned Bookings') }}
    </x-nav-link>
@endif
```

(Add the matching `<x-responsive-nav-link>` version to the mobile block too,
same as previous weeks.)

## 4. Test it end to end

**Path A — customer picks a technician directly (existing flow, unchanged):**
1. Book as a customer, selecting an available technician.
2. Confirm the new fields (building type, service group, rooms) save and
   display correctly on the confirmation page.

**Path B — customer leaves it unassigned (new flow):**
1. Book as a customer, but don't click "Request this technician" — just
   fill in the rest and submit.
2. Confirm the booking shows "Technician: Not yet assigned."
3. Log in as admin, go to "Unassigned Bookings," confirm the booking
   appears with a dropdown of available technicians.
4. Assign one. Confirm the booking now shows that technician.

**Path C — availability blocks double-booking:**
1. As a technician, set a booking's status to "Accepted" or "On-going."
2. As a different customer (or admin), check that technician's card —
   it should show "Busy" and the request button should be disabled /
   that technician should disappear from the "available" dropdown.
3. Mark the job "Repaired" (completed) as that technician — confirm they
   become "Available" again automatically (no manual toggle needed).

**Path D — payment recording:**
1. With a booking marked "Repaired," open it as the assigned technician
   (or as admin).
2. The Payment section should appear. Try submitting Cash with no
   reference number — should succeed. Try GCash with no reference number —
   should show a validation error (reference required for non-cash).
3. Submit GCash with a reference number — confirm it shows "Paid via GCash
   — Ref: [number]" afterward, and the form disappears (already paid).

## What this still doesn't include

- No live payment gateway — by design, see the decision above.
- No email notifications on assignment or payment (Integration dev,
  later).
- The customer's own booking history list (`bookings.index` /
  `bookings/index.blade.php`) still has no view — that was always a later
  week's task and is unaffected by this change.
