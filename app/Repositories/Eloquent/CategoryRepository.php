<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function find(string $id): ?Category
    {
        return Category::find($id);
    }

    public function allForUser(string $userId): Collection
    {
        return Category::forUser($userId)->orderBy('name', 'asc')->get();
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category;
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
