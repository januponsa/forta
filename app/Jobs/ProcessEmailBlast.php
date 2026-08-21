<?php

namespace App\Jobs;

use App\Models\EmailBlast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessEmailBlast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $emailBlast;

    public function __construct(EmailBlast $emailBlast)
    {
        $this->emailBlast = $emailBlast;
    }

    public function handle(): void
    {
        if ($this->emailBlast->status === 'cancelled') {
            return;
        }

        $this->emailBlast->update(['status' => 'sending']);

        $chunkSize = 50; // default 50 per batch
        
        $recipients = $this->emailBlast->recipients()->where('status', 'pending')->get();
        
        if ($recipients->isEmpty()) {
            $this->emailBlast->update([
                'status' => 'completed',
                'sent_at' => now(),
            ]);
            return;
        }

        $chunks = $recipients->chunk($chunkSize);

        foreach ($chunks as $chunk) {
            $recipientIds = $chunk->pluck('id')->toArray();
            SendEmailBatch::dispatch($this->emailBlast, $recipientIds);
        }
    }
}
