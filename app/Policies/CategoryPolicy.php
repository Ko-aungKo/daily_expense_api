<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can view the category.
     */
    public function view(User $user, Category $category): bool
    {
        return is_null($category->user_id) || $category->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the category.
     */
    public function update(User $user, Category $category): bool
    {
        return ! is_null($category->user_id) && $category->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the category.
     */
    public function delete(User $user, Category $category): bool
    {
        return ! is_null($category->user_id) && $category->user_id === $user->id;
    }
}
