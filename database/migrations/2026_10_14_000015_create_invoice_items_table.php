<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('description', 255);
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->enum('kind', [
                'visit',
                'bed',
                'test',
                'service',
            ])->default('service');
            $table->unsignedBigInteger('bed_stay_id')->nullable()->unique();
            $table->unsignedBigInteger('lab_order_id')->nullable()->unique();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
