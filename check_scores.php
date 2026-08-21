<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AssessmentScore;

$scores = AssessmentScore::where('assessment_id', 3)->get();
foreach($scores as $s) {
    echo "Score: '" . $s->score . "' (Type: " . gettype($s->score) . ")\n";
}
