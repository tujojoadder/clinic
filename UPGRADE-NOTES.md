# Before production

1. Confirm a supported target Laravel version.
2. Update the framework skeleton and middleware/bootstrap conventions for that release.
3. Resolve Composer dependencies with default advisory protection, and retain the resulting lock file.
4. Run native migrations, PHPUnit and authenticated HTTP checks on disposable MySQL.
5. Verify Blade views, role boundaries, rent/refund/discharge rules and backups.
6. Configure HTTPS, APP_DEBUG=false and private credentials; then review deployment.

No existing real clinic database should be overwritten during this process.
