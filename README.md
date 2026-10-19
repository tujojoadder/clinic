# Clinic Care — Laravel 10 + MySQL

Laravel source project for outpatient consultations, inpatient admissions and diagnostic services. The approved teal/white Clinic Care UI is retained in Blade. Prescription and pharmacy modules are excluded.

## Important delivery status

This is a **source project with SQL and migrations**, not a preinstalled vendor bundle. PHP syntax checks and SQL import checks passed. Framework boot, Blade compilation, PHPUnit, and HTTP workflows **have not been executed** for this Laravel version because dependency installation was blocked by known Laravel 10 security advisories. Composer's protection is enabled; no advisory-ignore override is shipped. See SECURITY.md and tests/VALIDATION.md.

Laravel 10 regular security support ended on 4 February 2025. Prefer a supported Laravel release before deployment with real patient information. This project must not be described as production-security-certified.

## Requirements

- PHP 8.1–8.3 with PDO MySQL, mbstring, XML/DOM, ctype, fileinfo, tokenizer, OpenSSL.
- Composer, MySQL 8.0+ or MariaDB 10.6+.
- Web document root: `public/`.
- Writable `storage/` and `bootstrap/cache/`.

## Setup

1. Extract the full project into its own directory.
2. Create a **new empty** MySQL database and a separate database user. Do not import over an existing clinic database.
3. Copy `.env.example` to `.env` and configure DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME and DB_PASSWORD.
4. Set APP_URL to your domain, APP_ENV=production, APP_DEBUG=false, and SESSION_SECURE_COOKIE=true when using HTTPS.
5. The standard dependency command is `composer install`. Modern Composer may refuse this Laravel 10 dependency due to the advisories in SECURITY.md. Do not weaken that protection for production. Resolve the version decision first; no bypass command is provided here.
6. After dependencies are installed in an approved environment:

```bash
php artisan key:generate
php artisan migrate --seed --force
php artisan clinic:admin
```

`clinic:admin` prompts for name, email and a hidden password of at least 12 characters. No default admin password or production key is included.

7. On Plesk, select PHP 8.3 and point document root to the project's `public/`. Ensure storage/cache ownership is correct; do not use chmod 777.
8. Local-only server: `php artisan serve`. No npm/Vite build is required for the existing UI assets.

## Database import alternative

`database/clinic-care.sql` contains all 20 clinic tables plus Laravel's `migrations` table, settings and migration bookkeeping. It contains no admin account.

You may import that file into a **new empty** database instead of running initial migrations. Then run `php artisan clinic:admin`. Existing migration bookkeeping prevents `artisan migrate` from recreating imported tables. Do not combine a manual schema-only import with migrations on the same database.

`database/schema.sql` is schema-only documentation, without migration bookkeeping; prefer clinic-care.sql for phpMyAdmin import.

Demo data is optional:

```bash
php artisan db:seed --class=DemoSeeder
```

The demo uses synthetic records only.

## Architecture

| Responsibility | Location |
|---|---|
| HTTP routes | routes/web.php |
| Login/session | AuthController, ActiveSession middleware |
| Entity validation | app/Http/Requests/EntityRequest.php |
| CRUD | EntityController + EntityService |
| Admissions, beds, discharge | AdmissionService |
| Bills, receipts, refunds | BillingService |
| Tests and reports | LabService |
| Eloquent models | app/Models/ |
| Module fields / role permissions | config/clinic.php |
| Layout/sidebar/topbar | resources/views/layouts/ and partials/ |
| Domain screens | resources/views/ |
| Existing CSS/JS | public/assets/ |
| Native migrations | database/migrations/ |
| SQL | database/clinic-care.sql |
| Server commands | app/Console/Commands/ |

No raw-PHP bootstrap or MySQLi adapter is used in this version. Database work uses Laravel Eloquent/query builder and Laravel-managed transactions.

## Features

- Patient and doctor create/list/detail/edit/delete. Doctor department, phone, fees, schedule and active state.
- Appointment queue, serial validation and visit bills.
- Admissions, advance payment, bed occupancy and transfer.
- Nursing observations: BP, temperature, pulse and notes.
- Tests, pricing, report parameters, direct orders, sample collection, results, approval and revision.
- Running inpatient bills, bed charges, service charge CRUD, discounts, receipts, refund, payment void and invoice finalize/void.
- Discharge summary, due approval, advance adjustment and printable final bill.
- Expenses, manual doctor payable/settlement, collection/due/test/admission reports.
- Users with admin/reception/doctor/nurse/lab/cashier roles, audit history and settings.
- Laravel CSRF/session/password hashing, validation and server-side permissions.

## Rules and current limits

- Rent counts started 24-hour periods, minimum one day per bed stay. A transfer can therefore create two minimum rental days. Change AdmissionService::days if your clinic uses another rule.
- Running rent refresh is explicit; discharge computes final rent. Reports use currently stored charges.
- Advance on open admission bills can exceed current charges. Outpatient and final bills reject overpayment. Excess advance must be refunded before discharge.
- Discount, refund, lab final approval, report reopening and due-discharge approval require admin.
- Foreign keys protect used doctors/patients/beds/tests from deletion. Use inactive state for a doctor with clinical history. Financial/clinical transactions are cancelled or voided rather than hard-deleted.
- Doctor payable is entered manually; commission calculation is not automatic.
- Collection less expenses is a cash summary, not accrual profit-and-loss.
- Permissions are module-level. Doctor-specific patient ownership and individually configurable permission checkboxes are not included.
- SMS gateway, online patient portal and email password-reset provider are not connected.
- Approved lab results keep value/unit/range snapshots. Reopening records previous results in audit history.
- This is a new Laravel project. Existing Raw PHP user password_hash becomes Laravel password; no automatic old-data import/migration is included. Keep old database backups and use an explicit migration process if transferring real records.

## Backup / maintenance

```bash
php artisan clinic:backup /absolute/private/path/clinic-backup.sql
php artisan clinic:password
```

The backup command exports MySQL schema/data to a new private file outside public/. Never restore its DROP TABLE statements into a live database without a separate recovery procedure. Back up `.env` securely and source files separately.

## Tests

Included PHPUnit unit and feature tests cover rental periods, doctor CRUD/roles, module page rendering, admissions, lab approval and discharge. They have not been executed because the framework dependencies are blocked. Once the version/dependency issue is resolved:

```bash
php artisan test
```

phpunit.xml uses an isolated in-memory SQLite database and a **test-only** encryption key. Never run destructive tests against production or reuse that test key as APP_KEY.
