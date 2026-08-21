<?php

namespace Tests\Feature;

use App\Exports\SubmissionsExport;
use App\Models\ActivityType;
use App\Models\AuditLog;
use App\Models\Form;
use App\Models\FormField;
use App\Models\Student;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardAndExportTest extends TestCase
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

    public function test_submission_manager_form_filtering()
    {
        $formA = Form::create([
            'title' => 'Form A',
            'slug' => 'form-a',
            'activity_type_id' => $this->activityType->id,
            'phase' => 'reg',
            'semester' => 'Ganjil',
            'status' => 'active',
        ]);

        $formB = Form::create([
            'title' => 'Form B',
            'slug' => 'form-b',
            'activity_type_id' => $this->activityType->id,
            'phase' => 'reg',
            'semester' => 'Ganjil',
            'status' => 'active',
        ]);

        $subA = Submission::create([
            'form_id' => $formA->id,
            'nim' => '1234567890',
            'name' => 'Jane Doe',
            'email' => 'janedoe@student.pradita.ac.id',
            'status' => 'submitted',
            'answers' => [],
            'submitted_at' => now(),
        ]);

        $subB = Submission::create([
            'form_id' => $formB->id,
            'nim' => '0987654321',
            'name' => 'John Doe',
            'email' => 'johndoe@student.pradita.ac.id',
            'status' => 'approved',
            'answers' => [],
            'submitted_at' => now(),
        ]);

        // Filter Form A
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.submission-manager')
            ->set('formFilter', $formA->id)
            ->assertSee('Jane Doe')
            ->assertDontSee('John Doe');

        // Filter Form B
        Livewire::actingAs($this->admin, 'web')
            ->test('admin.submission-manager')
            ->set('formFilter', $formB->id)
            ->assertSee('John Doe')
            ->assertDontSee('Jane Doe');
    }

    public function test_submissions_export_dynamic_column_mapping()
    {
        $form = Form::create([
            'title' => 'Form KMM',
            'slug' => 'form-kmm',
            'activity_type_id' => $this->activityType->id,
            'phase' => 'reg',
            'semester' => 'Ganjil',
            'status' => 'active',
        ]);

        $field1 = FormField::create([
            'form_id' => $form->id,
            'label' => 'Nama Perusahaan',
            'name' => 'perusahaan',
            'type' => 'text',
            'order' => 1,
        ]);

        $field2 = FormField::create([
            'form_id' => $form->id,
            'label' => 'Nama Mentor',
            'name' => 'mentor',
            'type' => 'text',
            'order' => 2,
        ]);

        $submission = Submission::create([
            'form_id' => $form->id,
            'nim' => $this->student->nim,
            'name' => $this->student->name,
            'email' => $this->student->email,
            'status' => 'submitted',
            'answers' => [
                $field1->id => 'Pradita Corp',
                $field2->id => 'Budi Santoso'
            ],
            'submitted_at' => now(),
        ]);

        // Test with form filter
        $export = new SubmissionsExport($form->id);
        $headings = $export->headings();
        
        $this->assertContains('Nama Perusahaan', $headings);
        $this->assertContains('Nama Mentor', $headings);

        $mapped = $export->map($submission);
        $this->assertContains('Pradita Corp', $mapped);
        $this->assertContains('Budi Santoso', $mapped);

        // Test without form filter (fallback to JSON)
        $exportFallback = new SubmissionsExport(null);
        $headingsFallback = $exportFallback->headings();
        $this->assertContains('Jawaban (JSON)', $headingsFallback);

        $mappedFallback = $exportFallback->map($submission);
        $this->assertContains(json_encode($submission->answers), $mappedFallback);
    }

    public function test_admin_dashboard_metrics_and_logs()
    {
        // Create an audit log record
        AuditLog::create([
            'actor_id' => $this->admin->id,
            'actor_role' => $this->admin->role,
            'action' => 'test_audit_trail',
            'target_type' => 'test',
            'target_id' => 1,
            'ip_address' => '127.0.0.1',
        ]);

        Livewire::actingAs($this->admin, 'web')
            ->test('admin.dashboard')
            ->assertSee('Sistem Overview')
            ->assertSee('test_audit_trail');
    }
}
