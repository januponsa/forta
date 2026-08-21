<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DefenseCase;
use App\Services\DefenseDocumentGenerator;

// Find a case to test with
$case = DefenseCase::with(['student', 'submission', 'latestSchedule', 'assignments.lecturer', 'assessments.scores.rubricItem.section', 'suggestions.lecturer'])->first();
if (!$case) {
    echo "No case found.\n";
    exit;
}
echo "Testing case ID: " . $case->id . "\n";
echo "Student: " . ($case->student->name ?? 'N/A') . "\n";
echo "Submission title: " . ($case->submission->title ?? 'N/A') . "\n";

$generator = new DefenseDocumentGenerator();

try {
    $start = microtime(true);
    $docs = $generator->generateAllDocuments($case);
    $end = microtime(true);

    echo "Total time: " . round($end - $start, 2) . " seconds\n";
    echo "Generated " . count(array_filter($docs)) . " documents.\n";
    
    foreach ($docs as $doc) {
        if ($doc) {
            echo "  - {$doc->document_type}: {$doc->file_path}\n";
        }
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
