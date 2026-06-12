<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\ForbiddenException;
use App\Models\Category;
use App\Models\User;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CategoryMutation
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    /**
     * Create a category.
     */
    public function create($_, array $args, GraphQLContext $context): Category
    {
        /** @var User $user */
        $user = $context->user();
        $userId = $user->id;

        Validator::make($args, [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->where(function ($q) use ($userId) {
                    return $q->where('user_id', $userId)->orWhereNull('user_id');
                }),
            ],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{3,6}$/'],
        ])->validate();

        return $this->categoryService->createCategory($userId, $args);
    }

    /**
     * Update a category.
     */
    public function update($_, array $args, GraphQLContext $context): Category
    {
        /** @var User $user */
        $user = $context->user();
        $userId = $user->id;

        $category = Category::findOrFail($args['id']);

        if ($category->user_id !== $userId) {
            throw new ForbiddenException('You cannot update this category.');
        }

        Validator::make($args, [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id)->where(function ($q) use ($userId) {
                    return $q->where('user_id', $userId)->orWhereNull('user_id');
                }),
            ],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{3,6}$/'],
        ])->validate();

        return $this->categoryService->updateCategory($category, $args);
    }

    /**
     * Delete a category.
     */
    public function delete($_, array $args, GraphQLContext $context): array
    {
        /** @var User $user */
        $user = $context->user();
        $userId = $user->id;

        $category = Category::findOrFail($args['id']);

        if ($category->user_id !== $userId) {
            throw new ForbiddenException('You cannot delete this category.');
        }

        $this->categoryService->deleteCategory($category);

        return [
            'success' => true,
            'message' => 'Category deleted successfully.',
        ];
    }
}
