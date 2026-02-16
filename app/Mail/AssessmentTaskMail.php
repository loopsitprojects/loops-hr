<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssessmentTaskMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailSubject;
    public $mailContent;
    public $replyToEmail;
    public $actionUrl;
    public $attachmentPath;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $content, $replyToEmail, $actionUrl = null, $attachmentPath = null)
    {
        $this->mailSubject = $subject;
        $this->mailContent = $content;
        $this->replyToEmail = $replyToEmail;
        $this->actionUrl = $actionUrl;
        $this->attachmentPath = $attachmentPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(config('mail.from.address'), 'Loops HR Team'),
            replyTo: [
                new \Illuminate\Mail\Mailables\Address($this->replyToEmail),
            ],
            subject: $this->mailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.assessment-task',
            with: [
                'content' => $this->mailContent,
                'actionUrl' => $this->actionUrl,
                'attachmentName' => $this->attachmentPath ? basename($this->attachmentPath) : null,
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
        if ($this->attachmentPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->attachmentPath)) {
            return [
                \Illuminate\Mail\Mailables\Attachment::fromPath(storage_path('app/public/' . $this->attachmentPath)),
            ];
        }
        return [];
    }
}
