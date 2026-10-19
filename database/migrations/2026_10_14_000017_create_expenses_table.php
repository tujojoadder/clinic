<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id('id');
            $table->string('category', 100);
            $table->string('description', 255);
            $table->decimal('amount', 12, 2);
            $table->enum('method', [
                'cash',
                'bkash',
                'nagad',
                'bank',
            ]);
            $table->date('expense_date');
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
