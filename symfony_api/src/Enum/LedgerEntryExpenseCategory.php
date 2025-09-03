<?php

namespace App\Enum;

enum LedgerEntryExpenseCategory: string
{
    case UTILITIES = 'utilities';
    case STAFF = 'staff';
    case SYNDIC_FEES = 'syndic_fees';
    case MAINTENANCE = 'maintenance';
    case ADMINISTRATION = 'administration';
    case OTHER = 'other';
}
