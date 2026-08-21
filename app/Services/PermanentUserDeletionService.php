<?php

namespace App\Services;

use App\Jobs\CleanupStudentFilesJob;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\ReviewerAssignment;
use App\Models\StudentRegistrationRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PermanentUserDeletionService
{
    /**
     * Delete a student permanently along with all submissions, files, and logs.
     *
     * @param Student $student
     * @param User $actor
     * @param string $ipAddress
     * @return int The total number of freed bytes from deleted files.
     */
    public function deleteStudentPermanently(Student $student, User $actor, string $ipAddress): int
    {
        $freedBytes = 0;
        $filePaths = [];

        // Save student state before deleting for audit trail
        $dataBefore = [
            'nim' => $student->nim,
            'name' => $student->name,
            'email' => $student->email,
            'angkatan' => $student->angkatan,
            'status_akademik' => $student->status_akademik,
            'submissions_count' => Submission::where('nim', $student->nim)->count(),
        ];

        DB::transaction(function () use ($student, $actor, $ipAddress, &$freedBytes, &$filePaths, $dataBefore) {
            // 1. Get submissions and associated files
            $submissions = Submission::where('nim', $student->nim)->get();

            foreach ($submissions as $submission) {
                // Get submission files
                $files = SubmissionFile::where('submission_id', $submission->id)->get();
                foreach ($files as $file) {
                    $filePaths[] = $file->stored_path;
                    $freedBytes += $file->size_bytes;
                    $file->delete();
                }

                // Delete reviewer assignments
                ReviewerAssignment::where('submission_id', $submission->id)->delete();

                // Delete submission itself
                $submission->delete();
            }

            // 2. Delete registration request if exists
            StudentRegistrationRequest::where('nim', $student->nim)
                ->orWhere('student_id', $student->id)
                ->delete();

            // 3. Delete authentication logs
            DB::table('authentication_logs')
                ->where('actor_type', 'student')
                ->where('actor_id', $student->id)
                ->delete();

            // 4. Record Audit Log
            AuditLog::create([
                'actor_id' => $actor->id,
                'actor_role' => $actor->role,
                'action' => 'permanent_delete_student',
                'target_type' => 'student',
                'target_id' => $student->nim,
                'data_before' => $dataBefore,
                'data_after' => null,
                'freed_bytes' => $freedBytes,
                'ip_address' => $ipAddress,
            ]);

            // 5. Force delete the student record (this overrides soft delete)
            $student->forceDelete();
        });

        // 6. Dispatch physical file cleanup job in queue
        if (!empty($filePaths)) {
            CleanupStudentFilesJob::dispatch($filePaths);
        }

        return $freedBytes;
    }
}
