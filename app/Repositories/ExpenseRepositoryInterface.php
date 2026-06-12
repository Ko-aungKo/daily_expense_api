<?php

namespace App\Repositories;

use App\DTOs\ExpenseDTO;
use App\DTOs\ExpenseQueryFiltersDTO;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;

interface ExpenseRepositoryInterface
{
    public function find(int $id): ?Expense;

    public function queryWithFilters(int $userId, ExpenseQueryFiltersDTO $filters): Builder;

    public function create(int $userId, ExpenseDTO $dto): Expense;

    public function update(Expense $expense, ExpenseDTO $dto): Expense;

    public function delete(Expense $expense): bool;

    public function getDashboardMetrics(int $userId): array;

    public function getMonthlySummaries(int $userId): array;
}
