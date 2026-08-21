<?php

namespace App\Jobs;

use App\Mail\EmailBlastMail;
use App\Models\EmailBlast;
use App\Models\EmailBlastRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendEmailBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $emailBlast;
    public $recipientIds;

    public function __construct(EmailBlast $emailBlast, array $recipientIds)
    {
        $this->emailBlast = $emailBlast;
        $this->recipientIds = $recipientIds;
    }

    public function handle(): void
    {
        if ($this->emailBlast->status === 'cancelled') {
            return;
        }

        $recipients = EmailBlastRecipient::whereIn('id', $this->recipientIds)->get();
        if ($recipients->isEmpty()) {
            return;
        }

        $mode = $this->emailBlast->delivery_mode;
        
        try {
            if ($mode === 'individual') {
                foreach ($recipients as $recipient) {
                    try {
                        $htmlBody = $this->replacePlaceholders($this->emailBlast->body_html, $recipient);
                        $mailable = new EmailBlastMail($this->emailBlast, $htmlBody);
                        Mail::to($recipient->email)->send($mailable);
                        
                        $recipient->update([
                            'status' => 'sent',
                            'sent_at' => now(),
                            'error_message' => null
                        ]);
                    } catch (Exception $e) {
                        $recipient->update([
                            'status' => 'failed',
                            'error_message' => $e->getMessage()
                        ]);
                    }
                }
            } else {
                // Bulk mode (to, cc, bcc)
                $emails = $recipients->pluck('email')->toArray();
                $htmlBody = $this->emailBlast->body_html; // No individual placeholders
                $mailable = new EmailBlastMail($this->emailBlast, $htmlBody);

                // By default send to the first email so 'to' is not empty, unless we are sending everything in 'to'
                $firstEmail = array_shift($emails);

                if ($mode === 'to') {
                    $mailable->to($firstEmail);
                    if (!empty($emails)) {
                        $mailable->to($emails); // Mailer handles array of To's
                    }
                } elseif ($mode === 'cc') {
                    $mailable->to($firstEmail);
                    if (!empty($emails)) {
                        $mailable->cc($emails);
                    }
                } elseif ($mode === 'bcc') {
                    $mailable->to($firstEmail); // Need at least one To usually, or fallback to sender/admin
                    if (!empty($emails)) {
                        $mailable->bcc($emails);
                    }
                }

                Mail::send($mailable);

                // Mark all as sent
                foreach ($recipients as $recipient) {
                    $recipient->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'error_message' => null
                    ]);
                }
            }
        } catch (Exception $e) {
            // If bulk failed, mark all in this batch as failed
            if ($mode !== 'individual') {
                foreach ($recipients as $recipient) {
                    $recipient->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage()
                    ]);
                }
            }
        }

        $this->checkCampaignStatus();
    }

    private function replacePlaceholders($html, $recipient)
    {
        $replacements = [
            '{{nama}}' => $recipient->name,
            '{{nim}}' => $recipient->nim,
            '{{angkatan}}' => $recipient->angkatan,
            '{{email}}' => $recipient->email,
            // Assuming program_studi is available via student relation if needed, 
            // but we might not have it in the snapshot directly if it's not saved.
            // Will fallback to empty string if not found.
            '{{program_studi}}' => $recipient->student ? ($recipient->student->program_studi ?? '') : ''
        ];

        return strtr($html, $replacements);
    }

    private function checkCampaignStatus()
    {
        // Check if there are any pending recipients left
        $pendingCount = EmailBlastRecipient::where('email_blast_id', $this->emailBlast->id)
            ->where('status', 'pending')
            ->count();

        if ($pendingCount === 0) {
            $failedCount = EmailBlastRecipient::where('email_blast_id', $this->emailBlast->id)
                ->where('status', 'failed')
                ->count();

            if ($failedCount === 0) {
                $this->emailBlast->update([
                    'status' => 'completed',
                    'sent_at' => $this->emailBlast->sent_at ?? now()
                ]);
            } elseif ($failedCount === $this->emailBlast->total_recipients) {
                $this->emailBlast->update([
                    'status' => 'failed',
                    'sent_at' => $this->emailBlast->sent_at ?? now()
                ]);
            } else {
                $this->emailBlast->update([
                    'status' => 'partially_failed',
                    'sent_at' => $this->emailBlast->sent_at ?? now()
                ]);
            }
        }
    }
}
