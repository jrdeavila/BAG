<?php

namespace App\Mail;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActivityCreatedCreatorNotificacion extends Mailable
{
    public Activity $activity;
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Haz asignado una actividad',
            to: $this->activity->createdBy->employee->email,
        );
    }


    public function content(): Content
    {
        return new Content(
            markdown: 'mail.activity-created-creator-notificacion',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
