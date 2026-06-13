<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class DefaultCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Food', 'icon' => 'fastfood', 'color' => '#FF5733', 'is_default' => true],
            ['name' => 'Transport', 'icon' => 'directions_transit', 'color' => '#33C1FF', 'is_default' => true],
            ['name' => 'Shopping', 'icon' => 'shopping_bag', 'color' => '#FF33F6', 'is_default' => true],
            ['name' => 'Entertainment', 'icon' => 'sports_esports', 'color' => '#FFC133', 'is_default' => true],
            ['name' => 'Bills', 'icon' => 'receipt_long', 'color' => '#8E44AD', 'is_default' => true],
            ['name' => 'Health', 'icon' => 'medical_services', 'color' => '#2ECC71', 'is_default' => true],
            ['name' => 'Education', 'icon' => 'school', 'color' => '#3498DB', 'is_default' => true],
            ['name' => 'Others', 'icon' => 'more_horiz', 'color' => '#95A5A6', 'is_default' => true],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['user_id' => null, 'name' => $category['name']],
                $category
            );
        }
    }
}
