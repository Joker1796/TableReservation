<?php

namespace App\Mail;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewPostSuggestionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Post $post) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новое предложение новости',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-post-suggestion',
        );
    }
}
