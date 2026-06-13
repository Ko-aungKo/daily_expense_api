<?php

namespace App\Services;

use App\Models\PaymentMethod;
use App\Repositories\PaymentMethodRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PaymentMethodService
{
    public function __construct(
        protected PaymentMethodRepositoryInterface $paymentMethodRepository
    ) {}

    public function getPaymentMethodsForUser(string $userId): Collection
    {
        return $this->paymentMethodRepository->allForUser($userId);
    }

    public function createPaymentMethod(string $userId, array $data): PaymentMethod
    {
        $data['user_id'] = $userId;

        return $this->paymentMethodRepository->create($data);
    }

    public function updatePaymentMethod(PaymentMethod $paymentMethod, array $data): PaymentMethod
    {
        return $this->paymentMethodRepository->update($paymentMethod, $data);
    }

    public function deletePaymentMethod(PaymentMethod $paymentMethod): bool
    {
        return $this->paymentMethodRepository->delete($paymentMethod);
    }
}
