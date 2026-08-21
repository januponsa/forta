<?php

namespace App\Mail;

use App\Models\EmailBlast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailBlastMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailBlast;
    public $htmlBody;

    /**
     * Create a new message instance.
     */
    public function __construct(EmailBlast $emailBlast, $htmlBody)
    {
        $this->emailBlast = $emailBlast;
        $this->htmlBody = $htmlBody;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailBlast->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->htmlBody,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        foreach ($this->emailBlast->attachments as $attachment) {
            $attachments[] = Attachment::fromStorageDisk('local', $attachment->file_path)
                ->as($attachment->file_name)
                ->withMime($attachment->mime_type);
        }
        return $attachments;
    }
}
