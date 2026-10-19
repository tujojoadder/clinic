<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctor_ledgers', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->enum('type', [
                'payable',
                'settlement',
            ]);
            $table->decimal('amount', 12, 2);
            $table->date('entry_date');
            $table->string('notes', 255);
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_ledgers');
    }
};
