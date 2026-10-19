<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 150);
            $table->date('birth_date')->nullable();
            $table->unsignedSmallInteger('age_years')->nullable();
            $table->unsignedSmallInteger('reference_id')->nullable();
            $table->enum('gender', [
                'female',
                'male',
                'other',
            ]);
            $table->string('phone', 30);
            $table->text('address')->nullable();
            $table->string('emergency_contact', 150)->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index([
                'phone',
            ]);
            $table->index([
                'name',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
