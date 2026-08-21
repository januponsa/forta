<?php

namespace App\Livewire\Admin\InternshipLetter;

use App\Models\LetterTemplate;
use App\Models\DocumentAsset;
use App\Models\Lecturer;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class TemplateSettings extends Component
{
    public $template;

    public $university_name;
    public $campus_address;
    public $contact_info;
    public $city;
    public $letter_code;
    public $subject;
    
    public $opening_paragraph;
    public $purpose_paragraph;
    public $closing_paragraph;
    
    public $signatory_name;
    public $signatory_position;
    
    public $number_format;
    public $margin_top, $margin_bottom, $margin_left, $margin_right;

    public $logo_asset_id;
    public $lecturer_id;
    
    public $availableLogos = [];
    public $availableLecturers = [];

    protected $rules = [
        'university_name' => 'required|string',
        'campus_address' => 'required|string',
        'contact_info' => 'required|string',
        'city' => 'required|string',
        'letter_code' => 'required|string',
        'subject' => 'required|string',
        'opening_paragraph' => 'required|string',
        'purpose_paragraph' => 'required|string',
        'closing_paragraph' => 'required|string',
        'signatory_name' => 'required|string',
        'signatory_position' => 'required|string',
        'number_format' => 'required|string',
        'margin_top' => 'required|numeric',
        'margin_bottom' => 'required|numeric',
        'margin_left' => 'required|numeric',
        'margin_right' => 'required|numeric',
        'logo_asset_id' => 'nullable|exists:document_assets,id',
        'lecturer_id' => 'nullable|exists:lecturers,id',
    ];

    public function mount()
    {
        $this->template = LetterTemplate::firstOrCreate(
            ['type' => 'internship'],
            [
                'university_name' => 'Universitas Pradita',
                'campus_address' => 'Jl. Boulevard Raya Gading Serpong, Tangerang',
                'contact_info' => 'Telp: 021-xxxx | Web: www.pradita.ac.id',
                'city' => 'Tangerang',
                'letter_code' => 'P-IF/Pradita',
                'subject' => 'Surat Pengantar Kerja Praktik',
                'opening_paragraph' => 'Dengan hormat,\nSehubungan dengan program pelaksanaan kerja praktik mahasiswa Program Studi Informatika Universitas Pradita, bersama ini kami memohon kesedaiaan Bapak/Ibu untuk memberikan izin serta kesempatan kepada mahasiswa kami untuk melaksanakan kegiatan kerja praktik di perusahaan yang Bapak/Ibu pimpin. Adapun mahasiswa tersebut adalah:',
                'purpose_paragraph' => 'Kegiatan kerja praktik ini merupakan salah satu mata kuliah yang wajib diambil oleh setiap mahasiswa dengan tujuan untuk memberikan pengalaman kerja nyata kepada mahasiswa serta meningkatkan kompetensi sesuai bidang keilmuan yang dipelajari. Adapun waktu pelaksanaan kerja praktik ini direncanakan selama {{durasi}} ({{tanggal_mulai}} – {{tanggal_selesai}}). Oleh karenanya kami mengharapkan kerjasama dan bantuan Bapak/Ibu, agar mahasiswa kami dengan nama tersebut di atas dapat melaksanakan kerja praktik hingga selesai di perusahaan yang Bapak/Ibu pimpin.',
                'closing_paragraph' => 'Demikian surat pengantar ini kami sampaikan. Atas perhatian dan kerjasama Bapak/Ibu kami ucapkan terima kasih.',
                'signatory_name' => 'Dr. Budi Santoso',
                'signatory_position' => 'Ketua Program Studi Informatika',
                'number_format' => '{nomor_urut}/{kode_surat}/{bulan_romawi}/{tahun}',
                'margin_top' => 30,
                'margin_bottom' => 30,
                'margin_left' => 30,
                'margin_right' => 30,
            ]
        );

        $this->university_name = $this->template->university_name;
        $this->campus_address = $this->template->campus_address;
        $this->contact_info = $this->template->contact_info;
        $this->city = $this->template->city;
        $this->letter_code = $this->template->letter_code;
        $this->subject = $this->template->subject;
        
        $this->opening_paragraph = $this->template->opening_paragraph;
        $this->purpose_paragraph = $this->template->purpose_paragraph;
        $this->closing_paragraph = $this->template->closing_paragraph;
        
        $this->signatory_name = $this->template->signatory_name;
        $this->signatory_position = $this->template->signatory_position;
        
        $this->number_format = $this->template->number_format;
        $this->margin_top = $this->template->margin_top;
        $this->margin_bottom = $this->template->margin_bottom;
        $this->margin_left = $this->template->margin_left;
        $this->margin_right = $this->template->margin_right;

        $this->logo_asset_id = $this->template->logo_asset_id;
        $this->lecturer_id = $this->template->lecturer_id;

        $this->availableLogos = DocumentAsset::whereIn('asset_type', ['logo', 'image'])->get();
        $this->availableLecturers = Lecturer::where('is_active', true)->whereNotNull('position')->get();
    }

    public function save()
    {
        $this->validate();

        $this->template->update([
            'university_name' => $this->university_name,
            'campus_address' => $this->campus_address,
            'contact_info' => $this->contact_info,
            'city' => $this->city,
            'letter_code' => $this->letter_code,
            'subject' => $this->subject,
            'opening_paragraph' => $this->opening_paragraph,
            'purpose_paragraph' => $this->purpose_paragraph,
            'closing_paragraph' => $this->closing_paragraph,
            'signatory_name' => $this->signatory_name,
            'signatory_position' => $this->signatory_position,
            'number_format' => $this->number_format,
            'margin_top' => $this->margin_top,
            'margin_bottom' => $this->margin_bottom,
            'margin_left' => $this->margin_left,
            'margin_right' => $this->margin_right,
            'logo_asset_id' => $this->logo_asset_id,
            'lecturer_id' => $this->lecturer_id,
        ]);

        session()->flash('message', 'Template surat berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.admin.internship-letter.template-settings')->layout('layouts.admin');
    }
}
