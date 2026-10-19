<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reference_persons', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 150);
            $table->string('phone', 30);
            $table->decimal('commission_percent', 5, 2)->default(0);
            $table->date('joining_date')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reference_persons');
    }
};
