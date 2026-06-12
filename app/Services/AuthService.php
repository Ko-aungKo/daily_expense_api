<?php

namespace App\Services;

use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    /**
     * Register a new user and return user and auth token.
     */
    public function register(RegisterDTO $dto): array
    {
        $user = $this->userRepository->create($dto);
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Authenticate a user and return user and auth token.
     */
    public function login(LoginDTO $dto): array
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Log out a user by deleting their current access token.
     */
    public function logout(User $user): bool
    {
        /** @var mixed $token */
        $token = $user->currentAccessToken();
        if ($token) {
            return $token->delete();
        }

        // Fallback: delete all tokens for the user
        $user->tokens()->delete();

        return true;
    }
}
