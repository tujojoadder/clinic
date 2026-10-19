# Verification — 2026-10-14

Passed:

- PHP 8.3.6 syntax lint of first-party app/controllers/services/models/requests/middleware/commands, config, migrations, routes and PHP test sources.
- Composer JSON validation with Composer 2.10.3.
- Model table mapping and migration/table count consistency checks.
- database/clinic-care.sql imported into a private disposable MariaDB 10.11.7 database: 21 tables, 20 migration records and 4 settings.
- Confirmed no Composer advisory-ignore policy is included.

Not run:

- Dependency installation and framework boot.
- Native migrations through artisan (SQL import was checked independently).
- Blade compilation/rendering and browser visual QA.
- PHPUnit feature/unit tests and authenticated Laravel HTTP workflows.
- Laravel backup command runtime and Plesk/Apache deployment.

Reason: Laravel 10 dependencies were blocked by known advisories; automatic approval review rejected a proposed advisory-ignore install. No security-control bypass was retained.

The previous Raw PHP/MySQLi tests do NOT count as Laravel tests. Runtime checks must be completed after the dependency/version issue is resolved.
