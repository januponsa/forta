<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
Auth::guard('web')->login($user);

$case = App\Models\DefenseCase::first();
if ($case) {
    if (Illuminate\Support\Facades\Gate::allows('viewMentorDocument', $case)) {
        echo "ALLOWED";
    } else {
        echo "FORBIDDEN";
    }
}
