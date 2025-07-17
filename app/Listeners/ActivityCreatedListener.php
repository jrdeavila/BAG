<?php

namespace App\Listeners;

use App\Events\ActivityCreatedEvent;
use App\Mail\ActivityCreatedCreatorNotificacion;
use App\Mail\ActivityCreatedUserNotification;
use Illuminate\Support\Facades\Mail;

class ActivityCreatedListener
{
    public function __construct() {}


    public function handle(ActivityCreatedEvent $event): void
    {
        $mailToCreator = new ActivityCreatedCreatorNotificacion($event->activity);
        $mailToUser = new ActivityCreatedUserNotification($event->activity);

        Mail::send($mailToCreator);
        Mail::send($mailToUser);
    }
}
