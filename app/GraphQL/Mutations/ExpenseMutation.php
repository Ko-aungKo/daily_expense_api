<?php

namespace App\GraphQL\Mutations;

use App\DTOs\ExpenseDTO;
use App\Exceptions\ForbiddenException;
use App\Models\Expense;
use App\Models\User;
use App\Services\ExpenseService;
use App\ValueObjects\Amount;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ExpenseMutation
{
    public function __construct(
        protected ExpenseService $expenseService
    ) {}

    /**
     * Create an expense.
     */
    public function create($_, array $args, GraphQLContext $context): Expense
    {
        /** @var User $user */
        $user = $context->user();
        $userId = $user->id;

        Validator::make($args, [
            'amount' => ['required', 'numeric', 'gt:0'],
            'category_id' => [
                'required',
                'string',
                Rule::exists('categories', 'id')->where(function ($q) use ($userId) {
                    return $q->whereNull('user_id')->orWhere('user_id', $userId);
                }),
            ],
            'payment_method_id' => [
                'required',
                'string',
                Rule::exists('payment_methods', 'id')->where(function ($q) use ($userId) {
                    return $q->whereNull('user_id')->orWhere('user_id', $userId);
                }),
            ],
            'expense_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ])->validate();

        // Enforce the business domain rule via ValueObject
        new Amount($args['amount']);

        $dto = ExpenseDTO::fromArray($args);

        return $this->expenseService->createExpense($userId, $dto);
    }

    /**
     * Update an expense.
     */
    public function update($_, array $args, GraphQLContext $context): Expense
    {
        /** @var User $user */
        $user = $context->user();
        $userId = $user->id;

        $expense = Expense::findOrFail($args['id']);

        if ($expense->user_id !== $userId) {
            throw new ForbiddenException('You cannot update this expense.');
        }

        Validator::make($args, [
            'amount' => ['required', 'numeric', 'gt:0'],
            'category_id' => [
                'required',
                'string',
                Rule::exists('categories', 'id')->where(function ($q) use ($userId) {
                    return $q->whereNull('user_id')->orWhere('user_id', $userId);
                }),
            ],
            'payment_method_id' => [
                'required',
                'string',
                Rule::exists('payment_methods', 'id')->where(function ($q) use ($userId) {
                    return $q->whereNull('user_id')->orWhere('user_id', $userId);
                }),
            ],
            'expense_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ])->validate();

        // Enforce the business domain rule via ValueObject
        new Amount($args['amount']);

        $dto = ExpenseDTO::fromArray($args);

        return $this->expenseService->updateExpense($expense, $dto);
    }

    /**
     * Delete an expense.
     */
    public function delete($_, array $args, GraphQLContext $context): array
    {
        /** @var User $user */
        $user = $context->user();
        $userId = $user->id;

        $expense = Expense::findOrFail($args['id']);

        if ($expense->user_id !== $userId) {
            throw new ForbiddenException('You cannot delete this expense.');
        }

        $this->expenseService->deleteExpense($expense);

        return [
            'success' => true,
            'message' => 'Expense deleted successfully.',
        ];
    }
}
