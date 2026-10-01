<?php

namespace App\Livewire\Admin;

use App\Models\DocumentTemplate;
use App\Models\LetterLogbook;
use App\Models\DocumentInstance;
use App\Services\DocumentBuilderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class GenericLetterGenerator extends Component
{
    public $templates = [];
    public $selectedTemplateId = null;
    public $placeholders = [];
    public $formData = [];
    
    // Logbook explicit fields
    public $letter_number = '';
    public $letter_title = '';
    public $recipient_name = '';
    public $letter_date = '';
    
    public $generatedPdfUrl = null;

    public function mount()
    {
        $this->templates = DocumentTemplate::where('status', 'published')->get();
        $this->letter_date = now()->format('Y-m-d');
    }

    public function updatedSelectedTemplateId($value)
    {
        $this->placeholders = [];
        $this->formData = [];
        $this->generatedPdfUrl = null;
        
        if (!$value) return;

        $template = DocumentTemplate::find($value);
        if ($template && $template->activeVersion) {
            $html = $template->activeVersion->body_html;
            
            // Extract all {{ placeholder }} using regex
            preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', $html, $matches);
            
            if (!empty($matches[1])) {
                // Get unique placeholders
                $this->placeholders = array_values(array_unique($matches[1]));
                foreach ($this->placeholders as $placeholder) {
                    $this->formData[$placeholder] = '';
                }
            }
            
            $this->letter_title = $template->name;
        }
    }

    public function generateLetter(DocumentBuilderService $builderService)
    {
        $this->validate([
            'selectedTemplateId' => 'required',
            'letter_number' => 'required',
            'letter_title' => 'required',
            'letter_date' => 'required|date',
        ]);

        $template = DocumentTemplate::find($this->selectedTemplateId);
        if (!$template || !$template->activeVersion) {
            session()->flash('error', 'Template tidak valid atau tidak memiliki versi aktif.');
            return;
        }

        // We use DocumentBuilderService to render HTML directly
        // We will create a temporary DocumentInstance just for rendering
        $instance = new DocumentInstance([
            'document_template_id' => $template->id,
            'template_version_id' => $template->activeVersion->id,
            'letterhead_version_id' => $template->activeVersion->letterhead_version_id,
            'source_type' => DocumentTemplate::class, // Treat template as source for manual
            'source_id' => $template->id,
            'status' => 'final',
            'data_snapshot' => $this->formData,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
        
        // Save to get ID
        $instance->save();

        $filename = 'manual_letter_' . time() . '_' . Str::random(5) . '.pdf';
        $directory = storage_path('app/private/documents/manual');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        $outputPath = $directory . '/' . $filename;

        try {
            $builderService->generatePdf($instance, $outputPath);
            
            // Register to Logbook
            $isBackdated = false;
            $parsedDate = \Carbon\Carbon::parse($this->letter_date)->startOfDay();
            if ($parsedDate->lt(now()->startOfDay())) {
                $isBackdated = true;
            }

            $dbPath = 'documents/manual/' . $filename;
            
            LetterLogbook::create([
                'letter_number' => $this->letter_number,
                'letter_title' => $this->letter_title,
                'recipient_name' => $this->recipient_name,
                'letter_date' => $this->letter_date,
                'is_backdated' => $isBackdated,
                'source_type' => DocumentInstance::class,
                'source_id' => $instance->id,
                'final_pdf_path' => $dbPath,
                'created_by' => Auth::id(),
            ]);

            $this->generatedPdfUrl = route('admin.file.download', ['path' => $dbPath]);
            session()->flash('message', 'Surat berhasil di-generate dan dicatat ke Logbook!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mem-build PDF: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.generic-letter-generator')->layout('layouts.admin');
    }
}
