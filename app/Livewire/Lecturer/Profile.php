<?php

namespace App\Livewire\Lecturer;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Lecturer;

class Profile extends Component
{
    use WithFileUploads;

    public $signature;
    public $existingSignaturePath;
    
    public function mount()
    {
        $lecturer = Lecturer::where('user_id', Auth::id())->first();
        if ($lecturer) {
            $this->existingSignaturePath = $lecturer->signature_path;
        }
    }

    public function saveSignature()
    {
        $this->validate([
            'signature' => 'image|max:2048', // 2MB Max
        ]);

        $lecturer = Lecturer::where('user_id', Auth::id())->first();
        
        if (!$lecturer) {
            session()->flash('error', 'Profil Dosen tidak ditemukan.');
            return;
        }

        if ($this->signature) {
            // Delete old signature if exists
            if ($lecturer->signature_path) {
                Storage::disk('public')->delete($lecturer->signature_path);
            }

            // Store new signature
            $path = $this->signature->store('document-assets/signatures', 'public');
            
            $lecturer->update([
                'signature_path' => $path
            ]);

            $this->existingSignaturePath = $path;
            $this->signature = null; // Reset upload field
            
            session()->flash('message', 'Tanda tangan berhasil diperbarui.');
        }
    }

    public function render()
    {
        return view('livewire.lecturer.profile')
            ->layout('layouts.admin'); // Use existing layout
    }
}
