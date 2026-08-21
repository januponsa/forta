<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Assessment;

$assessments = Assessment::where('assessor_role', 'mentor')->with('scores')->get();
foreach ($assessments as $assessment) {
    $sum = 0;
    $count = 0;
    $hasDecimal = false;
    foreach ($assessment->scores as $scoreObj) {
        $val = $scoreObj->score;
        if (is_numeric($val) && $val >= 0 && $val <= 100) {
            $fVal = (float) $val;
            $sum += $fVal;
            $count++;
            if (floor($fVal) != $fVal) {
                $hasDecimal = true;
            }
        }
    }
    if ($count > 0) {
        $avg = $sum / $count;
        $assessment->total_score = $hasDecimal ? round($avg, 2) : round($avg, 0);
        $assessment->save();
        echo "Updated Assessment ID {$assessment->id} to {$assessment->total_score}\n";
    }
}
