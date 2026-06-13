<?php

namespace App\Repositories\Eloquent;

use App\DTOs\ExpenseDTO;
use App\DTOs\ExpenseQueryFiltersDTO;
use App\Models\Expense;
use App\Repositories\ExpenseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function find(string $id): ?Expense
    {
        return Expense::find($id);
    }

    public function queryWithFilters(string $userId, ExpenseQueryFiltersDTO $filters): Builder
    {
        return Expense::where('user_id', $userId)
            ->with(['category', 'paymentMethod'])
            ->filterDateRange($filters->startDate, $filters->endDate)
            ->filterCategory($filters->categoryId)
            ->filterPaymentMethod($filters->paymentMethodId)
            ->filterAmountRange($filters->minAmount, $filters->maxAmount)
            ->searchDescription($filters->search)
            ->sort($filters->sort);
    }

    public function create(string $userId, ExpenseDTO $dto): Expense
    {
        return Expense::create([
            'user_id' => $userId,
            'category_id' => $dto->categoryId,
            'payment_method_id' => $dto->paymentMethodId,
            'amount' => $dto->amount,
            'description' => $dto->description,
            'expense_date' => $dto->expenseDate,
        ]);
    }

    public function update(Expense $expense, ExpenseDTO $dto): Expense
    {
        $expense->update([
            'category_id' => $dto->categoryId,
            'payment_method_id' => $dto->paymentMethodId,
            'amount' => $dto->amount,
            'description' => $dto->description,
            'expense_date' => $dto->expenseDate,
        ]);

        return $expense->load(['category', 'paymentMethod']);
    }

    public function delete(Expense $expense): bool
    {
        return $expense->delete();
    }

    public function getDashboardMetrics(string $userId): array
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();
        $startOfYear = now()->startOfYear()->toDateString();
        $endOfYear = now()->endOfYear()->toDateString();

        $todaySum = Expense::where('user_id', $userId)
            ->where('expense_date', $today)
            ->sum('amount');

        $monthSum = Expense::where('user_id', $userId)
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $yearSum = Expense::where('user_id', $userId)
            ->whereBetween('expense_date', [$startOfYear, $endOfYear])
            ->sum('amount');

        // Top categories (grouped by category, eager loading details)
        $topCategories = Expense::where('user_id', $userId)
            ->selectRaw('category_id, SUM(amount) as total_amount, COUNT(*) as count')
            ->groupBy('category_id')
            ->orderBy('total_amount', 'desc')
            ->take(5)
            ->get()
            ->map(function (Expense $item) {
                return [
                    'category' => $item->category,
                    'totalAmount' => (float) $item->total_amount,
                    'count' => (int) $item->count,
                ];
            });

        // Recent expenses (load associations)
        $recentExpenses = Expense::where('user_id', $userId)
            ->with(['category', 'paymentMethod'])
            ->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $expenseCount = Expense::where('user_id', $userId)->count();

        return [
            'totalSpentToday' => (float) $todaySum,
            'totalSpentThisMonth' => (float) $monthSum,
            'totalSpentThisYear' => (float) $yearSum,
            'topCategories' => $topCategories,
            'recentExpenses' => $recentExpenses,
            'expenseCount' => (int) $expenseCount,
        ];
    }

    public function getMonthlySummaries(string $userId): array
    {
        return Expense::where('user_id', $userId)
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as total_spent, COUNT(*) as expense_count")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function (Expense $item) {
                return [
                    'month' => $item->month,
                    'totalSpent' => (float) $item->total_spent,
                    'expenseCount' => (int) $item->expense_count,
                ];
            })
            ->toArray();
    }
}
