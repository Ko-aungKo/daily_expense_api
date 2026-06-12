<?php

namespace App\Repositories;

use App\DTOs\RegisterDTO;
use App\Models\User;

interface UserRepositoryInterface
{
    public function create(RegisterDTO $dto): User;

    public function findByEmail(string $email): ?User;

    public function find(int $id): ?User;
}
