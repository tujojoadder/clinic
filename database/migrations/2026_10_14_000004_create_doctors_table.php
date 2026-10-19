<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 150);
            $table->unsignedBigInteger('specialty_id')->nullable();
            $table->string('phone', 30)->nullable();
            $table->decimal('fee', 12, 2)->default(0);
            $table->decimal('followup_fee', 12, 2)->default(0);
            $table->string('available_days', 150)->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
