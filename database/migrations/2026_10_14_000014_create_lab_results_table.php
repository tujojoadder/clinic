<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('parameter_name', 150);
            $table->string('result_value', 150);
            $table->string('unit', 60)->nullable();
            $table->string('reference_range', 150)->nullable();

            $table->unique([
                'order_id',
                'parameter_name',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_results');
    }
};
