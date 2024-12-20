<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\DatabaseService;
use Inertia\Inertia;
use Illuminate\Support\Facades\File;
use App\Models\DatabaseBackup;
use Illuminate\Support\Facades\Auth;

class DatabaseBackupController extends Controller
{
    protected $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    public function index()
    {
        $backups = DatabaseBackup::with('user')
            ->latest()
            ->get()
            ->map(function ($backup) {
                return [
                    'id' => $backup->id,
                    'filename' => $backup->filename,
                    'path' => $backup->path,
                    'size' => $backup->size,
                    'category' => $backup->category,
                    'type' => $backup->type,
                    'created_by' => $backup->user->name,
                    'created_at' => $backup->created_at->format('Y-m-d H:i:s')
                ];
            });

        return Inertia::render('HRD/database-management', [
            'title' => 'Database Management',
            'backups' => $backups
        ]);
    }

    public function backup()
    {
        try {
            $backupPath = $this->databaseService->backup();
            $fullPath = storage_path('app/' . $backupPath);

            // Get file size using PHP's filesize() function
            $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;

            // Create backup record
            DatabaseBackup::create([
                'filename' => basename($backupPath),
                'path' => $backupPath,
                'category' => 'manual',
                'user_id' => Auth::id(),
                'size' => $fileSize
            ]);

            return redirect()->back()->with('success', 'Database backup created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function backupFiles()
    {
        try {
            $backupPath = $this->databaseService->backupFiles();
            $fullPath = storage_path('app/' . $backupPath);

            // Get file size
            $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;

            // Create backup record
            DatabaseBackup::create([
                'filename' => basename($backupPath),
                'path' => $backupPath,
                'category' => 'manual',
                'type' => 'files',
                'user_id' => Auth::id(),
                'size' => $fileSize
            ]);

            return redirect()->back()->with('success', 'Project files backup created successfully');
        } catch (\Exception $e) {
            \Log::error('Backup failed', [
                'error' => $e->getMessage(),
                'type' => 'files'
            ]);
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|string'
        ]);

        try {
            // Verify file exists in database
            $backup = DatabaseBackup::where('filename', $request->backup_file)->firstOrFail();

            // Attempt restore
            $this->databaseService->restore($request->backup_file);

            return redirect()->back()->with('success', 'Database restored successfully');
        } catch (\Exception $e) {
            \Log::error('Restore failed', [
                'error' => $e->getMessage(),
                'file' => $request->backup_file
            ]);
            return redirect()->back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        // Find backup record first
        $backup = DatabaseBackup::where('filename', $filename)->first();

        if (!$backup) {
            return redirect()->back()->with('error', 'Backup record not found');
        }

        // Use absolute path to storage
        $path = storage_path('app/backups/' . $filename);

        // Verify file exists using PHP's file_exists
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Backup file not found');
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename=' . $filename
        ]);
    }

    public function delete($filename)
    {
        try {
            // Find backup record first
            $backup = DatabaseBackup::where('filename', $filename)->firstOrFail();

            // Get full path to backup file
            $path = 'backups/' . $filename;
            $fullPath = storage_path('app/' . $path);

            // Delete physical file if exists
            if (Storage::exists($path)) {
                Storage::delete($path);
            } elseif (file_exists($fullPath)) {
                unlink($fullPath);
            }

            // Delete database record
            $backup->delete();

            return redirect()->back()->with('success', 'Backup deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Backup deletion failed', [
                'error' => $e->getMessage(),
                'filename' => $filename
            ]);
            return redirect()->back()->with('error', 'Failed to delete backup: ' . $e->getMessage());
        }
    }
}