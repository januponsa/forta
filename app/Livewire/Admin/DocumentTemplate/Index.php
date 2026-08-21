<?php

namespace App\Livewire\Admin\DocumentTemplate;

use App\Models\DocumentTemplate;
use App\Models\DocumentTemplateVersion;
use App\Models\DocumentHistory;
use App\Models\LetterheadMaster;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $statusFilter = '';
    public $categoryFilter = '';
    public $search = '';

    // For "Atur Peruntukan" modal
    public $showPurposeModal = false;
    public $purposeTemplateId = null;
    public $purposeValue = '';

    // For "Lihat Penggunaan" modal
    public $showUsageModal = false;
    public $usageTemplateId = null;
    public $usageCount = 0;

    protected $availablePurposes = [
        'internship_introduction_letter' => 'Surat Pengantar Magang/KP',
        'signature_request_overlay' => 'Overlay Tanda Tangan',
        'internship_defense_biodata' => 'Sidang KP - Biodata',
        'internship_defense_f1' => 'Sidang KP - F1 Berita Acara',
        'internship_defense_f2' => 'Sidang KP - F2 Rekap Nilai',
        'internship_defense_f3' => 'Sidang KP - F3 Penilaian Pembimbing',
        'internship_defense_f4' => 'Sidang KP - F4 Penilaian Penguji',
        'internship_defense_f5' => 'Sidang KP - F5 Penilaian Mentor',
        'internship_defense_f6' => 'Sidang KP - F6 Saran',
    ];

    public function publish($id)
    {
        $template = DocumentTemplate::with('activeVersion')->findOrFail($id);
        $version = $template->activeVersion;

        if (!$version) {
            session()->flash('error', 'Template tidak memiliki versi aktif.');
            return;
        }

        if (!$template->document_purpose) {
            session()->flash('error', 'Template harus memiliki peruntukan sebelum dipublish.');
            return;
        }

        $version->update(['status' => 'published', 'published_at' => now()]);
        $template->update(['status' => 'published', 'updated_by' => Auth::id()]);

        DocumentHistory::create([
            'target_type' => 'DocumentTemplate', 'target_id' => $template->id,
            'action' => 'published',
            'description' => "Template v{$version->version_number} dipublish",
            'template_version_id' => $version->id,
            'user_id' => Auth::id(), 'ip_address' => request()->ip(),
        ]);

        session()->flash('message', 'Template berhasil dipublish.');
    }

    public function createNewVersion($id)
    {
        $template = DocumentTemplate::with('activeVersion')->findOrFail($id);
        $old = $template->activeVersion;
        $newNum = ($template->versions()->max('version_number') ?? 0) + 1;

        $new = DocumentTemplateVersion::create([
            'document_template_id' => $template->id,
            'version_number' => $newNum,
            'status' => 'draft',
            'header_html' => $old?->header_html,
            'body_html' => $old?->body_html,
            'footer_html' => $old?->footer_html,
            'canvas_layout' => $old?->canvas_layout,
            'paper_size' => $old?->paper_size ?? 'A4',
            'orientation' => $old?->orientation ?? 'portrait',
            'margin_top' => $old?->margin_top ?? 25,
            'margin_bottom' => $old?->margin_bottom ?? 25,
            'margin_left' => $old?->margin_left ?? 25,
            'margin_right' => $old?->margin_right ?? 25,
            'letterhead_version_id' => $old?->letterhead_version_id,
            'signatory_config' => $old?->signatory_config,
            'created_by' => Auth::id(),
        ]);

        $template->update(['active_version_id' => $new->id, 'status' => 'draft', 'updated_by' => Auth::id()]);

        DocumentHistory::create([
            'target_type' => 'DocumentTemplate', 'target_id' => $template->id,
            'action' => 'version_created',
            'description' => "Versi baru v{$newNum} dibuat",
            'user_id' => Auth::id(), 'ip_address' => request()->ip(),
        ]);

        session()->flash('message', "Versi baru v{$newNum} dibuat. Silakan edit draft.");
    }

    public function duplicate($id)
    {
        $template = DocumentTemplate::with('activeVersion')->findOrFail($id);

        $new = $template->replicate(['active_version_id', 'document_purpose']);
        $new->name = $template->name . ' (Salinan)';
        $new->type = $template->type . '_copy_' . time();
        $new->document_purpose = null;
        $new->status = 'draft';
        $new->created_by = Auth::id();
        $new->save();

        if ($template->activeVersion) {
            $ver = $template->activeVersion->replicate();
            $ver->document_template_id = $new->id;
            $ver->version_number = 1;
            $ver->status = 'draft';
            $ver->published_at = null;
            $ver->created_by = Auth::id();
            $ver->save();
            $new->update(['active_version_id' => $ver->id]);
        }

        session()->flash('message', 'Template berhasil diduplikat.');
    }

    public function openPurposeModal($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        $this->purposeTemplateId = $template->id;
        $this->purposeValue = $template->document_purpose ?? '';
        $this->showPurposeModal = true;
    }

    public function savePurpose()
    {
        $template = DocumentTemplate::findOrFail($this->purposeTemplateId);

        // Check if purpose is already used by another template
        if ($this->purposeValue) {
            $existing = DocumentTemplate::where('document_purpose', $this->purposeValue)
                ->where('id', '!=', $template->id)->first();
            if ($existing) {
                session()->flash('error', "Peruntukan ini sudah digunakan oleh template \"{$existing->name}\".");
                return;
            }
        }

        $template->update([
            'document_purpose' => $this->purposeValue ?: null,
            'updated_by' => Auth::id(),
        ]);

        DocumentHistory::create([
            'target_type' => 'DocumentTemplate', 'target_id' => $template->id,
            'action' => 'purpose_changed',
            'description' => "Peruntukan diubah menjadi: {$this->purposeValue}",
            'user_id' => Auth::id(), 'ip_address' => request()->ip(),
        ]);

        $this->showPurposeModal = false;
        session()->flash('message', 'Peruntukan berhasil disimpan.');
    }

    public function deactivate($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        $template->update(['status' => 'inactive', 'updated_by' => Auth::id()]);
        session()->flash('message', 'Template dinonaktifkan.');
    }

    public function archive($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        $template->update(['status' => 'archived', 'updated_by' => Auth::id()]);
        session()->flash('message', 'Template diarsipkan.');
    }

    public function showUsage($id)
    {
        $this->usageTemplateId = $id;
        $this->usageCount = \App\Models\DocumentInstance::where('document_template_id', $id)->count();
        $this->showUsageModal = true;
    }

    public function delete($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        $instanceCount = \App\Models\DocumentInstance::where('document_template_id', $id)->count();
        if ($instanceCount > 0) {
            session()->flash('error', "Template digunakan oleh {$instanceCount} dokumen. Arsipkan saja.");
            return;
        }
        $template->delete();
        session()->flash('message', 'Template berhasil dihapus.');
    }

    public function render()
    {
        $query = DocumentTemplate::with(['activeVersion', 'letterheadVersion.master', 'creator', 'updater'])
            ->withCount('instances')
            ->where(function ($q) {
                // Exclude the old master_header type
                $q->where('type', '!=', 'master_header');
            });

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('type', 'like', "%{$this->search}%")
                  ->orWhere('document_purpose', 'like', "%{$this->search}%");
            });
        }

        $templates = $query->orderBy('created_at', 'desc')->get();
        $letterheads = LetterheadMaster::active()->get();

        return view('livewire.admin.document-template.index', [
            'templates' => $templates,
            'letterheads' => $letterheads,
            'availablePurposes' => $this->availablePurposes,
        ])->layout('layouts.admin');
    }
}
