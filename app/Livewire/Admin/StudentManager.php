<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Student;
use App\Services\PermanentUserDeletionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class StudentManager extends Component
{
    use WithFileUploads, WithPagination;

    public $search = '';
    public $angkatanFilter = '';
    public $statusFilter = '';

    // Create / Edit Modal state
    public $isModalOpen = false;
    public $studentId = null;
    public $nim = '';
    public $name = '';
    public $username = '';
    public $email = '';
    public $password = '';
    public $angkatan = '2024';
    public $status_akademik = 'Aktif';
    public $approval_status = 'approved';
    public $login_enabled = true;

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

    public function createStudent()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function editStudent($id)
    {
        $this->resetValidation();
        $student = Student::withTrashed()->findOrFail($id);

        $this->studentId = $student->id;
        $this->nim = $student->nim;
        $this->name = $student->name;
        $this->username = $student->username ?? explode('@', preg_replace('/\.archived(\.\d+)?$/', '', $student->email))[0];
        $this->email = preg_replace('/\.archived(\.\d+)?$/', '', $student->email);
        $this->password = '';
        $this->angkatan = $student->angkatan;
        $this->status_akademik = $student->status_akademik ?: ($student->academic_status === 'active' ? 'Aktif' : 'Nonaktif');
        $this->approval_status = $student->approval_status ?: 'approved';
        $this->login_enabled = (bool) $student->login_enabled;

        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->resetValidation();
        $this->studentId = null;
        $this->nim = '';
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->angkatan = '2024';
        $this->status_akademik = 'Aktif';
        $this->approval_status = 'approved';
        $this->login_enabled = true;
    }

    public function saveStudent()
    {
        $idSuffix = $this->studentId ? ',' . $this->studentId : '';

        $rules = [
            'nim' => 'required|string|max:50|unique:students,nim' . $idSuffix,
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:100|unique:students,username' . $idSuffix,
            'email' => 'required|email|max:255|unique:students,email' . $idSuffix,
            'angkatan' => 'required|string|max:10',
            'status_akademik' => 'required|string|in:Aktif,Cuti,Lulus,Nonaktif',
            'approval_status' => 'required|string|in:approved,pending,rejected',
            'login_enabled' => 'boolean',
        ];

        if ($this->password) {
            $rules['password'] = 'string|min:6';
        }

        $this->validate($rules);

        $normalizedEmail = strtolower(trim($this->email));
        $cleanUsername = $this->username ? strtolower(trim($this->username)) : explode('@', $normalizedEmail)[0];

        $academicStatusMap = [
            'Aktif' => 'active',
            'Cuti' => 'leave',
            'Lulus' => 'graduated',
            'Nonaktif' => 'inactive',
        ];

        $data = [
            'nim' => trim($this->nim),
            'name' => trim($this->name),
            'username' => $cleanUsername,
            'email' => $normalizedEmail,
            'normalized_email' => $normalizedEmail,
            'angkatan' => trim($this->angkatan),
            'status_akademik' => $this->status_akademik,
            'academic_status' => $academicStatusMap[$this->status_akademik] ?? 'active',
            'approval_status' => $this->approval_status,
            'login_enabled' => (bool) $this->login_enabled,
            'status_akun' => $this->login_enabled ? 'Login Diizinkan' : 'Login Dinonaktifkan',
            'manual_override' => true,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        } elseif (!$this->studentId) {
            $data['password'] = Hash::make('password');
        }

        if ($this->studentId) {
            $student = Student::withTrashed()->findOrFail($this->studentId);
            $before = $student->only(['nim', 'name', 'username', 'email', 'angkatan', 'status_akademik', 'status_akun']);
            $student->update($data);

            AuditLog::create([
                'actor_id' => auth()->id(),
                'actor_role' => auth()->user()->role ?? 'admin_forta',
                'action' => 'update_student',
                'target_type' => 'student',
                'target_id' => $student->nim,
                'data_before' => $before,
                'data_after' => $student->only(['nim', 'name', 'username', 'email', 'angkatan', 'status_akademik', 'status_akun']),
                'ip_address' => request()->ip(),
            ]);

            session()->flash('message', "Data mahasiswa {$student->name} ({$student->nim}) berhasil diperbarui.");
        } else {
            $data['approved_at'] = $this->approval_status === 'approved' ? now() : null;
            $data['approved_by'] = auth()->id();
            $student = Student::create($data);

            AuditLog::create([
                'actor_id' => auth()->id(),
                'actor_role' => auth()->user()->role ?? 'admin_forta',
                'action' => 'create_student',
                'target_type' => 'student',
                'target_id' => $student->nim,
                'data_before' => null,
                'data_after' => $student->only(['nim', 'name', 'username', 'email', 'angkatan']),
                'ip_address' => request()->ip(),
            ]);

            session()->flash('message', "Mahasiswa baru {$student->name} ({$student->nim}) berhasil ditambahkan.");
        }

        $this->closeModal();
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

        session()->flash('message', "Akun login mahasiswa {$student->name} ({$student->nim}) berhasil dinonaktifkan (suspended).");
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

    public function deleteStudent($id, PermanentUserDeletionService $service)
    {
        $student = Student::withTrashed()->findOrFail($id);

        try {
            DB::beginTransaction();

            $name = $student->name;
            $nim = $student->nim;
            $service->deleteStudentPermanently($student, auth()->user(), request()->ip());

            DB::commit();

            session()->flash('message', "Mahasiswa {$name} ({$nim}) berhasil dihapus dari sistem.");
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', "Gagal menghapus mahasiswa: " . $e->getMessage());
        }
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
                  ->orWhere('username', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->angkatanFilter) {
            $query->where('angkatan', $this->angkatanFilter);
        }

        if ($this->statusFilter === 'active') {
            $query->where('status_akademik', 'Aktif')->where('login_enabled', true);
        } elseif ($this->statusFilter === 'disabled') {
            $query->where(function ($q) {
                $q->where('status_akun', 'Login Dinonaktifkan')
                  ->orWhere('login_enabled', false);
            });
        }

        $students = $query->orderBy('angkatan', 'asc')
                          ->orderBy('nim', 'asc')
                          ->paginate(15);

        $totalActive = Student::where('status_akademik', 'Aktif')->where('login_enabled', true)->count();
        $angkatans = Student::withTrashed()->select('angkatan')->distinct()->orderBy('angkatan')->pluck('angkatan');

        return view('livewire.admin.student-manager', compact('students', 'totalActive', 'angkatans'))
            ->layout('layouts.admin');
    }
}
