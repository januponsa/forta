<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\ReviewerAssignment;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use Livewire\Component;

class SubmissionDetail extends Component
{
    public $submission;
    
    // Field review status
    public $fieldReviewStatuses = [];

    // File review state
    public $fileStatuses = [];
    public $fileNotes = [];

    // Reviewer assignment state
    public $availableReviewers = [];
    public $selectedReviewerId = '';

    public function mount($id)
    {
        $this->submission = Submission::with([
            'form.fields', 
            'files.field', 
            'reviewerAssignments.user'
        ])->findOrFail($id);

        // Initialize field review statuses
        $this->fieldReviewStatuses = $this->submission->field_review_statuses ?: [];

        // Initialize file review statuses and notes
        foreach ($this->submission->files as $file) {
            $this->fileStatuses[$file->id] = $file->review_status ?: 'Belum Diperiksa';
            $this->fileNotes[$file->id] = $file->review_note ?: '';
        }

        // Load available reviewers
        $this->availableReviewers = User::where('role', 'superadmin')
            ->where('is_active', true)
            ->get();
    }

    public function updateStatus($status)
    {
        if (! in_array($status, ['approved', 'rejected', 'revision'])) {
            return;
        }

        $oldStatus = $this->submission->status;
        $this->submission->status = $status;
        $this->submission->reviewed_by = auth()->id();
        $this->submission->reviewed_at = now();
        $this->submission->save();

        AuditLog::create([
            'actor_id' => auth()->id(),
            'actor_role' => auth()->user()->role,
            'action' => 'update_submission_status',
            'target_type' => 'submission',
            'target_id' => $this->submission->id,
            'data_before' => ['status' => $oldStatus],
            'data_after' => ['status' => $status],
            'ip_address' => request()->ip(),
        ]);

        session()->flash('message', 'Status pengajuan berhasil diubah menjadi '.$status.'.');
    }

    public function updateFieldReviewStatus($fieldId, $status)
    {
        if (! in_array($status, ['approved', 'rejected', 'pending'])) {
            return;
        }

        $this->fieldReviewStatuses[$fieldId] = $status;
        $this->submission->field_review_statuses = $this->fieldReviewStatuses;
        $this->submission->save();

        session()->flash('message', 'Status verifikasi kolom berhasil diperbarui.');
    }

    public function updateFileReview($fileId)
    {
        $file = SubmissionFile::findOrFail($fileId);
        
        $file->update([
            'review_status' => $this->fileStatuses[$fileId],
            'review_note' => $this->fileNotes[$fileId],
        ]);

        $this->submission->load('files');
        session()->flash('message', 'Status verifikasi berkas "' . $file->original_name . '" berhasil disimpan.');
    }

    public function assignReviewer()
    {
        if (empty($this->selectedReviewerId)) {
            session()->flash('error', 'Silakan pilih reviewer terlebih dahulu.');
            return;
        }

        $alreadyAssigned = ReviewerAssignment::where('submission_id', $this->submission->id)
            ->where('user_id', $this->selectedReviewerId)
            ->exists();

        if ($alreadyAssigned) {
            session()->flash('error', 'Reviewer tersebut sudah ditugaskan pada pengajuan ini.');
            return;
        }

        ReviewerAssignment::create([
            'submission_id' => $this->submission->id,
            'user_id' => $this->selectedReviewerId,
            'status' => 'Belum Diperiksa',
        ]);

        AuditLog::create([
            'actor_id' => auth()->id(),
            'actor_role' => auth()->user()->role,
            'action' => 'assign_reviewer',
            'target_type' => 'submission',
            'target_id' => $this->submission->id,
            'data_after' => ['reviewer_id' => $this->selectedReviewerId],
            'ip_address' => request()->ip(),
        ]);

        $this->submission->load('reviewerAssignments.user');
        $this->selectedReviewerId = '';

        session()->flash('message', 'Reviewer berhasil ditugaskan.');
    }

    public function removeReviewer($assignmentId)
    {
        $assignment = ReviewerAssignment::findOrFail($assignmentId);
        $reviewerId = $assignment->user_id;
        $assignment->delete();

        AuditLog::create([
            'actor_id' => auth()->id(),
            'actor_role' => auth()->user()->role,
            'action' => 'remove_reviewer',
            'target_type' => 'submission',
            'target_id' => $this->submission->id,
            'data_before' => ['reviewer_id' => $reviewerId],
            'ip_address' => request()->ip(),
        ]);

        $this->submission->load('reviewerAssignments.user');
        session()->flash('message', 'Penugasan reviewer berhasil dihapus.');
    }

    public function updateAssignmentStatus($assignmentId, $status)
    {
        if (! in_array($status, ['Belum Diperiksa', 'Sedang Diperiksa', 'Selesai'])) {
            return;
        }

        $assignment = ReviewerAssignment::findOrFail($assignmentId);
        $oldStatus = $assignment->status;
        $assignment->update(['status' => $status]);

        AuditLog::create([
            'actor_id' => auth()->id(),
            'actor_role' => auth()->user()->role,
            'action' => 'update_assignment_status',
            'target_type' => 'reviewer_assignment',
            'target_id' => $assignmentId,
            'data_before' => ['status' => $oldStatus],
            'data_after' => ['status' => $status],
            'ip_address' => request()->ip(),
        ]);

        $this->submission->load('reviewerAssignments.user');
        session()->flash('message', 'Status peninjauan reviewer berhasil diubah.');
    }

    public function render()
    {
        return view('livewire.admin.submission-detail')
            ->layout('layouts.admin');
    }
}
