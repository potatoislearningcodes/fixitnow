# FixItNow — Week 2 (Accounts)

Covers these Week 2 tasks from the task sheet:
- Build login/registration for customer & technician
- Build technician document upload for verification
- Build admin panel shell

(QA's "start test case list" isn't code — see the note at the bottom.)

## 1. Copy these files into your project, overwriting where named

```
app/Http/Controllers/Auth/RegisteredUserController.php   (overwrite)
app/Http/Controllers/TechnicianProfileController.php      (new)
app/Http/Controllers/Admin/DashboardController.php        (new)
resources/views/auth/register.blade.php                   (overwrite)
resources/views/technician/complete-profile.blade.php     (new)
resources/views/admin/dashboard.blade.php                 (new)
resources/views/admin/technicians/index.blade.php         (new)
routes/fixitnow.php                                        (overwrite — adds 3 routes)
```

## 2. Allow file uploads to be viewable

Technician documents are stored in `storage/app/public/technician-documents`.
Laravel needs a symlink so the browser can actually reach them:

```bash
php artisan storage:link
```

Run this once per machine (each teammate needs to run it locally too — it's
not something Git can carry over, since it's a symlink, not a tracked file).

## 3. Add navigation links

Open `resources/views/layouts/navigation.blade.php`. Find where the existing
nav links are defined (look for `<x-nav-link>` entries, usually near
"Dashboard"), and add these right after it:

```blade
@if (auth()->user()->isAdmin())
    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
        {{ __('Admin') }}
    </x-nav-link>
@endif

@if (auth()->user()->isTechnician())
    <x-nav-link :href="route('technician.profile.edit')" :active="request()->routeIs('technician.profile.edit')">
        {{ __('My Profile') }}
    </x-nav-link>
@endif
```

There are usually two nav-link blocks in that file — one for desktop, one
for the mobile responsive menu. Add this snippet to **both** so the links
show up on phones too. Use `<x-responsive-nav-link>` instead of
`<x-nav-link>` in the mobile block (same attributes).

## 4. Test it end to end

1. `php artisan migrate:fresh --seed --seeder=FixItNowSeeder` — start clean.
2. Register a brand new account at `/register`, choosing "Technician."
   You should land on "Complete your profile" automatically.
3. Fill in a specialty and upload any PDF/image as the document, submit.
4. Log out, log in as `admin@fixitnow.test` / `password`.
5. Go to the new "Admin" nav link → you should see the dashboard with a
   count of 1 pending technician → click "Review technicians" → see the
   one you just registered, with a working "View submitted document" link.
6. Click **Approve**. Status badge should flip to green next time that
   technician logs in and visits "My Profile."
7. Repeat registration as a **Customer** instead — confirm it skips the
   profile step and goes straight to the normal dashboard.

## What this doesn't include yet (later weeks)

- The customer's "browse technicians" page only shows **approved**
  technicians — that's Week 3, using `Technician::approved()` (already
  defined on the model from Week 1).
- Email notifications on registration/approval — Week 6 (Integration dev).
- The booking flow itself — Weeks 4–5.

## QA: test case list

This isn't code, but since Week 2 also has the QA tester starting the test
case list, here's a starting shape for that spreadsheet/doc (one row per
case):

| ID | Feature | Steps | Expected Result | Actual Result | Pass/Fail |
|---|---|---|---|---|---|
| TC-01 | Registration | Register as Technician with valid info | Redirected to Complete Profile | | |
| TC-02 | Registration | Register as Customer with valid info | Redirected to Dashboard | | |
| TC-03 | Registration | Register with an email already in use | Validation error shown | | |
| TC-04 | Technician profile | Submit profile with no document | Saved, status stays pending | | |
| TC-05 | Technician profile | Upload a non-PDF/image file (e.g. .exe) | Validation error shown | | |
| TC-06 | Admin verification | Approve a pending technician | Status changes to approved | | |
| TC-07 | Admin verification | Reject with a note | Status changes to rejected, note visible to technician | | |
| TC-08 | Access control | Customer tries to visit /admin/dashboard directly | 403 Forbidden | | |
| TC-09 | Access control | Technician tries to visit /admin/dashboard directly | 403 Forbidden | | |

Want this as an actual spreadsheet file instead of a markdown table? Ask
and I'll generate the `.xlsx`.
