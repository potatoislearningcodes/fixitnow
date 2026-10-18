# FixItNow — Week 5 (Technician Dashboard)

From the task sheet: "Technician dashboard: view/accept/update jobs."

This is exactly what was missing to test Path C from last week — a
technician now has a real screen listing every booking assigned to them,
instead of needing a URL handed to them manually.

## 1. Copy these files in

```
app/Http/Controllers/TechnicianDashboardController.php   (new)
resources/views/technician/dashboard.blade.php            (new)
routes/fixitnow.php                                        (overwrite — adds 1 route)
```

No migrations this week — this only reads data that already exists.

## 2. Add the nav link

In `resources/views/layouts/navigation.blade.php`, add this inside the
`@if (auth()->user()->isTechnician())` block you already have — right
next to the existing "My Profile" link, in **both** the desktop and
mobile nav sections:

```blade
@if (auth()->user()->isTechnician())
    <x-nav-link :href="route('technician.dashboard')" :active="request()->routeIs('technician.dashboard')">
        {{ __('My Jobs') }}
    </x-nav-link>
    <x-nav-link :href="route('technician.profile.edit')" :active="request()->routeIs('technician.profile.edit')">
        {{ __('My Profile') }}
    </x-nav-link>
@endif
```

(Use `<x-responsive-nav-link>` for the mobile block, same as always.)

## 3. Test it

1. Log in as a technician with at least one assigned booking (from last
   week's testing, or assign one now via the admin's "Unassigned
   Bookings" screen).
2. Click "My Jobs." You should see:
   - A banner showing Available/Busy at the top.
   - Each assigned booking, sorted so Accepted/On-going jobs show first.
   - A status dropdown + "Update" button right on the list — no need to
     click into the full page just to accept or progress a job.
3. Change a job's status to "Accepted" or "On-going," then refresh —
   confirm the "Your current status" banner flips to "Busy."
4. Mark that same job "Repaired" — confirm the banner flips back to
   "Available."
5. Click "View full details" on any job — confirms it still goes to the
   same `bookings.show` page from before, where payment gets recorded
   once a job is Repaired.
6. Now redo last week's **Path C** properly: as a different customer,
   check that busy technician's card on the booking page — it should
   show "Busy" without you needing to know any booking URLs by hand.

## What this still doesn't include

- A customer still can't see a list of *their own* bookings — that's
  Week 6's "Customer: view request status and history."
- No email notifications yet.
