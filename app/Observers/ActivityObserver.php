<?php


namespace App\Observers;

use App\Events\ActivityCreatedEvent;
use App\Events\ActivityUpdatedEvent;
use App\Models\Activity;

class ActivityObserver
{
    public function created(Activity $activity)
    {
        event(new ActivityCreatedEvent($activity));
    }

    public function updated(Activity $activity)
    {
        event(new ActivityUpdatedEvent($activity));
    }
}
