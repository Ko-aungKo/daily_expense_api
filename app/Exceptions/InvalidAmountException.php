<?php

namespace App\Exceptions;

use Exception;
use GraphQL\Error\ClientAware;

class InvalidAmountException extends Exception implements ClientAware
{
    public function __construct(string $message = 'The amount must be a positive number greater than zero.')
    {
        parent::__construct($message, 422);
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'validation';
    }
}
