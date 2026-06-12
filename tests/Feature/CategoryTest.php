<?php

use App\Models\Category;
use App\Models\User;
use Database\Seeders\DefaultCategorySeeder;

beforeEach(function () {
    // Seed default categories for every test
    $this->seed(DefaultCategorySeeder::class);
});

test('guest cannot access categories', function () {
    $response = $this->graphQL('
        query {
            categories {
                name
            }
        }
    ');
    $response->assertJson([
        'errors' => [
            ['message' => 'Unauthenticated.'],
        ],
    ]);
});

test('authenticated user can list categories including defaults', function () {
    $user = User::factory()->create();

    // Create a custom category for the user
    Category::factory()->create([
        'user_id' => $user->id,
        'name' => 'Custom Salary',
        'is_default' => false,
    ]);

    // Create a custom category for another user
    Category::factory()->create([
        'user_id' => User::factory()->create()->id,
        'name' => 'Secret Category',
        'is_default' => false,
    ]);

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        query {
            categories {
                name
                is_default
                user_id
            }
        }
    ');

    $response->assertJsonMissing([
        'name' => 'Secret Category',
    ]);

    $data = $response->json('data.categories');
    // Default categories + 1 custom category
    expect(count($data))->toBe(9);
});

test('user can create a custom category', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        mutation {
            createCategory(name: "Bonus", icon: "star", color: "#FF5733") {
                id
                name
                icon
                color
                is_default
            }
        }
    ');

    $response->assertJsonPath('data.createCategory.name', 'Bonus');
    $response->assertJsonPath('data.createCategory.is_default', false);

    $this->assertDatabaseHas('categories', [
        'user_id' => $user->id,
        'name' => 'Bonus',
    ]);
});

test('user cannot create a category with duplicate name', function () {
    $user = User::factory()->create();
    Category::factory()->create(['user_id' => $user->id, 'name' => 'Bonus']);

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        mutation {
            createCategory(name: "Bonus") {
                id
            }
        }
    ');

    $response->assertJsonStructure(['errors']);
});

test('user can update their own custom category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'name' => 'Old Name']);

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        mutation($id: ID!, $name: String!) {
            updateCategory(id: $id, name: $name) {
                id
                name
            }
        }
    ', [
        'id' => $category->id,
        'name' => 'New Name',
    ]);

    $response->assertJsonPath('data.updateCategory.name', 'New Name');
});

test('user cannot update default categories or other users custom categories', function () {
    $user = User::factory()->create();

    // System default category (user_id = null)
    $defaultCategory = Category::whereNull('user_id')->first();

    $responseDefault = $this->actingAs($user, 'sanctum')->graphQL('
        mutation($id: ID!, $name: String!) {
            updateCategory(id: $id, name: $name) {
                id
            }
        }
    ', [
        'id' => $defaultCategory->id,
        'name' => 'Modified Name',
    ]);

    $responseDefault->assertJsonStructure(['errors']);

    // Other user's custom category
    $otherCategory = Category::factory()->create([
        'user_id' => User::factory()->create()->id,
        'name' => 'Other Custom',
    ]);

    $responseOther = $this->actingAs($user, 'sanctum')->graphQL('
        mutation($id: ID!, $name: String!) {
            updateCategory(id: $id, name: $name) {
                id
            }
        }
    ', [
        'id' => $otherCategory->id,
        'name' => 'Modified Name',
    ]);

    $responseOther->assertJsonStructure(['errors']);
});

test('user can delete their own custom category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'name' => 'To Delete']);

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        mutation($id: ID!) {
            deleteCategory(id: $id) {
                success
                message
            }
        }
    ', [
        'id' => $category->id,
    ]);

    $response->assertJson([
        'data' => [
            'deleteCategory' => [
                'success' => true,
                'message' => 'Category deleted successfully.',
            ],
        ],
    ]);

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});
