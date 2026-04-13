<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('sender_name')->nullable()->after('notes');
            $table->string('sender_phone')->nullable()->after('sender_name');
            $table->string('original_currency')->nullable()->after('sender_phone');
            $table->decimal('original_amount', 20, 4)->nullable()->after('original_currency');
            $table->decimal('original_rate', 20, 6)->nullable()->after('original_amount');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['sender_name', 'sender_phone', 'original_currency', 'original_amount', 'original_rate']);
        });
    }
};