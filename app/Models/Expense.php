<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property float|null $total_amount
 * @property int|null $count
 * @property string|null $month
 * @property float|null $total_spent
 * @property int|null $expense_count
 */
#[Fillable(['user_id', 'category_id', 'payment_method_id', 'amount', 'description', 'expense_date'])]
class Expense extends Model
{
    use HasFactory, SoftDeletes, HasUlids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    /**
     * Get the user that owns the expense.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category associated with the expense.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the payment method associated with the expense.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeFilterDateRange(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        if ($startDate) {
            $query->where('expense_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('expense_date', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Scope to filter by category.
     */
    public function scopeFilterCategory(Builder $query, ?string $categoryId): Builder
    {
        return $categoryId ? $query->where('category_id', $categoryId) : $query;
    }

    /**
     * Scope to filter by payment method.
     */
    public function scopeFilterPaymentMethod(Builder $query, ?string $paymentMethodId): Builder
    {
        return $paymentMethodId ? $query->where('payment_method_id', $paymentMethodId) : $query;
    }

    /**
     * Scope to filter by amount range.
     */
    public function scopeFilterAmountRange(Builder $query, ?float $minAmount, ?float $maxAmount): Builder
    {
        if ($minAmount !== null) {
            $query->where('amount', '>=', $minAmount);
        }
        if ($maxAmount !== null) {
            $query->where('amount', '<=', $maxAmount);
        }

        return $query;
    }

    /**
     * Scope to search description.
     */
    public function scopeSearchDescription(Builder $query, ?string $search): Builder
    {
        return $search ? $query->where('description', 'like', '%'.$search.'%') : $query;
    }

    /**
     * Scope to sort results.
     */
    public function scopeSort(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'oldest' => $query->orderBy('expense_date', 'asc')->orderBy('id', 'asc'),
            'highest_amount' => $query->orderBy('amount', 'desc')->orderBy('id', 'desc'),
            'lowest_amount' => $query->orderBy('amount', 'asc')->orderBy('id', 'asc'),
            'newest', null => $query->orderBy('expense_date', 'desc')->orderBy('id', 'desc'),
            default => $query->orderBy('expense_date', 'desc')->orderBy('id', 'desc'),
        };
    }
}
