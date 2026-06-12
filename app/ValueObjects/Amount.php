<?php

namespace App\ValueObjects;

use App\Exceptions\InvalidAmountException;

readonly class Amount
{
    public float $value;

    public function __construct(float $value)
    {
        if ($value <= 0) {
            throw new InvalidAmountException;
        }
        $this->value = round($value, 2);
    }

    public function equals(Amount $other): bool
    {
        return $this->value === $other->value;
    }

    public function formatted(): string
    {
        return number_format($this->value, 2);
    }
}
