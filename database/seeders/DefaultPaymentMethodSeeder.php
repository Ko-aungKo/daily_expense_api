<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class DefaultPaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            ['name' => 'Cash'],
            ['name' => 'Debit Card'],
            ['name' => 'Credit Card'],
            ['name' => 'PromptPay'],
            ['name' => 'Bank Transfer'],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['user_id' => null, 'name' => $method['name']],
                $method
            );
        }
    }
}
