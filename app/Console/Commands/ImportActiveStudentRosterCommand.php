<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;

class ImportActiveStudentRosterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forta:students:import-active-roster';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import active Informatics student roster idempotently';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting student roster import...');
        
        $dataPath = database_path('data/active_informatics_students_2023_2025.php');
        
        if (!file_exists($dataPath)) {
            $this->error("Data file not found at: {$dataPath}");
            return Command::FAILURE;
        }

        $students = require $dataPath;
        $count = count($students);
        
        $this->info("Found {$count} student records to process.");
        
        $inserted = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($students as $s) {
            $email = trim($s['email_institusi']);
            $normalizedEmail = strtolower($email); // Keeping original exactly as is for uniqueness
            
            // Note: The prompt explicitly requested storing anomalous emails like `erdine.a.@student.pradita.ac.id` exactly.
            // That's why we use strtolower($email) directly.
            
            $nim = trim($s['nim']);
            $name = trim($s['nama']);
            $angkatan = trim($s['angkatan']);
            
            // Compute source hash to avoid unnecessary updates
            $sourceHash = md5(json_encode([$nim, $name, $angkatan, $normalizedEmail]));

            $student = Student::where('email', $email)->orWhere('nim', $nim)->first();

            if ($student) {
                if ($student->manual_override) {
                    $skipped++;
                    $this->warn("Skipped {$nim} (manual override active).");
                    continue;
                }

                if ($student->source_hash === $sourceHash) {
                    $skipped++;
                    continue; // No changes
                }
                
                // Update existing
                $student->nim = $nim;
                $student->name = $name;
                $student->email = $email;
                $student->normalized_email = $normalizedEmail;
                $student->angkatan = $angkatan;
                $student->academic_status = 'active';
                $student->login_enabled = true;
                $student->approval_status = 'approved';
                $student->source_type = 'roster_import';
                $student->source_hash = $sourceHash;
                $student->save();
                
                $updated++;
            } else {
                // Insert new
                Student::create([
                    'nim' => $nim,
                    'name' => $name,
                    'email' => $email,
                    'normalized_email' => $normalizedEmail,
                    'angkatan' => $angkatan,
                    'academic_status' => 'active',
                    'login_enabled' => true,
                    'approval_status' => 'approved',
                    'approved_at' => now(), // Auto-approve system imports
                    'source_type' => 'roster_import',
                    'source_hash' => $sourceHash,
                ]);
                $inserted++;
            }
        }

        $this->info("Import completed successfully!");
        $this->line("Inserted: {$inserted}");
        $this->line("Updated: {$updated}");
        $this->line("Skipped: {$skipped}");

        return Command::SUCCESS;
    }
}
