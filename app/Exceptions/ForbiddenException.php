<?php

namespace App\Exceptions;

use Exception;
use GraphQL\Error\ClientAware;

class ForbiddenException extends Exception implements ClientAware
{
    public function __construct(string $message = 'You are not authorized to access this resource.')
    {
        parent::__construct($message, 403);
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'authorization';
    }
}
