<?php

namespace App\GraphQL\Queries;

use App\DTOs\ExpenseQueryFiltersDTO;
use App\Models\User;
use App\Services\ExpenseService;
use Illuminate\Database\Eloquent\Builder;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ExpenseQuery
{
    public function __construct(
        protected ExpenseService $expenseService
    ) {}

    /**
     * Resolve the expenses query by returning an Eloquent builder.
     */
    public function resolve($_, array $args, GraphQLContext $context): Builder
    {
        /** @var User $user */
        $user = $context->user();

        $filters = ExpenseQueryFiltersDTO::fromArray($args);

        return $this->expenseService->getExpensesQuery($user->id, $filters);
    }
}
