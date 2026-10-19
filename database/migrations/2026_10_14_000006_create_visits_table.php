<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->dateTime('appointment_at');
            $table->unsignedInteger('serial_no')->nullable();
            $table->enum('visit_type', [
                'new',
                'followup',
            ])->default('new');
            $table->decimal('fee', 12, 2);
            $table->enum('status', [
                'waiting',
                'completed',
                'cancelled',
            ])->default('waiting');
            $table->text('notes')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->unique([
                'doctor_id',
                'appointment_at',
                'serial_no',
            ]);
            $table->index([
                'appointment_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
