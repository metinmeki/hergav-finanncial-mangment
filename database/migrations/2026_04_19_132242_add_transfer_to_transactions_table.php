<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('transfer_to_client_id')->nullable()->after('client_id');
            $table->unsignedBigInteger('transfer_from_client_id')->nullable()->after('transfer_to_client_id');
            $table->string('transfer_reference')->nullable()->after('reference_no');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['transfer_to_client_id', 'transfer_from_client_id', 'transfer_reference']);
        });
    }
};