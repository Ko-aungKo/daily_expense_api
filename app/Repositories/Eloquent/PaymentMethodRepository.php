<?php

namespace App\Repositories\Eloquent;

use App\Models\PaymentMethod;
use App\Repositories\PaymentMethodRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PaymentMethodRepository implements PaymentMethodRepositoryInterface
{
    public function find(int $id): ?PaymentMethod
    {
        return PaymentMethod::find($id);
    }

    public function allForUser(int $userId): Collection
    {
        return PaymentMethod::forUser($userId)->orderBy('name', 'asc')->get();
    }

    public function create(array $data): PaymentMethod
    {
        return PaymentMethod::create($data);
    }

    public function update(PaymentMethod $paymentMethod, array $data): PaymentMethod
    {
        $paymentMethod->update($data);

        return $paymentMethod;
    }

    public function delete(PaymentMethod $paymentMethod): bool
    {
        return $paymentMethod->delete();
    }
}
