<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewEventRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Event $event,
        public readonly User $registrant,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новая запись на событие',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-event-registration',
        );
    }
}
