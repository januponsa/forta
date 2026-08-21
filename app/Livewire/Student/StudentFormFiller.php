<?php

namespace App\Livewire\Student;

use App\Models\Form;
use App\Models\Submission;
use App\Models\SubmissionFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class StudentFormFiller extends Component
{
    use WithFileUploads;

    public $form;
    
    // Grid of steps / wizard
    public $steps = []; // Keep for blade compatibility if needed temporarily, but we'll remove currentSectionIndex
    public $currentSectionIndex = 0; // Not used anymore for pagination, but kept to prevent breaking blade before we edit it

    // Student responses
    public $responses = [];
    public $files = [];
    public $parentAnswers = [];
    public $masterDataOptions = [];

    // Double submit lock
    public $isSubmitting = false;

    public function mount($slug)
    {
        $this->form = Form::with(['sections', 'fields'])->where('slug', $slug)->firstOrFail();

        // Cek status form
        if ($this->form->status !== 'active') {
            abort(403, 'Formulir ini tidak aktif atau sudah ditutup.');
        }

        // Cek range tanggal jika diisi
        if ($this->form->open_at && now() < $this->form->open_at) {
            abort(403, 'Formulir belum dibuka.');
        }
        if ($this->form->close_at && now() > $this->form->close_at) {
            abort(403, 'Formulir sudah ditutup.');
        }

        // Relational check
        if ($this->form->depends_on_form_id) {
            $parentSubmission = Submission::where('form_id', $this->form->depends_on_form_id)
                ->where('nim', Auth::guard('student')->user()->nim)
                ->first();
                
            if (!$parentSubmission || $parentSubmission->status !== 'approved') {
                abort(403, 'Anda belum menyelesaikan formulir prasyarat atau form prasyarat belum disetujui (Approved).');
            }
            $this->parentAnswers = is_string($parentSubmission->answers) ? json_decode($parentSubmission->answers, true) : $parentSubmission->answers;
        }

        $student = Auth::guard('student')->user();

        // Initialize responses
        foreach ($this->form->fields as $field) {
            if ($field->type === 'checkbox') {
                $this->responses[$field->id] = [];
            } elseif ($field->type === 'repeater') {
                $this->responses[$field->id] = [[]]; // 1 empty row
            } elseif ($field->type !== 'file') {
                $suffix = substr($field->name, strpos($field->name, '_') + 1);
                
                // Auto-fill identities
                if ($suffix === 'nim') $this->responses[$field->id] = $student->nim;
                elseif ($suffix === 'student_name') $this->responses[$field->id] = $student->name;
                elseif ($suffix === 'cohort') $this->responses[$field->id] = $student->angkatan;
                
                // Pre-fill from parent form
                elseif (!empty($this->parentAnswers) && $this->findParentAnswer($suffix) !== null) {
                    $this->responses[$field->id] = $this->findParentAnswer($suffix);
                } 
                else {
                    $this->responses[$field->id] = $field->default_value ?: '';
                }
            }
        }

        $this->buildSteps();
        $this->loadMasterData();
    }

    protected function findParentAnswer($suffix)
    {
        $parentFormFields = Form::find($this->form->depends_on_form_id)->fields->pluck('id', 'name');
        foreach ($parentFormFields as $pName => $pId) {
            if (str_ends_with($pName, '_' . $suffix) && isset($this->parentAnswers[$pId])) {
                return $this->parentAnswers[$pId];
            }
        }
        return null;
    }

    protected function loadMasterData()
    {
        foreach ($this->form->fields as $field) {
            if ($field->type === 'master_data') {
                $this->fetchMasterData($field->id, $field->options);
            } elseif ($field->type === 'repeater') {
                $opts = is_string($field->options) ? json_decode($field->options, true) : $field->options;
                if (isset($opts['fields'])) {
                    foreach ($opts['fields'] as $subF) {
                        if ($subF['type'] === 'master_data') {
                            $key = $field->id . '_' . $subF['name'];
                            $this->fetchMasterData($key, $subF['options']);
                        }
                    }
                }
            }
        }
    }

    protected function fetchMasterData($key, $modelName)
    {
        if ($modelName === 'StudyFocus') {
            $this->masterDataOptions[$key] = \App\Models\StudyFocus::where('is_active', true)->pluck('name', 'id')->toArray();
        } elseif ($modelName === 'Lecturer') {
            $this->masterDataOptions[$key] = \App\Models\Lecturer::where('is_active', true)->pluck('name', 'id')->toArray();
        } elseif ($modelName === 'Course') {
            $this->masterDataOptions[$key] = \App\Models\Course::where('is_active', true)->select(DB::raw("CONCAT(code, ' - ', name) as full_name"), 'id')->pluck('full_name', 'id')->toArray();
        }
    }

    protected function buildSteps()
    {
        // Removed logic for steps as we are using a single page form now
    }

    public function addRepeaterRow($fieldId)
    {
        $this->responses[$fieldId][] = [];
    }
    
    public function removeRepeaterRow($fieldId, $index)
    {
        unset($this->responses[$fieldId][$index]);
        $this->responses[$fieldId] = array_values($this->responses[$fieldId]);
        if (count($this->responses[$fieldId]) === 0) {
            $this->responses[$fieldId][] = [];
        }
    }

    public function render()
    {
        return view('livewire.student.student-form-filler')->layout('layouts.student');
    }

    public function isFieldVisible($field)
    {
        if (is_array($field)) {
            return true; // For repeater subfields
        }
        
        if (!$field->is_active) {
            return false;
        }
        if (!is_array($field->conditions) || empty($field->conditions['trigger_field_id'])) {
            return true;
        }

        // Find the actual trigger field object to get its ID
        $triggerFieldName = $field->conditions['trigger_field_id'];
        
        // Find trigger field ID
        $triggerFieldObj = $this->form->fields->where('name', $triggerFieldName)->first();
        if (!$triggerFieldObj) {
            return true;
        }
        $triggerId = $triggerFieldObj->id;
        
        $operator = $field->conditions['operator'] ?? 'equals';
        $val = $field->conditions['value'] ?? '';

        $triggerValue = $this->responses[$triggerId] ?? null;

        if (is_array($triggerValue)) {
            $matched = in_array($val, $triggerValue);
            return $operator === 'equals' ? $matched : !$matched;
        }

        if ($operator === 'equals') {
            return $triggerValue == $val;
        } elseif ($operator === 'not_equals') {
            return $triggerValue != $val;
        } elseif ($operator === 'contains') {
            return str_contains(strtolower($triggerValue), strtolower($val));
        }

        return true;
    }

    public function validateAll()
    {
        $allFields = $this->form->fields;
        $rules = [];
        $messages = [];

        foreach ($allFields as $field) {
            if (!$this->isFieldVisible($field)) continue;

            $fieldRules = [];

            if ($field->is_required) {
                if ($field->type === 'file') {
                    $rules["files.{$field->id}"] = 'required';
                    $messages["files.{$field->id}.required"] = "File {$field->label} wajib diunggah.";
                } elseif ($field->type === 'repeater') {
                    $rules["responses.{$field->id}"] = 'required|array|min:1';
                    $messages["responses.{$field->id}.required"] = "Daftar {$field->label} wajib diisi.";
                } else {
                    $fieldRules[] = 'required';
                    $messages["responses.{$field->id}.required"] = "{$field->label} wajib diisi.";
                }
            } else {
                if ($field->type === 'file') {
                    $rules["files.{$field->id}"] = 'nullable';
                } else {
                    $fieldRules[] = 'nullable';
                }
            }

            if ($field->type === 'repeater') {
                $opts = is_string($field->options) ? json_decode($field->options, true) : $field->options;
                if (isset($opts['fields'])) {
                    foreach ($opts['fields'] as $sub) {
                        $rules["responses.{$field->id}.*.{$sub['name']}"] = 'required';
                        $messages["responses.{$field->id}.*.{$sub['name']}.required"] = "Bagian {$sub['label']} wajib diisi semua baris.";
                    }
                }
            }

            if ($field->type === 'email') {
                $fieldRules[] = 'email';
                $messages["responses.{$field->id}.email"] = "Format email {$field->label} tidak valid.";
            }

            if ($field->type === 'number') {
                $fieldRules[] = 'numeric';
                $messages["responses.{$field->id}.numeric"] = "{$field->label} harus berupa angka.";
            }

            if ($field->type === 'file' && !empty($this->files[$field->id])) {
                $uploaded = $this->files[$field->id];
                $maxKb = ($field->max_size_mb ?: 2) * 1024;
                $allowed = is_array($field->allowed_types) ? strtolower(implode(',', $field->allowed_types)) : 'pdf,jpg,png,docx,xlsx,zip';

                if (is_array($uploaded)) {
                    $rules["files.{$field->id}"] = "array|max:{$field->max_files}";
                    $messages["files.{$field->id}.max"] = "Maksimal berkas {$field->label} adalah {$field->max_files}.";
                    foreach($uploaded as $idx => $f) {
                        if ($f instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            try {
                                if (!$f->exists()) throw new \Exception();
                                $rules["files.{$field->id}.{$idx}"] = "file|max:{$maxKb}|mimes:{$allowed}";
                                $messages["files.{$field->id}.{$idx}.max"] = "Ukuran {$field->label} melebihi {$field->max_size_mb} MB.";
                                $messages["files.{$field->id}.{$idx}.mimes"] = "Format {$field->label} harus: {$allowed}.";
                            } catch (\Exception $e) {
                                $this->addError("files.{$field->id}", "File sementara telah kadaluwarsa, silakan unggah ulang file Anda.");
                            }
                        }
                    }
                } else {
                    if ($uploaded instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                        try {
                            if (!$uploaded->exists()) throw new \Exception();
                            $rules["files.{$field->id}"] = "file|max:{$maxKb}|mimes:{$allowed}";
                            $messages["files.{$field->id}.max"] = "Ukuran {$field->label} melebihi {$field->max_size_mb} MB.";
                            $messages["files.{$field->id}.mimes"] = "Format {$field->label} harus: {$allowed}.";
                        } catch (\Exception $e) {
                            $this->addError("files.{$field->id}", "File sementara telah kadaluwarsa, silakan unggah ulang file Anda.");
                        }
                    }
                }
            }

            if ($field->type !== 'file' && $field->type !== 'repeater' && count($fieldRules) > 0) {
                $rules["responses.{$field->id}"] = implode('|', $fieldRules);
            }
        }

        if (count($rules) > 0) {
            $this->validate($rules, $messages);
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            throw \Illuminate\Validation\ValidationException::withMessages($this->getErrorBag()->toArray());
        }
    }

    public function submit()
    {
        if ($this->isSubmitting) return;

        $this->validateAll();

        $this->isSubmitting = true;
        $student = Auth::guard('student')->user();

        $existingSubmission = Submission::where('form_id', $this->form->id)
            ->where('nim', $student->nim)
            ->first();

        if ($existingSubmission) {
            $this->isSubmitting = false;
            session()->flash('error', 'Anda sudah pernah mengisi formulir ini sebelumnya.');
            return;
        }

        $uploadedPaths = [];

        try {
            DB::beginTransaction();

            $submission = Submission::create([
                'form_id' => $this->form->id,
                'nim' => $student->nim,
                'name' => $student->name,
                'email' => $student->email,
                'status' => 'submitted',
                'submitted_at' => now(),
                'answers' => $this->responses,
            ]);

            foreach ($this->form->fields as $field) {
                if ($field->type === 'file' && !empty($this->files[$field->id])) {
                    $uploaded = $this->files[$field->id];
                    $fileArray = is_array($uploaded) ? $uploaded : [$uploaded];

                    foreach ($fileArray as $file) {
                        $originalName = $file->getClientOriginalName();
                        $mimeType = $file->getMimeType();
                        $sizeBytes = $file->getSize();

                        $path = $file->store('submissions/'.$this->form->id, 'local');
                        $uploadedPaths[] = $path;

                        SubmissionFile::create([
                            'submission_id' => $submission->id,
                            'field_id' => $field->id,
                            'stored_path' => $path,
                            'original_name' => $originalName,
                            'mime_type' => $mimeType,
                            'size_bytes' => $sizeBytes,
                            'uploaded_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();
            session()->flash('message', 'Formulir berhasil dikirim!');
            return redirect()->route('student.dashboard');

        } catch (\Exception $e) {
            DB::rollBack();
            
            foreach ($uploadedPaths as $path) {
                if (\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
                    \Illuminate\Support\Facades\Storage::disk('local')->delete($path);
                }
            }
            
            $this->isSubmitting = false;
            
            \Illuminate\Support\Facades\Log::error('Gagal submit form: ' . $e->getMessage());
            session()->flash('error', 'Form gagal dikirim: ' . $e->getMessage());
        }
    }
}
