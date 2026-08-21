<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$path = 'defense_documents/1/Biodata_KP_2023012345_januponsa-dio-firizqi.pdf';
$disk = Illuminate\Support\Facades\Storage::disk('local');

echo "Resolved path: " . $disk->path($path) . "\n";
echo "File exists: " . ($disk->exists($path) ? 'YES' : 'NO') . "\n";
echo "File size: " . ($disk->exists($path) ? $disk->size($path) : 'N/A') . "\n";
