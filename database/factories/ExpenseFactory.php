<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Expense;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'amount' => fake()->randomFloat(2, 5, 500),
            'description' => fake()->sentence(),
            'expense_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        ];
    }
}
