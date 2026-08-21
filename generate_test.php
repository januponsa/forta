<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$case = App\Models\DefenseCase::where('defense_type', 'internship_defense')->first();
if ($case) {
    echo "Found case: " . $case->id . "\n";
    $generator = new App\Services\DefenseDocumentGenerator();
    $docs = $generator->generateAllDocuments($case);
    echo "Generated " . count($docs) . " documents.\n";
    foreach($docs as $doc) {
        echo $doc->original_name . " -> " . $doc->file_path . "\n";
        // Copy to artifacts
        $dest = "C:/Users/userJ/.gemini/antigravity-ide/brain/979955bb-067b-46fd-a536-e9b7fcf55a48/scratch/" . $doc->original_name;
        copy(storage_path("app/" . $doc->file_path), $dest);
    }
} else {
    echo "No case found\n";
}
