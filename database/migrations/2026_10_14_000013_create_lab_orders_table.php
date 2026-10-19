<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('admission_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('test_id')->nullable();
            $table->enum('status', [
                'ordered',
                'collected',
                'processing',
                'review',
                'approved',
                'cancelled',
            ])->default('ordered');
            $table->dateTime('ordered_at');
            $table->dateTime('collected_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('report_notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_orders');
    }
};
