<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\DefenseCase;
use App\Models\Submission;
use App\Models\DefenseSchedule;
use App\Models\DefenseAssignment;
use App\Models\LetterTemplate;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\RubricVersion;
use App\Models\RubricSection;
use App\Models\RubricItem;
use App\Models\DefenseSuggestion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class DefensePdfGeneratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->template = new LetterTemplate([
            'university_name' => 'UNIVERSITAS PRADITA',
            'campus_address' => 'Kampus Utama - Menara Satu Kelapa Gading Lt. 11',
            'contact_info' => '(021) 5568 9999 | www.pradita.ac.id'
        ]);
        
        $this->student = new Student([
            'nim' => '2023012345',
            'name' => 'Alfred Gerald Thendiwijaya',
            'semester' => 'Ganjil 2025/2026'
        ]);
        
        $this->supervisor = new Lecturer([
            'nip' => '0301018501',
            'name' => 'Dr. Budi Rahardjo, S.Kom., M.T.'
        ]);
        
        $this->examiner = new Lecturer([
            'nip' => '0302028602',
            'name' => 'Prof. Dr. Ir. Anita Yuliana, M.Kom.'
        ]);
        
        $this->submission = new Submission([
            'title' => 'Sistem Rekomendasi Restoran Berbasis AI',
            'status' => 'approved',
        ]);
        
        $this->defenseCase = new DefenseCase([
            'defense_type' => 'internship_defense',
            'semester' => 'Ganjil 2025/2026',
            'status' => 'passed',
            'final_score' => 97.47,
            'final_grade' => 'A',
            'final_decision' => 'Lulus',
            'metadata' => [
                'mentor_name' => 'Reza Pahlevi, S.T.',
                'mentor_nip' => 'EMP-2021-098',
                'company_name' => 'PT Solusi Teknologi Terpadu'
            ]
        ]);
        
        $this->defenseCase->setRelation('student', $this->student);
        $this->defenseCase->setRelation('submission', $this->submission);
        
        $schedule = new DefenseSchedule([
            'scheduled_at' => '2026-07-20 09:00:00',
            'duration_minutes' => 60,
        ]);
        $this->defenseCase->setRelation('latestSchedule', $schedule);
        
        $spvAssign = new DefenseAssignment(['role' => 'supervisor']);
        $spvAssign->setRelation('lecturer', $this->supervisor);
        
        $exmAssign = new DefenseAssignment(['role' => 'examiner']);
        $exmAssign->setRelation('lecturer', $this->examiner);
        
        $this->defenseCase->setRelation('assignments', collect([$spvAssign, $exmAssign]));
        
        // Setup Assessments
        $this->spvAss = new Assessment(['assessor_role' => 'supervisor', 'total_score' => 96]);
        $this->exmAss = new Assessment(['assessor_role' => 'examiner', 'total_score' => 100, 'originality_status' => 'Tidak Ada Indikasi Pelanggaran']);
        $this->menAss = new Assessment(['assessor_role' => 'mentor', 'total_score' => 96.67]);
        
        $sec = new RubricSection(['id' => 1, 'name' => 'Mentor']);
        $itemA1 = new RubricItem(['id' => 1, 'code' => 'A1', 'description' => 'Disiplin', 'max_score' => 100]);
        $itemA1->setRelation('section', $sec);
        $sc = new AssessmentScore(['score' => 96.67]);
        $sc->setRelation('rubricItem', $itemA1);
        $this->menAss->setRelation('scores', collect([$sc]));
        $this->defenseCase->setRelation('assessments', collect([$this->spvAss, $this->exmAss, $this->menAss]));
        
        $this->defenseCase->setRelation('suggestions', collect());
    }
    
    private function renderView($name) {
        $data = [
            'case' => $this->defenseCase,
            'logoData' => 'dummy',
            'template' => $this->template,
            'isDraft' => false,
            'signatures' => ['date' => '20 Juli 2026'],
            'schedule' => $this->defenseCase->latestSchedule,
            'supervisorName' => 'Dr. Budi Rahardjo, S.Kom., M.T.',
            'examinerName' => 'Prof. Dr. Ir. Anita Yuliana, M.Kom.',
            'spvAssessment' => $this->spvAss,
            'exmAssessment' => $this->exmAss,
            'menAssessment' => $this->menAss,
            'assessment' => clone $this->menAss,
            'suggestions' => collect()
        ];
        return view("pdf.defense.{$name}", $data)->render();
    }

    public function test_biodata_view_renders_correctly()
    {
        $view = $this->renderView('biodata');
        $this->assertStringNotContainsString('Universitas FORTA', $view);
        $this->assertStringContainsString('UNIVERSITAS PRADITA', $view);
        $this->assertStringContainsString('Alfred Gerald Thendiwijaya', $view);
        $this->assertStringContainsString('2023012345', $view);
        $this->assertStringContainsString('Reza Pahlevi, S.T.', $view);
        $this->assertStringContainsString('PT Solusi Teknologi Terpadu', $view);
    }
    
    public function test_f1_view_renders_correctly()
    {
        $view = $this->renderView('f1');
        $this->assertStringContainsString('LULUS', $view);
        $this->assertStringContainsString('Disetujui via sistem', $view);
        $this->assertStringContainsString('10 hari', $view);
        $this->assertStringContainsString('Juli', $view);
    }

    public function test_f2_view_renders_correctly()
    {
        $view = $this->renderView('f2');
        $this->assertStringContainsString('97.47', $view);
        $this->assertStringContainsString('>A<', $view);
        $this->assertStringContainsString('NP1', $view);
        $this->assertStringContainsString('NP2', $view);
        $this->assertStringContainsString('NPem', $view);
    }

    public function test_f5_view_renders_correctly()
    {
        $view = $this->renderView('f5');
        $this->assertStringContainsString('Rata-Rata Nilai Mentor', $view);
        $this->assertStringContainsString('96.67', $view);
        $this->assertStringContainsString('Tanda tangan asli terdapat', $view);
    }
}
