<?php

namespace App\Http\Controllers;

use App\Models\HrdAction;
use App\Models\CandidateDetail;
use App\Models\User;
use App\Models\TestsList;
use App\Models\TestResult;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class dataCandidateController extends Controller // Ubah ke huruf kapital
{
    public function getUser(Request $request)
    {
        $search = $request->input('search');
        $emptyScores = $request->boolean('empty_scores');

        $query = User::where('role', 'Candidate')
            ->with(['testResults.test']);

        // Apply search filter first
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Then apply empty scores filter
        if ($emptyScores) {
            $query->where(function ($q) {
                $q->whereDoesntHave('testResults')
                    ->orWhereHas('testResults', function ($query) {
                        $query->whereNull('score')
                            ->orWhere('score', '');
                    });
            });
        }

        $candidates = $query->paginate(6);

        return Inertia::render('HRD/data-candidate', [
            'title' => "Data Candidate",
            'candidates' => $candidates,
        ]);
    }

    public function edit($id)
    {
        $candidate = User::findOrFail($id);
        $testResults = TestResult::where('user_id', $id)->get();
        $tests = TestsList::all();

        return Inertia::render('HRD/edit-data-candidate', [
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

        // Format the date while preserving the model relationship
        if ($candidate->candidateDetail) {
            $candidate->candidateDetail->birth_date = Carbon::parse($candidate->candidateDetail->birth_date)
                ->format('Y-m-d');
        }


        // Format the date while preserving the model relationship
        if ($candidate->candidateDetail) {
            $candidate->candidateDetail->birth_date = Carbon::parse($candidate->candidateDetail->birth_date)
                ->format('Y-m-d');
        }

        return Inertia::render('HRD/CandidateDetails', [
            'title' => 'Candidate Details',
            'candidateDetails' => $candidate
        ]);
    }

    public function updateFileStatus(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:users,id',
            'file_type' => 'required|in:photo,cv,ijazah_smp,ijazah_sma,ijazah_d3,ijazah_s1,ijazah_s2,ijazah_s3',
            'status' => 'required|in:pending,accepted,rejected'
        ]);

        $candidateDetail = CandidateDetail::where('user_id', $request->candidate_id)->first();

        if (!$candidateDetail) {
            return response()->json(['message' => 'Candidate detail not found'], 404);
        }

        $field = $request->file_type . '_status';
        $oldStatus = $candidateDetail->$field;
        $candidateDetail->$field = $request->status;
        $candidateDetail->save();

        // Create history record
        $historyController = new HRDHistoryController();
        $historyController->SimpanAksi(
            Auth::id(),
            $request->candidate_id,
            null,
            null,
            $candidateDetail->user->name,
            'update_file_status',
            $oldStatus,
            $request->status,
            $request->file_type
        );

        return back()->with('message', 'File status updated successfully');
    }

    public function getPendingFiles(Request $request)
    {
        $query = User::where('role', 'Candidate')
            ->with('candidateDetail');

        // Add search filter
        if ($search = $request->input('search')) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Apply not uploaded filter
        if ($request->boolean('not_uploaded')) {
            $query->where(function ($q) {
                $q->whereDoesntHave('candidateDetail')
                    ->orWhereHas('candidateDetail', function ($q) {
                        $q->where(function ($subq) {
                            // Base documents check
                            $subq->whereNull('photo_path')
                                ->orWhereNull('cv_path');

                            // Education level specific checks
                            $subq->orWhere(function ($edu) {
                                $edu->where('education_level', 'SMA')->where(function ($q) {
                                    $q->whereNull('ijazah_smp_path')
                                        ->orWhereNull('ijazah_sma_path');
                                });
                            })->orWhere(function ($edu) {
                                $edu->where('education_level', 'D3')->where(function ($q) {
                                    $q->whereNull('ijazah_smp_path')
                                        ->orWhereNull('ijazah_sma_path')
                                        ->orWhereNull('ijazah_d3_path');
                                });
                            })->orWhere(function ($edu) {
                                $edu->where('education_level', 'S1')->where(function ($q) {
                                    $q->whereNull('ijazah_smp_path')
                                        ->orWhereNull('ijazah_sma_path')
                                        ->orWhereNull('ijazah_s1_path');
                                });
                            })->orWhere(function ($edu) {
                                $edu->where('education_level', 'S2')->where(function ($q) {
                                    $q->whereNull('ijazah_smp_path')
                                        ->orWhereNull('ijazah_sma_path')
                                        ->orWhereNull('ijazah_s1_path')
                                        ->orWhereNull('ijazah_s2_path');
                                });
                            })->orWhere(function ($edu) {
                                $edu->where('education_level', 'S3')->where(function ($q) {
                                    $q->whereNull('ijazah_smp_path')
                                        ->orWhereNull('ijazah_sma_path')
                                        ->orWhereNull('ijazah_s1_path')
                                        ->orWhereNull('ijazah_s2_path')
                                        ->orWhereNull('ijazah_s3_path');
                                });
                            });
                        });
                    });
            });
        }

        $candidates = $query->paginate(8);

        return Inertia::render('HRD/pending-files', [
            'title' => "Files Review",
            'candidates' => $candidates
        ]);
    }
}
