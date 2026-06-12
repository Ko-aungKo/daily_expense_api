<?php

use App\Models\PaymentMethod;
use App\Models\User;
use Database\Seeders\DefaultPaymentMethodSeeder;

beforeEach(function () {
    // Seed default payment methods for every test
    $this->seed(DefaultPaymentMethodSeeder::class);
});

test('guest cannot access payment methods', function () {
    $response = $this->graphQL('
        query {
            paymentMethods {
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

test('authenticated user can list payment methods including defaults', function () {
    $user = User::factory()->create();

    // Create custom payment method
    PaymentMethod::factory()->create([
        'user_id' => $user->id,
        'name' => 'Custom Crypto Wallet',
    ]);

    // Create custom for another user
    PaymentMethod::factory()->create([
        'user_id' => User::factory()->create()->id,
        'name' => 'Secret Bank Account',
    ]);

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        query {
            paymentMethods {
                name
                user_id
            }
        }
    ');

    $response->assertJsonMissing([
        'name' => 'Secret Bank Account',
    ]);

    $data = $response->json('data.paymentMethods');
    // 5 defaults + 1 custom
    expect(count($data))->toBe(6);
});

test('user can create a custom payment method', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        mutation {
            createPaymentMethod(name: "Bitcoin Wallet") {
                id
                name
            }
        }
    ');

    $response->assertJsonPath('data.createPaymentMethod.name', 'Bitcoin Wallet');

    $this->assertDatabaseHas('payment_methods', [
        'user_id' => $user->id,
        'name' => 'Bitcoin Wallet',
    ]);
});

test('user cannot create a duplicate payment method', function () {
    $user = User::factory()->create();
    PaymentMethod::factory()->create(['user_id' => $user->id, 'name' => 'Cash Back']);

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        mutation {
            createPaymentMethod(name: "Cash Back") {
                id
            }
        }
    ');

    $response->assertJsonStructure(['errors']);
});

test('user can update their own custom payment method', function () {
    $user = User::factory()->create();
    $pm = PaymentMethod::factory()->create(['user_id' => $user->id, 'name' => 'Old Wallet']);

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        mutation($id: ID!, $name: String!) {
            updatePaymentMethod(id: $id, name: $name) {
                id
                name
            }
        }
    ', [
        'id' => $pm->id,
        'name' => 'New Wallet',
    ]);

    $response->assertJsonPath('data.updatePaymentMethod.name', 'New Wallet');
});

test('user cannot update default or other users payment methods', function () {
    $user = User::factory()->create();

    // System default
    $defaultPm = PaymentMethod::whereNull('user_id')->first();

    $responseDefault = $this->actingAs($user, 'sanctum')->graphQL('
        mutation($id: ID!, $name: String!) {
            updatePaymentMethod(id: $id, name: $name) {
                id
            }
        }
    ', [
        'id' => $defaultPm->id,
        'name' => 'Bad Update',
    ]);

    $responseDefault->assertJsonStructure(['errors']);

    // Other user
    $otherPm = PaymentMethod::factory()->create([
        'user_id' => User::factory()->create()->id,
        'name' => 'Other Wallet',
    ]);

    $responseOther = $this->actingAs($user, 'sanctum')->graphQL('
        mutation($id: ID!, $name: String!) {
            updatePaymentMethod(id: $id, name: $name) {
                id
            }
        }
    ', [
        'id' => $otherPm->id,
        'name' => 'Bad Update',
    ]);

    $responseOther->assertJsonStructure(['errors']);
});

test('user can delete their own payment method', function () {
    $user = User::factory()->create();
    $pm = PaymentMethod::factory()->create(['user_id' => $user->id, 'name' => 'To Delete']);

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        mutation($id: ID!) {
            deletePaymentMethod(id: $id) {
                success
                message
            }
        }
    ', [
        'id' => $pm->id,
    ]);

    $response->assertJson([
        'data' => [
            'deletePaymentMethod' => [
                'success' => true,
                'message' => 'Payment method deleted successfully.',
            ],
        ],
    ]);

    $this->assertDatabaseMissing('payment_methods', ['id' => $pm->id]);
});
