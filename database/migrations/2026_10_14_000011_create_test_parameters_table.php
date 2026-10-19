<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('test_parameters', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('test_id')->nullable();
            $table->string('name', 150);
            $table->string('unit', 60)->nullable();
            $table->string('reference_range', 150)->nullable();
            $table->integer('sort_order')->default(0);
            $table->unique([
                'test_id',
                'name',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_parameters');
    }
};
