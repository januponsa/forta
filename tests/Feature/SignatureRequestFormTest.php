<?php

namespace Tests\Feature;

use App\Livewire\Student\SignatureRequestForm;
use App\Models\SignatureRequest;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SignatureRequestFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
    }

    public function test_valid_student_can_submit_request()
    {
        $student = Student::create(['nim' => '123', 'name' => 'Test', 'email' => 'test@test.com', 'angkatan' => 2020]);

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        Livewire::actingAs($student, 'student')
            ->test(SignatureRequestForm::class)
            ->set('title', 'Surat Izin')
            ->set('page_number', 1)
            ->set('x_pos', 10)
            ->set('y_pos', 20)
            ->set('width', 40)
            ->set('height', 20)
            ->set('page_width', 210)
            ->set('page_height', 297)
            ->set('document', $file)
            ->call('store')
            ->assertRedirect(route('student.signature-requests.index'));

        $this->assertDatabaseHas('signature_requests', [
            'student_id' => $student->id,
            'title' => 'Surat Izin',
            'status' => 'submitted',
        ]);

        $request = SignatureRequest::first();
        Storage::disk('private')->assertExists($request->original_file_path);
    }

    public function test_cannot_edit_other_students_request()
    {
        $student1 = Student::create(['nim' => '1234', 'name' => 'A', 'email' => 'a@a.com', 'angkatan' => 2020]);
        $student2 = Student::create(['nim' => '1235', 'name' => 'B', 'email' => 'b@b.com', 'angkatan' => 2020]);

        $request = SignatureRequest::create([
            'student_id' => $student1->id,
            'title' => 'Surat Budi',
            'page_number' => 1,
            'x_pos' => 10,
            'y_pos' => 10,
            'width' => 10,
            'height' => 10,
            'page_width' => 210,
            'page_height' => 297,
            'status' => 'draft',
            'original_file_path' => 'dummy.pdf',
            'original_filename' => 'dummy.pdf',
        ]);

        Livewire::actingAs($student2, 'student')
            ->test(SignatureRequestForm::class, ['id' => $request->id])
            ->assertForbidden();
    }

    public function test_can_update_own_draft()
    {
        $student = Student::create(['nim' => '1236', 'name' => 'C', 'email' => 'c@c.com', 'angkatan' => 2020]);

        $request = SignatureRequest::create([
            'student_id' => $student->id,
            'title' => 'Draft Awal',
            'page_number' => 1,
            'x_pos' => 10,
            'y_pos' => 10,
            'width' => 10,
            'height' => 10,
            'page_width' => 210,
            'page_height' => 297,
            'status' => 'draft',
            'original_file_path' => 'signature-requests/original/old.pdf',
            'original_filename' => 'old.pdf',
        ]);

        Storage::disk('private')->put('signature-requests/original/old.pdf', 'dummy');

        $file = UploadedFile::fake()->create('new.pdf', 100, 'application/pdf');

        Livewire::actingAs($student, 'student')
            ->test(SignatureRequestForm::class, ['id' => $request->id])
            ->set('title', 'Draft Baru')
            ->set('document', $file)
            ->call('store')
            ->assertRedirect(route('student.signature-requests.index'));

        $this->assertDatabaseHas('signature_requests', [
            'id' => $request->id,
            'title' => 'Draft Baru',
            'status' => 'submitted',
        ]);

        Storage::disk('private')->assertMissing('signature-requests/original/old.pdf');
    }

    public function test_cannot_edit_submitted_request()
    {
        $student = Student::create(['nim' => '1237', 'name' => 'D', 'email' => 'd@d.com', 'angkatan' => 2020]);

        $request = SignatureRequest::create([
            'student_id' => $student->id,
            'title' => 'Sudah Submit',
            'page_number' => 1,
            'x_pos' => 10,
            'y_pos' => 10,
            'width' => 10,
            'height' => 10,
            'page_width' => 210,
            'page_height' => 297,
            'status' => 'submitted',
            'original_file_path' => 'dummy.pdf',
            'original_filename' => 'dummy.pdf',
        ]);

        Livewire::actingAs($student, 'student')
            ->test(SignatureRequestForm::class, ['id' => $request->id])
            ->assertForbidden();
    }
}
