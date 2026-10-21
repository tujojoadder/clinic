<?php

use App\Http\Controllers\{AccountController, AuthController, DashboardController, EntityController, WorkflowController, ReportController, SettingsController, PatientController, DoctorController, ReferenceController, BedController, VisitController, AdmissionController, NursingRecordController, LabOrderController, InvoiceController, ExpenseController, DoctorLedgerController, SpecialtyController, TestController, TestParameterController, UserController};
use App\Http\Middleware\{ActiveSession, SecurityHeaders};
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


Route::get('/dump-autoload', function () {
    exec('composer dump-autoload');

    return 'Composer dump-autoload completed!';
});

Route::get('/run-migrate', function () {
    Artisan::call('migrate', [
        '--force' => true,
    ]);

    return Artisan::output();
});

// Route::get('/run-rollback', function () {
//     Artisan::call('migrate:rollback', [
//         '--force' => true,
//     ]);

//     return Artisan::output();
// });

Route::middleware(SecurityHeaders::class)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    });
    Route::middleware(['auth', ActiveSession::class])->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/income', [ReportController::class, 'income'])->name('reports.income');
        Route::get('/reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('/reports/dues', [ReportController::class, 'dues'])->name('reports.dues');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/admissions', [ReportController::class, 'admissions'])->name('reports.admissions');
        Route::get('/reports/doctors', [ReportController::class, 'doctors'])->name('reports.doctors');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::get('/discharge', [DashboardController::class, 'discharge'])->name('discharge');

        // ── Separate module routes (must stay ABOVE the generic /{module} routes) ──
        Route::resource('patients', PatientController::class)->whereNumber('patient');
        Route::get('doctors/for-date', [DoctorController::class, 'forDate'])->name('doctors.for_date');
        Route::resource('doctors', DoctorController::class)->whereNumber('doctor');
        Route::resource('references', ReferenceController::class)->whereNumber('reference');
        Route::resource('beds', BedController::class)->whereNumber('bed');
        Route::resource('visits', VisitController::class)->except('destroy')->whereNumber('visit');
        Route::resource('admissions', AdmissionController::class)->except('destroy')->whereNumber('admission');
        Route::resource('nursing_records', NursingRecordController::class)->whereNumber('nursing_record');
        Route::resource('lab_orders', LabOrderController::class)->only(['index', 'create', 'store', 'show'])->whereNumber('lab_order');
        Route::resource('invoices', InvoiceController::class)->except('destroy')->whereNumber('invoice');
        Route::resource('expenses', ExpenseController::class)->whereNumber('expense');
        Route::resource('doctor_ledgers', DoctorLedgerController::class)->whereNumber('doctor_ledger');
        Route::resource('tests', TestController::class)->whereNumber('test');
        Route::resource('test_parameters', TestParameterController::class)->whereNumber('test_parameter');
        Route::resource('users', UserController::class)->whereNumber('user');
        Route::resource('specialties', SpecialtyController::class)->whereNumber('specialty');
        Route::resource('accounts', AccountController::class)->only(['index', 'store', 'update', 'destroy'])->whereNumber('account');


        // ── Generic fallback (remove after all modules are migrated) ──
        Route::get('/{module}', [EntityController::class, 'index'])->name('entities.index');
        Route::get('/{module}/create', [EntityController::class, 'create'])->name('entities.create');
        Route::post('/{module}', [EntityController::class, 'store'])->name('entities.store');
        Route::get('/{module}/{record}/edit', [EntityController::class, 'edit'])->whereNumber('record')->name('entities.edit');
        Route::get('/{module}/{record}', [EntityController::class, 'show'])->whereNumber('record')->name('entities.show');
        Route::put('/{module}/{record}', [EntityController::class, 'update'])->whereNumber('record')->name('entities.update');
        Route::delete('/{module}/{record}', [EntityController::class, 'destroy'])->whereNumber('record')->name('entities.destroy');
        Route::post('/{module}/{record}/actions/{action}', [WorkflowController::class, 'action'])->whereNumber('record')->name('workflows.action');
    });
});
