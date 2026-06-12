<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Food', 'icon' => 'fastfood', 'color' => '#FF5733', 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Transport', 'icon' => 'directions_transit', 'color' => '#33C1FF', 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Shopping', 'icon' => 'shopping_bag', 'color' => '#FF33F6', 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Entertainment', 'icon' => 'sports_esports', 'color' => '#FFC133', 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bills', 'icon' => 'receipt_long', 'color' => '#8E44AD', 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Health', 'icon' => 'medical_services', 'color' => '#2ECC71', 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Education', 'icon' => 'school', 'color' => '#3498DB', 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Others', 'icon' => 'more_horiz', 'color' => '#95A5A6', 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['user_id' => null, 'name' => $category['name']],
                $category
            );
        }
    }
}
