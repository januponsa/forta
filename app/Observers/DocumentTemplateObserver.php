<?php

namespace App\Observers;

use App\Models\DocumentTemplate;
use App\Models\DocumentHistory;
use Illuminate\Support\Facades\Auth;

class DocumentTemplateObserver
{
    private function logHistory(DocumentTemplate $template, string $action)
    {
        DocumentHistory::create([
            'target_type' => 'DocumentTemplate',
            'target_id' => $template->id,
            'user_id' => Auth::id() ?? 1,
            'action' => $action,
            'description' => 'Template ' . $action,
            'after_state' => $template->toArray(),
            'ip_address' => request()->ip(),
        ]);
    }

    public function created(DocumentTemplate $documentTemplate): void
    {
        $this->logHistory($documentTemplate, 'created');
    }

    public function updated(DocumentTemplate $documentTemplate): void
    {
        $this->logHistory($documentTemplate, 'updated');
    }

    public function deleted(DocumentTemplate $documentTemplate): void
    {
        $this->logHistory($documentTemplate, 'deleted');
    }
}
