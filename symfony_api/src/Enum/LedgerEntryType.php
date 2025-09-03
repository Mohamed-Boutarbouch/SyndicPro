<?php

namespace App\Enum;

enum LedgerEntryType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
}
