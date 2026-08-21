<?php

namespace App\Exports;

use App\Models\Submission;
use App\Models\FormField;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubmissionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $formId;
    protected $fields = [];

    public function __construct($formId = null)
    {
        $this->formId = $formId;
        if ($this->formId) {
            $this->fields = FormField::where('form_id', $this->formId)
                ->orderBy('order')
                ->get();
        }
    }

    public function collection()
    {
        $query = Submission::with(['form.fields']);

        if ($this->formId) {
            $query->where('form_id', $this->formId);
        }

        return $query->latest('submitted_at')->get();
    }

    public function headings(): array
    {
        $headings = [
            'ID Pengajuan',
            'Formulir',
            'Nama Mahasiswa',
            'Email',
            'NIM',
            'Status',
            'Tanggal Pengajuan',
        ];

        if ($this->formId) {
            foreach ($this->fields as $field) {
                $headings[] = $field->label;
            }
        } else {
            $headings[] = 'Jawaban (JSON)';
        }

        return $headings;
    }

    public function map($submission): array
    {
        $row = [
            $submission->id,
            $submission->form->title,
            $submission->name,
            $submission->email,
            $submission->nim,
            ucfirst($submission->status),
            $submission->submitted_at->format('Y-m-d H:i:s'),
        ];

        if ($this->formId) {
            foreach ($this->fields as $field) {
                $ans = $submission->answers[$field->id] ?? '-';
                if (is_array($ans)) {
                    $row[] = implode(', ', $ans);
                } else {
                    $row[] = $ans;
                }
            }
        } else {
            $row[] = json_encode($submission->answers);
        }

        return $row;
    }
}
