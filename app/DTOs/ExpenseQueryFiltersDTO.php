<?php

namespace App\DTOs;

readonly class ExpenseQueryFiltersDTO
{
    public function __construct(
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?string $categoryId = null,
        public ?string $paymentMethodId = null,
        public ?float $minAmount = null,
        public ?float $maxAmount = null,
        public ?string $search = null,
        public ?string $sort = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            startDate: $data['startDate'] ?? $data['start_date'] ?? null,
            endDate: $data['endDate'] ?? $data['end_date'] ?? null,
            categoryId: isset($data['categoryId']) ? (string) $data['categoryId'] : (isset($data['category_id']) ? (string) $data['category_id'] : null),
            paymentMethodId: isset($data['paymentMethodId']) ? (string) $data['paymentMethodId'] : (isset($data['payment_method_id']) ? (string) $data['payment_method_id'] : null),
            minAmount: isset($data['minAmount']) ? (float) $data['minAmount'] : (isset($data['min_amount']) ? (float) $data['min_amount'] : null),
            maxAmount: isset($data['maxAmount']) ? (float) $data['maxAmount'] : (isset($data['max_amount']) ? (float) $data['max_amount'] : null),
            search: $data['search'] ?? null,
            sort: $data['sort'] ?? null
        );
    }
}
