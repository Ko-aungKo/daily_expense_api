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

    public function getExpensesQuery(int $userId, ExpenseQueryFiltersDTO $filters): Builder
    {
        return $this->expenseRepository->queryWithFilters($userId, $filters);
    }

    public function createExpense(int $userId, ExpenseDTO $dto): Expense
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

    public function getDashboardMetrics(int $userId): array
    {
        return $this->expenseRepository->getDashboardMetrics($userId);
    }

    public function getMonthlySummaries(int $userId): array
    {
        return $this->expenseRepository->getMonthlySummaries($userId);
    }
}
