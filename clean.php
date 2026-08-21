<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cases = App\Models\DefenseCase::all();
foreach($cases as $case) {
    $docs = $case->documents;
    $types = $docs->pluck('document_type')->unique();
    foreach($types as $type) {
        $duplicates = $case->documents()->where('document_type', $type)->orderBy('created_at', 'desc')->get();
        if($duplicates->count() > 1) {
            $duplicates->shift(); // keep the first one
            foreach($duplicates as $dup) {
                if(Illuminate\Support\Facades\Storage::disk('local')->exists($dup->file_path)) {
                    Illuminate\Support\Facades\Storage::disk('local')->delete($dup->file_path);
                }
                $dup->delete();
            }
        }
    }
}
echo "Cleanup done\n";
