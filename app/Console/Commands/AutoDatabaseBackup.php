<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseService;
use App\Models\DatabaseBackup;
use Illuminate\Support\Facades\Log;

class AutoDatabaseBackup extends Command
{
    protected $signature = 'db:auto-backup';
    protected $description = 'Create automatic database backup every 24 hours';

    protected $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        parent::__construct();
        $this->databaseService = $databaseService;
    }

    public function handle()
    {
        try {
            $backupPath = $this->databaseService->backup();
            $fullPath = storage_path('app/' . $backupPath);

            // Get file size
            $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;

            // Create backup record with automatic category
            DatabaseBackup::create([
                'filename' => basename($backupPath),
                'path' => $backupPath,
                'category' => 'automatic',
                'user_id' => 1,
                'size' => $fileSize
            ]);

            $this->info('Automatic database backup created successfully');
            Log::info('Automatic database backup created successfully');
        } catch (\Exception $e) {
            $this->error('Automatic backup failed: ' . $e->getMessage());
            Log::error('Automatic backup failed: ' . $e->getMessage());
        }
    }
}
