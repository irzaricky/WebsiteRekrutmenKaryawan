<?php

namespace App\Http\Controllers;

use App\Models\HrdAction;
use App\Models\User;
use App\Models\TestsList;
use App\Models\TestResult;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class dataCandidateController extends Controller // Ubah ke huruf kapital
{
    public function getUser(Request $request)
    {
        $search = $request->input('search');

        $candidates = User::where('role', 'Candidate')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%");
            })
            ->paginate(10);

        return Inertia::render('SubDashboard/data-candidate', [
            'title' => "Data Candidate",
            'candidates' => $candidates, // Mengirim data kandidat ke komponen
        ]);
    }

    public function edit($id)
    {
        $candidate = User::findOrFail($id);
        $testResults = TestResult::where('user_id', $id)->get();
        $tests = TestsList::all();

        return Inertia::render('SubDashboard/edit-data-candidate', [
            'title' => "Edit Data Kandidat",
            'candidate' => $candidate,
            'testResults' => $testResults,
            'tests' => $tests
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'test_results' => 'required|array',
            'test_results.*' => 'required|numeric|min:0'
        ]);
        $candidate = User::findOrFail($id);

        foreach ($request->test_results as $testId => $score) {
            // Fetch the previous score
            $previousTestResult = TestResult::where('user_id', $id)->where('test_id', $testId)->first();
            $previousScore = $previousTestResult ? $previousTestResult->score : null;

            // Update or create the test result
            $testResult = TestResult::updateOrCreate(
                ['user_id' => $id, 'test_id' => $testId],
                ['score' => $score]
            );

            // Log the action only if the score has changed
            if ($previousScore !== null && $previousScore != $score) {
                // Fetch the test name
                $test = TestsList::findOrFail($testId);

                HrdAction::create([
                    'hrd_id' => Auth::id(),
                    'user_id' => $candidate->id,
                    'action_type' => 'update',
                    'test_result_id' => $testResult->id,
                    'details' => "Memperbarui score test {$test->name} untuk Candidate yang bernama {$candidate->name} dari {$previousScore} menjadi {$score}",
                ]);
            }
        }

        return redirect()->route('dashboard.data-candidate')
            ->with('message', 'Nilai kandidat berhasil diperbarui');
    }
}
