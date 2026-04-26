<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make sure all 4 currencies exist for every client
        $clients = DB::table('clients')->get();
        $currencies = DB::table('currencies')->whereIn('code', ['USD', 'IQD', 'EUR', 'TRY'])->get();

        foreach ($clients as $client) {
            foreach ($currencies as $currency) {
                $exists = DB::table('client_accounts')
                    ->where('client_id', $client->id)
                    ->where('currency_id', $currency->id)
                    ->exists();

                if (!$exists) {
                    DB::table('client_accounts')->insert([
                        'client_id'   => $client->id,
                        'currency_id' => $currency->id,
                        'balance'     => 0,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void {}
};