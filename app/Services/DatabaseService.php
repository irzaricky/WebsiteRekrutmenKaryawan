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
        if (empty($password)) {
            $command = sprintf(
                '"%s" --host="%s" --user="%s" --databases "%s" > "%s"',
                $mysqldump,
                $host,
                $username,
                $database,
                $path
            );
        } else {
            $command = sprintf(
                '"%s" --host="%s" --user="%s" --password="%s" --databases "%s" > "%s"',
                $mysqldump,
                $host,
                $username,
                $password,
                $database,
                $path
            );
        }

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

        // Create restore command
        $command = sprintf(
            'mysql -h %s -u %s -p%s %s < %s',
            $host,
            $username,
            $password,
            $database,
            $path
        );

        exec($command);
    }
}