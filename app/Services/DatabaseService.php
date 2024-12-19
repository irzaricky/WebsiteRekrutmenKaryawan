<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseService
{
    protected function getMysqldumpPath()
    {
        // Default Laragon MySQL path
        $defaultPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';

        if (file_exists($defaultPath)) {
            return $defaultPath;
        }

        // Try to find mysqldump in common Laragon paths
        $possiblePaths = [
            'C:\\laragon\\bin\\mysql\\mysql-5.7.33-winx64\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0\\bin\\mysqldump.exe'
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \Exception('mysqldump executable not found. Please check your MySQL installation.');
    }

    protected function getMysqlPath()
    {
        // Default Laragon MySQL path
        $defaultPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysql.exe';

        if (file_exists($defaultPath)) {
            return $defaultPath;
        }

        // Try to find mysql in common Laragon paths
        $possiblePaths = [
            'C:\\laragon\\bin\\mysql\\mysql-5.7.33-winx64\\bin\\mysql.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0\\bin\\mysql.exe'
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \Exception('mysql executable not found. Please check your MySQL installation.');
    }

    public function backup()
    {
        $filename = 'backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
        }

        $host = config('database.connections.mysql.host');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $database = config('database.connections.mysql.database');

        $mysqldump = $this->getMysqldumpPath();

        // Build command based on whether password is set
        $command = sprintf(
            '"%s" --host="%s" --user="%s" %s --databases "%s" --ignore-table=%s.database_backups > "%s"',
            $mysqldump,
            $host,
            $username,
            $password ? '--password="' . $password . '"' : '',
            $database,
            $database,
            $path
        );

        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Database backup failed: ' . implode("\n", $output));
        }

        if (!file_exists($path)) {
            throw new \Exception('Backup file was not created');
        }

        return 'backups/' . $filename;
    }

    public function restore($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (!file_exists($path)) {
            throw new \Exception('Backup file not found');
        }

        // Get database configuration
        $host = config('database.connections.mysql.host');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $database = config('database.connections.mysql.database');

        $mysql = $this->getMysqlPath();

        // First save current database_backups data
        $backupsData = \DB::table('database_backups')->get();

        // Execute restore
        $command = sprintf(
            '"%s" -h %s -u %s %s %s < "%s" 2>&1',
            $mysql,
            escapeshellarg($host),
            escapeshellarg($username),
            $password ? '-p' . escapeshellarg($password) : '',
            escapeshellarg($database),
            $path
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            \Log::error('Database restore failed', [
                'output' => $output,
                'command' => preg_replace('/-p[^ ]+/', '-p***', $command)
            ]);
            throw new \Exception('Database restore failed: ' . implode("\n", $output));
        }

        // Restore database_backups data
        foreach ($backupsData as $backup) {
            \DB::table('database_backups')
                ->updateOrInsert(
                    ['id' => $backup->id],
                    (array) $backup
                );
        }

        return true;
    }
}