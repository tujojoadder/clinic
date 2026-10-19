<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bed_stays', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('admission_id')->nullable();
            $table->unsignedBigInteger('bed_id')->nullable();
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->decimal('daily_rate', 12, 2);
            $table->index([
                'bed_id',
                'ended_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bed_stays');
    }
};
