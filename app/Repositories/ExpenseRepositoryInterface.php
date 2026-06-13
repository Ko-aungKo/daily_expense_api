<?php

namespace App\Repositories;

use App\DTOs\ExpenseDTO;
use App\DTOs\ExpenseQueryFiltersDTO;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;

interface ExpenseRepositoryInterface
{
    public function find(string $id): ?Expense;

    public function queryWithFilters(string $userId, ExpenseQueryFiltersDTO $filters): Builder;

    public function create(string $userId, ExpenseDTO $dto): Expense;

    public function update(Expense $expense, ExpenseDTO $dto): Expense;

    public function delete(Expense $expense): bool;

    public function getDashboardMetrics(string $userId): array;

    public function getMonthlySummaries(string $userId): array;
}
