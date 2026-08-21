<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\DefenseCase;
use App\Services\DefenseDocumentGenerator;
use Illuminate\Support\Facades\Log;

class GenerateDefenseDocumentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $caseId;

    /**
     * Create a new job instance.
     */
    public function __construct($caseId)
    {
        $this->caseId = $caseId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $case = DefenseCase::with(['student', 'latestSchedule', 'assignments.lecturer', 'assessments.scores.rubricItem', 'suggestions.lecturer', 'documents'])->findOrFail($this->caseId);

            // Check statuses again
            $allowedStatuses = ['ready_to_finalize', 'passed', 'passed_with_revision', 'failed', 'completed', 'scored'];
            if (!in_array($case->status, $allowedStatuses)) {
                Log::warning("GenerateDefenseDocumentsJob skipped. Status invalid for case {$this->caseId}");
                return;
            }

            // Hapus dokumen lama untuk generate ulang
            $case->documents()->delete();
            
            $generator = new DefenseDocumentGenerator();
            $docs = $generator->generateAllDocuments($case);
            
            $count = collect($docs)->filter()->count();
            Log::info("GenerateDefenseDocumentsJob successful. Generated {$count} documents for case {$this->caseId}.");
        } catch (\Exception $e) {
            Log::error('Defense Document Generation Job Failed', [
                'case_id' => $this->caseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
