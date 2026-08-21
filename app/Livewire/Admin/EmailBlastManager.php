<?php

namespace App\Livewire\Admin;

use App\Models\EmailBlast;
use App\Models\EmailBlastRecipient;
use App\Models\EmailTemplate;
use App\Models\SavedAudience;
use App\Models\Student;
use App\Jobs\ProcessEmailBlast;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class EmailBlastManager extends Component
{
    use WithFileUploads, WithPagination;

    // Audience selection
    public $audienceType = 'all_active'; // all_active, by_batch, manual, filter
    public $selectedBatches = [];
    public $selectedStudents = [];
    public $selectAllStudents = false;
    
    // Filters for manual selection
    public $searchStudent = '';
    public $filterBatch = '';
    public $filterStatus = 'Aktif';

    // Email Data
    public $subject = '';
    public $bodyHtml = '';
    public $deliveryMode = 'bcc'; // to, cc, bcc, individual
    public $attachments = [];
    
    // Schedule
    public $scheduleType = 'now'; // now, scheduled
    public $scheduledAt = '';
    
    // UI state
    public $activeTab = 'audience'; // audience, compose, preview
    
    public function selectAllFiltered()
    {
        // Get all IDs from current filter
        $studentsQuery = Student::query();
        
        if ($this->searchStudent) {
            $studentsQuery->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchStudent . '%')
                  ->orWhere('nim', 'like', '%' . $this->searchStudent . '%')
                  ->orWhere('email', 'like', '%' . $this->searchStudent . '%');
            });
        }
        
        if ($this->filterBatch) {
            $studentsQuery->where('angkatan', $this->filterBatch);
        }
        
        if ($this->filterStatus) {
            $studentsQuery->where('status_akademik', $this->filterStatus);
        }

        $ids = $studentsQuery->pluck('id')->toArray();
        $this->selectedStudents = array_unique(array_merge($this->selectedStudents, $ids));
    }

    public function clearSelection()
    {
        $this->selectedStudents = [];
    }

    public function sendTestEmail()
    {
        $this->validate([
            'subject' => 'required',
            'bodyHtml' => 'required',
        ]);

        $adminEmail = auth()->user()->email;
        $mockRecipient = new EmailBlastRecipient([
            'name' => auth()->user()->name,
            'nim' => 'ADMIN',
            'angkatan' => '-',
            'email' => $adminEmail
        ]);

        $dummyBlast = new EmailBlast([
            'subject' => '[TEST] ' . $this->subject,
        ]);
        
        // Handle placeholders logic manually for test
        $html = strtr($this->bodyHtml, [
            '{{nama}}' => $mockRecipient->name,
            '{{nim}}' => $mockRecipient->nim,
            '{{angkatan}}' => $mockRecipient->angkatan,
            '{{email}}' => $mockRecipient->email,
            '{{program_studi}}' => 'Sistem Informasi'
        ]);

        $mailable = new \App\Mail\EmailBlastMail($dummyBlast, $html);
        \Illuminate\Support\Facades\Mail::to($adminEmail)->send($mailable);

        session()->flash('message', 'Email ujicoba berhasil dikirim ke ' . $adminEmail);
    }

    public function submitCampaign()
    {
        $this->validate([
            'subject' => 'required',
            'bodyHtml' => 'required',
            'deliveryMode' => 'required|in:to,cc,bcc,individual',
        ]);

        // Validate placeholders
        if ($this->deliveryMode !== 'individual') {
            if (strpos($this->bodyHtml, '{{nama}}') !== false || strpos($this->bodyHtml, '{{nim}}') !== false) {
                $this->addError('bodyHtml', 'Placeholder personal ({{nama}}, {{nim}}) hanya dapat digunakan pada mode Kirim Individual.');
                return;
            }
        }

        // Get Final Recipients
        $students = collect();
        if ($this->audienceType === 'all_active') {
            $students = Student::where('status_akademik', 'Aktif')->get();
        } elseif ($this->audienceType === 'by_batch') {
            $students = Student::whereIn('angkatan', $this->selectedBatches)->where('status_akademik', 'Aktif')->get();
        } elseif ($this->audienceType === 'manual') {
            $students = Student::whereIn('id', $this->selectedStudents)->get();
        }

        if ($students->isEmpty()) {
            $this->addError('audienceType', 'Tidak ada penerima yang valid untuk dikirim.');
            return;
        }

        // Create Campaign Snapshot
        $campaign = EmailBlast::create([
            'subject' => $this->subject,
            'body_html' => $this->bodyHtml,
            'delivery_mode' => $this->deliveryMode,
            'target_description' => $this->getTargetDescription(),
            'total_recipients' => $students->count(),
            'status' => $this->scheduleType === 'scheduled' ? 'scheduled' : 'queued',
            'created_by' => auth()->id(),
            'scheduled_at' => $this->scheduleType === 'scheduled' && $this->scheduledAt ? $this->scheduledAt : null,
        ]);

        // Insert Recipients
        $recipientsData = [];
        foreach ($students as $student) {
            $recipientsData[] = [
                'email_blast_id' => $campaign->id,
                'student_id' => $student->id,
                'name' => $student->name,
                'nim' => $student->nim,
                'email' => $student->email,
                'angkatan' => $student->angkatan,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        // Chunk insert
        foreach (array_chunk($recipientsData, 500) as $chunk) {
            EmailBlastRecipient::insert($chunk);
        }

        // Handle Attachments
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $file) {
                $path = $file->store('email_attachments', 'private');
                $campaign->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        // Dispatch Job if now
        if ($campaign->status === 'queued') {
            ProcessEmailBlast::dispatch($campaign);
        }

        session()->flash('message', 'Kampanye email berhasil dibuat dan dimasukkan ke antrean pengiriman.');
        return redirect()->route('admin.email-blast.history');
    }

    private function getTargetDescription()
    {
        if ($this->audienceType === 'all_active') return 'Semua Mahasiswa Aktif';
        if ($this->audienceType === 'by_batch') return 'Angkatan: ' . implode(', ', $this->selectedBatches);
        if ($this->audienceType === 'manual') return 'Pilihan Manual (' . count($this->selectedStudents) . ' mahasiswa)';
        return 'Filter Khusus';
    }

    public function render()
    {
        $studentsQuery = Student::query();
        
        if ($this->searchStudent) {
            $studentsQuery->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchStudent . '%')
                  ->orWhere('nim', 'like', '%' . $this->searchStudent . '%')
                  ->orWhere('email', 'like', '%' . $this->searchStudent . '%');
            });
        }
        
        if ($this->filterBatch) {
            $studentsQuery->where('angkatan', $this->filterBatch);
        }
        
        if ($this->filterStatus) {
            $studentsQuery->where('status_akademik', $this->filterStatus);
        }

        $students = $studentsQuery->paginate(20);
        $availableBatches = Student::select('angkatan')->distinct()->pluck('angkatan');

        return view('livewire.admin.email-blast-manager', compact('students', 'availableBatches'))
            ->layout('layouts.admin');
    }
}
