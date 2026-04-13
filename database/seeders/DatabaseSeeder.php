<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('branches')->insert([
            'name' => 'Main Branch',
            'name_ar' => 'الفرع الرئيسي',
            'phone' => '+964 750 000 0000',
            'address' => 'Sulaymaniyah, Iraq',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@hergav.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'branch_id' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('currencies')->insert([
            ['code' => 'USD', 'name' => 'US Dollar',     'symbol' => '$',  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'IQD', 'name' => 'Iraqi Dinar',   'symbol' => 'IQD','is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'EUR', 'name' => 'Euro',           'symbol' => '€',  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'TRY', 'name' => 'Turkish Lira',  'symbol' => '₺',  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $usd = DB::table('currencies')->where('code', 'USD')->first();
        $iqd = DB::table('currencies')->where('code', 'IQD')->first();
        $eur = DB::table('currencies')->where('code', 'EUR')->first();
        $try = DB::table('currencies')->where('code', 'TRY')->first();
        $admin = DB::table('users')->first();
        $branch = DB::table('branches')->first();

        $pairs = [
            [$usd->id, $iqd->id, 1480],
            [$usd->id, $eur->id, 0.92],
            [$usd->id, $try->id, 32.50],
            [$eur->id, $iqd->id, 1608],
            [$try->id, $iqd->id, 45.50],
        ];

        foreach ($pairs as [$from, $to, $rate]) {
            DB::table('exchange_rates')->insert([
                'from_currency_id' => $from,
                'to_currency_id' => $to,
                'rate' => $rate,
                'rate_date' => today(),
                'set_by' => $admin->id,
                'branch_id' => $branch->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}