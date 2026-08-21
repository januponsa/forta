<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    
    public $userId;
    public $name;
    public $username;
    public $email;
    public $role = 'admin_forta';
    public $is_active = true;
    public $password;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username' . ($this->userId ? ',' . $this->userId : ''),
            'email' => 'required|email|max:255|unique:users,email' . ($this->userId ? ',' . $this->userId : ''),
            'role' => 'required|string',
            'is_active' => 'boolean',
        ];

        if (!$this->userId || $this->password) {
            $rules['password'] = 'required|min:6';
        }

        return $rules;
    }

    public function render()
    {
        $users = User::where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('username', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.user-manager', [
            'users' => $users
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
        $this->userId = null;
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->role = 'admin_forta';
        $this->is_active = true;
        $this->password = '';
    }

    public function store()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(['id' => $this->userId], $data);

        session()->flash('message', $this->userId ? 'User berhasil diperbarui.' : 'User berhasil ditambahkan.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role = $user->role ?? 'admin_forta';
        $this->is_active = $user->is_active;
        $this->password = ''; // Don't populate password
        
        $this->openModal();
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
            return;
        }

        // Check if this is the last admin
        if ($user->role === 'admin_forta') {
            $adminCount = User::where('role', 'admin_forta')->count();
            if ($adminCount <= 1) {
                session()->flash('error', 'Tidak dapat menghapus admin terakhir. Sistem membutuhkan setidaknya satu admin.');
                return;
            }
        }

        $user->delete();
        session()->flash('message', 'User berhasil dihapus.');
    }
}
