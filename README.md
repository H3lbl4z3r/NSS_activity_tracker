# NSS Activity Tracker

A Laravel application built for the Npontu Technologies Systems Reliability Engineer (NSS)
interview assignment. It tracks the daily activities of an applications support team —
what needs checking, who checked it, what they found, and when — so pending work can be
handed over cleanly between shifts.

Built on top of the [TailAdmin](https://github.com/TailAdmin/tailadmin-laravel) Tailwind CSS
admin dashboard template.

## Requirements covered

| # | Requirement | Where it lives |
|---|---|---|
| 1 | Input activities (e.g. "Daily SMS count vs logs") | `Activities` page — Add Activity modal |
| 2 | Update status (pending/done) + remark | `Activities` page — Update Status modal |
| 3 | Capture who updated + when | `activity_updates` table (`updated_by`, `created_at`); shown on the activity detail page |
| 4 | Daily view of all activities and updates, for handover | `Daily Handover` page |
| 5 | Query activity history over a custom date range | `Reports` page |
| 6 | User authentication before access | Session-based auth (`auth`/`guest` middleware) on every route |

## Tech stack

- Laravel 13 (PHP)
- SQLite (single-file database, no server setup needed)
- Blade templates + Tailwind CSS
- Alpine.js for interactive bits (modals, dropdowns)

## Local setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Install JS dependencies
npm install

# 3. Copy the example environment file and generate an app key
cp .env.example .env
php artisan key:generate

# 4. Create the SQLite database file
touch database/database.sqlite

# 5. Run migrations and seed a test user
php artisan migrate:fresh --seed

# 6. Build front-end assets
npm run build

# 7. Serve the app
php artisan serve
```

Visit `http://localhost:8000` and sign in with the seeded test account:

- **Email:** `test@example.com`
- **Password:** `password`

## Project structure (what's custom to this assignment)

```
app/Http/Controllers/
    Auth/LoginController.php      # sign in / sign up / sign out
    ActivityController.php        # list, create, view activities
    ActivityUpdateController.php  # log a status update
    HandoverController.php        # daily handover view
    ReportController.php          # custom date-range reporting

app/Models/
    Activity.php
    ActivityUpdate.php
    User.php                      # extended with a `role` field

database/migrations/
    ..._add_role_to_users_table.php
    ..._create_activities_table.php
    ..._create_activity_updates_table.php

resources/views/pages/
    activities/index.blade.php
    activities/show.blade.php
    handover/index.blade.php
    reports/index.blade.php
```

## Design notes

- Every status change is logged as its own row in `activity_updates`, rather than
  overwriting a single status field — this preserves a full audit trail (who said what,
  and when) instead of only ever showing the most recent update.
- The `activities.status` column is kept as a fast "current state" snapshot, updated
  alongside each new log entry, so listing pages don't need to recompute it from history
  every time.
- All routes require authentication except the sign-in/sign-up pages themselves.