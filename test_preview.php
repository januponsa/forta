<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new App\Http\Controllers\MentorDocumentController();
$case = App\Models\DefenseCase::first();
if ($case) {
    try {
        $response = $controller->preview($case);
        echo $response->headers;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
