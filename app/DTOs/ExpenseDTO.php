<?php

namespace App\DTOs;

readonly class ExpenseDTO
{
    public function __construct(
        public float $amount,
        public int $categoryId,
        public int $paymentMethodId,
        public string $expenseDate,
        public ?string $description = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            amount: (float) $data['amount'],
            categoryId: (int) ($data['category_id'] ?? $data['categoryId']),
            paymentMethodId: (int) ($data['payment_method_id'] ?? $data['paymentMethodId']),
            expenseDate: $data['expense_date'] ?? $data['expenseDate'],
            description: $data['description'] ?? null
        );
    }
}
