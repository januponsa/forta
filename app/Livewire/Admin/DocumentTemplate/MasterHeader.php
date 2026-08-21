<?php

namespace App\Livewire\Admin\DocumentTemplate;

use App\Models\LetterheadMaster;
use App\Models\LetterheadVersion;
use App\Models\DocumentAsset;
use App\Models\DocumentAssetVersion;
use App\Models\DocumentHistory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MasterHeader extends Component
{
    use WithFileUploads;

    public $letterheads;
    public $showForm = false;
    public $editingId = null;

    // Form fields
    public $name = '';
    public $code = '';
    public $unit = '';
    public $university_name = 'Universitas Pradita';
    public $faculty = 'Fakultas Sains dan Teknologi';
    public $study_program = 'Program Studi Informatika';
    public $campus_address = '';
    public $phone = '';
    public $website = '';
    public $email = '';
    public $header_html = '';
    public $footer_html = '';
    public $header_height = 100;
    public $footer_height = 30;
    public $separator_style = 'solid';
    public $separator_width = 2;
    public $separator_color = '#000000';
    public $margin_top = 25;
    public $margin_bottom = 25;
    public $margin_left = 25;
    public $margin_right = 25;

    public $logo_upload;
    public $change_notes = '';

    // Preview
    public $previewingId = null;

    protected function rules()
    {
        $uniqueRule = $this->editingId
            ? 'required|string|unique:letterhead_masters,code,' . $this->editingId
            : 'required|string|unique:letterhead_masters,code';

        return [
            'name' => 'required|string|max:255',
            'code' => $uniqueRule,
            'unit' => 'nullable|string|max:255',
            'university_name' => 'required|string|max:255',
            'faculty' => 'nullable|string|max:255',
            'study_program' => 'nullable|string|max:255',
            'campus_address' => 'nullable|string',
            'phone' => 'nullable|string|max:100',
            'website' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'header_html' => 'nullable|string',
            'footer_html' => 'nullable|string',
            'header_height' => 'required|numeric|min:0',
            'footer_height' => 'required|numeric|min:0',
            'separator_style' => 'required|in:solid,double,none',
            'separator_width' => 'required|integer|min:0|max:10',
            'margin_top' => 'required|numeric|min:0',
            'margin_bottom' => 'required|numeric|min:0',
            'margin_left' => 'required|numeric|min:0',
            'margin_right' => 'required|numeric|min:0',
            'logo_upload' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
        ];
    }

    public function mount()
    {
        $this->loadLetterheads();
    }

    public function loadLetterheads()
    {
        $this->letterheads = LetterheadMaster::with(['activeVersion', 'creator'])
            ->withCount('versions')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function openCreateForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editDraft($id)
    {
        $master = LetterheadMaster::with('activeVersion')->findOrFail($id);

        if ($master->status === 'published') {
            session()->flash('error', 'Kop surat yang sudah Published tidak dapat diedit langsung. Gunakan "Buat Versi Baru".');
            return;
        }

        $this->editingId = $master->id;
        $this->name = $master->name;
        $this->code = $master->code;
        $this->unit = $master->unit;
        $this->university_name = $master->university_name;
        $this->faculty = $master->faculty;
        $this->study_program = $master->study_program;
        $this->campus_address = $master->campus_address;
        $this->phone = $master->phone;
        $this->website = $master->website;
        $this->email = $master->email;

        if ($version = $master->activeVersion) {
            $this->header_html = $version->header_html ?? '';
            $this->footer_html = $version->footer_html ?? '';
            $this->header_height = $version->header_height;
            $this->footer_height = $version->footer_height;
            $this->separator_style = $version->separator_style;
            $this->separator_width = $version->separator_width;
            $this->separator_color = $version->separator_color;
            $this->margin_top = $version->margin_top;
            $this->margin_bottom = $version->margin_bottom;
            $this->margin_left = $version->margin_left;
            $this->margin_right = $version->margin_right;
        }

        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $userId = Auth::id();

        if ($this->editingId) {
            $master = LetterheadMaster::findOrFail($this->editingId);
            $beforeState = $master->toArray();

            $master->update([
                'name' => $this->name,
                'code' => $this->code,
                'unit' => $this->unit,
                'university_name' => $this->university_name,
                'faculty' => $this->faculty,
                'study_program' => $this->study_program,
                'campus_address' => $this->campus_address,
                'phone' => $this->phone,
                'website' => $this->website,
                'email' => $this->email,
                'updated_by' => $userId,
            ]);

            // Update existing draft version
            $version = $master->activeVersion;
            if ($version && $version->status === 'draft') {
                $this->updateVersion($version);
            }

            DocumentHistory::create([
                'target_type' => 'LetterheadMaster',
                'target_id' => $master->id,
                'action' => 'edited',
                'description' => 'Kop surat diperbarui',
                'before_state' => $beforeState,
                'after_state' => $master->fresh()->toArray(),
                'user_id' => $userId,
                'ip_address' => request()->ip(),
            ]);

            session()->flash('message', 'Kop surat berhasil diperbarui.');
        } else {
            $master = LetterheadMaster::create([
                'name' => $this->name,
                'code' => $this->code,
                'unit' => $this->unit,
                'university_name' => $this->university_name,
                'faculty' => $this->faculty,
                'study_program' => $this->study_program,
                'campus_address' => $this->campus_address,
                'phone' => $this->phone,
                'website' => $this->website,
                'email' => $this->email,
                'status' => 'draft',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $logoAssetId = $this->processLogoUpload($master);

            $version = LetterheadVersion::create([
                'letterhead_master_id' => $master->id,
                'version_number' => 1,
                'status' => 'draft',
                'logo_asset_id' => $logoAssetId,
                'header_html' => $this->header_html,
                'header_height' => $this->header_height,
                'separator_style' => $this->separator_style,
                'separator_width' => $this->separator_width,
                'separator_color' => $this->separator_color,
                'footer_html' => $this->footer_html,
                'footer_height' => $this->footer_height,
                'margin_top' => $this->margin_top,
                'margin_bottom' => $this->margin_bottom,
                'margin_left' => $this->margin_left,
                'margin_right' => $this->margin_right,
                'created_by' => $userId,
            ]);

            $master->update(['active_version_id' => $version->id]);

            DocumentHistory::create([
                'target_type' => 'LetterheadMaster',
                'target_id' => $master->id,
                'action' => 'created',
                'description' => 'Kop surat baru dibuat',
                'after_state' => $master->fresh()->toArray(),
                'user_id' => $userId,
                'ip_address' => request()->ip(),
            ]);

            session()->flash('message', 'Kop surat berhasil ditambahkan.');
        }

        $this->resetForm();
        $this->showForm = false;
        $this->loadLetterheads();
    }

    public function createNewVersion($id)
    {
        $master = LetterheadMaster::with('activeVersion')->findOrFail($id);
        $oldVersion = $master->activeVersion;

        $newVersionNumber = ($master->versions()->max('version_number') ?? 0) + 1;

        $newVersion = LetterheadVersion::create([
            'letterhead_master_id' => $master->id,
            'version_number' => $newVersionNumber,
            'status' => 'draft',
            'logo_asset_id' => $oldVersion?->logo_asset_id,
            'header_html' => $oldVersion?->header_html,
            'header_height' => $oldVersion?->header_height ?? 100,
            'separator_style' => $oldVersion?->separator_style ?? 'solid',
            'separator_width' => $oldVersion?->separator_width ?? 2,
            'separator_color' => $oldVersion?->separator_color ?? '#000000',
            'footer_html' => $oldVersion?->footer_html,
            'footer_height' => $oldVersion?->footer_height ?? 30,
            'margin_top' => $oldVersion?->margin_top ?? 25,
            'margin_bottom' => $oldVersion?->margin_bottom ?? 25,
            'margin_left' => $oldVersion?->margin_left ?? 25,
            'margin_right' => $oldVersion?->margin_right ?? 25,
            'created_by' => Auth::id(),
        ]);

        $master->update([
            'status' => 'draft',
            'active_version_id' => $newVersion->id,
            'updated_by' => Auth::id(),
        ]);

        DocumentHistory::create([
            'target_type' => 'LetterheadMaster',
            'target_id' => $master->id,
            'action' => 'version_created',
            'description' => "Versi baru v{$newVersionNumber} dibuat",
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);

        session()->flash('message', "Versi baru v{$newVersionNumber} berhasil dibuat. Silakan edit draft.");
        $this->loadLetterheads();
    }

    public function publish($id)
    {
        $master = LetterheadMaster::with('activeVersion')->findOrFail($id);
        $version = $master->activeVersion;

        if (!$version) {
            session()->flash('error', 'Tidak ada versi yang tersedia untuk dipublish.');
            return;
        }

        $version->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        $master->update([
            'status' => 'published',
            'effective_date' => now(),
            'updated_by' => Auth::id(),
        ]);

        DocumentHistory::create([
            'target_type' => 'LetterheadMaster',
            'target_id' => $master->id,
            'action' => 'published',
            'description' => "Kop surat v{$version->version_number} dipublish",
            'template_version_id' => $version->id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);

        session()->flash('message', 'Kop surat berhasil dipublish.');
        $this->loadLetterheads();
    }

    public function deactivate($id)
    {
        $master = LetterheadMaster::findOrFail($id);
        $master->update(['status' => 'inactive', 'updated_by' => Auth::id()]);

        DocumentHistory::create([
            'target_type' => 'LetterheadMaster',
            'target_id' => $master->id,
            'action' => 'deactivated',
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);

        session()->flash('message', 'Kop surat dinonaktifkan.');
        $this->loadLetterheads();
    }

    public function archive($id)
    {
        $master = LetterheadMaster::findOrFail($id);
        $master->update(['status' => 'archived', 'updated_by' => Auth::id()]);

        DocumentHistory::create([
            'target_type' => 'LetterheadMaster',
            'target_id' => $master->id,
            'action' => 'archived',
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);

        session()->flash('message', 'Kop surat diarsipkan.');
        $this->loadLetterheads();
    }

    public function duplicate($id)
    {
        $master = LetterheadMaster::with('activeVersion')->findOrFail($id);
        $newCode = $master->code . '_copy_' . time();

        $newMaster = $master->replicate(['active_version_id']);
        $newMaster->code = $newCode;
        $newMaster->name = $master->name . ' (Salinan)';
        $newMaster->status = 'draft';
        $newMaster->created_by = Auth::id();
        $newMaster->save();

        if ($master->activeVersion) {
            $newVersion = $master->activeVersion->replicate();
            $newVersion->letterhead_master_id = $newMaster->id;
            $newVersion->version_number = 1;
            $newVersion->status = 'draft';
            $newVersion->published_at = null;
            $newVersion->created_by = Auth::id();
            $newVersion->save();

            $newMaster->update(['active_version_id' => $newVersion->id]);
        }

        session()->flash('message', 'Kop surat berhasil diduplikat.');
        $this->loadLetterheads();
    }

    public function togglePreview($id)
    {
        $this->previewingId = $this->previewingId === $id ? null : $id;
    }

    public function deleteMaster($id)
    {
        $master = LetterheadMaster::findOrFail($id);

        // Check if used by any template
        $usedCount = \App\Models\DocumentTemplateVersion::where('letterhead_version_id', $master->active_version_id)->count();
        if ($usedCount > 0) {
            session()->flash('error', "Kop surat ini digunakan oleh {$usedCount} template. Nonaktifkan saja, jangan hapus.");
            return;
        }

        $master->delete();
        session()->flash('message', 'Kop surat berhasil dihapus.');
        $this->loadLetterheads();
    }

    // --- Private helpers ---

    private function updateVersion(LetterheadVersion $version)
    {
        $logoAssetId = $this->processLogoUpload($version->master) ?? $version->logo_asset_id;

        $version->update([
            'logo_asset_id' => $logoAssetId,
            'header_html' => $this->header_html,
            'header_height' => $this->header_height,
            'separator_style' => $this->separator_style,
            'separator_width' => $this->separator_width,
            'separator_color' => $this->separator_color,
            'footer_html' => $this->footer_html,
            'footer_height' => $this->footer_height,
            'margin_top' => $this->margin_top,
            'margin_bottom' => $this->margin_bottom,
            'margin_left' => $this->margin_left,
            'margin_right' => $this->margin_right,
            'change_notes' => $this->change_notes,
        ]);
    }

    private function processLogoUpload($master): ?int
    {
        if (!$this->logo_upload) return null;

        $file = $this->logo_upload;
        $path = $file->store('document-assets/logos', 'public');

        $imageInfo = getimagesize($file->getRealPath());
        $width = $imageInfo[0] ?? 100;
        $height = $imageInfo[1] ?? 80;

        $asset = DocumentAsset::create([
            'name' => "Logo - {$master->name}",
            'asset_type' => 'logo',
            'mime_type' => $file->getMimeType(),
            'status' => 'active',
            'owner_type' => 'system',
            'default_width' => $width,
            'default_height' => $height,
            'created_by' => Auth::id(),
        ]);

        $assetVersion = DocumentAssetVersion::create([
            'document_asset_id' => $asset->id,
            'version_number' => 1,
            'original_path' => $path,
            'original_width' => $width,
            'original_height' => $height,
            'aspect_ratio' => $height > 0 ? round($width / $height, 6) : 1,
            'file_size' => $file->getSize(),
            'file_format' => $file->getClientOriginalExtension(),
            'has_transparency' => in_array($file->getMimeType(), ['image/png', 'image/webp', 'image/svg+xml']),
            'created_by' => Auth::id(),
        ]);

        $asset->update(['active_version_id' => $assetVersion->id]);

        $this->logo_upload = null;
        return $asset->id;
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->code = '';
        $this->unit = '';
        $this->university_name = 'Universitas Pradita';
        $this->faculty = 'Fakultas Sains dan Teknologi';
        $this->study_program = 'Program Studi Informatika';
        $this->campus_address = '';
        $this->phone = '';
        $this->website = '';
        $this->email = '';
        $this->header_html = '';
        $this->footer_html = '';
        $this->header_height = 100;
        $this->footer_height = 30;
        $this->separator_style = 'solid';
        $this->separator_width = 2;
        $this->separator_color = '#000000';
        $this->margin_top = 25;
        $this->margin_bottom = 25;
        $this->margin_left = 25;
        $this->margin_right = 25;
        $this->logo_upload = null;
        $this->change_notes = '';
    }

    public function render()
    {
        return view('livewire.admin.document-template.master-header')->layout('layouts.admin');
    }
}
