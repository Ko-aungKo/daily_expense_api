<?php

namespace App\GraphQL\Mutations;

use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
use App\Models\User;
use App\Services\AuthService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AuthMutation
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Handle user registration.
     */
    public function register($_, array $args): array
    {
        $dto = RegisterDTO::fromArray($args);

        return $this->authService->register($dto);
    }

    /**
     * Handle user login.
     */
    public function login($_, array $args): array
    {
        $dto = LoginDTO::fromArray($args);

        return $this->authService->login($dto);
    }

    /**
     * Handle user logout.
     */
    public function logout($_, array $args, GraphQLContext $context): array
    {
        $user = $context->user();
        if ($user instanceof User) {
            $this->authService->logout($user);

            return [
                'success' => true,
                'message' => 'Logged out successfully.',
            ];
        }

        return [
            'success' => false,
            'message' => 'No authenticated user found.',
        ];
    }
}
