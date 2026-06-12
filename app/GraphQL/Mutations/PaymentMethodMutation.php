<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\ForbiddenException;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Services\PaymentMethodService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PaymentMethodMutation
{
    public function __construct(
        protected PaymentMethodService $paymentMethodService
    ) {}

    /**
     * Create a payment method.
     */
    public function create($_, array $args, GraphQLContext $context): PaymentMethod
    {
        /** @var User $user */
        $user = $context->user();
        $userId = $user->id;

        Validator::make($args, [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_methods')->where(function ($q) use ($userId) {
                    return $q->where('user_id', $userId)->orWhereNull('user_id');
                }),
            ],
        ])->validate();

        return $this->paymentMethodService->createPaymentMethod($userId, $args);
    }

    /**
     * Update a payment method.
     */
    public function update($_, array $args, GraphQLContext $context): PaymentMethod
    {
        /** @var User $user */
        $user = $context->user();
        $userId = $user->id;

        $paymentMethod = PaymentMethod::findOrFail($args['id']);

        if ($paymentMethod->user_id !== $userId) {
            throw new ForbiddenException('You cannot update this payment method.');
        }

        Validator::make($args, [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_methods')->ignore($paymentMethod->id)->where(function ($q) use ($userId) {
                    return $q->where('user_id', $userId)->orWhereNull('user_id');
                }),
            ],
        ])->validate();

        return $this->paymentMethodService->updatePaymentMethod($paymentMethod, $args);
    }

    /**
     * Delete a payment method.
     */
    public function delete($_, array $args, GraphQLContext $context): array
    {
        /** @var User $user */
        $user = $context->user();
        $userId = $user->id;

        $paymentMethod = PaymentMethod::findOrFail($args['id']);

        if ($paymentMethod->user_id !== $userId) {
            throw new ForbiddenException('You cannot delete this payment method.');
        }

        $this->paymentMethodService->deletePaymentMethod($paymentMethod);

        return [
            'success' => true,
            'message' => 'Payment method deleted successfully.',
        ];
    }
}
