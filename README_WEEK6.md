# FixItNow — Week 6 (Notifications + Booking History)

From the task sheet:
- 🆕 Email notifications: booking confirmation, status change
- 🆕 Customer: view request status and history
- Bonus: technician gets notified when an admin assigns them a job
  (same pattern, one extra file, so it came along for free)

## 1. Copy these files in

```
app/Notifications/BookingConfirmed.php                      (new)
app/Notifications/BookingStatusChanged.php                  (new)
app/Notifications/TechnicianAssigned.php                    (new)
app/Http/Controllers/BookingController.php                  (overwrite)
app/Http/Controllers/Admin/BookingAssignmentController.php  (overwrite)
resources/views/bookings/index.blade.php                    (new)
```

No migrations this week.

## 2. Set up email — no signup needed to start

By default, Laravel's `.env` has `MAIL_MAILER=log`, which means "emails"
just get written to `storage/logs/laravel.log` as text instead of
actually being sent. **This already works with zero setup** — open that
log file after testing below and you'll see the full rendered email.

**For a nicer demo** (an actual inbox to show during your defense) sign
up free at https://mailtrap.io, create an "Inbox," and copy its SMTP
credentials into your `.env`:

```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS=noreply@fixitnow.test
MAIL_FROM_NAME="FixItNow"
```

Mailtrap catches emails in a fake inbox — nothing gets sent to real
addresses, which is exactly right for testing with fake `@fixitnow.test`
accounts. Each of your 9 teammates would need their own `.env` values
here (not committed to Git, same as the database credentials) — either
everyone signs up their own free Mailtrap account, or you share one
account's credentials with the group over a private channel, never
committed to the repo.

## 3. Add a nav link for booking history

In `resources/views/layouts/navigation.blade.php`, update the
`isCustomer()` block (desktop and mobile) to include a second link:

```blade
@if (auth()->user()->isCustomer())
    <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index') || request()->routeIs('bookings.show')">
        {{ __('My Bookings') }}
    </x-nav-link>
    <x-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.create')">
        {{ __('Book a Service') }}
    </x-nav-link>
@endif
```

(Matching `<x-responsive-nav-link>` version for the mobile block, as
usual.)

## 4. Test it

1. Book a new service as a customer. Check `storage/logs/laravel.log`
   (or your Mailtrap inbox) for a "Your booking request was received"
   email.
2. Click "My Bookings" in the nav — confirm you see this booking listed
   with its status, and clicking it goes to the full detail page.
3. As the assigned technician (or admin, via Unassigned Bookings),
   change the booking's status. Check for a "Your booking is now..."
   email to the customer.
4. If you used admin assignment instead of direct customer selection,
   check for a "You have a new job" email to the technician.
5. Book a second and third service as the same customer, then revisit
   "My Bookings" — confirm all three show, newest first.

## What this still doesn't include

- Full system testing with real sample users — that's QA's Week 7 task,
  not something I can substitute for.
- Deployment, feature freeze, and the paper itself are all still ahead.
