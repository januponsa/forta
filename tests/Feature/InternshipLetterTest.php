<?php

namespace Tests\Feature;

use App\Models\InternshipLetterRequest;
use App\Models\LetterTemplate;
use App\Models\Student;
use App\Models\User;
use App\Services\LetterGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class InternshipLetterTest extends TestCase
{
    use RefreshDatabase;

    public function test_letter_number_generation_is_sequential()
    {
        $template = LetterTemplate::create([
            'type' => 'internship',
            'university_name' => 'Univ Test',
            'campus_address' => 'Test',
            'contact_info' => 'Test',
            'city' => 'Test',
            'letter_code' => 'T/01',
            'subject' => 'Test',
            'opening_paragraph' => 'Test',
            'purpose_paragraph' => 'Test',
            'closing_paragraph' => 'Test',
            'signatory_name' => 'Test',
            'signatory_position' => 'Test',
            'number_format' => '{nomor_urut}/{kode_surat}/{bulan_romawi}/{tahun}',
        ]);

        $student1 = Student::create(['name' => 'S1', 'nim' => '123', 'email' => 's1@m.c', 'password' => 'x', 'angkatan' => 2020, 'program_studi' => 'Informatika']);
        $student2 = Student::create(['name' => 'S2', 'nim' => '124', 'email' => 's2@m.c', 'password' => 'x', 'angkatan' => 2020, 'program_studi' => 'Informatika']);

        $req1 = InternshipLetterRequest::create([
            'student_id' => $student1->id,
            'company_name' => 'PT Satu',
            'recipient_name' => 'HRD',
            'company_address' => 'Alamat',
            'company_city' => 'Kota',
            'start_date' => now()->addDays(5),
            'status' => 'submitted'
        ]);

        $req2 = InternshipLetterRequest::create([
            'student_id' => $student2->id,
            'company_name' => 'PT Dua',
            'recipient_name' => 'HRD',
            'company_address' => 'Alamat',
            'company_city' => 'Kota',
            'start_date' => now()->addDays(5),
            'status' => 'submitted'
        ]);

        $service = new LetterGeneratorService();
        
        // Approve req1
        $num1 = $service->generateNextLetterNumber($template);
        $req1->update(['letter_number' => $num1, 'status' => 'approved']);

        // Approve req2
        $num2 = $service->generateNextLetterNumber($template);
        $req2->update(['letter_number' => $num2, 'status' => 'approved']);

        // Check sequential
        $romanMonths = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $month = $romanMonths[now()->month];
        $year = now()->year;
        
        $this->assertEquals("001/T/01/$month/$year", $num1);
        $this->assertEquals("002/T/01/$month/$year", $num2);
    }

    public function test_admin_can_approve_and_generate_pdf()
    {
        Storage::fake('private');
        \Illuminate\Support\Facades\Mail::fake();
        
        $admin = User::create(['name' => 'A', 'email' => 'a@a.c', 'password' => 'x', 'role' => 'superadmin']);
        $student = Student::create(['name' => 'S1', 'nim' => '123', 'email' => 's1@m.c', 'password' => 'x', 'angkatan' => 2020, 'program_studi' => 'Informatika']);
        
        Storage::disk('public')->put('signatures/sig.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='));
        $lecturer = \App\Models\Lecturer::create([
            'name' => 'Sign Name',
            'position' => 'Sign Pos',
            'email' => 'sign@univ.ac.id',
            'is_active' => true,
            'signature_path' => 'signatures/sig.png',
            'allowed_roles' => ['superadmin'],
        ]);

        LetterTemplate::create([
            'type' => 'internship',
            'university_name' => 'Univ',
            'campus_address' => 'Addr',
            'contact_info' => 'Info',
            'city' => 'City',
            'letter_code' => 'TEST/KP',
            'subject' => 'Subject',
            'opening_paragraph' => 'Open',
            'purpose_paragraph' => 'Purpose',
            'closing_paragraph' => 'Close',
            'signatory_name' => 'Sign Name',
            'signatory_position' => 'Sign Pos',
            'number_format' => '{nomor_urut}/{kode_surat}/{tahun}',
            'lecturer_id' => $lecturer->id,
            'sig_page' => 1,
            'sig_x' => 100,
            'sig_y' => 100,
            'sig_width' => 40,
            'sig_height' => 20,
        ]);

        $req = InternshipLetterRequest::create([
            'student_id' => $student->id,
            'company_name' => 'Test Corp',
            'recipient_name' => 'HRD',
            'company_address' => 'Test Addr',
            'company_city' => 'City',
            'start_date' => now()->addDays(5),
            'status' => 'submitted'
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\InternshipLetter\RequestDetail::class, ['id' => $req->id])
            ->call('approveAndGenerate')
            ->assertHasNoErrors()
            ->assertSee('Surat Magang berhasil ditandatangani dan di-generate');

        $req->refresh();
        
        $this->assertEquals('generated', $req->status);
        $this->assertNotNull($req->letter_number);
        $this->assertNotNull($req->final_pdf_path);
        
        Storage::disk('private')->assertExists($req->final_pdf_path);
    }
}
