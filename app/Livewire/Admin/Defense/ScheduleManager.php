<?php

namespace App\Livewire\Admin\Defense;

use Livewire\Component;
use App\Models\DefenseCase;
use App\Models\DefenseSchedule;
use App\Models\DefenseAssignment;
use App\Models\Lecturer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleManager extends Component
{
    public $semesterFilter = '';
    public $statusFilter = ''; // '', 'scheduled', 'unscheduled'
    public $search = '';
    
    // Modal Form fields
    public $isModalOpen = false;
    public $caseId = null;
    public $studentName = '';
    public $studentNim = '';
    public $reportTitle = '';
    public $hasExistingSchedule = false;
    
    public $date = '';
    public $start_time = '';
    public $end_time = '';
    public $room_or_link = '';
    
    public $supervisor_id = '';
    public $examiner_id = '';

    // Detected Conflicts
    public $supervisorConflict = null;
    public $examinerConflict = null;
    public $roomConflict = null;
    
    protected $rules = [
        'date' => 'required|date',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'room_or_link' => 'required|string',
        'supervisor_id' => 'required|exists:lecturers,id',
        'examiner_id' => 'required|exists:lecturers,id|different:supervisor_id',
    ];

    protected $messages = [
        'date.required' => 'Tanggal sidang wajib diisi.',
        'date.date' => 'Format tanggal tidak valid.',
        'start_time.required' => 'Waktu mulai sidang wajib diisi.',
        'end_time.required' => 'Waktu selesai sidang wajib diisi.',
        'end_time.after' => 'Waktu selesai harus setelah waktu mulai.',
        'room_or_link.required' => 'Ruangan atau tautan sidang daring wajib diisi.',
        'supervisor_id.required' => 'Dosen pembimbing wajib dipilih.',
        'supervisor_id.exists' => 'Dosen pembimbing tidak valid.',
        'examiner_id.required' => 'Dosen penguji wajib dipilih.',
        'examiner_id.exists' => 'Dosen penguji tidak valid.',
        'examiner_id.different' => 'Dosen penguji tidak boleh sama dengan dosen pembimbing.',
    ];

    public function updatedDate()
    {
        $this->checkConflicts();
    }

    public function updatedStartTime()
    {
        $this->checkConflicts();
    }

    public function updatedEndTime()
    {
        $this->checkConflicts();
    }

    public function updatedSupervisorId()
    {
        $this->checkConflicts();
    }

    public function updatedExaminerId()
    {
        $this->checkConflicts();
    }

    public function updatedRoomOrLink()
    {
        $this->checkConflicts();
    }

    public function checkConflicts()
    {
        $this->supervisorConflict = null;
        $this->examinerConflict = null;
        $this->roomConflict = null;

        if (!$this->date || !$this->start_time || !$this->end_time) {
            return;
        }

        // 1. Cek bentrok dosen pembimbing
        if ($this->supervisor_id) {
            $this->supervisorConflict = $this->checkLecturerConflict(
                $this->supervisor_id,
                $this->date,
                $this->start_time,
                $this->end_time,
                $this->caseId
            );
        }

        // 2. Cek bentrok dosen penguji
        if ($this->examiner_id) {
            $this->examinerConflict = $this->checkLecturerConflict(
                $this->examiner_id,
                $this->date,
                $this->start_time,
                $this->end_time,
                $this->caseId
            );
        }

        // 3. Cek bentrok ruangan
        if ($this->room_or_link) {
            $this->roomConflict = $this->checkRoomConflict(
                $this->room_or_link,
                $this->date,
                $this->start_time,
                $this->end_time,
                $this->caseId
            );
        }
    }

    protected function checkLecturerConflict($lecturerId, $date, $startTime, $endTime, $excludeCaseId)
    {
        if (!$lecturerId || !$date || !$startTime || !$endTime) {
            return null;
        }

        // Normalisasi format HH:MM:SS
        $sTime = strlen($startTime) === 5 ? $startTime . ':00' : $startTime;
        $eTime = strlen($endTime) === 5 ? $endTime . ':00' : $endTime;

        if ($sTime >= $eTime) {
            return null;
        }

        $conflict = DefenseAssignment::where('lecturer_id', $lecturerId)
            ->where('defense_case_id', '!=', $excludeCaseId)
            ->whereHas('defenseCase', function($q) {
                $q->whereNotIn('status', ['cancelled', 'failed']);
            })
            ->whereHas('defenseCase.schedules', function($q) use ($date, $sTime, $eTime) {
                $q->whereDate('date', $date)
                  ->where('start_time', '<', $eTime)
                  ->where('end_time', '>', $sTime);
            })
            ->with(['defenseCase.student', 'defenseCase.latestSchedule', 'lecturer'])
            ->first();

        if ($conflict && $conflict->defenseCase && $conflict->defenseCase->latestSchedule) {
            $sched = $conflict->defenseCase->latestSchedule;
            $st = $sched->start_time instanceof Carbon ? $sched->start_time->format('H:i') : substr((string)$sched->start_time, 0, 5);
            $et = $sched->end_time instanceof Carbon ? $sched->end_time->format('H:i') : substr((string)$sched->end_time, 0, 5);
            $roleLabel = $conflict->role === 'supervisor' ? 'Pembimbing' : 'Penguji';
            $studentName = $conflict->defenseCase->student->name ?? 'Mahasiswa Lain';
            $lecturerName = $conflict->lecturer->name ?? 'Dosen';

            return [
                'lecturer_name' => $lecturerName,
                'student_name' => $studentName,
                'role' => $roleLabel,
                'time' => "{$st} - {$et} WIB",
                'room' => $sched->room_or_link,
                'message' => "Jadwal bentrok: {$lecturerName} sudah terjadwal sebagai {$roleLabel} sidang {$studentName} pada jam {$st} - {$et} WIB (" . ($sched->room_or_link ?: 'Ruangan -') . ").",
            ];
        }

        return null;
    }

    protected function checkRoomConflict($room, $date, $startTime, $endTime, $excludeCaseId)
    {
        if (!$room || !$date || !$startTime || !$endTime) {
            return null;
        }

        $trimmedRoom = trim($room);
        // Lewati jika tautan online generic
        $lowerRoom = strtolower($trimmedRoom);
        if (str_contains($lowerRoom, 'http') || str_contains($lowerRoom, 'zoom') || str_contains($lowerRoom, 'meet')) {
            return null;
        }

        $sTime = strlen($startTime) === 5 ? $startTime . ':00' : $startTime;
        $eTime = strlen($endTime) === 5 ? $endTime . ':00' : $endTime;

        if ($sTime >= $eTime) {
            return null;
        }

        $conflict = DefenseSchedule::where('room_or_link', $trimmedRoom)
            ->where('defense_case_id', '!=', $excludeCaseId)
            ->whereHas('defenseCase', function($q) {
                $q->whereNotIn('status', ['cancelled', 'failed']);
            })
            ->whereDate('date', $date)
            ->where('start_time', '<', $eTime)
            ->where('end_time', '>', $sTime)
            ->with('defenseCase.student')
            ->first();

        if ($conflict && $conflict->defenseCase) {
            $st = $conflict->start_time instanceof Carbon ? $conflict->start_time->format('H:i') : substr((string)$conflict->start_time, 0, 5);
            $et = $conflict->end_time instanceof Carbon ? $conflict->end_time->format('H:i') : substr((string)$conflict->end_time, 0, 5);
            $studentName = $conflict->defenseCase->student->name ?? 'Mahasiswa Lain';

            return [
                'room' => $trimmedRoom,
                'student_name' => $studentName,
                'time' => "{$st} - {$et} WIB",
                'message' => "Ruangan bentrok: Ruangan '{$trimmedRoom}' sudah terpakai pada jam {$st} - {$et} WIB untuk sidang {$studentName}.",
            ];
        }

        return null;
    }

    public function openModal($id)
    {
        $case = DefenseCase::with(['schedules', 'assignments', 'student'])->findOrFail($id);
        $this->caseId = $case->id;
        $this->studentName = $case->student->name ?? 'Unknown';
        $this->studentNim = $case->student->nim ?? '-';
        $this->reportTitle = $case->metadata['report_title'] ?? 'Laporan Kerja Praktik';
        
        $schedule = $case->latestSchedule;
        if ($schedule) {
            $this->hasExistingSchedule = true;
            $this->date = $schedule->date ? ($schedule->date instanceof Carbon ? $schedule->date->format('Y-m-d') : substr((string)$schedule->date, 0, 10)) : '';
            $this->start_time = $schedule->start_time ? ($schedule->start_time instanceof Carbon ? $schedule->start_time->format('H:i') : substr((string)$schedule->start_time, 0, 5)) : '';
            $this->end_time = $schedule->end_time ? ($schedule->end_time instanceof Carbon ? $schedule->end_time->format('H:i') : substr((string)$schedule->end_time, 0, 5)) : '';
            $this->room_or_link = $schedule->room_or_link ?? '';
        } else {
            $this->hasExistingSchedule = false;
            $this->date = '';
            $this->start_time = '09:00';
            $this->end_time = '10:00';
            $this->room_or_link = 'Ruang Sidang 101';
        }
        
        $supervisor = $case->assignments()->where('role', 'supervisor')->first();
        $this->supervisor_id = $supervisor ? (string)$supervisor->lecturer_id : '';
        
        $examiner = $case->assignments()->where('role', 'examiner')->first();
        $this->examiner_id = $examiner ? (string)$examiner->lecturer_id : '';
        
        $this->resetValidation();
        $this->checkConflicts();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset([
            'caseId', 'studentName', 'studentNim', 'reportTitle', 'hasExistingSchedule',
            'date', 'start_time', 'end_time', 'room_or_link', 'supervisor_id', 'examiner_id',
            'supervisorConflict', 'examinerConflict', 'roomConflict'
        ]);
        $this->resetValidation();
    }

    public function saveSchedule()
    {
        $this->validate();
        
        // Cek bentrok sebelum menyimpan
        $this->checkConflicts();

        $hasConflict = false;

        if ($this->supervisorConflict) {
            $this->addError('supervisor_id', $this->supervisorConflict['message']);
            $hasConflict = true;
        }

        if ($this->examinerConflict) {
            $this->addError('examiner_id', $this->examinerConflict['message']);
            $hasConflict = true;
        }

        if ($this->roomConflict) {
            $this->addError('room_or_link', $this->roomConflict['message']);
            $hasConflict = true;
        }

        if ($hasConflict) {
            session()->flash('error', 'Tidak dapat menjadwalkan karena terdapat jadwal dosen atau ruangan yang bentrok!');
            return;
        }

        DB::beginTransaction();
        try {
            $case = DefenseCase::findOrFail($this->caseId);
            
            // 1. Simpan Jadwal Sidang
            DefenseSchedule::updateOrCreate(
                ['defense_case_id' => $case->id],
                [
                    'date' => $this->date,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'room_or_link' => $this->room_or_link,
                ]
            );
            
            // 2. Simpan Penugasan Dosen Pembimbing
            DefenseAssignment::updateOrCreate(
                ['defense_case_id' => $case->id, 'role' => 'supervisor'],
                ['lecturer_id' => $this->supervisor_id]
            );
            
            // 3. Simpan Penugasan Dosen Penguji
            DefenseAssignment::updateOrCreate(
                ['defense_case_id' => $case->id, 'role' => 'examiner'],
                ['lecturer_id' => $this->examiner_id]
            );
            
            // 4. Update status kasus ke 'scheduled' jika masih tahap registrasi/menunggu jadwal
            if (in_array($case->status, ['registered', 'documents_verified', 'waiting_schedule'])) {
                $case->update(['status' => 'scheduled']);
            }
            
            DB::commit();
            
            session()->flash('message', "Jadwal dan penugasan dosen untuk {$case->student->name} berhasil disimpan.");
            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan jadwal: ' . $e->getMessage());
        }
    }

    public function deleteSchedule($id)
    {
        DB::beginTransaction();
        try {
            $case = DefenseCase::with('student')->findOrFail($id);
            
            // Hapus record jadwal
            DefenseSchedule::where('defense_case_id', $case->id)->delete();
            
            // Hapus penugasan penguji dan pembimbing
            DefenseAssignment::where('defense_case_id', $case->id)->delete();
            
            // Reset status kasus jika scheduled
            if ($case->status === 'scheduled') {
                $case->update(['status' => 'waiting_schedule']);
            }
            
            DB::commit();
            
            session()->flash('message', "Jadwal sidang untuk {$case->student->name} berhasil dihapus/dibatalkan.");
            
            if ($this->caseId == $id) {
                $this->closeModal();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }

    public function setQuickRoom($room)
    {
        $this->room_or_link = $room;
        $this->checkConflicts();
    }

    public function render()
    {
        $query = DefenseCase::with(['student', 'latestSchedule', 'assignments.lecturer'])
                            ->where('defense_type', 'internship_defense');
                            
        if ($this->semesterFilter) {
            $query->where('semester', $this->semesterFilter);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('student', function($sq) {
                    $sq->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('nim', 'like', '%' . $this->search . '%');
                })->orWhere('metadata', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter === 'scheduled') {
            $query->where('status', 'scheduled');
        } elseif ($this->statusFilter === 'unscheduled') {
            $query->whereIn('status', ['registered', 'documents_verified', 'waiting_schedule']);
        }
        
        $cases = $query->orderBy('created_at', 'desc')->get();
        $semesters = DefenseCase::select('semester')->distinct()->pluck('semester');
        
        // Fetch active lecturers and compute busy status for currently selected date/times
        $lecturers = Lecturer::where('is_active', true)->orderBy('name')->get();
        
        $busyLecturerIds = [];
        if ($this->isModalOpen && $this->date && $this->start_time && $this->end_time && $this->start_time < $this->end_time) {
            $sTime = strlen($this->start_time) === 5 ? $this->start_time . ':00' : $this->start_time;
            $eTime = strlen($this->end_time) === 5 ? $this->end_time . ':00' : $this->end_time;

            $busyLecturerIds = DefenseAssignment::where('defense_case_id', '!=', $this->caseId)
                ->whereHas('defenseCase', function($q) {
                    $q->whereNotIn('status', ['cancelled', 'failed']);
                })
                ->whereHas('defenseCase.schedules', function($q) use ($sTime, $eTime) {
                    $q->whereDate('date', $this->date)
                      ->where('start_time', '<', $eTime)
                      ->where('end_time', '>', $sTime);
                })
                ->pluck('lecturer_id')
                ->unique()
                ->toArray();
        }
        
        return view('livewire.admin.defense.schedule-manager', [
            'cases' => $cases,
            'semesters' => $semesters,
            'lecturers' => $lecturers,
            'busyLecturerIds' => $busyLecturerIds,
        ])->layout('layouts.admin');
    }
}
