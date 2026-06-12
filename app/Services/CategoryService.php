<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getCategoriesForUser(int $userId): Collection
    {
        return $this->categoryRepository->allForUser($userId);
    }

    public function createCategory(int $userId, array $data): Category
    {
        $data['user_id'] = $userId;
        $data['is_default'] = false; // Custom category

        return $this->categoryRepository->create($data);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        return $this->categoryRepository->update($category, $data);
    }

    public function deleteCategory(Category $category): bool
    {
        return $this->categoryRepository->delete($category);
    }
}
