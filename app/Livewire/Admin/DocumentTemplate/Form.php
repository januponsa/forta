<?php

namespace App\Livewire\Admin\DocumentTemplate;

use App\Models\DocumentTemplate;
use App\Models\DocumentTemplateVersion;
use App\Models\DocumentHistory;
use App\Models\LetterheadMaster;
use App\Models\LetterheadVersion;
use App\Models\DocumentAsset;
use App\Models\DocumentAssetVersion;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class Form extends Component
{
    use WithFileUploads;

    public $templateId = null;
    public $isEditing = false;
    public $isPreviewMode = false;

    // Template fields
    public $name = '';
    public $type = '';
    public $category = 'surat_magang';
    public $editor_type = 'flow';
    public $document_purpose = '';
    public $letterhead_version_id = null;

    // Version fields
    public $body_html = '';
    public $header_html = '';
    public $footer_html = '';
    public $paper_size = 'A4';
    public $orientation = 'portrait';
    public $margin_top = 25;
    public $margin_bottom = 25;
    public $margin_left = 25;
    public $margin_right = 25;
    public $signatory_config = [];
    public $change_notes = '';

    // Canvas layout for canvas editor (string JSON)
    public $canvas_layout = '';

    // Image upload
    public $imageUpload;

    // Preview data
    public $letterheadPreview = null;

    public $availablePurposes = [
        '' => '-- Tidak Ada --',
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

    public $placeholders = [
        'Mahasiswa' => [
            '{{ nama_mahasiswa }}' => 'Nama Mahasiswa',
            '{{ nim }}' => 'NIM',
            '{{ email_mahasiswa }}' => 'Email',
            '{{ program_studi }}' => 'Program Studi',
            '{{ angkatan }}' => 'Angkatan',
            '{{ semester }}' => 'Semester',
        ],
        'Magang/KP' => [
            '{{ nama_perusahaan }}' => 'Nama Perusahaan',
            '{{ alamat_perusahaan }}' => 'Alamat Perusahaan',
            '{{ nama_mentor }}' => 'Nama Mentor',
            '{{ periode_magang }}' => 'Periode Magang',
            '{{ judul_laporan }}' => 'Judul Laporan',
        ],
        'Sidang' => [
            '{{ tanggal_sidang }}' => 'Tanggal Sidang',
            '{{ waktu_sidang }}' => 'Waktu Sidang',
            '{{ pembimbing }}' => 'Pembimbing',
            '{{ penguji }}' => 'Penguji',
            '{{ nilai_pembimbing }}' => 'Nilai Pembimbing',
            '{{ nilai_penguji }}' => 'Nilai Penguji',
            '{{ nilai_mentor }}' => 'Nilai Mentor',
            '{{ nilai_akhir }}' => 'Nilai Akhir',
            '{{ huruf_mutu }}' => 'Huruf Mutu',
            '{{ keputusan_sidang }}' => 'Keputusan Sidang',
        ],
        'Dokumen' => [
            '{{ nomor_surat }}' => 'Nomor Surat',
            '{{ tanggal_terbit }}' => 'Tanggal Terbit',
            '{{ penandatangan }}' => 'Penandatangan',
            '{{ jabatan_penandatangan }}' => 'Jabatan',
        ],
    ];

    public $dynamicTables = [
        '[TABEL_RUBRIK_PEMBIMBING]' => 'Tabel Rubrik Pembimbing',
        '[TABEL_RUBRIK_PENGUJI]' => 'Tabel Rubrik Penguji',
        '[TABEL_RUBRIK_MENTOR]' => 'Tabel Rubrik Mentor',
        '[TABEL_SARAN]' => 'Tabel Daftar Saran',
        '[TABEL_PENANDATANGAN]' => 'Tabel Penandatangan',
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'category' => 'required|string',
            'editor_type' => 'required|in:flow,canvas,overlay',
            'body_html' => 'nullable|string',
            'paper_size' => 'required|string',
            'orientation' => 'required|in:portrait,landscape',
            'margin_top' => 'required|numeric|min:0',
            'margin_bottom' => 'required|numeric|min:0',
            'margin_left' => 'required|numeric|min:0',
            'margin_right' => 'required|numeric|min:0',
        ];
    }

    public function mount($id = null)
    {
        if ($id) {
            $this->templateId = $id;
            $this->isEditing = true;
            $this->loadTemplate();
        }

        if (request()->has('preview')) {
            $this->isPreviewMode = true;
        }
    }

    private function loadTemplate()
    {
        $template = DocumentTemplate::with('activeVersion')->findOrFail($this->templateId);

        $this->name = $template->name;
        $this->type = $template->type;
        $this->category = $template->category ?? 'surat_magang';
        $this->editor_type = $template->editor_type ?? 'flow';
        $this->document_purpose = $template->document_purpose ?? '';
        $this->letterhead_version_id = $template->letterhead_version_id;

        if ($version = $template->activeVersion) {
            $this->body_html = $version->body_html ?? '';
            $this->header_html = $version->header_html ?? '';
            $this->footer_html = $version->footer_html ?? '';
            $this->paper_size = $version->paper_size ?? 'A4';
            $this->orientation = $version->orientation ?? 'portrait';
            $this->margin_top = $version->margin_top;
            $this->margin_bottom = $version->margin_bottom;
            $this->margin_left = $version->margin_left;
            $this->margin_right = $version->margin_right;
            $this->signatory_config = $version->signatory_config ?? [];
            $this->canvas_layout = $version->canvas_layout ? json_encode($version->canvas_layout) : '';
        }

        // Load letterhead for preview
        if ($this->letterhead_version_id) {
            $this->letterheadPreview = LetterheadVersion::with(['master', 'logoAsset.activeVersion'])
                ->find($this->letterhead_version_id);
        }
    }

    public function save()
    {
        $this->validate();
        $userId = Auth::id();

        if ($this->isEditing) {
            $template = DocumentTemplate::findOrFail($this->templateId);

            if ($template->status === 'published') {
                session()->flash('error', 'Template yang sudah Published tidak bisa diedit langsung. Buat versi baru terlebih dahulu.');
                return;
            }

            $beforeState = $template->toArray();

            $template->update([
                'name' => $this->name,
                'type' => $this->type,
                'category' => $this->category,
                'editor_type' => $this->editor_type,
                'document_purpose' => $this->document_purpose ?: null,
                'letterhead_version_id' => $this->letterhead_version_id,
                'updated_by' => $userId,
            ]);

            $version = $template->activeVersion;
            if ($version) {
                $version->update([
                    'body_html' => $this->body_html,
                    'header_html' => $this->header_html,
                    'footer_html' => $this->footer_html,
                    'canvas_layout' => $this->canvas_layout ? json_decode($this->canvas_layout, true) : null,
                    'paper_size' => $this->paper_size,
                    'orientation' => $this->orientation,
                    'margin_top' => $this->margin_top,
                    'margin_bottom' => $this->margin_bottom,
                    'margin_left' => $this->margin_left,
                    'margin_right' => $this->margin_right,
                    'margin_right' => $this->margin_right,
                    'letterhead_version_id' => empty($this->letterhead_version_id) ? null : $this->letterhead_version_id,
                    'signatory_config' => $this->signatory_config ?: null,
                    'change_notes' => $this->change_notes,
                ]);
            }

            DocumentHistory::create([
                'target_type' => 'DocumentTemplate', 'target_id' => $template->id,
                'action' => 'edited', 'description' => 'Template diperbarui',
                'before_state' => $beforeState, 'after_state' => $template->fresh()->toArray(),
                'user_id' => $userId, 'ip_address' => request()->ip(),
            ]);

            session()->flash('message', 'Template berhasil disimpan.');
        } else {
            $template = DocumentTemplate::create([
                'name' => $this->name,
                'type' => $this->type,
                'category' => $this->category,
                'editor_type' => $this->editor_type,
                'document_purpose' => $this->document_purpose ?: null,
                'letterhead_version_id' => empty($this->letterhead_version_id) ? null : $this->letterhead_version_id,
                'status' => 'draft',
                'body_html' => $this->body_html ?? '',
                'header_html' => $this->header_html ?? '',
                'footer_html' => $this->footer_html ?? '',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $version = DocumentTemplateVersion::create([
                'document_template_id' => $template->id,
                'version_number' => 1,
                'status' => 'draft',
                'body_html' => $this->body_html,
                'header_html' => $this->header_html,
                'footer_html' => $this->footer_html,
                'canvas_layout' => $this->canvas_layout ? json_decode($this->canvas_layout, true) : null,
                'paper_size' => $this->paper_size,
                'orientation' => $this->orientation,
                'margin_top' => $this->margin_top,
                'margin_bottom' => $this->margin_bottom,
                'margin_left' => $this->margin_left,
                'margin_right' => $this->margin_right,
                'letterhead_version_id' => empty($this->letterhead_version_id) ? null : $this->letterhead_version_id,
                'signatory_config' => $this->signatory_config ?: null,
                'created_by' => $userId,
            ]);

            $template->update(['active_version_id' => $version->id]);

            DocumentHistory::create([
                'target_type' => 'DocumentTemplate', 'target_id' => $template->id,
                'action' => 'created', 'description' => 'Template baru dibuat',
                'after_state' => $template->fresh()->toArray(),
                'user_id' => $userId, 'ip_address' => request()->ip(),
            ]);

            session()->flash('message', 'Template berhasil dibuat.');
            return redirect()->route('admin.document-templates.edit', $template->id);
        }
    }

    public function uploadImage()
    {
        $this->validate(['imageUpload' => 'required|image|mimes:png,jpg,jpeg,webp,svg|max:2048']);

        $file = $this->imageUpload;

        // Validate MIME type (not just extension)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file->getRealPath());
        $allowedMimes = ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'];
        if (!in_array($realMime, $allowedMimes)) {
            session()->flash('error', 'File yang diunggah bukan gambar valid (MIME: ' . $realMime . ').');
            return;
        }

        $path = $file->store('document-assets/template-images', 'public');
        $imageInfo = @getimagesize($file->getRealPath());
        $width = $imageInfo[0] ?? 200;
        $height = $imageInfo[1] ?? 200;

        $asset = DocumentAsset::create([
            'name' => $file->getClientOriginalName(),
            'asset_type' => 'image',
            'mime_type' => $realMime,
            'status' => 'active',
            'owner_type' => 'system',
            'default_width' => min($width, 400),
            'default_height' => min($height, 400),
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
            'has_transparency' => in_array($realMime, ['image/png', 'image/webp', 'image/svg+xml']),
            'created_by' => Auth::id(),
        ]);

        $asset->update(['active_version_id' => $assetVersion->id]);

        $imageUrl = asset('storage/' . $path);
        $this->imageUpload = null;

        $this->dispatch('image-uploaded', url: $imageUrl, width: min($width, 400), height: min($height, 400), ratio: $height > 0 ? round($width / $height, 6) : 1);

        session()->flash('message', 'Gambar berhasil diunggah. Gunakan URL: ' . $imageUrl);
    }

    public function render()
    {
        $allLetterheadVersions = LetterheadVersion::published()->with('master')->get();
        $signatories = \App\Models\Lecturer::where('is_active', true)->get();

        // Reload letterhead preview if version changed
        if ($this->letterhead_version_id) {
            $this->letterheadPreview = LetterheadVersion::with(['master', 'logoAsset.activeVersion'])
                ->find($this->letterhead_version_id);
        } else {
            $this->letterheadPreview = null;
        }

        return view('livewire.admin.document-template.form', [
            'allLetterheadVersions' => $allLetterheadVersions,
            'signatories' => $signatories,
            'availablePurposes' => $this->availablePurposes,
            'placeholders' => $this->placeholders,
            'dynamicTables' => $this->dynamicTables,
            'letterheadPreview' => $this->letterheadPreview,
        ])->layout('layouts.admin');
    }
}
