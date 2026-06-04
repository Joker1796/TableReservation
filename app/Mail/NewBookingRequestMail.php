<?php

namespace App\Mail;

use App\Models\BookingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewBookingRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly BookingRequest $bookingRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новая заявка на бронирование',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-booking-request',
        );
    }
}
