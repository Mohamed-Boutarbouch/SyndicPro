<?php

namespace App\Enum;

enum ExpenseCategory: string
{
    case WATER_ELECTRICITY = 'water_electricity_consumption_charges';
    case PERSONNEL = 'personnel_charges';
    case SYNDIC_REMUNERATION = 'syndic_remuneration';
    case COMMON_AREA = 'common_area_maintenance_fees';
    case SYNDIC_ADMIN = 'syndic_administration_charges';
    case OTHER = 'other_expenses_charges';
}
