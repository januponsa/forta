<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Student;
use App\Services\PermanentUserDeletionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class StudentManager extends Component
{
    use WithFileUploads, WithPagination;

    public $search = '';
    public $angkatanFilter = '';
    public $statusFilter = '';

    // Deletion confirmation state
    public $confirmingStudentDeletion = false;
    public $studentIdBeingDeleted = null;
    public $nimConfirmInput = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingAngkatanFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function disableStudent($id)
    {
        $student = Student::findOrFail($id);
        $student->update([
            'status_akun' => 'Login Dinonaktifkan',
            'login_enabled' => false,
        ]);

        AuditLog::create([
            'actor_id' => auth()->id(),
            'actor_role' => auth()->user()->role,
            'action' => 'disable_student',
            'target_type' => 'student',
            'target_id' => $student->nim,
            'data_before' => ['status_akun' => 'Login Diizinkan'],
            'data_after' => ['status_akun' => 'Login Dinonaktifkan'],
            'ip_address' => request()->ip(),
        ]);

        session()->flash('message', "Akun login mahasiswa {$student->name} ({$student->nim}) berhasil dinonaktifkan.");
    }

    public function enableStudent($id)
    {
        $student = Student::findOrFail($id);
        $student->update([
            'status_akun' => 'Login Diizinkan',
            'login_enabled' => true,
        ]);

        AuditLog::create([
            'actor_id' => auth()->id(),
            'actor_role' => auth()->user()->role,
            'action' => 'enable_student',
            'target_type' => 'student',
            'target_id' => $student->nim,
            'data_before' => ['status_akun' => 'Login Dinonaktifkan'],
            'data_after' => ['status_akun' => 'Login Diizinkan'],
            'ip_address' => request()->ip(),
        ]);

        session()->flash('message', "Akun login mahasiswa {$student->name} ({$student->nim}) berhasil diaktifkan kembali.");
    }

    public function archiveStudent($id)
    {
        $student = Student::findOrFail($id);

        $newEmail = $student->email;
        if (!str_ends_with($newEmail, '.archived')) {
            $newEmail = $student->email . '.archived.' . $student->id;
        }

        $student->update([
            'academic_status' => 'archived',
            'login_enabled' => false,
            'status_akademik' => 'Diarsipkan',
            'status_akun' => 'Login Dinonaktifkan',
            'email' => $newEmail,
            'normalized_email' => strtolower($newEmail),
        ]);

        $student->delete(); // Soft delete

        AuditLog::create([
            'actor_id' => auth()->id(),
            'actor_role' => auth()->user()->role,
            'action' => 'archive_student',
            'target_type' => 'student',
            'target_id' => $student->nim,
            'data_before' => ['status_akademik' => 'Aktif'],
            'data_after' => ['status_akademik' => 'Diarsipkan'],
            'ip_address' => request()->ip(),
        ]);

        session()->flash('message', "Mahasiswa {$student->name} ({$student->nim}) berhasil diarsipkan.");
    }

    public function restoreStudent($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);

        // Strip archived emails to get original email
        $originalEmail = preg_replace('/\.archived\.\d+$/', '', $student->email);
        $originalEmail = preg_replace('/\.archived$/', '', $originalEmail);

        // Check email conflicts with active students
        $conflict = Student::where('email', $originalEmail)->first();
        if ($conflict) {
            session()->flash('error', "Gagal me-restore. Email '{$originalEmail}' sudah digunakan oleh mahasiswa aktif {$conflict->name} ({$conflict->nim}).");
            return;
        }

        $student->restore();

        $student->update([
            'email' => $originalEmail,
            'normalized_email' => strtolower($originalEmail),
            'academic_status' => 'active',
            'login_enabled' => true,
            'status_akademik' => 'Aktif',
            'status_akun' => 'Login Diizinkan',
        ]);

        AuditLog::create([
            'actor_id' => auth()->id(),
            'actor_role' => auth()->user()->role,
            'action' => 'restore_student',
            'target_type' => 'student',
            'target_id' => $student->nim,
            'data_before' => ['status_akademik' => 'Diarsipkan'],
            'data_after' => ['status_akademik' => 'Aktif'],
            'ip_address' => request()->ip(),
        ]);

        session()->flash('message', "Data mahasiswa {$student->name} ({$student->nim}) berhasil direstore.");
    }

    public function startDeleteStudent($id)
    {
        if (Gate::denies('users.delete_permanently')) {
            abort(403, 'Anda tidak memiliki otorisasi untuk menghapus mahasiswa secara permanen.');
        }

        $this->studentIdBeingDeleted = $id;
        $this->confirmingStudentDeletion = true;
        $this->nimConfirmInput = '';
    }

    public function cancelDeleteStudent()
    {
        $this->confirmingStudentDeletion = false;
        $this->studentIdBeingDeleted = null;
        $this->nimConfirmInput = '';
    }

    public function deleteStudentPermanently(PermanentUserDeletionService $service)
    {
        if (Gate::denies('users.delete_permanently')) {
            abort(403, 'Anda tidak memiliki otorisasi untuk menghapus mahasiswa secara permanen.');
        }

        $student = Student::withTrashed()->findOrFail($this->studentIdBeingDeleted);

        if ($this->nimConfirmInput !== 'HAPUS ' . $student->nim) {
            session()->flash('error', "Konfirmasi tidak cocok. Anda harus mengetikkan 'HAPUS {$student->nim}'.");
            return;
        }

        try {
            DB::beginTransaction();
            
            $freedBytes = $service->deleteStudentPermanently($student, auth()->user(), request()->ip());
            $freedKb = round($freedBytes / 1024, 2);

            $name = $student->name;
            $nim = $student->nim;

            DB::commit();

            $this->cancelDeleteStudent();
            session()->flash('message', "Mahasiswa {$name} ({$nim}) berhasil dihapus secara permanen. Ruang penyimpanan yang dibebaskan: {$freedKb} KB.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->cancelDeleteStudent();
            session()->flash('error', "Gagal menghapus mahasiswa secara permanen: " . $e->getMessage());
        }
    }

    public function render()
    {
        // Build query based on statusFilter
        if ($this->statusFilter === 'archived') {
            $query = Student::onlyTrashed();
        } else {
            $query = Student::query(); // Include active & disabled, exclude soft deleted
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('nim', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->angkatanFilter) {
            $query->where('angkatan', $this->angkatanFilter);
        }

        if ($this->statusFilter === 'active') {
            $query->where('status_akademik', 'Aktif')->where('login_enabled', true);
        } elseif ($this->statusFilter === 'disabled') {
            $query->where('status_akun', 'Login Dinonaktifkan');
        }

        $students = $query->orderBy('angkatan', 'asc')
                          ->orderBy('nim', 'asc')
                          ->paginate(15);

        $totalActive = Student::where('status_akademik', 'Aktif')->where('login_enabled', true)->count();

        return view('livewire.admin.student-manager', compact('students', 'totalActive'))
            ->layout('layouts.admin');
    }
}
