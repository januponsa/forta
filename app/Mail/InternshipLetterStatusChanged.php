<?php

namespace App\Mail;

use App\Models\InternshipLetterRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InternshipLetterStatusChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $request;
    public $statusLabel;
    public $note;

    /**
     * Create a new message instance.
     */
    public function __construct(InternshipLetterRequest $request, $note = null)
    {
        $this->request = $request;
        $this->note = $note;

        $labels = [
            'revision_required' => 'Perlu Revisi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'generated' => 'Surat Selesai',
        ];

        $this->statusLabel = $labels[$request->status] ?? ucfirst($request->status);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembaruan Status Surat Magang: ' . $this->statusLabel,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.student.internship-letter-status',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
