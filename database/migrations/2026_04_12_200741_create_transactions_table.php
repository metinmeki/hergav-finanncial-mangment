<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('currency_id')->constrained()->onDelete('cascade');
            $table->foreignId('to_currency_id')->nullable()->constrained('currencies')->onDelete('cascade');
            $table->enum('type', ['deposit', 'withdrawal', 'exchange', 'adjustment']);
            $table->decimal('amount', 20, 4);
            $table->decimal('to_amount', 20, 4)->nullable();
            $table->decimal('exchange_rate', 20, 6)->nullable();
            $table->decimal('balance_before', 20, 4);
            $table->decimal('balance_after', 20, 4);
            $table->string('reference_no')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};