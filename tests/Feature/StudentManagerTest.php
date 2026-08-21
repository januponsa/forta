<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Student;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use App\Jobs\CleanupStudentFilesJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class StudentManagerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $kaprodi;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Staff',
            'username' => 'admin_staff',
            'email' => 'admin_staff@pradita.ac.id',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $this->kaprodi = User::create([
            'name' => 'Kaprodi User',
            'username' => 'kaprodi_user',
            'email' => 'kaprodi_user@pradita.ac.id',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        \App\Models\Lecturer::create([
            'user_id' => $this->kaprodi->id,
            'nip' => 'KAPRODI-001',
            'name' => 'Kaprodi User',
            'email' => 'kaprodi_user@pradita.ac.id',
            'position' => 'Kaprodi',
            'is_active' => true,
        ]);

        $this->student = Student::create([
            'nim' => '1234567890',
            'name' => 'Jane Doe',
            'email' => 'janedoe@student.pradita.ac.id',
            'angkatan' => '2024',
            'academic_status' => 'active',
            'login_enabled' => true,
            'status_akademik' => 'Aktif',
            'status_akun' => 'Login Diizinkan',
            'approval_status' => 'approved',
        ]);
    }

    public function test_admin_can_disable_and_enable_student_login()
    {
        // 1. Test disable
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.student-manager')
            ->call('disableStudent', $this->student->id)
            ->assertHasNoErrors();

        $this->student->refresh();
        $this->assertEquals('Login Dinonaktifkan', $this->student->status_akun);
        $this->assertFalse($this->student->login_enabled);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->admin->id,
            'action' => 'disable_student',
            'target_id' => $this->student->nim,
        ]);

        // 2. Test enable
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.student-manager')
            ->call('enableStudent', $this->student->id)
            ->assertHasNoErrors();

        $this->student->refresh();
        $this->assertEquals('Login Diizinkan', $this->student->status_akun);
        $this->assertTrue($this->student->login_enabled);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->admin->id,
            'action' => 'enable_student',
            'target_id' => $this->student->nim,
        ]);
    }

    public function test_admin_can_archive_and_restore_student()
    {
        // 1. Test Archive
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.student-manager')
            ->call('archiveStudent', $this->student->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('students', ['id' => $this->student->id]);
        
        $archivedStudent = Student::withTrashed()->find($this->student->id);
        $this->assertEquals('Diarsipkan', $archivedStudent->status_akademik);
        $this->assertEquals('Login Dinonaktifkan', $archivedStudent->status_akun);
        $this->assertFalse($archivedStudent->login_enabled);
        $this->assertStringContainsString('.archived.', $archivedStudent->email);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->admin->id,
            'action' => 'archive_student',
            'target_id' => $this->student->nim,
        ]);

        // 2. Test Restore
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.student-manager')
            ->call('restoreStudent', $this->student->id)
            ->assertHasNoErrors();

        $this->student->refresh();
        $this->assertFalse($this->student->trashed());
        $this->assertEquals('janedoe@student.pradita.ac.id', $this->student->email);
        $this->assertEquals('Aktif', $this->student->status_akademik);
        $this->assertEquals('Login Diizinkan', $this->student->status_akun);
        $this->assertTrue($this->student->login_enabled);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->admin->id,
            'action' => 'restore_student',
            'target_id' => $this->student->nim,
        ]);
    }

    public function test_restore_fails_on_email_conflict()
    {
        // Archive first
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.student-manager')
            ->call('archiveStudent', $this->student->id);

        // Create new active student with same original email
        Student::create([
            'nim' => '9876543210',
            'name' => 'Another Student',
            'email' => 'janedoe@student.pradita.ac.id',
            'angkatan' => '2024',
            'academic_status' => 'active',
            'login_enabled' => true,
            'status_akademik' => 'Aktif',
            'status_akun' => 'Login Diizinkan',
        ]);

        // Try to restore archived student
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.student-manager')
            ->call('restoreStudent', $this->student->id)
            ->assertSee('Gagal me-restore. Email');

        $archivedStudent = Student::withTrashed()->find($this->student->id);
        $this->assertTrue($archivedStudent->trashed());
    }

    public function test_authorized_user_can_delete_student_permanently_with_files()
    {
        Bus::fake();
        Storage::fake('local');

        // Create form, submission and fake file
        $activityType = \App\Models\ActivityType::create(['name' => 'TA', 'slug' => 'ta']);
        $form = \App\Models\Form::create([
            'title' => 'Form TA',
            'slug' => 'form-ta',
            'activity_type_id' => $activityType->id,
            'phase' => 'registration',
            'status' => 'active',
            'semester' => 'Ganjil',
        ]);

        $submission = Submission::create([
            'form_id' => $form->id,
            'nim' => $this->student->nim,
            'name' => $this->student->name,
            'email' => $this->student->email,
            'status' => 'submitted',
            'answers' => [],
            'submitted_at' => now(),
        ]);

        $file = UploadedFile::fake()->create('report.pdf', 500, 'application/pdf');
        $storedPath = $file->store('submissions/'.$form->id, 'local');

        $field = \App\Models\FormField::create([
            'form_id' => $form->id,
            'label' => 'Upload Document',
            'name' => 'doc',
            'type' => 'file',
            'order' => 1,
        ]);

        $submissionFile = SubmissionFile::create([
            'submission_id' => $submission->id,
            'field_id' => $field->id,
            'original_name' => 'report.pdf',
            'stored_path' => $storedPath,
            'mime_type' => 'application/pdf',
            'size_bytes' => 512000,
            'uploaded_at' => now(),
        ]);

        // Soft delete/archive the student first
        $this->student->delete();

        // 1. Admin staff cannot access start delete
        $this->assertFalse(Gate::forUser($this->admin)->allows('users.delete_permanently'));

        Livewire::actingAs($this->admin, 'web')
            ->test('admin.student-manager')
            ->call('startDeleteStudent', $this->student->id)
            ->assertStatus(403);

        // 2. Kaprodi can access and execute
        $this->assertTrue(Gate::forUser($this->kaprodi)->allows('users.delete_permanently'));

        Livewire::actingAs($this->kaprodi, 'web')
            ->test('admin.student-manager')
            ->call('startDeleteStudent', $this->student->id)
            ->set('nimConfirmInput', 'HAPUS ' . $this->student->nim)
            ->call('deleteStudentPermanently')
            ->assertHasNoErrors();

        // Assert record is fully deleted from DB
        $this->assertDatabaseMissing('students', ['id' => $this->student->id]);
        $this->assertDatabaseMissing('submissions', ['id' => $submission->id]);
        $this->assertDatabaseMissing('submission_files', ['id' => $submissionFile->id]);

        // Assert AuditLog records freed_bytes
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->kaprodi->id,
            'action' => 'permanent_delete_student',
            'target_id' => $this->student->nim,
            'freed_bytes' => 512000,
        ]);

        // Assert file cleanup job is dispatched
        Bus::assertDispatched(CleanupStudentFilesJob::class, function ($job) use ($storedPath) {
            return in_array($storedPath, $job->filePaths);
        });
    }

    public function test_cleanup_student_files_job_removes_physical_files()
    {
        Storage::fake('local');
        
        $path1 = 'submissions/1/file1.pdf';
        $path2 = 'submissions/1/file2.pdf';

        Storage::disk('local')->put($path1, 'content');
        Storage::disk('local')->put($path2, 'content');

        Storage::disk('local')->assertExists($path1);
        Storage::disk('local')->assertExists($path2);

        $job = new CleanupStudentFilesJob([$path1, $path2]);
        $job->handle();

        Storage::disk('local')->assertMissing($path1);
        Storage::disk('local')->assertMissing($path2);
    }
}
