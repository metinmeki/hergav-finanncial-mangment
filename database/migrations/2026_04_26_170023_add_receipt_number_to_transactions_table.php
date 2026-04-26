<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add receipt_number to transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('receipt_number')->nullable()->after('reference_no');
        });

        // Add receipt_number to bailment_transactions
        Schema::table('bailment_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('receipt_number')->nullable()->after('reference_no');
        });

        // Create receipt counter table
        Schema::create('receipt_counters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('last_number')->default(0);
            $table->timestamps();
        });

        // Insert initial counter
        DB::table('receipt_counters')->insert(['last_number' => 0]);
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('receipt_number');
        });
        Schema::table('bailment_transactions', function (Blueprint $table) {
            $table->dropColumn('receipt_number');
        });
        Schema::dropIfExists('receipt_counters');
    }
};