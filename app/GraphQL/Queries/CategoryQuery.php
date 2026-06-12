<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use App\Services\CategoryService;
use Illuminate\Database\Eloquent\Collection;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CategoryQuery
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    /**
     * Resolve custom and system categories for the current user.
     */
    public function resolve($_, array $args, GraphQLContext $context): Collection
    {
        /** @var User $user */
        $user = $context->user();

        return $this->categoryService->getCategoriesForUser($user->id);
    }
}
