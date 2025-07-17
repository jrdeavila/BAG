<?php

namespace App\Listeners;

use App\Events\ActivityUpdatedEvent;
use App\Mail\ActivityUpdatedCreatorNotification;
use App\Mail\ActivityUpdatedUserNotification;
use Illuminate\Support\Facades\Mail;

class ActivityUpdatedListener
{

    public function __construct() {}


    public function handle(ActivityUpdatedEvent $event): void
    {
        $mailToCreator = new ActivityUpdatedCreatorNotification($event->activity);
        $mailToUser = new ActivityUpdatedUserNotification($event->activity);
        Mail::send($mailToCreator);
        Mail::send($mailToUser);
    }
}
