<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('visit_id')->nullable()->unique();
            $table->unsignedBigInteger('admission_id')->nullable()->unique();
            $table->decimal('discount', 12, 2)->default(0);
            $table->enum('status', [
                'open',
                'finalized',
                'void',
            ])->default('open');
            $table->text('notes')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
