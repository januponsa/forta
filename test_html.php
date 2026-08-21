<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$case = App\Models\DefenseCase::where('defense_type', 'internship_defense')->first();
$generator = new App\Services\DefenseDocumentGenerator();
$reflection = new ReflectionClass($generator);

$prepareShared = $reflection->getMethod('prepareSharedData');
$prepareShared->setAccessible(true);
$prepareShared->invoke($generator, $case);

$prop = $reflection->getProperty('logoData');
$prop->setAccessible(true);
echo "Logo Data length: " . strlen($prop->getValue($generator)) . "\n";
