<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Student;
use App\Models\User;
use App\Models\Form;
use App\Models\Submission;
use App\Models\DefenseCase;
use App\Livewire\Admin\Defense\ParticipantManager;
use Livewire\Livewire;

class DefenseParticipantSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_manager_can_sync_submissions_to_defense_cases()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = Student::factory()->create([
            'nim' => '121450099',
            'name' => 'Budi Pendaftar',
            'email' => 'budi@student.itera.ac.id',
            'angkatan' => '2021',
        ]);

        $activityType = \App\Models\ActivityType::firstOrCreate([
            'slug' => 'magang-kp',
        ], [
            'name' => 'Magang / Kerja Praktik',
            'description' => 'Test',
        ]);

        $form = Form::create([
            'activity_type_id' => $activityType->id,
            'title' => 'Pendaftaran Sidang Kerja Praktik 2026',
            'slug' => 'pendaftaran-sidang-kp-2026',
            'form_code' => 'INTERNSHIP_DEFENSE_REGISTRATION',
            'phase' => 'reporting',
            'semester' => 'Ganjil 2025/2026',
            'status' => 'active',
            'open_at' => now()->subDays(5),
            'close_at' => now()->addDays(5),
        ]);

        $submission = Submission::create([
            'form_id' => $form->id,
            'nim' => $student->nim,
            'name' => $student->name,
            'email' => $student->email,
            'status' => 'submitted',
            'answers' => [
                'INTDEF_company_name' => 'PT Telekomunikasi Indonesia',
                'INTDEF_company_address' => 'Bandung',
                'INTDEF_mentor_name' => 'Pak Joko',
                'INTDEF_mentor_position' => 'Senior Lead Engineer',
                'INTDEF_report_title' => 'Rancang Bangun SI Sidang',
                'INTDEF_internship_start_date' => '2026-06-01',
                'INTDEF_internship_end_date' => '2026-08-31',
            ],
            'submitted_at' => now(),
        ]);

        // Prior to sync, defense case does not exist
        $this->assertDatabaseMissing('defense_cases', [
            'submission_id' => $submission->id,
        ]);

        // Admin runs sync in Livewire component
        Livewire::actingAs($admin)
            ->test(ParticipantManager::class)
            ->call('syncParticipants')
            ->assertSee('1 peserta baru berhasil disinkronisasi');

        // Defense case now exists
        $this->assertDatabaseHas('defense_cases', [
            'submission_id' => $submission->id,
            'student_id' => $student->id,
            'defense_type' => 'internship_defense',
            'status' => 'registered',
        ]);

        $case = DefenseCase::where('submission_id', $submission->id)->first();
        $this->assertEquals('PT Telekomunikasi Indonesia', $case->metadata['company_name']);
        $this->assertEquals('Pak Joko', $case->metadata['mentor_name']);
        $this->assertEquals('Rancang Bangun SI Sidang', $case->metadata['report_title']);
        $this->assertEquals('2026-06-01 s/d 2026-08-31', $case->metadata['internship_period']);
    }

    public function test_student_submitting_defense_form_automatically_creates_defense_case()
    {
        $student = Student::factory()->create([
            'nim' => '121450100',
            'name' => 'Siti Pendaftar Langsung',
            'email' => 'siti@student.itera.ac.id',
            'angkatan' => '2021',
        ]);

        $activityType = \App\Models\ActivityType::firstOrCreate([
            'slug' => 'magang-kp',
        ], [
            'name' => 'Magang / Kerja Praktik',
            'description' => 'Test',
        ]);

        $form = Form::create([
            'activity_type_id' => $activityType->id,
            'title' => 'Pendaftaran Sidang Kerja Praktik 2026 Langsung',
            'slug' => 'pendaftaran-sidang-kp-langsung',
            'form_code' => 'INTERNSHIP_DEFENSE_REGISTRATION',
            'phase' => 'reporting',
            'semester' => 'Ganjil 2025/2026',
            'status' => 'active',
            'open_at' => now()->subDays(5),
            'close_at' => now()->addDays(5),
        ]);

        $field = \App\Models\FormField::create([
            'form_id' => $form->id,
            'name' => 'INTDEF_company_name',
            'label' => 'Nama Perusahaan',
            'type' => 'text',
            'is_required' => true,
            'order' => 1,
        ]);

        Livewire::actingAs($student, 'student')
            ->test('student.student-form-filler', ['slug' => $form->slug])
            ->set('responses.' . $field->id, 'Bank Mandiri')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('student.dashboard'));

        $this->assertDatabaseHas('defense_cases', [
            'student_id' => $student->id,
            'defense_type' => 'internship_defense',
            'status' => 'registered',
        ]);

        $case = DefenseCase::where('student_id', $student->id)->first();
        $this->assertEquals('Bank Mandiri', $case->metadata['company_name']);
    }
}
