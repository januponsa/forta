<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\DefenseCase;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\RubricVersion;
use App\Models\Student;
use App\Models\Submission;
use App\Models\Form;
use App\Services\DefenseCalculationService;
use Illuminate\Support\Facades\DB;
use Database\Seeders\InternshipRubricSeeder;

class DefenseCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Run the seeder
        $this->seed(InternshipRubricSeeder::class);
    }

    public function test_calculate_final_score_and_grade()
    {
        $service = new DefenseCalculationService();
        
        $form = $activity = \App\Models\ActivityType::create(['name' => 'Test', 'slug' => 'test']);
        Form::create(['title' => 'Test', 'slug' => 'test', 'form_code' => 'INTERNSHIP_DEFENSE_REGISTRATION', 'activity_type_id' => $activity->id, 'phase' => 'registration']);
        $student = Student::create(['nim' => '123456', 'name' => 'John', 'email' => 'john@test.com', 'angkatan' => '2020']);
        $submission = Submission::create(['form_id' => $form->id, 'nim' => '123456', 'name' => 'John', 'email' => 'john@test.com', 'status' => 'approved']);
        
        $case = DefenseCase::create([
            'submission_id' => $submission->id,
            'student_id' => $student->id,
            'defense_type' => 'internship_defense',
            'semester' => 'Ganjil',
            'status' => 'ready',
        ]);

        $spvRubric = RubricVersion::where('role', 'supervisor')->first();
        $exmRubric = RubricVersion::where('role', 'examiner')->first();
        $menRubric = RubricVersion::where('role', 'mentor')->first();

        // Spv Assessment (Max 100)
        $spvAss = Assessment::create(['defense_case_id' => $case->id, 'rubric_version_id' => $spvRubric->id, 'assessor_type' => 'lecturer', 'assessor_role' => 'supervisor', 'status' => 'final', 'total_score' => 90]);
        // Exm Assessment (Max 100)
        $exmAss = Assessment::create(['defense_case_id' => $case->id, 'rubric_version_id' => $exmRubric->id, 'assessor_type' => 'lecturer', 'assessor_role' => 'examiner', 'status' => 'final', 'total_score' => 80]);
        // Mentor Assessment (Max 100)
        $menAss = Assessment::create(['defense_case_id' => $case->id, 'rubric_version_id' => $menRubric->id, 'assessor_type' => 'admin', 'assessor_role' => 'mentor', 'status' => 'final', 'total_score' => 85]);

        $service->calculateFinalDefenseScore($case);
        $case->refresh();

        // 90 * 0.3 + 80 * 0.3 + 85 * 0.4 = 27 + 24 + 34 = 85
        $this->assertEquals(85, $case->final_score);
        $this->assertEquals('A-', $case->final_grade);
    }
    
    public function test_score_conversion_rules()
    {
        $service = new DefenseCalculationService();
        
        $this->assertEquals('A', $service->convertScoreToGrade(92)['grade']);
        $this->assertEquals('A-', $service->convertScoreToGrade(85)['grade']);
        $this->assertEquals('B+', $service->convertScoreToGrade(80)['grade']);
        $this->assertEquals('B', $service->convertScoreToGrade(75)['grade']);
        $this->assertEquals('B-', $service->convertScoreToGrade(70)['grade']);
        $this->assertEquals('C+', $service->convertScoreToGrade(65)['grade']);
        $this->assertEquals('C', $service->convertScoreToGrade(60)['grade']);
        $this->assertEquals('D', $service->convertScoreToGrade(50)['grade']);
        $this->assertEquals('D', $service->convertScoreToGrade(59)['grade']);
        $this->assertEquals('E', $service->convertScoreToGrade(49)['grade']);
        $this->assertEquals('E', $service->convertScoreToGrade(0)['grade']);
    }
}
