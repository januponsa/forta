<?php

namespace Tests\Feature;

use App\Livewire\Admin\FormFieldBuilder;
use App\Livewire\Admin\FormManager;
use App\Livewire\Student\StudentFormFiller;
use App\Models\ActivityType;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSection;
use App\Models\QuestionBank;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DynamicFormBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $student;
    protected $activityType;
    protected $form;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]);

        $this->student = Student::create([
            'nim' => '20230101999',
            'name' => 'Budi Mahasiswa',
            'email' => 'budi@student.pradita.ac.id',
            'angkatan' => '2023',
            'academic_status' => 'active',
            'login_enabled' => true,
            'approval_status' => 'approved',
        ]);

        $this->activityType = ActivityType::create([
            'name' => 'Magang',
            'slug' => 'magang',
        ]);

        $this->form = Form::create([
            'title' => 'Form Pengajuan Magang',
            'slug' => 'form-pengajuan-magang',
            'activity_type_id' => $this->activityType->id,
            'phase' => 'registration',
            'semester' => '2023/2024-Ganjil',
            'status' => 'draft',
        ]);
    }

    public function test_admin_can_manage_form_status_and_duplicate()
    {
        $this->actingAs($this->admin);

        // 1. Test activation
        Livewire::test(FormManager::class)
            ->call('activate', $this->form->id);

        $this->assertEquals('active', $this->form->fresh()->status);

        // 2. Test close
        Livewire::test(FormManager::class)
            ->call('closeForm', $this->form->id);

        $this->assertEquals('closed', $this->form->fresh()->status);

        // 3. Test archive
        Livewire::test(FormManager::class)
            ->call('archive', $this->form->id);

        $this->assertEquals('archived', $this->form->fresh()->status);

        // 4. Test duplicate
        // Add a section and field first
        $section = FormSection::create([
            'form_id' => $this->form->id,
            'title' => 'Section A',
            'order' => 1,
        ]);
        FormField::create([
            'form_id' => $this->form->id,
            'section_id' => $section->id,
            'label' => 'Pertanyaan A',
            'type' => 'text',
            'order' => 1,
        ]);

        Livewire::test(FormManager::class)
            ->call('duplicate', $this->form->id);

        $duplicateForm = Form::where('title', 'Form Pengajuan Magang Salinan')->first();
        $this->assertNotNull($duplicateForm);
        $this->assertEquals('draft', $duplicateForm->status);
        $this->assertNull($duplicateForm->open_at);
        $this->assertNull($duplicateForm->close_at);
        
        // Assert section and field were duplicated
        $this->assertEquals(1, $duplicateForm->sections()->count());
        $this->assertEquals(1, $duplicateForm->fields()->count());
    }

    public function test_admin_can_manage_fields_sections_and_templates()
    {
        $this->actingAs($this->admin);

        // 1. Create section
        Livewire::test(FormFieldBuilder::class, ['formId' => $this->form->id])
            ->set('sectionTitle', 'Detail Instansi')
            ->set('sectionDescription', 'Informasi instansi magang')
            ->set('sectionOrder', 1)
            ->call('saveSection')
            ->assertHasNoErrors();

        $this->assertEquals(1, $this->form->sections()->count());
        $section = $this->form->sections()->first();

        // 2. Create field inside section
        Livewire::test(FormFieldBuilder::class, ['formId' => $this->form->id])
            ->call('openFieldModal', null, $section->id)
            ->set('label', 'Nama Instansi')
            ->set('type', 'text')
            ->set('is_required', true)
            ->set('order', 1)
            ->call('saveField')
            ->assertHasNoErrors();

        $this->assertEquals(1, $this->form->fields()->count());
        $field = $this->form->fields()->first();
        $this->assertEquals($section->id, $field->section_id);

        // 3. Duplicate field
        Livewire::test(FormFieldBuilder::class, ['formId' => $this->form->id])
            ->call('duplicateField', $field->id);

        $this->assertEquals(2, $this->form->fields()->count());

        // 4. Save to Question Bank
        Livewire::test(FormFieldBuilder::class, ['formId' => $this->form->id])
            ->set('saveToBankCategory', 'Instansi')
            ->call('saveToBank', $field->id);

        $this->assertEquals(1, QuestionBank::count());
        $this->assertEquals('Nama Instansi', QuestionBank::first()->label);

        // 5. Apply Suggestion Template
        Livewire::test(FormFieldBuilder::class, ['formId' => $this->form->id])
            ->call('applyTemplate', 'pendaftaran_kegiatan');

        // Pendaftaran Kegiatan template has 14 questions
        $this->assertEquals(14, $this->form->fields()->count());
    }

    public function test_student_can_fill_dynamic_form()
    {
        // Set form to active with a valid close date
        $this->form->update([
            'status' => 'active',
            'open_at' => now()->subDay(),
            'close_at' => now()->addDay(),
        ]);

        $field = FormField::create([
            'form_id' => $this->form->id,
            'label' => 'Nama Kegiatan',
            'type' => 'text',
            'is_required' => true,
            'order' => 1,
        ]);

        $this->actingAs($this->student, 'student');

        // Test form filler loading and submitting
        Livewire::test(StudentFormFiller::class, ['slug' => $this->form->slug])
            ->set('responses.' . $field->id, 'Magang Industri')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('student.dashboard'));

        $submission = $this->form->submissions()->first();
        $this->assertNotNull($submission);
        $this->assertEquals('20230101999', $submission->nim);
        $this->assertEquals('Magang Industri', $submission->answers[$field->id]);
    }
}
