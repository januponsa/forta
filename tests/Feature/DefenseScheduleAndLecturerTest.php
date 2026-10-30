<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Student;
use App\Models\User;
use App\Models\Form;
use App\Models\ActivityType;
use App\Models\DefenseCase;
use App\Models\DefenseSchedule;
use App\Models\DefenseAssignment;
use App\Models\Lecturer;
use App\Livewire\Admin\Defense\ScheduleManager;
use Livewire\Livewire;
use Illuminate\Support\Facades\Hash;

class DefenseScheduleAndLecturerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $lecturer1;
    protected $lecturer2;
    protected $student;
    protected $defenseCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin_forta',
            'username' => 'admin_test',
            'email' => 'admin_test@pradita.ac.id',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $this->lecturer1 = Lecturer::create([
            'nip' => '316078403',
            'name' => 'Alfa Ryano Yohannis, S.T., M.T., Ph.D.',
            'email' => 'alfa.ryano@pradita.ac.id',
            'is_active' => true,
        ]);

        $this->lecturer2 = Lecturer::create([
            'nip' => '313046703',
            'name' => 'Arya Sanjaya, S.T., M.Kom.',
            'email' => 'arya.sanjaya@pradita.ac.id',
            'is_active' => true,
        ]);

        $this->student = Student::factory()->create([
            'nim' => '121450099',
            'name' => 'Budi Testing',
            'email' => 'budi@student.itera.ac.id',
            'angkatan' => '2021',
        ]);

        $activityType = ActivityType::firstOrCreate([
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

        $submission = \App\Models\Submission::create([
            'form_id' => $form->id,
            'nim' => $this->student->nim,
            'name' => $this->student->name,
            'email' => $this->student->email,
            'status' => 'submitted',
            'answers' => [],
            'submitted_at' => now(),
        ]);

        $this->defenseCase = DefenseCase::create([
            'submission_id' => $submission->id,
            'student_id' => $this->student->id,
            'defense_type' => 'internship_defense',
            'semester' => 'Ganjil 2025/2026',
            'status' => 'waiting_schedule',
            'metadata' => [
                'company_name' => 'PT Telkom',
                'report_title' => 'Sistem Informasi Sidang',
            ],
        ]);
    }

    public function test_admin_can_schedule_a_defense()
    {
        Livewire::actingAs($this->admin)
            ->test(ScheduleManager::class)
            ->call('openModal', $this->defenseCase->id)
            ->set('date', '2026-11-15')
            ->set('start_time', '09:00')
            ->set('end_time', '10:30')
            ->set('room_or_link', 'Ruang Sidang 101')
            ->set('supervisor_id', $this->lecturer1->id)
            ->set('examiner_id', $this->lecturer2->id)
            ->call('saveSchedule')
            ->assertHasNoErrors();

        // Verify schedule saved
        $this->assertDatabaseHas('defense_schedules', [
            'defense_case_id' => $this->defenseCase->id,
            'room_or_link' => 'Ruang Sidang 101',
        ]);

        // Verify supervisor assignment
        $this->assertDatabaseHas('defense_assignments', [
            'defense_case_id' => $this->defenseCase->id,
            'lecturer_id' => $this->lecturer1->id,
            'role' => 'supervisor',
        ]);

        // Verify examiner assignment
        $this->assertDatabaseHas('defense_assignments', [
            'defense_case_id' => $this->defenseCase->id,
            'lecturer_id' => $this->lecturer2->id,
            'role' => 'examiner',
        ]);

        // Verify status updated to scheduled
        $this->assertEquals('scheduled', $this->defenseCase->fresh()->status);
    }

    public function test_admin_can_edit_an_existing_schedule()
    {
        // Setup initial schedule
        DefenseSchedule::create([
            'defense_case_id' => $this->defenseCase->id,
            'date' => '2026-11-15',
            'start_time' => '09:00',
            'end_time' => '10:30',
            'room_or_link' => 'Ruang Lama',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ScheduleManager::class)
            ->call('openModal', $this->defenseCase->id)
            ->set('room_or_link', 'Ruang Sidang Baru 202')
            ->set('start_time', '13:00')
            ->set('end_time', '14:30')
            ->set('supervisor_id', $this->lecturer1->id)
            ->set('examiner_id', $this->lecturer2->id)
            ->call('saveSchedule')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('defense_schedules', [
            'defense_case_id' => $this->defenseCase->id,
            'room_or_link' => 'Ruang Sidang Baru 202',
        ]);
    }

    public function test_admin_can_delete_schedule()
    {
        // Setup initial schedule
        DefenseSchedule::create([
            'defense_case_id' => $this->defenseCase->id,
            'date' => '2026-11-15',
            'start_time' => '09:00',
            'end_time' => '10:30',
            'room_or_link' => 'Ruang Sidang 101',
        ]);
        DefenseAssignment::create([
            'defense_case_id' => $this->defenseCase->id,
            'lecturer_id' => $this->lecturer1->id,
            'role' => 'supervisor',
        ]);
        $this->defenseCase->update(['status' => 'scheduled']);

        Livewire::actingAs($this->admin)
            ->test(ScheduleManager::class)
            ->call('deleteSchedule', $this->defenseCase->id);

        $this->assertDatabaseMissing('defense_schedules', [
            'defense_case_id' => $this->defenseCase->id,
        ]);
        $this->assertDatabaseMissing('defense_assignments', [
            'defense_case_id' => $this->defenseCase->id,
        ]);
        $this->assertEquals('waiting_schedule', $this->defenseCase->fresh()->status);
    }

    public function test_examiner_cannot_be_same_as_supervisor()
    {
        Livewire::actingAs($this->admin)
            ->test(ScheduleManager::class)
            ->call('openModal', $this->defenseCase->id)
            ->set('date', '2026-11-15')
            ->set('start_time', '09:00')
            ->set('end_time', '10:30')
            ->set('room_or_link', 'Ruang Sidang 101')
            ->set('supervisor_id', $this->lecturer1->id)
            ->set('examiner_id', $this->lecturer1->id) // Same lecturer!
            ->call('saveSchedule')
            ->assertHasErrors(['examiner_id']);
    }

    public function test_lecturer_can_login_using_username_email_or_nip()
    {
        $lecturerUser = User::create([
            'name' => 'Alfa Ryano Yohannis, S.T., M.T., Ph.D.',
            'username' => 'alfa.ryano',
            'email' => 'alfa.ryano@pradita.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin_forta',
            'is_active' => true,
        ]);
        $this->lecturer1->update(['user_id' => $lecturerUser->id]);

        // 1. Login with username
        $response1 = $this->post(route('admin.login.submit'), [
            'username' => 'alfa.ryano',
            'password' => 'password',
        ]);
        $response1->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($lecturerUser, 'web');

        // Logout
        $this->post(route('admin.logout'));
        $this->assertGuest('web');

        // 2. Login with email
        $response2 = $this->post(route('admin.login.submit'), [
            'username' => 'alfa.ryano@pradita.ac.id',
            'password' => 'password',
        ]);
        $response2->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($lecturerUser, 'web');

        // Logout
        $this->post(route('admin.logout'));
        $this->assertGuest('web');

        // 3. Login with NIP
        $response3 = $this->post(route('admin.login.submit'), [
            'username' => '316078403',
            'password' => 'password',
        ]);
        $response3->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($lecturerUser, 'web');
    }

    public function test_detects_lecturer_conflict_when_scheduling_overlapping_time()
    {
        // 1. Jadwalkan Kasus Pertama: Kamis, 5 November 2026, 09:00 - 09:30 dengan Dosen Alfa Ryano sebagai Pembimbing
        DefenseSchedule::create([
            'defense_case_id' => $this->defenseCase->id,
            'date' => '2026-11-05',
            'start_time' => '09:00',
            'end_time' => '09:30',
            'room_or_link' => 'Ruang Sidang 101',
        ]);
        DefenseAssignment::create([
            'defense_case_id' => $this->defenseCase->id,
            'lecturer_id' => $this->lecturer1->id, // Alfa Ryano
            'role' => 'supervisor',
        ]);
        $this->defenseCase->update(['status' => 'scheduled']);

        // 2. Buat Kasus Kedua untuk mahasiswa lain
        $student2 = Student::factory()->create([
            'nim' => '121450101',
            'name' => 'Siti Calon Peserta',
            'email' => 'siti2@student.itera.ac.id',
            'angkatan' => '2021',
        ]);

        $submission2 = \App\Models\Submission::create([
            'form_id' => $this->defenseCase->submission->form_id,
            'nim' => $student2->nim,
            'name' => $student2->name,
            'email' => $student2->email,
            'status' => 'submitted',
            'answers' => [],
            'submitted_at' => now(),
        ]);

        $case2 = DefenseCase::create([
            'submission_id' => $submission2->id,
            'student_id' => $student2->id,
            'defense_type' => 'internship_defense',
            'semester' => 'Ganjil 2025/2026',
            'status' => 'waiting_schedule',
            'metadata' => [
                'company_name' => 'Shopee',
                'report_title' => 'Analisis Big Data',
            ],
        ]);

        // 3. Coba jadwalkan Kasus Kedua pada waktu bentrok (09:15 - 09:45) dengan Alfa Ryano sebagai Penguji
        Livewire::actingAs($this->admin)
            ->test(ScheduleManager::class)
            ->call('openModal', $case2->id)
            ->set('date', '2026-11-05')
            ->set('start_time', '09:15')
            ->set('end_time', '09:45')
            ->set('room_or_link', 'Ruang Sidang 202')
            ->set('supervisor_id', $this->lecturer2->id) // Arya Sanjaya
            ->set('examiner_id', $this->lecturer1->id)   // Alfa Ryano (BENTROK!)
            ->call('saveSchedule')
            ->assertHasErrors(['examiner_id']);

        // Pastikan tidak tersimpan di DB untuk Kasus Kedua
        $this->assertDatabaseMissing('defense_schedules', [
            'defense_case_id' => $case2->id,
        ]);
    }

    public function test_allows_back_to_back_scheduling_without_conflict()
    {
        // Kasus Pertama: 09:00 - 09:30
        DefenseSchedule::create([
            'defense_case_id' => $this->defenseCase->id,
            'date' => '2026-11-05',
            'start_time' => '09:00',
            'end_time' => '09:30',
            'room_or_link' => 'Ruang Sidang 101',
        ]);
        DefenseAssignment::create([
            'defense_case_id' => $this->defenseCase->id,
            'lecturer_id' => $this->lecturer1->id,
            'role' => 'supervisor',
        ]);

        // Kasus Kedua: mahasiswa lain pada 09:30 - 10:00 (tepat berurutan, tidak tumpang tindih)
        $student2 = Student::factory()->create([
            'nim' => '121450102',
            'name' => 'Bambang Sukses',
            'email' => 'bambang@student.itera.ac.id',
            'angkatan' => '2021',
        ]);
        $submission2 = \App\Models\Submission::create([
            'form_id' => $this->defenseCase->submission->form_id,
            'nim' => $student2->nim,
            'name' => $student2->name,
            'email' => $student2->email,
            'status' => 'submitted',
            'answers' => [],
            'submitted_at' => now(),
        ]);
        $case2 = DefenseCase::create([
            'submission_id' => $submission2->id,
            'student_id' => $student2->id,
            'defense_type' => 'internship_defense',
            'semester' => 'Ganjil 2025/2026',
            'status' => 'waiting_schedule',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ScheduleManager::class)
            ->call('openModal', $case2->id)
            ->set('date', '2026-11-05')
            ->set('start_time', '09:30')
            ->set('end_time', '10:00')
            ->set('room_or_link', 'Ruang Sidang 101')
            ->set('supervisor_id', $this->lecturer2->id)
            ->set('examiner_id', $this->lecturer1->id) // Alfa Ryano back-to-back
            ->call('saveSchedule')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('defense_schedules', [
            'defense_case_id' => $case2->id,
            'start_time' => '09:30:00',
            'end_time' => '10:00:00',
        ]);
    }
}
