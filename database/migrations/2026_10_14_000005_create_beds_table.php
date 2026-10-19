<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('beds', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 80)->unique();
            $table->string('ward', 100);
            $table->enum('type', [
                'ward',
                'cabin',
                'icu',
            ])->default('ward');
            $table->decimal('daily_rate', 12, 2);
            $table->enum('state', [
                'available',
                'cleaning',
                'inactive',
            ])->default('available');
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};
