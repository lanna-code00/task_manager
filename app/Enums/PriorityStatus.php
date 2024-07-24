<?php

namespace App\Enums;

enum PriorityStatus: String
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';
    
}
