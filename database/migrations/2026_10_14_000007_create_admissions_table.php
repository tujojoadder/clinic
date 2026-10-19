<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->dateTime('admitted_at');
            $table->text('reason');
            $table->enum('status', [
                'admitted',
                'discharged',
                'cancelled',
            ])->default('admitted');
            $table->dateTime('discharged_at')->nullable();
            $table->text('treatment_summary')->nullable();
            $table->text('followup_notes')->nullable();
            $table->enum('discharge_type', [
                'regular',
                'transfer',
                'own_risk',
            ])->default('regular');
            $table->dateTime('created_at')->useCurrent();
            $table->index([
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
