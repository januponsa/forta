<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SyncOfficialStudentRosterTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_official_roster_dry_run()
    {
        // Add a dummy student
        Student::create([
            'nim' => '99999',
            'name' => 'Dummy Student',
            'email' => 'dummy@student.pradita.ac.id',
            'angkatan' => '2022',
            'academic_status' => 'active',
            'login_enabled' => true,
        ]);

        $this->assertEquals(1, Student::count());

        $exitCode = Artisan::call('forta:students:sync-official-roster', ['--dry-run' => true]);
        
        $this->assertEquals(0, $exitCode);
        $this->assertEquals(1, Student::count()); // Still 1 because dry-run
    }

    public function test_sync_official_roster_apply()
    {
        // Add a dummy student
        Student::create([
            'nim' => '99999',
            'name' => 'Dummy Student',
            'email' => 'dummy@student.pradita.ac.id',
            'angkatan' => '2022',
            'academic_status' => 'active',
            'login_enabled' => true,
        ]);

        // Run sync
        $exitCode = Artisan::call('forta:students:sync-official-roster', ['--apply' => true]);
        
        $this->assertEquals(0, $exitCode);

        // Verify dummy student is archived
        $dummy = Student::withTrashed()->where('nim', '99999')->first();
        $this->assertNotNull($dummy->deleted_at);
        $this->assertEquals('archived', $dummy->academic_status);
        $this->assertFalse($dummy->login_enabled);

        // Verify active and approved students count is exactly 122
        $activeStudents = Student::activeAndApproved()->get();
        $this->assertCount(122, $activeStudents);

        // Verify distribution
        $count2023 = $activeStudents->where('angkatan', '2023')->count();
        $count2024 = $activeStudents->where('angkatan', '2024')->count();
        $count2025 = $activeStudents->where('angkatan', '2025')->count();

        $this->assertEquals(25, $count2023);
        $this->assertEquals(40, $count2024);
        $this->assertEquals(57, $count2025);

        // Verify idempotency
        $exitCode2 = Artisan::call('forta:students:sync-official-roster', ['--apply' => true]);
        $this->assertEquals(0, $exitCode2);

        $activeStudentsAfter = Student::activeAndApproved()->get();
        $this->assertCount(122, $activeStudentsAfter);
    }
}
