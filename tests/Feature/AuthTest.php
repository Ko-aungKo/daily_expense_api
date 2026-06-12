<?php

use App\Models\User;

test('user can register via GraphQL', function () {
    $response = $this->graphQL('
        mutation {
            register(name: "John Doe", email: "john@example.com", password: "password123") {
                user {
                    name
                    email
                }
                token
            }
        }
    ');

    $response->assertJsonStructure([
        'data' => [
            'register' => [
                'user' => ['name', 'email'],
                'token',
            ],
        ],
    ]);

    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
});

test('user can login via GraphQL', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->graphQL('
        mutation {
            login(email: "john@example.com", password: "password123") {
                user {
                    email
                }
                token
            }
        }
    ');

    $response->assertJsonStructure([
        'data' => [
            'login' => [
                'user' => ['email'],
                'token',
            ],
        ],
    ]);
});

test('authenticated user can query me', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        query {
            me {
                id
                name
                email
            }
        }
    ');

    $response->assertJson([
        'data' => [
            'me' => [
                'id' => (string) $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ],
    ]);
});

test('user can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->graphQL('
        mutation {
            logout {
                success
                message
            }
        }
    ');

    $response->assertJson([
        'data' => [
            'logout' => [
                'success' => true,
                'message' => 'Logged out successfully.',
            ],
        ],
    ]);
});
