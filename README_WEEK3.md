# FixItNow — Week 3 (Accounts, continued)

From the task sheet:
- ✅ Finish role-based access control — already done (came with the Week 2 build)
- ✅ Admin: approve/reject technician verification — already done (came with the Week 2 build)
- 🆕 Customer: browse verified technicians page — **this delivers it**
- Outline paper Chapters 1–2 — not code, ask me separately if you want a draft

Since the technician listing and the actual booking request are two halves of
one screen, this also gets you a working start on Week 4's "Customer booking
UI" — a customer can now pick a technician and submit a real booking request,
which lands in `pending` status ready for Week 4/5's technician dashboard
work.

## 1. Copy these files in

```
resources/views/bookings/create.blade.php   (new)
resources/views/bookings/show.blade.php     (new)
```

Both are brand new — no overwrites this time. Create the `bookings` folder
under `resources/views/` if it doesn't exist yet (it probably will from
Week 1's folder structure).

No controller or route changes needed — `BookingController` and the
`bookings.*` routes were already built in Week 1; they just didn't have
views yet.

## 2. Add a nav link for customers

In `resources/views/layouts/navigation.blade.php`, add this inside the same
desktop nav-links `<div>` as your other links (after the Admin/My Profile
`@if` blocks from Week 2):

```blade
@if (auth()->user()->isCustomer())
    <x-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.*')">
        {{ __('Book a Service') }}
    </x-nav-link>
@endif
```

And the matching mobile version in the responsive nav block:

```blade
@if (auth()->user()->isCustomer())
    <x-responsive-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.*')">
        {{ __('Book a Service') }}
    </x-responsive-nav-link>
@endif
```

## 3. Test it end to end

1. Make sure you have at least one **approved** technician (register one, or
   check one is already approved from last week's testing).
2. Log in as the seeded customer (`customer@fixitnow.test` / `password`), or
   register a fresh customer account.
3. Click "Book a Service" in the nav.
4. Confirm you see the approved technician's card (name, specialty, bio) —
   and that a rejected or still-pending technician does **not** appear here
   (this proves `Technician::approved()` from Week 1 is filtering correctly).
5. Click "Request this technician" — the form below should scroll into
   view and show "Booking with: [their name]."
6. Fill in service type, address, and a date/time, then submit.
7. You should land on the booking confirmation page showing status
   "Pending."
8. Log in as that technician and visit the booking's URL directly (or we'll
   wire a technician booking list in Week 5) — you should see a status
   dropdown to update it, since the `show.blade.php` page already supports
   that for technicians.

## What this doesn't include yet (later weeks)

- A technician doesn't yet have a dashboard listing *their* bookings — they
  can only act on one if they know its URL. That's Week 5's "Technician
  dashboard: view/accept/update jobs."
- A customer can't yet see a *list* of their own past bookings — that's
  Week 6's "Customer: view request status and history."
- No map/location display yet — Week 5.
- No payment yet — Week 6.
