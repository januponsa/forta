<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $case = App\Models\DefenseCase::first();
    $generator = new App\Services\DefenseDocumentGenerator();
    $docs = $generator->getAvailableDocuments($case);
    foreach($docs as $doc) {
        $html = $generator->getDocumentHtml($case, $doc['type'], true);
        echo "Generating " . $doc['type'] . "\n";
        $generator->saveDocumentFromHtml($case, $doc['type'], $doc['filename'], $html);
    }
    echo "Done.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
