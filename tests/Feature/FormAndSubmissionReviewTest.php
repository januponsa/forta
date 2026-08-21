<?php

namespace Tests\Feature;

use App\Models\ActivityType;
use App\Models\AuditLog;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSection;
use App\Models\ReviewerAssignment;
use App\Models\Student;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FormAndSubmissionReviewTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $student;
    protected $activityType;

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

        $this->student = Student::create([
            'nim' => '1234567890',
            'name' => 'Jane Doe',
            'email' => 'janedoe@student.pradita.ac.id',
            'angkatan' => '2024',
            'academic_status' => 'active',
            'login_enabled' => true,
            'status_akademik' => 'Aktif',
            'status_akun' => 'Login Diizinkan',
        ]);

        $this->activityType = ActivityType::create(['name' => 'Magang', 'slug' => 'magang']);
    }

    public function test_admin_can_archive_restore_and_safely_delete_form()
    {
        // Create form
        $form = Form::create([
            'title' => 'Form Magang',
            'slug' => 'form-magang',
            'activity_type_id' => $this->activityType->id,
            'phase' => 'registration',
            'semester' => 'Ganjil',
            'status' => 'draft',
        ]);

        // 1. Test Archive (Soft Delete)
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.form-manager')
            ->call('archive', $form->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('forms', ['id' => $form->id]);
        $form->refresh();
        $this->assertEquals('archived', $form->status);

        // 2. Test Restore
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.form-manager')
            ->call('restore', $form->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('forms', ['id' => $form->id, 'deleted_at' => null, 'status' => 'draft']);

        // 3. Test Deletion block if form has submissions
        Submission::create([
            'form_id' => $form->id,
            'nim' => $this->student->nim,
            'name' => $this->student->name,
            'email' => $this->student->email,
            'status' => 'submitted',
            'answers' => [],
            'submitted_at' => now(),
        ]);

        // Try soft delete (normal delete in manager)
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.form-manager')
            ->call('delete', $form->id)
            ->assertSee('Form tidak dapat dihapus'); // Blocked!

        $this->assertDatabaseHas('forms', ['id' => $form->id, 'deleted_at' => null]);
    }

    public function test_form_versioning_replicates_sections_and_fields()
    {
        // Create form with form_code, section and field
        $form = Form::create([
            'title' => 'Form Awal',
            'slug' => 'form-awal',
            'form_code' => 'KMM_FORM',
            'activity_type_id' => $this->activityType->id,
            'phase' => 'registration',
            'semester' => 'Ganjil',
            'status' => 'active',
            'version' => 1,
        ]);

        $section = FormSection::create([
            'form_id' => $form->id,
            'title' => 'Data Diri',
            'section_code' => 'sec_data_diri',
            'order' => 1,
        ]);

        $field = FormField::create([
            'form_id' => $form->id,
            'section_id' => $section->id,
            'label' => 'Alamat Rumah',
            'name' => 'alamat_rumah',
            'type' => 'text',
            'order' => 1,
        ]);

        // Create new version
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.form-manager')
            ->call('createNewVersion', $form->id)
            ->assertHasNoErrors();

        // Verify version 2 exists as draft
        $newForm = Form::where('form_code', 'KMM_FORM')->where('version', 2)->first();
        $this->assertNotNull($newForm);
        $this->assertEquals('draft', $newForm->status);
        $this->assertEquals($form->id, $newForm->parent_form_id);

        // Verify sections & fields are duplicated and correctly mapped
        $newSection = FormSection::where('form_id', $newForm->id)->first();
        $this->assertNotNull($newSection);
        $this->assertEquals('Data Diri', $newSection->title);

        $newField = FormField::where('form_id', $newForm->id)->first();
        $this->assertNotNull($newField);
        $this->assertEquals('Alamat Rumah', $newField->label);
        $this->assertEquals($newSection->id, $newField->section_id); // Mapped to new section ID
    }

    public function test_submission_review_per_field_and_files()
    {
        $form = Form::create([
            'title' => 'Form TA',
            'slug' => 'form-ta',
            'activity_type_id' => $this->activityType->id,
            'phase' => 'registration',
            'semester' => 'Ganjil',
            'status' => 'active',
        ]);

        $submission = Submission::create([
            'form_id' => $form->id,
            'nim' => $this->student->nim,
            'name' => $this->student->name,
            'email' => $this->student->email,
            'status' => 'submitted',
            'answers' => ['1' => 'Judul A', '2' => 'Dosen B'],
            'submitted_at' => now(),
        ]);

        $field = FormField::create([
            'form_id' => $form->id,
            'label' => 'File KTI',
            'name' => 'file_kti',
            'type' => 'file',
            'order' => 1,
        ]);

        $file = SubmissionFile::create([
            'submission_id' => $submission->id,
            'field_id' => $field->id,
            'original_name' => 'kti.pdf',
            'stored_path' => 'submissions/kti.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 1024,
            'uploaded_at' => now(),
        ]);

        // 1. Verify text field review status
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.submission-detail', ['id' => $submission->id])
            ->call('updateFieldReviewStatus', 1, 'approved')
            ->call('updateFieldReviewStatus', 2, 'rejected')
            ->assertHasNoErrors();

        $submission->refresh();
        $this->assertEquals([
            '1' => 'approved',
            '2' => 'rejected'
        ], $submission->field_review_statuses);

        // 2. Verify file status & note review
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.submission-detail', ['id' => $submission->id])
            ->set('fileStatuses.' . $file->id, 'Ditolak')
            ->set('fileNotes.' . $file->id, 'File corrupt')
            ->call('updateFileReview', $file->id)
            ->assertHasNoErrors();

        $file->refresh();
        $this->assertEquals('Ditolak', $file->review_status);
        $this->assertEquals('File corrupt', $file->review_note);
    }

    public function test_reviewer_assignment_flow()
    {
        $form = Form::create([
            'title' => 'Form TA',
            'slug' => 'form-ta',
            'activity_type_id' => $this->activityType->id,
            'phase' => 'registration',
            'semester' => 'Ganjil',
            'status' => 'active',
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

        // 1. Assign Reviewer
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.submission-detail', ['id' => $submission->id])
            ->set('selectedReviewerId', $this->admin->id)
            ->call('assignReviewer')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('reviewer_assignments', [
            'submission_id' => $submission->id,
            'user_id' => $this->admin->id,
            'status' => 'Belum Diperiksa',
        ]);

        $assignment = ReviewerAssignment::where('submission_id', $submission->id)->first();

        // 2. Change assignment status
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.submission-detail', ['id' => $submission->id])
            ->call('updateAssignmentStatus', $assignment->id, 'Sedang Diperiksa')
            ->assertHasNoErrors();

        $assignment->refresh();
        $this->assertEquals('Sedang Diperiksa', $assignment->status);

        // 3. Remove reviewer assignment
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.submission-detail', ['id' => $submission->id])
            ->call('removeReviewer', $assignment->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('reviewer_assignments', ['id' => $assignment->id]);
    }
}
