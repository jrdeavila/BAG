<?php

namespace App\Enums;

enum ActivityPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CREATED_BY_USER = 'created_by_user';
}
