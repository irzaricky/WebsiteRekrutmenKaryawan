<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TestsList;
use App\Models\TestResult;
use Inertia\Inertia;
use Illuminate\Http\Request;

class dataCandidateController extends Controller // Ubah ke huruf kapital
{
    public function getUser()
    {
        // Mengambil data kandidat dari database
        $candidates = User::where('role', 'Candidate')->paginate(10);

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
            TestResult::updateOrCreate(
                ['user_id' => $id, 'test_id' => $testId],
                ['score' => $score]
            );
        }

        return redirect()->route('dashboard.data-candidate')
            ->with('message', 'Nilai kandidat berhasil diperbarui');
    }
}
