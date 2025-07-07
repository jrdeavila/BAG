<?php

namespace App\Enums;

enum ActivityStatus: string
{
    case PENDING = 'pending';
    case FINISHED = 'finished';
    case CANCELLED = 'cancelled';
    case PAUSED = 'paused';
    case FINISHED_LATE = 'finished_late';
    case LATE = 'late';
    case CREATED_BY_USER = 'created_by_user';
}
