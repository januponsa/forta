<?php

namespace App\Jobs;

use App\Models\SignatureRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\SignatureRequestHistory;

class SendSignedDocumentEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $signatureRequest;

    public function __construct(SignatureRequest $signatureRequest)
    {
        $this->signatureRequest = $signatureRequest;
    }

    public function handle(): void
    {
        try {
            $student = $this->signatureRequest->student;
            $signedFilePath = Storage::disk('private')->path($this->signatureRequest->signed_file_path);

            if (!file_exists($signedFilePath)) {
                throw new \Exception("File PDF final tidak ditemukan.");
            }

            Mail::send([], [], function ($message) use ($student, $signedFilePath) {
                $message->to($student->email)
                    ->subject('Dokumen Telah Ditandatangani: ' . $this->signatureRequest->title)
                    ->html("
                        <p>Halo {$student->name},</p>
                        <p>Dokumen Anda dengan judul <strong>{$this->signatureRequest->title}</strong> telah selesai ditandatangani oleh {$this->signatureRequest->lecturer->name}.</p>
                        <p>Silakan temukan file PDF final pada lampiran email ini, atau Anda dapat mengunduhnya melalui portal FORTA.</p>
                        <p>Terima kasih.</p>
                    ")
                    ->attach($signedFilePath, [
                        'as' => 'Signed_' . $this->signatureRequest->original_filename,
                        'mime' => 'application/pdf',
                    ]);
            });

            $this->signatureRequest->update([
                'status' => 'completed',
                'emailed_at' => now(),
                'email_error' => null,
            ]);
            
            SignatureRequestHistory::create([
                'signature_request_id' => $this->signatureRequest->id,
                'action' => 'email_sent',
                'note' => 'Email sent to student successfully.'
            ]);

        } catch (\Exception $e) {
            $this->signatureRequest->update([
                'status' => 'email_failed',
                'email_error' => $e->getMessage()
            ]);
            
            SignatureRequestHistory::create([
                'signature_request_id' => $this->signatureRequest->id,
                'action' => 'email_failed',
                'note' => $e->getMessage()
            ]);
        }
    }
}
