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
        $historyController = new HRDHistoryController();

        foreach ($request->test_results as $testId => $score) {
            // Fetch the previous score
            $previousTestResult = TestResult::where('user_id', $id)
                ->where('test_id', $testId)
                ->first();

            $previousScore = $previousTestResult ? $previousTestResult->score : null;

            // Update or create the test result
            $testResult = TestResult::updateOrCreate(
                ['user_id' => $id, 'test_id' => $testId],
                ['score' => $score]
            );

            // Only log if score has changed or is new
            if ($previousScore !== $score) {
                $test = TestsList::findOrFail($testId);

                $historyController->SimpanAksi(
                    Auth::id(),
                    $candidate->id,
                    $testResult->id,
                    $test->name,
                    $candidate->name,
                    $previousScore === null ? 'create' : 'update',
                    $previousScore,
                    $score
                );
            }
        }

        return redirect()->route('dashboard.data-candidate')
            ->with('message', 'Nilai kandidat berhasil diperbarui');
    }

    public function show($id)
    {
        $candidate = User::with('candidateDetail')->findOrFail($id);
        return Inertia::render('SubDashboard/CandidateDetails', [
            'title' => 'Candidate Details',
            'candidateDetails' => $candidate
        ]);
    }
}
