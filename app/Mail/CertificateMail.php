<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class CertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $filePath;
    public $eventName;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $filePath, $eventName = 'Event')
    {
        $this->name = $name;
        $this->filePath = $filePath;
        $this->eventName = $eventName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Certificate for ' . $this->eventName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.certificate',
            with: [
                'name' => $this->name,
                'eventName' => $this->eventName,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (file_exists($this->filePath)) {
            return [
                Attachment::fromPath($this->filePath)
                    ->as('Certificate_' . str_replace(' ', '_', $this->name) . '.jpg')
                    ->withMime('image/jpeg'),
            ];
        }
        return [];
    }
}
