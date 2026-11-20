# LabaGo

Laundry management system with a **Laravel 12 API** and an **Ionic 8 + Angular 20** web frontend.
Customer records, orders, schedules, payments, and staff approvals live in MySQL; all existing
records were imported from the legacy SQLite database without deleting the source file.

## Stack

- **Backend:** Laravel 12, Sanctum personal access tokens, MySQL/MariaDB
- **Frontend:** Ionic 8, Angular 20, TypeScript, standalone components
- **Tests:** PHPUnit feature tests (in-memory SQLite), Angular production build

## Run locally

Requirements: PHP 8.2+, Composer, Node.js 20+, npm, XAMPP/MySQL.

```powershell
# Terminal 1 — API
cd "C:\Users\Admin\OneDrive\Desktop\mobile apk"
php artisan serve --host=127.0.0.1 --port=8000

# Terminal 2 — Ionic
cd "C:\Users\Admin\OneDrive\Desktop\mobile apk\ionic-frontend"
npm install
npm start -- --port 8100
```

Open **http://localhost:8100**.

- API base: `http://localhost:8000/api/v1`
- Health check: `http://localhost:8000/api/v1/health`
- Legacy Blade app: `http://localhost:8000`

Demo accounts (create or reset with `php artisan labago:demo-accounts`):

| Role | Email | Password |
|---|---|---|
| Admin | `admin@demo.com` | `admin123` |
| Staff | `staff@demo.com` | `staff123` |
| Rider | `rider@demo.com` | `rider123` |
| Customer | `customer@demo.com` | `customer123` |

Customer accounts are available immediately. Staff and Rider registrations stay pending until
an administrator approves them. `php artisan db:seed` adds the local Nueva Ecija sample orders,
rider assignments, pending registrations, and notifications; run it only against a disposable
development database.

## Documentation

See **[SETUP_TAGLISH.md](SETUP_TAGLISH.md)** for the complete Taglish guide: exact Windows,
VS Code, XAMPP and database steps, both start commands, local URLs, features, API/auth/CORS/CSRF
notes, tests, troubleshooting, limitations, and backup reminders.

Quality / usability acceptance: **[docs/qa/ACCEPTANCE_TEST_REPORT.md](docs/qa/ACCEPTANCE_TEST_REPORT.md)**
(results per acceptance criterion), plus test cases, heuristic evaluation and the usability
test kit in the same folder. The browser test scripts are in [qa/](qa/).

## Verification

```powershell
php artisan test --compact   # 55 passed, 468 assertions
cd ionic-frontend
npm run build                # production build succeeds
```

Browser verification: 30/30 functional tests and an accessibility audit of every screen (axe-core WCAG 2.2 AA, contrast, touch targets, 200% text) at phone and desktop sizes, against a separate SQLite test database. See `qa/README.md`.
An Android (Capacitor 8) project lives in `ionic-frontend/android`; see section 15 of the Taglish guide. Not yet tested on a real device; no iOS.

## Data safety

Never run `migrate:fresh`, `db:wipe`, or a repeated `db:seed` against the MySQL database that
contains real records. The legacy `database/database.sqlite` file is kept untouched; the
non-destructive importer is:

```powershell
php artisan laundry:import-sqlite
```
