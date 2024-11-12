<?php

namespace App\Http\Controllers;

use App\Models\HrdAction;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HRDHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = HrdAction::with('hrd', 'testResult.test');

        // Filter by HRD name
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('hrd', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        // Filter by action type
        if ($request->has('action_type') && $request->input('action_type') !== 'all') {
            $actionType = $request->input('action_type');
            $query->where('action_type', $actionType);
        }

        // Filter by time
        if ($request->has('time_filter') && $request->input('time_filter') !== 'all') {
            $timeFilter = $request->input('time_filter');
            switch ($timeFilter) {
                case 'hour':
                    $query->where('created_at', '>=', Carbon::now()->subHour());
                    break;
                case 'day':
                    $query->where('created_at', '>=', Carbon::now()->subDay());
                    break;
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->subMonth());
                    break;
            }
        }

        $actions = $query->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('SubDashboard/history', [
            'title' => 'History',
            'actions' => $actions,
        ]);
    }

    public function SimpanAksi($hrdId, $candidateId, $testResultId, $testName, $candidateName, $actionType, $previousScore, $newScore)
    {
        $details = $actionType === 'create'
            ? "Mengisi score test {$testName} untuk Candidate yang bernama {$candidateName} dengan nilai {$newScore}"
            : "Memperbarui score test {$testName} untuk Candidate yang bernama {$candidateName} dari {$previousScore} menjadi {$newScore}";

        return HrdAction::create([
            'hrd_id' => $hrdId,
            'user_id' => $candidateId,
            'action_type' => $actionType,
            'test_result_id' => $testResultId,
            'details' => $details,
        ]);
    }
}