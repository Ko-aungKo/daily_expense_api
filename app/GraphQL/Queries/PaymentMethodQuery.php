<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use App\Services\PaymentMethodService;
use Illuminate\Database\Eloquent\Collection;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PaymentMethodQuery
{
    public function __construct(
        protected PaymentMethodService $paymentMethodService
    ) {}

    /**
     * Resolve custom and system payment methods for the current user.
     */
    public function resolve($_, array $args, GraphQLContext $context): Collection
    {
        /** @var User $user */
        $user = $context->user();

        return $this->paymentMethodService->getPaymentMethodsForUser($user->id);
    }
}
