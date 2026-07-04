<?php

namespace App\Enums;

enum AllocationStatus: string
{
    case Active = 'active';
    case Vacated = 'vacated';
}
