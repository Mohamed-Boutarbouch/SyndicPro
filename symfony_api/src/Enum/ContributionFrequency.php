<?php

namespace App\Enum;

enum ContributionFrequency: string
{
    case MONTHLY = 'monthly';
    case BI_MONTHLY = 'bi_monthly';
    case QUARTERLY = 'quarterly';
    case FOUR_MONTHLY = 'four_monthly';
    case YEARLY = 'yearly';
}
