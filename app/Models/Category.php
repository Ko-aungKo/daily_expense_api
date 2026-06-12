<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'icon', 'color', 'is_default'])]
class Category extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the category.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the expenses for this category.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Scope to return categories available to the given user:
     * system defaults (user_id is null) plus user's custom categories.
     */
    public function scopeForUser(Builder $query, ?int $userId): Builder
    {
        return $query->whereNull('user_id')
            ->orWhere('user_id', $userId);
    }
}
