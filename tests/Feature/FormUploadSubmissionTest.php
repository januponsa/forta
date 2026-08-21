<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\FormField;
use App\Models\Student;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class FormUploadSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_form_with_files_securely()
    {
        Storage::fake('local');

        $user = User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Admin',
            'password' => bcrypt('password'),
        ]);

        $activityType = \App\Models\ActivityType::firstOrCreate([
            'name' => 'Skripsi',
        ], [
            'slug' => 'skripsi',
            'description' => 'Test',
        ]);

        // Create student
        $student = Student::firstOrCreate([
            'nim' => '12345678',
        ], [
            'name' => 'John Doe',
            'email' => 'johndoe@student.pradita.ac.id',
            'angkatan' => '2023',
            'program_studi' => 'Teknik Informatika',
            'status_mahasiswa' => 'Aktif',
            'is_active' => true,
        ]);

        // Create form & field
        $form = Form::firstOrCreate([
            'slug' => 'test-form',
        ], [
            'title' => 'Test Form',
            'description' => 'Test',
            'status' => 'active',
            'semester' => 'Ganjil',
            'phase' => 'registration',
            'created_by' => $user->id,
            'activity_type_id' => $activityType->id,
        ]);
        
        $field = FormField::firstOrCreate([
            'form_id' => $form->id,
            'name' => 'test_file_field',
        ], [
            'label' => 'Upload Document',
            'type' => 'file',
            'is_required' => true,
            'order' => 1,
            'max_size_mb' => 2,
        ]);

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        // Submit form via Livewire
        Livewire::actingAs($student, 'student')
            ->test('student.student-form-filler', ['slug' => $form->slug])
            ->set('files.' . $field->id, $file)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('student.dashboard'));

        // Assert DB records
        $this->assertDatabaseHas('submissions', [
            'form_id' => $form->id,
            'nim' => $student->nim,
        ]);

        $submission = Submission::where('form_id', $form->id)->where('nim', $student->nim)->first();
        $this->assertDatabaseHas('submission_files', [
            'submission_id' => $submission->id,
            'field_id' => $field->id,
            'original_name' => 'document.pdf',
        ]);

        $submissionFile = $submission->files()->first();
        $this->assertNotNull($submissionFile);

        // Assert file stored in local (private) disk
        Storage::disk('local')->assertExists($submissionFile->stored_path);
    }

    public function test_prevents_duplicate_submission()
    {
        $user = User::firstOrCreate(['email' => 'admin2@admin.com'], ['name' => 'Admin', 'password' => bcrypt('password')]);
        $activityType = \App\Models\ActivityType::firstOrCreate(['name' => 'Skripsi2'], ['slug' => 'skripsi2', 'description' => 'Test']);

        $student = Student::firstOrCreate([
            'nim' => '12345678',
        ], [
            'name' => 'John Doe',
            'email' => 'johndoe@student.pradita.ac.id',
            'angkatan' => '2023',
            'program_studi' => 'Teknik Informatika',
            'status_mahasiswa' => 'Aktif',
            'is_active' => true,
        ]);
        
        $form = Form::firstOrCreate([
            'slug' => 'test-form',
        ], [
            'title' => 'Test Form',
            'description' => 'Test',
            'status' => 'active',
            'semester' => 'Ganjil',
            'phase' => 'registration',
            'created_by' => $user->id,
            'activity_type_id' => $activityType->id,
        ]);
        
        Submission::firstOrCreate([
            'form_id' => $form->id,
            'nim' => $student->nim,
        ], [
            'name' => $student->name,
            'email' => $student->email,
            'status' => 'submitted',
            'submitted_at' => now(),
            'answers' => [],
        ]);

        $initialCount = Submission::count();

        Livewire::actingAs($student, 'student')
            ->test('student.student-form-filler', ['slug' => $form->slug])
            ->call('submit');
            
        $this->assertEquals($initialCount, Submission::count());
    }
}
