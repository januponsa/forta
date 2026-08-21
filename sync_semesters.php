<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$semesters = App\Models\Form::select('semester')->distinct()->whereNotNull('semester')->pluck('semester');
foreach($semesters as $sem) {
    $exists = App\Models\AcademicCalendar::where('semester_name', $sem)->exists();
    if(!$exists) {
        App\Models\AcademicCalendar::create([
            'semester_name' => $sem,
            'semester_type' => 'Gasal', // default fallback
            'academic_year' => '2025/2026', // default fallback
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_active' => false,
            'publication_status' => 'draft'
        ]);
        echo 'Added: ' . $sem . "\n";
    }
}
echo "Done.";
