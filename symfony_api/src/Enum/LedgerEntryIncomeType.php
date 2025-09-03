<?php

namespace App\Enum;

enum LedgerEntryIncomeType: string
{
    case REGULAR_CONTRIBUTION = 'regular_contribution';
    case SPECIAL_ASSESSMENT = 'special_assessment';
}
