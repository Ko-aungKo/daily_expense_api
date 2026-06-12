<?php

namespace App\ValueObjects;

use InvalidArgumentException;

readonly class Color
{
    public string $hex;

    public function __construct(string $hex)
    {
        if (! preg_match('/^#[0-9A-Fa-f]{3,6}$/', $hex)) {
            throw new InvalidArgumentException('Invalid hex color format (must be a valid hex color like #FFF or #FFFFFF).');
        }
        $this->hex = strtoupper($hex);
    }

    public function equals(Color $other): bool
    {
        return $this->hex === $other->hex;
    }
}
