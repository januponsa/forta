<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_database_models_and_relationships()
    {
        // 1. Test User relations and SoftDeletes
        $user = \App\Models\User::create([
            'name' => 'Test Actor',
            'username' => 'testactor',
            'email' => 'testactor@pradita.ac.id',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'status_akun' => 'Login Diizinkan',
        ]);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $user->delete();
        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $user->restore();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);

        // 2. Test Form relations and SoftDeletes
        $activityType = \App\Models\ActivityType::create([
            'name' => 'Kecerdasan Buatan',
            'slug' => 'kecerdasan-buatan',
        ]);

        $parentForm = \App\Models\Form::create([
            'title' => 'Form Utama',
            'slug' => 'form-utama',
            'activity_type_id' => $activityType->id,
            'phase' => 'registration',
            'semester' => 'Ganjil',
            'status' => 'active',
            'version' => 1,
        ]);

        $childForm = \App\Models\Form::create([
            'title' => 'Form Turunan',
            'slug' => 'form-turunan',
            'activity_type_id' => $activityType->id,
            'phase' => 'registration',
            'semester' => 'Ganjil',
            'status' => 'active',
            'version' => 2,
            'parent_form_id' => $parentForm->id,
        ]);

        $this->assertEquals($parentForm->id, $childForm->parentForm->id);
        $this->assertCount(1, $parentForm->childForms);

        $parentForm->delete();
        $this->assertSoftDeleted('forms', ['id' => $parentForm->id]);

        // 3. Test Student status fields
        $student = \App\Models\Student::create([
            'nim' => '1111222233',
            'name' => 'Active Student',
            'email' => 'active@student.pradita.ac.id',
            'angkatan' => '2023',
            'academic_status' => 'active',
            'login_enabled' => true,
            'status_akademik' => 'Aktif',
            'status_akun' => 'Login Diizinkan',
        ]);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'status_akademik' => 'Aktif',
            'status_akun' => 'Login Diizinkan',
        ]);

        // 4. Test Submission and ReviewerAssignment relations
        $submission = \App\Models\Submission::create([
            'form_id' => $childForm->id,
            'nim' => $student->nim,
            'name' => $student->name,
            'email' => $student->email,
            'status' => 'submitted',
            'answers' => ['key' => 'value'],
            'field_review_statuses' => ['field_1' => 'approved'],
            'submitted_at' => now(),
        ]);

        $this->assertEquals(['field_1' => 'approved'], $submission->field_review_statuses);

        $reviewer = \App\Models\User::create([
            'name' => 'Reviewer 1',
            'username' => 'reviewer1',
            'email' => 'reviewer1@pradita.ac.id',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]);

        $assignment = \App\Models\ReviewerAssignment::create([
            'submission_id' => $submission->id,
            'user_id' => $reviewer->id,
            'status' => 'Belum Diperiksa',
        ]);

        $this->assertCount(1, $submission->reviewerAssignments);
        $this->assertEquals($reviewer->id, $submission->assignedReviewers->first()->id);
        $this->assertEquals('Belum Diperiksa', $submission->reviewerAssignments->first()->status);

        // 5. Test AuditLog creation
        $audit = \App\Models\AuditLog::create([
            'actor_id' => $user->id,
            'actor_role' => 'admin_prodi',
            'action' => 'delete_user',
            'target_type' => 'student',
            'target_id' => $student->id,
            'data_before' => ['status_akademik' => 'Aktif'],
            'data_after' => ['status_akademik' => 'Nonaktif'],
            'freed_bytes' => 0,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertEquals('delete_user', $audit->action);
        $this->assertEquals(['status_akademik' => 'Aktif'], $audit->data_before);
        $this->assertEquals($user->id, $audit->actor->id);
    }
}
