<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SyncOfficialStudentRosterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forta:students:sync-official-roster {--dry-run : Simulate the synchronization} {--apply : Execute the synchronization}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize the active student roster with the official kanonical list.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $isApply = $this->option('apply');

        if (!$isDryRun && !$isApply) {
            $this->error('You must specify either --dry-run or --apply option.');
            return Command::FAILURE;
        }

        if ($isDryRun && $isApply) {
            $this->error('Cannot use both --dry-run and --apply simultaneously.');
            return Command::FAILURE;
        }

        $dataPath = database_path('data/official_active_students_2023_2025.json');
        
        if (!File::exists($dataPath)) {
            $this->error("Official data file not found at: {$dataPath}");
            return Command::FAILURE;
        }

        $sourceData = json_decode(File::get($dataPath), true);

        // Validation 1: Exact 122 rows
        if (count($sourceData) !== 122) {
            $this->error('Invalid source data: Expected exactly 122 students, found ' . count($sourceData));
            return Command::FAILURE;
        }

        // Validation 2: Distribution by Angkatan
        $count2023 = 0;
        $count2024 = 0;
        $count2025 = 0;
        $nims = [];
        $emails = [];

        foreach ($sourceData as $s) {
            if ($s['angkatan'] == 2023) $count2023++;
            if ($s['angkatan'] == 2024) $count2024++;
            if ($s['angkatan'] == 2025) $count2025++;

            if (strlen($s['nim']) !== 10) {
                $this->error("Invalid NIM length for {$s['nim']}");
                return Command::FAILURE;
            }

            if (isset($nims[$s['nim']])) {
                $this->error("Duplicate NIM in source data: {$s['nim']}");
                return Command::FAILURE;
            }
            $nims[$s['nim']] = true;

            if (isset($emails[$s['email']])) {
                $this->error("Duplicate Email in source data: {$s['email']}");
                return Command::FAILURE;
            }
            $emails[$s['email']] = true;

            if (!str_ends_with($s['email'], '@student.pradita.ac.id')) {
                $this->error("Invalid email suffix for {$s['email']}");
                return Command::FAILURE;
            }
        }

        if ($count2023 !== 25 || $count2024 !== 40 || $count2025 !== 57) {
            $this->error("Invalid distribution. Expected 25/40/57, got {$count2023}/{$count2024}/{$count2025}");
            return Command::FAILURE;
        }

        $this->info("Source data validation passed (122 valid records).");

        // Backup existing data
        $existingStudents = Student::withTrashed()->get();
        if ($isApply) {
            $backupFilename = 'backups/students-before-official-sync-' . now()->format('Ymd-His') . '.json';
            Storage::put($backupFilename, $existingStudents->toJson(JSON_PRETTY_PRINT));
            $this->info("Backup created at: storage/app/{$backupFilename}");
        }

        $stats = [
            'created' => 0,
            'updated' => 0,
            'archived' => 0,
            'conflicts' => 0
        ];

        // Process data
        if ($isApply) {
            DB::beginTransaction();
        }

        try {
            $targetNims = array_keys($nims);

            // Archive others FIRST to free up any conflicting emails from dummy accounts
            $studentsToArchive = Student::withTrashed()->whereNotIn('nim', $targetNims)->get();
            foreach ($studentsToArchive as $sa) {
                if ($isApply) {
                    // To free up the unique email index, append timestamp/id if it's not already archived
                    $newEmail = $sa->email;
                    if (!str_ends_with($newEmail, '.archived')) {
                        $newEmail = $sa->email . '.archived.' . $sa->id;
                    }
                    $sa->update([
                        'academic_status' => 'archived',
                        'login_enabled' => false,
                        'email' => $newEmail,
                        'normalized_email' => strtolower($newEmail),
                    ]);
                    if (!$sa->trashed()) {
                        $sa->delete(); // Soft delete
                    }
                }
                $stats['archived']++;
            }

            // Sync targets
            foreach ($sourceData as $target) {
                $student = Student::withTrashed()->where('nim', $target['nim'])->first();

                if (!$student) {
                    // Check if email belongs to another NIM
                    $emailConflict = Student::withTrashed()->where('email', $target['email'])->first();
                    if ($emailConflict) {
                        $this->warn("Email conflict: {$target['email']} already used by NIM {$emailConflict->nim}");
                        $stats['conflicts']++;
                        if ($isApply) {
                            DB::rollBack();
                            $this->error("Transaction aborted due to email conflict.");
                            return Command::FAILURE;
                        }
                        continue;
                    }

                    if ($isApply) {
                        Student::create([
                            'nim' => $target['nim'],
                            'name' => $target['name'],
                            'email' => $target['email'],
                            'normalized_email' => strtolower($target['email']),
                            'angkatan' => $target['angkatan'],
                            'academic_status' => 'active',
                            'login_enabled' => true,
                            'approval_status' => 'approved',
                            'source_type' => 'official_roster',
                            'source_batch' => 'Data Mahasiswa IF 2023-2025',
                            'approved_at' => now(),
                        ]);
                    }
                    $stats['created']++;
                } else {
                    $emailConflict = Student::withTrashed()->where('email', $target['email'])->where('id', '!=', $student->id)->first();
                    if ($emailConflict) {
                        $this->warn("Email conflict for NIM {$target['nim']}: {$target['email']} already used by NIM {$emailConflict->nim}");
                        $stats['conflicts']++;
                        if ($isApply) {
                            DB::rollBack();
                            $this->error("Transaction aborted due to email conflict.");
                            return Command::FAILURE;
                        }
                        continue;
                    }

                    if ($isApply) {
                        $student->restore(); // Restore if it was soft deleted
                        $student->update([
                            'name' => $target['name'],
                            'email' => $target['email'],
                            'normalized_email' => strtolower($target['email']),
                            'angkatan' => $target['angkatan'],
                            'academic_status' => 'active',
                            'login_enabled' => true,
                            'approval_status' => 'approved',
                            'source_type' => 'official_roster',
                            'source_batch' => 'Data Mahasiswa IF 2023-2025',
                            'approved_at' => $student->approved_at ?? now(),
                        ]);
                    }
                    $stats['updated']++;
                }
            }

            if ($isApply) {
                DB::commit();
                $this->info("Synchronization successfully applied.");
            } else {
                $this->info("Dry-run completed successfully.");
            }

            // Report
            $this->table(
                ['Action', 'Count'],
                [
                    ['Created', $stats['created']],
                    ['Updated', $stats['updated']],
                    ['Archived/Soft Deleted', $stats['archived']],
                    ['Conflicts', $stats['conflicts']],
                ]
            );

            // Final active count check
            if ($isApply) {
                $finalCount = Student::activeAndApproved()->count();
                $this->info("Final total active students: {$finalCount}");
                if ($finalCount !== 122) {
                    $this->error("WARNING: Final count is {$finalCount}, expected 122.");
                }
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            if ($isApply) {
                DB::rollBack();
                $this->error("Transaction rolled back due to error: " . $e->getMessage());
            }
            return Command::FAILURE;
        }
    }
}
