<?php

namespace App\DTOs;

readonly class ExpenseDTO
{
    public function __construct(
        public float $amount,
        public string $categoryId,
        public string $paymentMethodId,
        public string $expenseDate,
        public ?string $description = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            amount: (float) $data['amount'],
            categoryId: (string) ($data['category_id'] ?? $data['categoryId']),
            paymentMethodId: (string) ($data['payment_method_id'] ?? $data['paymentMethodId']),
            expenseDate: $data['expense_date'] ?? $data['expenseDate'],
            description: $data['description'] ?? null
        );
    }
}
