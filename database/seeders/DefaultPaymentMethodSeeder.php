<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            ['name' => 'Cash', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Debit Card', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Credit Card', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PromptPay', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bank Transfer', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($methods as $method) {
            DB::table('payment_methods')->updateOrInsert(
                ['user_id' => null, 'name' => $method['name']],
                $method
            );
        }
    }
}
