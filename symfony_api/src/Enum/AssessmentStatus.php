<?php

namespace App\Enum;

enum AssessmentStatus: string
{
    case DRAFT = 'draft';
    case ISSUED = 'issued';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
