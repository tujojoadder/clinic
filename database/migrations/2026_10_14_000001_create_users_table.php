<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 150);
            $table->string('email', 190)->unique();
            $table->string('password', 255);
            $table->string('remember_token', 100)->nullable();
            $table->enum('role', [
                'admin',
                'reception',
                'doctor',
                'nurse',
                'lab',
                'cashier',
            ]);
            $table->tinyInteger('active')->default(1);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
