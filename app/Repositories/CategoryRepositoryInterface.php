<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function find(string $id): ?Category;

    public function allForUser(string $userId): Collection;

    public function create(array $data): Category;

    public function update(Category $category, array $data): Category;

    public function delete(Category $category): bool;
}
