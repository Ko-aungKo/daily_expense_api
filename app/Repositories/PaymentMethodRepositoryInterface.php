<?php

namespace App\Repositories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;

interface PaymentMethodRepositoryInterface
{
    public function find(int $id): ?PaymentMethod;

    public function allForUser(int $userId): Collection;

    public function create(array $data): PaymentMethod;

    public function update(PaymentMethod $paymentMethod, array $data): PaymentMethod;

    public function delete(PaymentMethod $paymentMethod): bool;
}
