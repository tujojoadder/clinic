<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id('id');
            $table->string('email', 190);
            $table->string('ip', 64);
            $table->dateTime('occurred_at')->useCurrent();
            $table->index([
                'email',
                'occurred_at',
            ]);
            $table->index([
                'ip',
                'occurred_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
