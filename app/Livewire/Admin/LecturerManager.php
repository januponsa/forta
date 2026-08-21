<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Lecturer;
use Illuminate\Support\Facades\Storage;

class LecturerManager extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $isOpen = false;
    
    public $lecturerId;
    public $nip;
    public $name;
    public $email;
    public $is_active = true;
    
    public $position;
    
    // For signature upload
    public $signature;
    public $existingSignaturePath;
    
    // For structural roles / stamps
    public $stamp;
    public $existingStampPath;
    
    // Configs
    public $include_name = true;
    public $include_position = false;
    public $include_date = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'nip' => 'nullable|string|max:255|unique:lecturers,nip,' . $this->lecturerId,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:lecturers,email,' . $this->lecturerId,
            'position' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'include_name' => 'boolean',
            'include_position' => 'boolean',
            'include_date' => 'boolean',
            'signature' => 'nullable|image|max:2048', // 2MB Max
            'stamp' => 'nullable|image|max:2048', // 2MB Max
        ];
    }

    public function render()
    {
        $lecturers = Lecturer::where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('nip', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.lecturer-manager', [
            'lecturers' => $lecturers
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->lecturerId = null;
        $this->nip = '';
        $this->name = '';
        $this->email = '';
        $this->is_active = true;
        
        $this->position = null;
        $this->include_name = true;
        $this->include_position = false;
        $this->include_date = false;

        $this->signature = null;
        $this->existingSignaturePath = null;
        
        $this->stamp = null;
        $this->existingStampPath = null;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'nip' => $this->nip,
            'name' => $this->name,
            'email' => $this->email,
            'position' => $this->position,
            'is_active' => $this->is_active,
            'include_name' => $this->include_name,
            'include_position' => $this->include_position,
            'include_date' => $this->include_date,
        ];

        // Retrieve existing lecturer if editing
        $lecturer = $this->lecturerId ? Lecturer::find($this->lecturerId) : null;

        if ($this->signature) {
            // Delete old signature if exists
            if ($lecturer && $lecturer->signature_path) {
                Storage::disk('public')->delete($lecturer->signature_path);
            }

            // Store new signature
            $data['signature_path'] = $this->signature->store('document-assets/signatures', 'public');
        }

        if ($this->stamp) {
            if ($lecturer && $lecturer->stamp_path) {
                Storage::disk('public')->delete($lecturer->stamp_path);
            }
            $data['stamp_path'] = $this->stamp->store('document-assets/stamps', 'public');
        }

        Lecturer::updateOrCreate(['id' => $this->lecturerId], $data);

        session()->flash('message', $this->lecturerId ? 'Dosen berhasil diperbarui.' : 'Dosen berhasil ditambahkan.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $lecturer = Lecturer::findOrFail($id);
        $this->lecturerId = $lecturer->id;
        $this->nip = $lecturer->nip;
        $this->name = $lecturer->name;
        $this->email = $lecturer->email;
        $this->position = $lecturer->position;
        $this->is_active = $lecturer->is_active;
        $this->include_name = (bool)$lecturer->include_name;
        $this->include_position = (bool)$lecturer->include_position;
        $this->include_date = (bool)$lecturer->include_date;
        
        $this->existingSignaturePath = $lecturer->signature_path;
        $this->signature = null; // Reset upload field
        
        $this->existingStampPath = $lecturer->stamp_path;
        $this->stamp = null;
        
        $this->openModal();
    }

    public function delete($id)
    {
        $lecturer = Lecturer::findOrFail($id);
        
        if ($lecturer->signature_path) {
            Storage::disk('public')->delete($lecturer->signature_path);
        }

        $lecturer->delete();
        session()->flash('message', 'Dosen berhasil dihapus.');
    }
}
