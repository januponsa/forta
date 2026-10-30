<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
    public $password;
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
            'password' => 'nullable|string|min:6',
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
        $this->password = '';
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

        $normalizedEmail = $this->email ? strtolower(trim($this->email)) : null;

        $data = [
            'nip' => $this->nip ?: null,
            'name' => $this->name,
            'email' => $normalizedEmail,
            'position' => $this->position,
            'is_active' => $this->is_active,
            'include_name' => $this->include_name,
            'include_position' => $this->include_position,
            'include_date' => $this->include_date,
        ];

        // Retrieve existing lecturer if editing
        $lecturer = $this->lecturerId ? Lecturer::find($this->lecturerId) : null;

        if ($this->signature) {
            if ($lecturer && $lecturer->signature_path) {
                Storage::disk('public')->delete($lecturer->signature_path);
            }
            $data['signature_path'] = $this->signature->store('document-assets/signatures', 'public');
        }

        if ($this->stamp) {
            if ($lecturer && $lecturer->stamp_path) {
                Storage::disk('public')->delete($lecturer->stamp_path);
            }
            $data['stamp_path'] = $this->stamp->store('document-assets/stamps', 'public');
        }

        // Sync or create User account so the Lecturer can log in directly
        if ($normalizedEmail || $this->nip) {
            $user = null;
            if ($lecturer && $lecturer->user_id) {
                $user = User::find($lecturer->user_id);
            }
            if (!$user && $normalizedEmail) {
                $user = User::where('normalized_email', $normalizedEmail)
                    ->orWhere('email', $normalizedEmail)
                    ->first();
            }

            $userEmail = $normalizedEmail ?: (strtolower(trim($this->nip)) . '@' . config('auth.admin_domain', 'pradita.ac.id'));
            $baseUsername = $this->nip ? strtolower(trim($this->nip)) : explode('@', $userEmail)[0];

            if ($user) {
                $user->name = $this->name;
                $user->email = $userEmail;
                $user->normalized_email = $userEmail;
                $user->is_active = $this->is_active;
                if ($this->password) {
                    $user->password = Hash::make($this->password);
                }
                $user->save();
            } else {
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter++;
                }

                $defaultPassword = $this->password ?: ($this->nip ?: 'dosen123');
                $user = User::create([
                    'name' => $this->name,
                    'username' => $username,
                    'email' => $userEmail,
                    'normalized_email' => $userEmail,
                    'password' => Hash::make($defaultPassword),
                    'role' => 'dosen',
                    'is_active' => $this->is_active,
                ]);
            }

            $data['user_id'] = $user->id;
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
        $this->password = '';
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
