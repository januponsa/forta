<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
Auth::guard('web')->login($user);

$controller = new App\Http\Controllers\GeneratedDocumentController();
$doc = App\Models\GeneratedDocument::first();
if ($doc) {
    try {
        $response = $controller->download($doc->id);
        echo $response->headers;
        echo "\nSUCCESS DOWNLOAD";
    } catch (Exception $e) {
        echo get_class($e) . ': ' . $e->getMessage();
    }
} else {
    echo "NO DOCS";
}
