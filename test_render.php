<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $case = App\Models\DefenseCase::first();
    $generator = new App\Services\DefenseDocumentGenerator();
    $docs = $generator->getAvailableDocuments($case);
    echo "Found " . count($docs) . " docs\n";
    $htmls = [];
    foreach($docs as $doc) {
        $html = $generator->getDocumentHtml($case, $doc['type'], true);
        echo "Rendered " . $doc['type'] . ": " . strlen($html) . " bytes\n";
        $htmls[] = $html;
    }
    
    // Simulate what Livewire does (json encode)
    $payload = json_encode(['html' => $htmls]);
    if ($payload === false) {
        echo "JSON Encode failed: " . json_last_error_msg() . "\n";
    } else {
        echo "JSON Payload size: " . strlen($payload) . " bytes\n";
    }
    
    // Simulate Blade escaping
    $escaped = htmlspecialchars($htmls[0], ENT_QUOTES, 'UTF-8', false);
    echo "Escaped size: " . strlen($escaped) . " bytes\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
