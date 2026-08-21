<?php

namespace App\Livewire\Admin;

use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSection;
use App\Models\QuestionBank;
use Livewire\Component;

class FormFieldBuilder extends Component
{
    public $form;
    
    // Properties for Form Field modal / edit state
    public $fieldId;
    public $section_id;
    public $label;
    public $type = 'text';
    public $description;
    public $placeholder;
    public $options; // text comma separated or array
    public $importOptionsText; // multiline text area for importing
    public $is_required = false;
    public $is_active = true;
    public $order = 0;
    public $max_files = 1;
    public $max_size_mb = 2;
    public $allowed_types; // comma separated or array
    public $default_value;
    
    // Conditional logic properties
    public $trigger_field_id;
    public $condition_operator;
    public $condition_value;

    // Form Section properties
    public $sectionId;
    public $sectionTitle;
    public $sectionDescription;
    public $sectionOrder = 0;

    // Suggestion template properties
    public $selectedTemplate;

    // Question Bank properties
    public $bankSearch = '';
    public $bankCategory = '';
    public $saveToBankCategory = 'Lainnya';

    // State toggles
    public $isFieldModalOpen = false;
    public $isSectionModalOpen = false;
    public $isPreviewModalOpen = false;
    public $isBankModalOpen = false;
    
    // Preview state
    public $previewMode = 'desktop'; // desktop, tablet, mobile
    public $previewActiveSectionIndex = 0; // for multi-step preview

    // Draft/Autosave notification state
    public $saveStatus = 'Tersimpan';

    protected $listeners = ['refreshBuilder' => '$refresh'];

    public function mount($formId)
    {
        $this->form = Form::with(['sections', 'fields'])->findOrFail($formId);
    }

    public function render()
    {
        // Reload form with relations
        $this->form->load(['sections', 'fields']);
        
        $bankQuestions = QuestionBank::query()
            ->when($this->bankSearch, function($q) {
                $q->where('label', 'like', '%' . $this->bankSearch . '%');
            })
            ->when($this->bankCategory, function($q) {
                $q->where('category', $this->bankCategory);
            })
            ->get();

        return view('livewire.admin.form-field-builder', [
            'bankQuestions' => $bankQuestions
        ])->layout('layouts.admin');
    }

    // --- SAVE DRAFT ---
    public function saveDraft()
    {
        $this->saveStatus = 'Menyimpan...';
        try {
            // Save state of form details if needed, but since it's saved in real-time, we just flash success
            $this->form->touch();
            $this->saveStatus = 'Tersimpan';
        } catch (\Exception $e) {
            $this->saveStatus = 'Gagal Menyimpan';
        }
    }

    // --- FORM SECTIONS CRUD ---
    public function openSectionModal($sectionId = null)
    {
        $this->resetSectionFields();
        if ($sectionId) {
            $section = FormSection::findOrFail($sectionId);
            $this->sectionId = $section->id;
            $this->sectionTitle = $section->title;
            $this->sectionDescription = $section->description;
            $this->sectionOrder = $section->order;
        } else {
            $this->sectionOrder = $this->form->sections()->count() + 1;
        }
        $this->isSectionModalOpen = true;
    }

    public function closeSectionModal()
    {
        $this->isSectionModalOpen = false;
    }

    public function resetSectionFields()
    {
        $this->sectionId = null;
        $this->sectionTitle = '';
        $this->sectionDescription = '';
        $this->sectionOrder = 0;
    }

    public function saveSection()
    {
        $this->validate([
            'sectionTitle' => 'required|string|max:255',
            'sectionDescription' => 'nullable|string',
            'sectionOrder' => 'required|integer',
        ]);

        if ($this->sectionId) {
            $section = FormSection::findOrFail($this->sectionId);
            $section->update([
                'title' => $this->sectionTitle,
                'description' => $this->sectionDescription,
                'order' => $this->sectionOrder,
            ]);
        } else {
            FormSection::create([
                'form_id' => $this->form->id,
                'title' => $this->sectionTitle,
                'description' => $this->sectionDescription,
                'order' => $this->sectionOrder,
            ]);
        }

        $this->closeSectionModal();
        $this->saveDraft();
        session()->flash('message', 'Section berhasil disimpan.');
    }

    public function deleteSection($id)
    {
        $section = FormSection::findOrFail($id);
        // Dissociate fields
        FormField::where('section_id', $section->id)->update(['section_id' => null]);
        $section->delete();
        $this->saveDraft();
        session()->flash('message', 'Section berhasil dihapus.');
    }

    public function duplicateSection($id)
    {
        $section = FormSection::with('fields')->findOrFail($id);
        $newSection = $section->replicate();
        $newSection->title = $section->title . ' Salinan';
        $newSection->order = $this->form->sections()->count() + 1;
        $newSection->save();

        foreach ($section->fields as $field) {
            $newField = $field->replicate();
            $newField->section_id = $newSection->id;
            $newField->save();
        }

        $this->saveDraft();
        session()->flash('message', 'Section berhasil diduplikasi.');
    }

    // --- FORM FIELDS CRUD ---
    public function openFieldModal($fieldId = null, $targetSectionId = null)
    {
        $this->resetFieldInput();
        $this->section_id = $targetSectionId;
        
        if ($fieldId) {
            $field = FormField::findOrFail($fieldId);
            $this->fieldId = $field->id;
            $this->section_id = $field->section_id;
            $this->label = $field->label;
            $this->type = $field->type;
            $this->description = $field->description;
            $this->placeholder = $field->placeholder;
            $this->options = is_array($field->options) ? implode(', ', $field->options) : '';
            $this->is_required = $field->is_required;
            $this->is_active = $field->is_active;
            $this->order = $field->order;
            $this->max_files = $field->max_files ?: 1;
            $this->max_size_mb = $field->max_size_mb ?: 2;
            $this->allowed_types = is_array($field->allowed_types) ? implode(', ', $field->allowed_types) : '';
            $this->default_value = $field->default_value;
            
            // Set conditions if present
            if ($field->conditions) {
                $this->trigger_field_id = $field->conditions['trigger_field_id'] ?? null;
                $this->condition_operator = $field->conditions['operator'] ?? 'equals';
                $this->condition_value = $field->conditions['value'] ?? '';
            }
        } else {
            $this->order = $this->form->fields()->count() + 1;
        }
        $this->isFieldModalOpen = true;
    }

    public function closeFieldModal()
    {
        $this->isFieldModalOpen = false;
    }

    public function resetFieldInput()
    {
        $this->fieldId = null;
        $this->section_id = null;
        $this->label = '';
        $this->type = 'text';
        $this->description = '';
        $this->placeholder = '';
        $this->options = '';
        $this->importOptionsText = '';
        $this->is_required = false;
        $this->is_active = true;
        $this->order = 0;
        $this->max_files = 1;
        $this->max_size_mb = 2;
        $this->allowed_types = 'PDF, JPG, PNG, DOCX, XLSX, ZIP';
        $this->default_value = '';
        
        $this->trigger_field_id = null;
        $this->condition_operator = 'equals';
        $this->condition_value = '';
    }

    public function importOptions()
    {
        if (!empty($this->importOptionsText)) {
            $lines = explode("\n", str_replace("\r", "", $this->importOptionsText));
            $cleaned = [];
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed !== '') {
                    $cleaned[] = $trimmed;
                }
            }
            $this->options = implode(', ', $cleaned);
            $this->importOptionsText = '';
            session()->flash('import_message', 'Opsi berhasil diimport.');
        }
    }

    public function saveField()
    {
        $this->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|string',
            'order' => 'required|integer',
        ]);

        $optionsArray = null;
        if (in_array($this->type, ['select', 'checkbox', 'radio', 'dropdown', 'linear_scale']) && !empty($this->options)) {
            $optionsArray = array_map('trim', explode(',', $this->options));
        }

        $allowedTypesArray = null;
        if ($this->type === 'file' && !empty($this->allowed_types)) {
            $allowedTypesArray = array_map('trim', explode(',', $this->allowed_types));
        }

        // Build conditions JSON if set
        $conditionsJson = null;
        if ($this->trigger_field_id) {
            $conditionsJson = [
                'trigger_field_id' => $this->trigger_field_id,
                'operator' => $this->condition_operator,
                'value' => $this->condition_value,
            ];
        }

        $data = [
            'form_id' => $this->form->id,
            'section_id' => $this->section_id ?: null,
            'label' => $this->label,
            'type' => $this->type,
            'description' => $this->description ?: null,
            'placeholder' => $this->placeholder ?: null,
            'options' => $optionsArray,
            'is_required' => $this->is_required ? true : false,
            'is_active' => $this->is_active ? true : false,
            'order' => $this->order,
            'max_files' => $this->type === 'file' ? $this->max_files : null,
            'max_size_mb' => $this->type === 'file' ? $this->max_size_mb : null,
            'allowed_types' => $allowedTypesArray,
            'default_value' => $this->default_value ?: null,
            'conditions' => $conditionsJson,
        ];

        if ($this->fieldId) {
            $field = FormField::findOrFail($this->fieldId);
            $field->update($data);
        } else {
            FormField::create($data);
        }

        $this->closeFieldModal();
        $this->saveDraft();
        session()->flash('message', 'Pertanyaan berhasil disimpan.');
    }

    public function deleteField($id)
    {
        FormField::findOrFail($id)->delete();
        $this->saveDraft();
        session()->flash('message', 'Pertanyaan berhasil dihapus.');
    }

    public function duplicateField($id)
    {
        $field = FormField::findOrFail($id);
        $newField = $field->replicate();
        $newField->label = $field->label . ' Salinan';
        $newField->order = $this->form->fields()->count() + 1;
        $newField->save();
        $this->saveDraft();
        session()->flash('message', 'Pertanyaan berhasil diduplikasi.');
    }

    // --- REORDERING (MOBILE & DESKTOP ALTERNATIVE) ---
    public function moveFieldUp($id)
    {
        $field = FormField::findOrFail($id);
        $previousField = FormField::where('form_id', $this->form->id)
            ->where('section_id', $field->section_id)
            ->where('order', '<', $field->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousField) {
            $oldOrder = $field->order;
            $field->update(['order' => $previousField->order]);
            $previousField->update(['order' => $oldOrder]);
            $this->saveDraft();
        }
    }

    public function moveFieldDown($id)
    {
        $field = FormField::findOrFail($id);
        $nextField = FormField::where('form_id', $this->form->id)
            ->where('section_id', $field->section_id)
            ->where('order', '>', $field->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextField) {
            $oldOrder = $field->order;
            $field->update(['order' => $nextField->order]);
            $nextField->update(['order' => $oldOrder]);
            $this->saveDraft();
        }
    }

    public function moveSectionUp($id)
    {
        $section = FormSection::findOrFail($id);
        $previousSection = FormSection::where('form_id', $this->form->id)
            ->where('order', '<', $section->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousSection) {
            $oldOrder = $section->order;
            $section->update(['order' => $previousSection->order]);
            $previousSection->update(['order' => $oldOrder]);
            $this->saveDraft();
        }
    }

    public function moveSectionDown($id)
    {
        $section = FormSection::findOrFail($id);
        $nextSection = FormSection::where('form_id', $this->form->id)
            ->where('order', '>', $section->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextSection) {
            $oldOrder = $section->order;
            $section->update(['order' => $nextSection->order]);
            $nextSection->update(['order' => $oldOrder]);
            $this->saveDraft();
        }
    }

    public function updateSectionOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            FormSection::where('id', $id)->update(['order' => $index + 1]);
        }
        $this->saveDraft();
    }

    public function updateFieldOrder($sectionId, $orderedIds)
    {
        $sectionId = empty($sectionId) ? null : $sectionId;
        foreach ($orderedIds as $index => $id) {
            FormField::where('id', $id)->update([
                'order' => $index + 1,
                'section_id' => $sectionId
            ]);
        }
        $this->saveDraft();
    }

    // --- TEMPLATES LOADER ---
    public function applyTemplate($category)
    {
        // Delete current sections & fields to apply clean template
        $this->form->fields()->delete();
        $this->form->sections()->delete();

        switch ($category) {
            case 'pendaftaran_kegiatan':
                $this->loadPendaftaranKegiatanTemplate();
                break;
            case 'pelaporan_kegiatan':
                $this->loadPelaporanKegiatanTemplate();
                break;
            case 'tugas_akhir':
                $this->loadTugasAkhirTemplate();
                break;
            case 'kkn':
                $this->loadKknTemplate();
                break;
            case 'magang':
                $this->loadMagangTemplate();
                break;
            case 'konversi_mata_kuliah':
                $this->loadKonversiTemplate();
                break;
        }

        $this->saveDraft();
        session()->flash('message', 'Template pendaftaran berhasil diterapkan.');
    }

    protected function loadPendaftaranKegiatanTemplate()
    {
        $fields = [
            ['label' => 'Nama Kegiatan', 'type' => 'text', 'is_required' => true],
            ['label' => 'Jenis Kegiatan', 'type' => 'select', 'options' => ['Tugas Akhir', 'KKN', 'Magang / Kerja Praktik', 'Konversi Mata Kuliah', 'Jaringan', 'Lainnya'], 'is_required' => true],
            ['label' => 'Nama Penyelenggara', 'type' => 'text', 'is_required' => true],
            ['label' => 'Tingkat Kegiatan', 'type' => 'select', 'options' => ['Internal', 'Kota/Kabupaten', 'Provinsi', 'Nasional', 'Internasional'], 'is_required' => true],
            ['label' => 'Peran Mahasiswa', 'type' => 'select', 'options' => ['Peserta', 'Panitia', 'Narasumber', 'Ketua Tim', 'Anggota Tim'], 'is_required' => true],
            ['label' => 'Tanggal Mulai', 'type' => 'date', 'is_required' => true],
            ['label' => 'Tanggal Selesai', 'type' => 'date', 'is_required' => true],
            ['label' => 'Lokasi Kegiatan', 'type' => 'text', 'is_required' => true],
            ['label' => 'Mode Kegiatan', 'type' => 'select', 'options' => ['Online', 'Offline', 'Hybrid'], 'is_required' => true],
            ['label' => 'Deskripsi Singkat', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Alasan Mengikuti', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Link Informasi Kegiatan', 'type' => 'url', 'is_required' => false],
            ['label' => 'Upload Bukti Pendaftaran', 'type' => 'file', 'allowed_types' => ['PDF', 'JPG', 'PNG'], 'max_files' => 1, 'max_size_mb' => 2, 'is_required' => true],
            ['label' => 'Catatan Tambahan', 'type' => 'textarea', 'is_required' => false],
        ];

        foreach ($fields as $idx => $f) {
            FormField::create(array_merge($f, [
                'form_id' => $this->form->id,
                'order' => $idx + 1,
            ]));
        }
    }

    protected function loadPelaporanKegiatanTemplate()
    {
        $fields = [
            ['label' => 'Nama Kegiatan', 'type' => 'text', 'is_required' => true],
            ['label' => 'Tanggal Pelaksanaan', 'type' => 'date', 'is_required' => true],
            ['label' => 'Penyelenggara', 'type' => 'text', 'is_required' => true],
            ['label' => 'Peran Mahasiswa', 'type' => 'select', 'options' => ['Peserta', 'Panitia', 'Narasumber', 'Ketua Tim', 'Anggota Tim'], 'is_required' => true],
            ['label' => 'Hasil Kegiatan', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Capaian atau Prestasi', 'type' => 'text', 'is_required' => false],
            ['label' => 'Deskripsi Pelaksanaan', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Manfaat Kegiatan', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Kendala', 'type' => 'textarea', 'is_required' => false],
            ['label' => 'Rencana Tindak Lanjut', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Link Dokumentasi', 'type' => 'url', 'is_required' => false],
            ['label' => 'Upload Sertifikat', 'type' => 'file', 'allowed_types' => ['PDF', 'JPG', 'PNG'], 'max_files' => 1, 'max_size_mb' => 2, 'is_required' => true],
            ['label' => 'Upload Laporan', 'type' => 'file', 'allowed_types' => ['PDF', 'DOCX'], 'max_files' => 1, 'max_size_mb' => 5, 'is_required' => true],
            ['label' => 'Upload Dokumentasi', 'type' => 'file', 'allowed_types' => ['JPG', 'PNG'], 'max_files' => 3, 'max_size_mb' => 3, 'is_required' => false],
            ['label' => 'Catatan Tambahan', 'type' => 'textarea', 'is_required' => false],
        ];

        foreach ($fields as $idx => $f) {
            FormField::create(array_merge($f, [
                'form_id' => $this->form->id,
                'order' => $idx + 1,
            ]));
        }
    }

    protected function loadTugasAkhirTemplate()
    {
        $fields = [
            ['label' => 'Judul Tugas Akhir', 'type' => 'text', 'is_required' => true],
            ['label' => 'Bidang Penelitian', 'type' => 'text', 'is_required' => true],
            ['label' => 'Topik Penelitian', 'type' => 'text', 'is_required' => true],
            ['label' => 'Latar Belakang Singkat', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Rumusan Masalah', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Metode yang Digunakan', 'type' => 'text', 'is_required' => true],
            ['label' => 'Teknologi yang Digunakan', 'type' => 'text', 'is_required' => true],
            ['label' => 'Calon Dosen Pembimbing', 'type' => 'text', 'is_required' => true],
            ['label' => 'Mitra atau Objek Penelitian', 'type' => 'text', 'is_required' => false],
            ['label' => 'Link Proposal', 'type' => 'url', 'is_required' => false],
            ['label' => 'Upload Proposal', 'type' => 'file', 'allowed_types' => ['PDF'], 'max_files' => 1, 'max_size_mb' => 5, 'is_required' => true],
            ['label' => 'Upload Lembar Persetujuan', 'type' => 'file', 'allowed_types' => ['PDF', 'JPG', 'PNG'], 'max_files' => 1, 'max_size_mb' => 2, 'is_required' => true],
            ['label' => 'Catatan Tambahan', 'type' => 'textarea', 'is_required' => false],
        ];

        foreach ($fields as $idx => $f) {
            FormField::create(array_merge($f, [
                'form_id' => $this->form->id,
                'order' => $idx + 1,
            ]));
        }
    }

    protected function loadKknTemplate()
    {
        $fields = [
            ['label' => 'Lokasi KKN', 'type' => 'text', 'is_required' => true],
            ['label' => 'Nama Mitra', 'type' => 'text', 'is_required' => true],
            ['label' => 'Alamat Mitra', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Penanggung Jawab Mitra', 'type' => 'text', 'is_required' => true],
            ['label' => 'Nomor Kontak Mitra', 'type' => 'tel', 'is_required' => true],
            ['label' => 'Judul Program KKN', 'type' => 'text', 'is_required' => true],
            ['label' => 'Deskripsi Program', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Target Penerima Manfaat', 'type' => 'text', 'is_required' => true],
            ['label' => 'Tanggal Mulai', 'type' => 'date', 'is_required' => true],
            ['label' => 'Tanggal Selesai', 'type' => 'date', 'is_required' => true],
            ['label' => 'Anggota Kelompok', 'type' => 'textarea', 'is_required' => false],
            ['label' => 'Dosen Pembimbing Lapangan', 'type' => 'text', 'is_required' => true],
            ['label' => 'Rencana Luaran', 'type' => 'text', 'is_required' => true],
            ['label' => 'Estimasi Biaya', 'type' => 'number', 'is_required' => false],
            ['label' => 'Upload Proposal', 'type' => 'file', 'allowed_types' => ['PDF'], 'max_files' => 1, 'max_size_mb' => 5, 'is_required' => true],
            ['label' => 'Upload Surat Kesediaan Mitra', 'type' => 'file', 'allowed_types' => ['PDF', 'JPG', 'PNG'], 'max_files' => 1, 'max_size_mb' => 2, 'is_required' => true],
            ['label' => 'Catatan Tambahan', 'type' => 'textarea', 'is_required' => false],
        ];

        foreach ($fields as $idx => $f) {
            FormField::create(array_merge($f, [
                'form_id' => $this->form->id,
                'order' => $idx + 1,
            ]));
        }
    }

    protected function loadMagangTemplate()
    {
        $fields = [
            ['label' => 'Nama Perusahaan', 'type' => 'text', 'is_required' => true],
            ['label' => 'Bidang Perusahaan', 'type' => 'text', 'is_required' => true],
            ['label' => 'Alamat Perusahaan', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Divisi Penempatan', 'type' => 'text', 'is_required' => true],
            ['label' => 'Posisi Magang', 'type' => 'text', 'is_required' => true],
            ['label' => 'Nama Pembimbing Lapangan', 'type' => 'text', 'is_required' => false],
            ['label' => 'Email Pembimbing Lapangan', 'type' => 'email', 'is_required' => false],
            ['label' => 'Nomor Kontak Pembimbing', 'type' => 'tel', 'is_required' => false],
            ['label' => 'Tanggal Mulai', 'type' => 'date', 'is_required' => true],
            ['label' => 'Tanggal Selesai', 'type' => 'date', 'is_required' => true],
            ['label' => 'Deskripsi Pekerjaan', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Teknologi yang Digunakan', 'type' => 'text', 'is_required' => false],
            ['label' => 'Link Website Perusahaan', 'type' => 'url', 'is_required' => false],
            ['label' => 'Upload Surat Penerimaan', 'type' => 'file', 'allowed_types' => ['PDF'], 'max_files' => 1, 'max_size_mb' => 3, 'is_required' => true],
            ['label' => 'Upload Proposal', 'type' => 'file', 'allowed_types' => ['PDF'], 'max_files' => 1, 'max_size_mb' => 5, 'is_required' => false],
            ['label' => 'Catatan Tambahan', 'type' => 'textarea', 'is_required' => false],
        ];

        foreach ($fields as $idx => $f) {
            FormField::create(array_merge($f, [
                'form_id' => $this->form->id,
                'order' => $idx + 1,
            ]));
        }
    }

    protected function loadKonversiTemplate()
    {
        $fields = [
            ['label' => 'Nama Kegiatan Asal', 'type' => 'text', 'is_required' => true],
            ['label' => 'Jenis Kegiatan', 'type' => 'text', 'is_required' => true],
            ['label' => 'Nama Instansi', 'type' => 'text', 'is_required' => true],
            ['label' => 'Periode Kegiatan', 'type' => 'text', 'is_required' => true],
            ['label' => 'Mata Kuliah yang Diajukan', 'type' => 'text', 'is_required' => true],
            ['label' => 'Kode Mata Kuliah', 'type' => 'text', 'is_required' => true],
            ['label' => 'Jumlah SKS', 'type' => 'number', 'is_required' => true],
            ['label' => 'Capaian Pembelajaran', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Deskripsi Aktivitas', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Bukti Kegiatan', 'type' => 'file', 'allowed_types' => ['PDF', 'ZIP'], 'max_files' => 1, 'max_size_mb' => 10, 'is_required' => true],
            ['label' => 'Sertifikat', 'type' => 'file', 'allowed_types' => ['PDF', 'JPG', 'PNG'], 'max_files' => 1, 'max_size_mb' => 3, 'is_required' => false],
            ['label' => 'Transkrip atau Nilai', 'type' => 'file', 'allowed_types' => ['PDF'], 'max_files' => 1, 'max_size_mb' => 2, 'is_required' => false],
            ['label' => 'Dokumen Pendukung', 'type' => 'file', 'allowed_types' => ['PDF', 'ZIP'], 'max_files' => 1, 'max_size_mb' => 10, 'is_required' => false],
            ['label' => 'Catatan Tambahan', 'type' => 'textarea', 'is_required' => false],
        ];

        foreach ($fields as $idx => $f) {
            FormField::create(array_merge($f, [
                'form_id' => $this->form->id,
                'order' => $idx + 1,
            ]));
        }
    }

    // --- QUESTION BANK INTERACTION ---
    public function openBankModal()
    {
        $this->isBankModalOpen = true;
    }

    public function closeBankModal()
    {
        $this->isBankModalOpen = false;
    }

    public function addFromBank($bankId)
    {
        $bankItem = QuestionBank::findOrFail($bankId);
        FormField::create([
            'form_id' => $this->form->id,
            'section_id' => null,
            'label' => $bankItem->label,
            'type' => $bankItem->type,
            'description' => $bankItem->description,
            'placeholder' => $bankItem->placeholder,
            'options' => $bankItem->options,
            'validation_rules' => $bankItem->validation_rules,
            'is_required' => $bankItem->is_required,
            'max_files' => $bankItem->max_files,
            'max_size_mb' => $bankItem->max_size_mb,
            'allowed_types' => $bankItem->allowed_types,
            'order' => $this->form->fields()->count() + 1,
        ]);

        $this->saveDraft();
        session()->flash('message', 'Pertanyaan ditambahkan dari bank.');
    }

    public function saveToBank($fieldId)
    {
        $field = FormField::findOrFail($fieldId);
        
        QuestionBank::create([
            'label' => $field->label,
            'type' => $field->type,
            'category' => $this->saveToBankCategory,
            'description' => $field->description,
            'placeholder' => $field->placeholder,
            'options' => $field->options,
            'validation_rules' => $field->validation_rules,
            'is_required' => $field->is_required,
            'max_files' => $field->max_files,
            'max_size_mb' => $field->max_size_mb,
            'allowed_types' => $field->allowed_types,
        ]);

        session()->flash('message', 'Pertanyaan berhasil disimpan ke bank.');
    }
}
