# FixItNow — Setup & Contribution Guide
---
## 1. Before you clone: install these first

Each person needs these installed once on their own PC:

| Tool | Why | Check it's installed |
|---|---|---|
| [XAMPP](https://www.apachefriends.org/) | Gives you PHP + MySQL together | `php -v` in a terminal |
| [Composer](https://getcomposer.org/) | Installs Laravel's PHP packages | `composer -v` |
| [Node.js](https://nodejs.org/) (LTS version) | Builds the CSS/JS | `node -v` |
| [VS Code](https://code.visualstudio.com/) | Editor | — |
| [MySQL Workbench](https://dev.mysql.com/downloads/workbench/) | View/manage the database visually | — |
| [Git](https://git-scm.com/) | Clone and sync the repo | `git -v` |

In VS Code, install the extension **PHP Intelephense** for autocomplete.

---

## 2. One-time Git setup (first time using Git on this PC)

```bash
git config --global user.name "Your Name"
git config --global user.email "your-github-email@example.com"
```

Use the email tied to your GitHub account so your commits link to your
profile correctly.

---

## 3. Clone the repo

```bash
git clone https://github.com/potatoislearningcodes/fixitnow.git
cd fixitnow
code .
```

---

## 4. Install dependencies

In the VS Code terminal:

```bash
composer install
npm install
```

> If `npm install` shows `EBADENGINE` warnings about your Node version, that's
> just a notice, not an error — it still installs fine as long as it finishes
> with "added X packages."

---

## 5. Set up your own `.env`

The `.env` file is **never committed to GitHub** (it's in `.gitignore`)
because it holds machine-specific settings and secrets. Every member
creates their own:

```bash
copy .env.example .env        # Windows
# or: cp .env.example .env    # Mac/Linux
php artisan key:generate
```

Then open `.env` and set the database section to match your own MySQL:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fixitnow
DB_USERNAME=root
DB_PASSWORD=

---

## 6. Create the database

Open **MySQL Workbench**, connect to your local server, right-click under
Schemas → **Create Schema** → name it `fixitnow` → Apply.

You don't create tables by hand — Laravel's migrations do that next.

---

## 7. Run migrations and seed test accounts

```bash
php artisan migrate
php artisan db:seed --class=FixItNowSeeder
```

This creates three test logins (password for all: `password`):

| Role | Email |
|---|---|
| Admin | admin@fixitnow.test |
| Technician (pre-approved) | technician@fixitnow.test |
| Customer | customer@fixitnow.test |

---

## 8. Build the front-end assets

```bash
npm run build
```

While actively working on views/CSS, use this instead — it rebuilds
automatically every time you save a file, so you don't retype the command:

```bash
npm run dev
```

---

## 9. Run the app

```bash
php artisan serve
```

Open **http://127.0.0.1:8000** and log in with one of the seeded accounts
above to confirm everything works before you start building.

---

## 10. Troubleshooting — fixes for issues we already hit

**`fatal: not a git repository`**
You're either in the wrong folder, or the repo wasn't cloned properly.
Run `cd fixitnow` first, or re-clone with `git clone <url>`.

**`Author identity unknown` when committing**
Run the two `git config --global` commands from section 2.

**`git-credential-manager.exe` error: "The string binding is invalid"**
Run `git config --global credential.msauthUseBroker false`, then try your
`git push` or `git pull` again. If it still fails, use a Personal Access
Token instead: GitHub → Settings → Developer settings → Personal access
tokens → generate one with "repo" permissions, then use your GitHub
username and the token (as the password) when Git prompts you.

**`could not find driver` when running `php artisan migrate`**
PHP's MySQL extension isn't enabled. Run `php --ini` to find your active
`php.ini`, open it, remove the `;` in front of `extension=pdo_mysql` and
`extension=mysqli`, save, then close and reopen your terminal.

**`Vite manifest not found` when opening any page**
You skipped step 8. Run `npm run build`.

**`npm run build` fails with "Cannot find native binding" / rolldown error**
This repo already pins `vite` to `^6.0.0` and `laravel-vite-plugin` to
`^1.0` to avoid this exact bug in Vite 8. If you still see it, delete
`node_modules` and `package-lock.json`, then run `npm install` again.

**`npm install` fails with an ERESOLVE / peer dependency error**
Check that your `package.json` still has `vite` on `^6.0.0`. If someone
accidentally bumped it, change it back and reinstall.

**Login says "these credentials do not match our records"**
The seeder hasn't run yet, or didn't finish. Run step 7 again. If it
errors about a duplicate entry, the account already exists — open
`php artisan tinker` and run:
```php
App\Models\User::where('email', 'admin@fixitnow.test')->update(['password' => bcrypt('password')]);
```

**A `.npmrc` setting breaks `npm install`**
If you see `ignore-scripts=true` in `.npmrc`, remove that line — it
blocks native package installs that Vite's build needs.

---

## 11. Our Git workflow

We use one shared repository with **branches**, so 10 people can work at
once without overwriting each other.

- **`main`** — always working. Nobody commits to `main` directly.
- **One branch per feature**, named like `feature/booking-form` or
  `fix/login-bug`.
- **Pull Requests (PRs)** — when your feature branch is ready, open a PR
  into `main`. The Project Manager (or whoever's doing code review that
  week) reviews it before merging.

### Day-to-day commands

```bash
git checkout main
git pull                          # get the latest changes first
git checkout -b feature/your-task-name

# ...do your work, then...

git add .
git commit -m "Add booking creation form"
git push -u origin feature/your-task-name
```

Then open a Pull Request on GitHub from that branch into `main`.

### Rules to avoid conflicts

1. **Pull before you start work each day** (`git checkout main && git pull`),
   so your branch starts from the latest code.
2. **Don't both edit the same file for unrelated tasks.** If two people
   need to touch `routes/web.php` in the same week, coordinate in the
   group chat first.
3. **Commit often, in small chunks**, not one giant commit at the end of
   the week — small commits are easier to review and easier to undo if
   something breaks.
4. **Never commit `.env`, `node_modules/`, or `vendor/`.** These are
   already in `.gitignore`; if you ever see them show up in `git status`,
   stop and ask before committing.
5. **Test your own feature locally before opening a PR** — run the app
   and click through what you built.

---

## 12. How we add a new feature (general pattern)

Most FixItNow features follow the same shape. Example: adding a new field
to bookings.

1. **Migration** — if you're changing the database, create a migration:
   `php artisan make:migration add_notes_to_bookings_table`
2. **Model** — update the `$fillable` array in the relevant model
   (`app/Models/Booking.php`, etc.) if you added a column.
3. **Controller** — add or update a method in the relevant controller
   (`app/Http/Controllers/BookingController.php`, etc.).
4. **Route** — wire it up in `routes/fixitnow.php`.
5. **View** — build or update the Blade file in `resources/views/`.
6. **Test it locally**, then commit and open a PR.

If your task doesn't touch the database, you can usually skip straight to
the controller/route/view steps.

---

## 13. Who to ask

- **Stuck on your own machine (install/setup errors):** check section 10
  above first, then ask in the group chat with a screenshot of the exact
  error.
- **Not sure which file your task touches:** ask the System Analyst or
  Database Designer — they own the requirements and schema.
- **Merge conflicts you can't resolve:** ask the Project Manager before
  force-pushing anything.
