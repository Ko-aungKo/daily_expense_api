<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name'])]
class PaymentMethod extends Model
{
    use HasFactory, HasUlids;

    /**
     * Get the user that owns the payment method.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the expenses for this payment method.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Scope to return payment methods available to the given user:
     * system defaults (user_id is null) plus user's custom payment methods.
     */
    public function scopeForUser(Builder $query, ?string $userId): Builder
    {
        return $query->whereNull('user_id')
            ->orWhere('user_id', $userId);
    }
}
