# Dependency/security status

This project targets Laravel 10 because the user explicitly requested that version. Laravel 10 regular security maintenance ended on 2025-02-04.

On 2026-10-14, Composer 2.10.3 refused to resolve laravel/framework ^10.48, including versions through 10.50.3, due to these advisories:

- PKSA-d5tc-s1qs-h781
- PKSA-m5cs-t1y6-qpcs
- PKSA-3r5d-mb8f-1qw9
- PKSA-mdq4-51ck-6kdq
- PKSA-8qx3-n5y5-vvnd
- PKSA-w7xr-vk7n-rstm

Automatic approval review rejected persisting an advisory-ignore override and installing those dependencies. That override was removed. No vendor packages or Composer lock were generated, and no indirect bypass was attempted.

The safer next step is an approved upgrade to a supported Laravel release followed by dependency installation, audit, runtime tests and deployment checks. Otherwise, an exact explicit authorization for the legacy dependency exception is required before any such install can be attempted in this environment. No override/bypass instructions are shipped in this project.

Laravel's application protections do not remove framework-level vulnerabilities. PHP lint and SQL import verification are not substitutes for runtime or security testing.
