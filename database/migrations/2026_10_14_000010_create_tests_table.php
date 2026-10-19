<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 150)->unique();
            $table->string('sample_type', 80);
            $table->decimal('price', 12, 2);
            $table->unsignedSmallInteger('turnaround_hours')->default(24);
            $table->tinyInteger('active')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
