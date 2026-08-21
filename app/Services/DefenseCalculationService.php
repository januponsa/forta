<?php

namespace App\Services;

use App\Models\DefenseCase;
use App\Models\Assessment;
use App\Models\RubricVersion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DefenseCalculationService
{
    /**
     * Calculate and save the total score for a specific assessment based on its items.
     * For supervisor and examiner, it's just SUM(scores).
     * For mentor, it's AVERAGE(scores).
     */
    public function calculateAssessmentTotal(Assessment $assessment)
    {
        $role = $assessment->assessor_role; // 'supervisor', 'examiner', 'mentor'
        
        $scores = $assessment->scores()->pluck('score');
        
        if ($scores->isEmpty()) {
            $assessment->update(['total_score' => 0]);
            return 0;
        }

        if ($role === 'mentor') {
            // Formula: rata-rata A1, B1, B2, C1, C2, D1, D2, E1, E2
            $total = $scores->average();
        } else {
            // Formula: SUM
            $total = $scores->sum();
        }
        
        $assessment->update(['total_score' => $total]);
        return $total;
    }

    /**
     * Finalize an assessment and try to calculate the final case score.
     */
    public function finalizeAssessment(Assessment $assessment, $userId = null)
    {
        $this->calculateAssessmentTotal($assessment);
        
        $assessment->update([
            'status' => 'final',
            'finalized_at' => Carbon::now()
        ]);
        
        // Try to calculate final defense score if all required assessments are finalized
        $this->calculateFinalDefenseScore($assessment->defenseCase);
    }

    /**
     * Check if all required components are finalized and calculate the final score.
     */
    public function calculateFinalDefenseScore(DefenseCase $case)
    {
        // For internship_defense, we need: Supervisor, Examiner, Mentor assessments to be final.
        $assessments = $case->assessments()->where('status', 'final')->get();
        
        $hasSupervisor = $assessments->where('assessor_role', 'supervisor')->first();
        $hasExaminer = $assessments->where('assessor_role', 'examiner')->first();
        $hasMentor = $assessments->where('assessor_role', 'mentor')->first();
        
        // Check suggestions status (must be provided or explicitly 'no suggestions')
        // In this simple implementation, we'll assume suggestions can be handled independently 
        // but for safety, we require all 3 roles to be final.
        
        if ($hasSupervisor && $hasExaminer && $hasMentor) {
            
            // Check originality status from examiner
            if ($hasExaminer->originality_status === 'Terbukti Plagiarisme') {
                $case->update([
                    'final_score' => 0,
                    'final_grade' => 'E',
                    'final_decision' => 'Tidak Lulus',
                    'status' => 'failed'
                ]);
                return;
            }

            // Get weights from rubric versions
            $spvWeight = RubricVersion::where('id', $hasSupervisor->rubric_version_id)->value('weight_percentage') / 100;
            $exmWeight = RubricVersion::where('id', $hasExaminer->rubric_version_id)->value('weight_percentage') / 100;
            $menWeight = RubricVersion::where('id', $hasMentor->rubric_version_id)->value('weight_percentage') / 100;
            
            $finalScore = ($hasSupervisor->total_score * $spvWeight) + 
                          ($hasExaminer->total_score * $exmWeight) + 
                          ($hasMentor->total_score * $menWeight);
            
            $gradeInfo = $this->convertScoreToGrade($finalScore);
            
            $case->update([
                'final_score' => $finalScore,
                'final_grade' => $gradeInfo['grade'],
                'final_decision' => $gradeInfo['decision'],
                'status' => 'ready_to_finalize' // Or 'revision_required' based on suggestions later
            ]);
        }
    }

    /**
     * Convert numeric score to Letter Grade and Decision
     */
    public function convertScoreToGrade($score)
    {
        if ($score >= 90) return ['grade' => 'A', 'decision' => 'Lulus'];
        if ($score >= 85) return ['grade' => 'A-', 'decision' => 'Lulus'];
        if ($score >= 80) return ['grade' => 'B+', 'decision' => 'Lulus'];
        if ($score >= 75) return ['grade' => 'B', 'decision' => 'Lulus'];
        if ($score >= 70) return ['grade' => 'B-', 'decision' => 'Lulus'];
        if ($score >= 65) return ['grade' => 'C+', 'decision' => 'Lulus dengan Revisi'];
        if ($score >= 60) return ['grade' => 'C', 'decision' => 'Lulus dengan Revisi'];
        if ($score >= 50) return ['grade' => 'D', 'decision' => 'Tidak Lulus'];
        return ['grade' => 'E', 'decision' => 'Tidak Lulus']; // < 50
    }
}
