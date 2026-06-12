<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->word().' '.fake()->unique()->randomNumber(4),
            'icon' => fake()->word(),
            'color' => fake()->hexColor(),
            'is_default' => false,
        ];
    }
}
