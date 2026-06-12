<?php

namespace App\Enums;

enum ExpenseSort: string
{
    case NEWEST = 'newest';
    case OLDEST = 'oldest';
    case HIGHEST_AMOUNT = 'highest_amount';
    case LOWEST_AMOUNT = 'lowest_amount';
}
