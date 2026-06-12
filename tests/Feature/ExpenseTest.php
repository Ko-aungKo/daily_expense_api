<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\PaymentMethod;
use App\Models\User;

test('guest cannot access expenses', function () {
    $response = $this->graphQL('
        query {
            expenses {
                edges {
                    node {
                        amount
                    }
                }
            }
        }
    ');
    $response->assertJson([
        'errors' => [
            ['message' => 'Unauthenticated.'],
        ],
    ]);
});

test('user can create expense with valid inputs', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $pm = PaymentMethod::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        mutation($amount: Float!, $catId: ID!, $pmId: ID!, $date: Date!, $desc: String) {
            createExpense(amount: $amount, category_id: $catId, payment_method_id: $pmId, expense_date: $date, description: $desc) {
                id
                amount
                expense_date
                description
                category {
                    id
                }
                paymentMethod {
                    id
                }
            }
        }
    ', [
        'amount' => 120.50,
        'catId' => $category->id,
        'pmId' => $pm->id,
        'date' => '2026-06-09',
        'desc' => 'Lunch meeting',
    ]);

    $response->assertJsonPath('data.createExpense.amount', 120.50);
    $response->assertJsonPath('data.createExpense.description', 'Lunch meeting');

    $this->assertDatabaseHas('expenses', [
        'user_id' => $user->id,
        'amount' => 120.50,
        'description' => 'Lunch meeting',
    ]);
});

test('user cannot create expense with negative or zero amount', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $pm = PaymentMethod::factory()->create(['user_id' => $user->id]);

    // Negative amount
    $responseNeg = $this->actingAs($user, 'sanctum')->graphQL('
        mutation($amount: Float!, $catId: ID!, $pmId: ID!, $date: Date!) {
            createExpense(amount: $amount, category_id: $catId, payment_method_id: $pmId, expense_date: $date) {
                id
            }
        }
    ', [
        'amount' => -50.00,
        'catId' => $category->id,
        'pmId' => $pm->id,
        'date' => '2026-06-09',
    ]);
    $responseNeg->assertJsonStructure(['errors']);

    // Zero amount
    $responseZero = $this->actingAs($user, 'sanctum')->graphQL('
        mutation($amount: Float!, $catId: ID!, $pmId: ID!, $date: Date!) {
            createExpense(amount: $amount, category_id: $catId, payment_method_id: $pmId, expense_date: $date) {
                id
            }
        }
    ', [
        'amount' => 0.00,
        'catId' => $category->id,
        'pmId' => $pm->id,
        'date' => '2026-06-09',
    ]);
    $responseZero->assertJsonStructure(['errors']);
});

test('user can update their own expense', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $pm = PaymentMethod::factory()->create(['user_id' => $user->id]);
    $expense = Expense::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'payment_method_id' => $pm->id,
        'amount' => 100.00,
    ]);

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        mutation($id: ID!, $amount: Float!, $catId: ID!, $pmId: ID!, $date: Date!) {
            updateExpense(id: $id, amount: $amount, category_id: $catId, payment_method_id: $pmId, expense_date: $date) {
                id
                amount
            }
        }
    ', [
        'id' => $expense->id,
        'amount' => 150.00,
        'catId' => $category->id,
        'pmId' => $pm->id,
        'date' => '2026-06-09',
    ]);

    expect($response->json('data.updateExpense.amount'))->toEqual(150.00);
});

test('user cannot update other users expense', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $expense = Expense::factory()->create(['user_id' => $otherUser->id]);

    $category = Category::factory()->create(['user_id' => $user->id]);
    $pm = PaymentMethod::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        mutation($id: ID!, $amount: Float!, $catId: ID!, $pmId: ID!, $date: Date!) {
            updateExpense(id: $id, amount: $amount, category_id: $catId, payment_method_id: $pmId, expense_date: $date) {
                id
            }
        }
    ', [
        'id' => $expense->id,
        'amount' => 150.00,
        'catId' => $category->id,
        'pmId' => $pm->id,
        'date' => '2026-06-09',
    ]);

    $response->assertJsonStructure(['errors']);
});

test('user can delete their own expense (soft deletes)', function () {
    $user = User::factory()->create();
    $expense = Expense::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        mutation($id: ID!) {
            deleteExpense(id: $id) {
                success
                message
            }
        }
    ', [
        'id' => $expense->id,
    ]);

    $response->assertJson([
        'data' => [
            'deleteExpense' => [
                'success' => true,
                'message' => 'Expense deleted successfully.',
            ],
        ],
    ]);

    // Check it is soft deleted in the database
    $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
});

test('user can query and filter expenses with cursor pagination', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $pm = PaymentMethod::factory()->create(['user_id' => $user->id]);

    // Create a few expenses
    Expense::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'payment_method_id' => $pm->id,
        'amount' => 50.00,
        'expense_date' => '2026-06-01',
        'description' => 'Target Search',
    ]);
    Expense::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'payment_method_id' => $pm->id,
        'amount' => 150.00,
        'expense_date' => '2026-06-05',
        'description' => 'Grocery shopping',
    ]);

    // Filter by description search
    $response = $this->actingAs($user, 'sanctum')->graphQL('
        query($search: String) {
            expenses(search: $search) {
                edges {
                    node {
                        amount
                        description
                    }
                }
                pageInfo {
                    hasNextPage
                }
            }
        }
    ', [
        'search' => 'Target',
    ]);

    $edges = $response->json('data.expenses.edges');
    expect(count($edges))->toBe(1);
    expect($edges[0]['node']['description'])->toBe('Target Search');
    expect($edges[0]['node']['amount'])->toEqual(50.00);
});
