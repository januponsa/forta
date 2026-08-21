<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CleanupStudentFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $filePaths;

    public function __construct(array $filePaths)
    {
        $this->filePaths = $filePaths;
    }

    public function handle(): void
    {
        foreach ($this->filePaths as $path) {
            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
                Log::info("Deleted physical file: {$path} via CleanupStudentFilesJob");
            } else {
                Log::warning("File not found for cleanup: {$path}");
            }
        }
    }
}
