<?php

namespace App\Services;

use App\DTOs\ExpenseDTO;
use App\DTOs\ExpenseQueryFiltersDTO;
use App\Models\Expense;
use App\Repositories\ExpenseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class ExpenseService
{
    public function __construct(
        protected ExpenseRepositoryInterface $expenseRepository
    ) {}

    public function getExpensesQuery(string $userId, ExpenseQueryFiltersDTO $filters): Builder
    {
        return $this->expenseRepository->queryWithFilters($userId, $filters);
    }

    public function createExpense(string $userId, ExpenseDTO $dto): Expense
    {
        return $this->expenseRepository->create($userId, $dto);
    }

    public function updateExpense(Expense $expense, ExpenseDTO $dto): Expense
    {
        return $this->expenseRepository->update($expense, $dto);
    }

    public function deleteExpense(Expense $expense): bool
    {
        return $this->expenseRepository->delete($expense);
    }

    public function getDashboardMetrics(string $userId): array
    {
        return $this->expenseRepository->getDashboardMetrics($userId);
    }

    public function getMonthlySummaries(string $userId): array
    {
        return $this->expenseRepository->getMonthlySummaries($userId);
    }
}
