<?php

namespace App\Console\Commands;

use App\Services\AcademicCalendarImportService;
use Illuminate\Console\Command;

class ImportOfficialAcademicCalendarCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'academic-calendar:import-official-2025-2026';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports the official 2025-2026 academic calendar data into the database.';

    /**
     * Execute the console command.
     */
    public function handle(AcademicCalendarImportService $importService)
    {
        $this->info('Starting official academic calendar import for 2025/2026...');

        $dataPath = database_path('data/official_academic_calendar_2025_2026.php');
        
        if (!file_exists($dataPath)) {
            $this->error("Data file not found at: {$dataPath}");
            return Command::FAILURE;
        }

        $data = require $dataPath;

        if (!is_array($data) || empty($data['academic_calendars'])) {
            $this->error("Invalid data format in {$dataPath}");
            return Command::FAILURE;
        }

        $stats = $importService->import($data);

        $this->info("Import completed with the following stats:");
        $this->line("- Calendars created: " . $stats['calendars_created']);
        $this->line("- Calendars updated: " . $stats['calendars_updated']);
        $this->line("- Events created: " . $stats['events_created']);
        $this->line("- Events updated: " . $stats['events_updated']);
        $this->line("- Meeting weeks created: " . $stats['weeks_created']);
        $this->line("- Meeting weeks updated: " . $stats['weeks_updated']);

        if (!empty($stats['errors'])) {
            $this->error("Validation / Import Errors:");
            foreach ($stats['errors'] as $error) {
                $this->line("- " . $error);
            }
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
