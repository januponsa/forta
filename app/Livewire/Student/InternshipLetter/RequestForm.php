<?php

namespace App\Livewire\Student\InternshipLetter;

use App\Models\InternshipLetterRequest;
use App\Models\LetterRequestHistory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class RequestForm extends Component
{
    use WithFileUploads;

    public $requestId = null;
    
    // Form fields
    public $company_name;
    public $recipient_name = 'Bapak/Ibu HRD';
    public $company_address;
    public $company_city;
    public $placement_location;
    public $internship_position;
    public $start_date;
    public $end_date;
    public $duration_notes;
    public $purpose;
    public $additional_notes;
    public $attachment;
    public $declaration = false;

    protected $rules = [
        'company_name' => 'required|string|max:255',
        'recipient_name' => 'required|string|max:255',
        'company_address' => 'required|string',
        'company_city' => 'required|string|max:255',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'declaration' => 'accepted',
        'attachment' => 'nullable|file|mimes:pdf|max:2048',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->requestId = $id;
            $request = InternshipLetterRequest::where('student_id', Auth::guard('student')->id())->findOrFail($id);
            
            // Only allow edit if draft or revision_required
            if (!in_array($request->status, ['draft', 'revision_required'])) {
                abort(403, 'Tidak dapat mengedit permohonan ini.');
            }

            $this->company_name = $request->company_name;
            $this->recipient_name = $request->recipient_name;
            $this->company_address = $request->company_address;
            $this->company_city = $request->company_city;
            $this->placement_location = $request->placement_location;
            $this->internship_position = $request->internship_position;
            $this->start_date = $request->start_date ? $request->start_date->format('Y-m-d') : null;
            $this->end_date = $request->end_date ? $request->end_date->format('Y-m-d') : null;
            $this->duration_notes = $request->duration_notes;
            $this->purpose = $request->purpose;
            $this->additional_notes = $request->additional_notes;
        }
    }

    public function saveDraft()
    {
        $this->save('draft');
    }

    public function submit()
    {
        $this->validate();
        $this->save('submitted');
    }

    private function save($status)
    {
        $studentId = Auth::guard('student')->id();

        // Check for active duplicates
        if ($status === 'submitted' && !$this->requestId) {
            $existing = InternshipLetterRequest::where('student_id', $studentId)
                ->where('company_name', $this->company_name)
                ->whereNotIn('status', ['rejected', 'completed'])
                ->first();

            if ($existing) {
                $this->addError('company_name', 'Anda sudah memiliki permohonan aktif untuk perusahaan ini.');
                return;
            }
        }

        $data = [
            'student_id' => $studentId,
            'company_name' => $this->company_name,
            'recipient_name' => $this->recipient_name,
            'company_address' => $this->company_address,
            'company_city' => $this->company_city,
            'placement_location' => $this->placement_location,
            'internship_position' => $this->internship_position,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ?: null,
            'duration_notes' => $this->duration_notes,
            'purpose' => $this->purpose,
            'additional_notes' => $this->additional_notes,
        ];

        if ($this->attachment) {
            $path = $this->attachment->store('internship_attachments', 'private');
            $data['attachment_path'] = $path;
        }

        if ($this->requestId) {
            $request = InternshipLetterRequest::findOrFail($this->requestId);
            $previousStatus = $request->status;
            
            $data['status'] = $status === 'submitted' ? 'submitted' : $request->status;
            
            $request->update($data);

            if ($status === 'submitted') {
                LetterRequestHistory::create([
                    'internship_letter_request_id' => $request->id,
                    'actor_type' => 'student',
                    'actor_id' => $studentId,
                    'action' => 'revised_and_submitted',
                    'previous_status' => $previousStatus,
                    'new_status' => 'submitted',
                ]);
            }
        } else {
            $data['status'] = $status;
            $request = InternshipLetterRequest::create($data);

            LetterRequestHistory::create([
                'internship_letter_request_id' => $request->id,
                'actor_type' => 'student',
                'actor_id' => $studentId,
                'action' => $status === 'draft' ? 'draft_created' : 'submitted',
                'new_status' => $status,
            ]);
        }

        session()->flash('message', $status === 'draft' ? 'Draft berhasil disimpan.' : 'Permohonan berhasil dikirim.');
        return redirect()->route('student.internship-letters.index');
    }

    public function render()
    {
        return view('livewire.student.internship-letter.request-form')->layout('layouts.student');
    }
}
