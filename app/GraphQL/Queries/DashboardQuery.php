<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use App\Services\ExpenseService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DashboardQuery
{
    public function __construct(
        protected ExpenseService $expenseService
    ) {}

    /**
     * Resolve dashboard metrics query.
     */
    public function resolve($_, array $args, GraphQLContext $context): array
    {
        /** @var User $user */
        $user = $context->user();

        return $this->expenseService->getDashboardMetrics($user->id);
    }
}
