<?php

namespace App\Http\Controllers;

use App\Models\HrdAction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class HRDHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = HrdAction::query()
            ->with(['hrd', 'candidate'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->action_type && $request->action_type !== 'all') {
            $query->where('action_type', $request->action_type);
        }

        // Time filter
        if ($request->time_filter) {
            $query->where('created_at', '>=', match ($request->time_filter) {
                'hour' => Carbon::now()->subHour(),
                'day' => Carbon::now()->subDay(),
                'week' => Carbon::now()->subWeek(),
                'month' => Carbon::now()->subMonth(),
                default => Carbon::now()->subYears(100)
            });
        }

        $actions = $query->paginate(8);

        return Inertia::render('HRD/history', [
            'title' => 'History',
            'actions' => $actions
        ]);
    }

    public function SimpanAksi($hrdId, $candidateId, $testResultId = null, $testName = null, $candidateName, $actionType, $previousScore = null, $newScore = null, $fileType = null)
    {
        $details = match ($actionType) {
            'create' => "Mengisi score test {$testName} untuk Candidate yang bernama {$candidateName} dengan nilai {$newScore}",
            'update' => "Memperbarui score test {$testName} untuk Candidate yang bernama {$candidateName} dari {$previousScore} menjadi {$newScore}",
            'update_file_status' => "Mengubah status file {$fileType} untuk Candidate yang bernama {$candidateName} menjadi {$newScore}",
            default => "Melakukan aksi {$actionType}"
        };

        return HrdAction::create([
            'hrd_id' => $hrdId,
            'user_id' => $candidateId,
            'action_type' => $actionType,
            'test_result_id' => $testResultId,
            'details' => $details,
        ]);
    }
}