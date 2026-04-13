<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('currency_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 20, 4)->default(0);
            $table->timestamps();

            $table->unique(['client_id', 'currency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_accounts');
    }
};