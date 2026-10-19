<?php

use App\Http\Controllers\{DoctorController, PatientController, ReferenceController, SpecialtyController};
use App\Http\Middleware\{ActiveSession, SecurityHeaders};
use Illuminate\Support\Facades\Route;

Route::middleware([SecurityHeaders::class, 'auth', ActiveSession::class])
    ->prefix('ajax')
    ->name('ajax.')
    ->group(function () {

        Route::get('specialties/search', [SpecialtyController::class, 'search'])->name('specialties.search');
        Route::post('specialties/quick', [SpecialtyController::class, 'quickStore'])->name('specialties.quick');

        Route::get('doctors/{doctor}/fees', [DoctorController::class, 'fees'])
            ->whereNumber('doctor')->name('doctors.fees');

        // quick-add (modal)
        Route::post('patients', [PatientController::class, 'quickStore'])->name('patients.store');
        Route::post('doctors', [DoctorController::class, 'quickStore'])->name('doctors.store');
        Route::post('references', [ReferenceController::class, 'quickStore'])->name('references.store');


        /* slect2 */
        Route::get('patients/search', [PatientController::class, 'search'])->name('patients.search');
        Route::get('doctors/search', [DoctorController::class, 'search'])->name('doctors.search');
        Route::get('references/search', [ReferenceController::class, 'search'])->name('references.search');
    });
