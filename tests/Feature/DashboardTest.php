<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\PaymentMethod;
use App\Models\User;

test('authenticated user can query dashboard metrics', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $pm = PaymentMethod::factory()->create(['user_id' => $user->id]);

    // Create expenses on today, this month, this year
    Expense::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'payment_method_id' => $pm->id,
        'amount' => 100.00,
        'expense_date' => now()->toDateString(),
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'payment_method_id' => $pm->id,
        'amount' => 250.00,
        'expense_date' => now()->startOfMonth()->toDateString(),
    ]);

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        query {
            dashboard {
                totalSpentToday
                totalSpentThisMonth
                totalSpentThisYear
                expenseCount
                topCategories {
                    category {
                        id
                    }
                    totalAmount
                    count
                }
                recentExpenses {
                    amount
                }
            }
        }
    ');

    $response->assertJsonStructure([
        'data' => [
            'dashboard' => [
                'totalSpentToday',
                'totalSpentThisMonth',
                'totalSpentThisYear',
                'expenseCount',
                'topCategories' => [
                    '*' => [
                        'category' => ['id'],
                        'totalAmount',
                        'count',
                    ],
                ],
                'recentExpenses' => [
                    '*' => ['amount'],
                ],
            ],
        ],
    ]);

    $data = $response->json('data.dashboard');
    expect($data['totalSpentToday'])->toEqual(100.00);
    // 100 + 250 = 350
    expect($data['totalSpentThisMonth'])->toEqual(350.00);
    expect($data['expenseCount'])->toBe(2);
});

test('authenticated user can query monthly summaries', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $pm = PaymentMethod::factory()->create(['user_id' => $user->id]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'payment_method_id' => $pm->id,
        'amount' => 100.00,
        'expense_date' => '2026-06-01',
    ]);

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        query {
            monthlySummaries {
                month
                totalSpent
                expenseCount
            }
        }
    ');

    $response->assertJsonStructure([
        'data' => [
            'monthlySummaries' => [
                '*' => [
                    'month',
                    'totalSpent',
                    'expenseCount',
                ],
            ],
        ],
    ]);

    $summaries = $response->json('data.monthlySummaries');
    expect($summaries[0]['month'])->toBe('2026-06');
    expect($summaries[0]['totalSpent'])->toEqual(100.00);
    expect($summaries[0]['expenseCount'])->toBe(1);
});
