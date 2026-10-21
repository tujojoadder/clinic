<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('invoice_id');
            $table->decimal('amount', 12, 2);
            $table->enum('type', [
                'receipt',
                'refund',
            ])->default('receipt');
            $table->unsignedBigInteger('account_id')->nullable();

            $table->string('transaction_ref', 150)->nullable();
            $table->string('reason', 255)->nullable();
            $table->dateTime('paid_at');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->dateTime('voided_at')->nullable();
            $table->string('void_reason', 255)->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index([
                'paid_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
