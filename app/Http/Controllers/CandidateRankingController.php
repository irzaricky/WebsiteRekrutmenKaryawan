<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TestResult;
use App\Models\TestsList;
use App\Models\CandidateRanking;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CandidateRankingController extends Controller
{
    public function calculateRanking()
    {
        // Step 1: Mengambil semua data hasil test dari candidate
        $candidates = User::where('role', 'Candidate')->with('testResults')->get();

        // Step 2: Decision Matrix (Mengkuadratkan setiap hasil test)
        $decisionMatrix = [];
        foreach ($candidates as $candidate) {
            $decisionMatrix[$candidate->id] = [
                'TIU' => pow($candidate->testResults->where('test_id', 1)->first()->score, 2),
                'TWK' => pow($candidate->testResults->where('test_id', 2)->first()->score, 2),
                'TKB' => pow($candidate->testResults->where('test_id', 3)->first()->score, 2),
                'TW' => pow($candidate->testResults->where('test_id', 4)->first()->score, 2),
            ];
        }

        // dd($decisionMatrix);

        // Step 3: Normalized Decision Matrix

        // Kalkulasi total nilai dari decision matrix yang dipangkatkan 0.5
        $decisionMatrixTotal = [
            'TIU' => pow(array_sum(array_column($decisionMatrix, 'TIU')), 0.5),
            'TWK' => pow(array_sum(array_column($decisionMatrix, 'TWK')), 0.5),
            'TKB' => pow(array_sum(array_column($decisionMatrix, 'TKB')), 0.5),
            'TW' => pow(array_sum(array_column($decisionMatrix, 'TW')), 0.5),
        ];

        // dd($decisionMatrixTotal);

        // kalkulasi normalized matrix menggunakan score awal test dibagi dengan total nilai decision matrix dipangkatkan 0.5
        $normalizedMatrix = [];
        foreach ($candidates as $candidate) {
            $normalizedMatrix[$candidate->id] = [
                'TIU' => ($candidate->testResults->where('test_id', 1)->first()->score) / $decisionMatrixTotal['TIU'],
                'TWK' => ($candidate->testResults->where('test_id', 2)->first()->score) / $decisionMatrixTotal['TWK'],
                'TKB' => ($candidate->testResults->where('test_id', 3)->first()->score) / $decisionMatrixTotal['TKB'],
                'TW' => ($candidate->testResults->where('test_id', 4)->first()->score) / $decisionMatrixTotal['TW'],
            ];
        }

        //dd($normalizedMatrix);

        // Step 4: Normalized Weighted Matrix (mengalikan setiap skor yang dinormalisasi dengan bobotnya)

        //membuat list bobot setiap hasil test
        $weights = [
            'TIU' => 0.3,
            'TWK' => 0.1,
            'TKB' => 0.2,
            'TW' => 0.4,
        ];

        //mengalikan setiap skor yang dinormalisasi dengan bobotnya
        $weightedMatrix = [];
        foreach ($normalizedMatrix as $candidateId => $scores) {
            $weightedMatrix[$candidateId] = [
                'TIU' => $scores['TIU'] * $weights['TIU'],
                'TWK' => $scores['TWK'] * $weights['TWK'],
                'TKB' => $scores['TKB'] * $weights['TKB'],
                'TW' => $scores['TW'] * $weights['TW'],
            ];
        }

        // dd($weightedMatrix);

        // Step 5: Kalkulasi A+ and A- untuk setiap tests
        $Aplus = [
            'TIU' => min(array_column($weightedMatrix, 'TIU')),
            'TWK' => min(array_column($weightedMatrix, 'TWK')),
            'TKB' => min(array_column($weightedMatrix, 'TKB')),
            'TW' => min(array_column($weightedMatrix, 'TW')),
        ];

        $Aminus = [
            'TIU' => max(array_column($weightedMatrix, 'TIU')),
            'TWK' => max(array_column($weightedMatrix, 'TWK')),
            'TKB' => max(array_column($weightedMatrix, 'TKB')),
            'TW' => max(array_column($weightedMatrix, 'TW')),
        ];

        // dd($Aplus, $Aminus);

        // Step 6: Kalkulasi Dj+, Dj-, Dj, and Normalized Dj
        $preferences = [];
        foreach ($weightedMatrix as $candidateId => $scores) {
            $DjPlus = sqrt(
                pow($scores['TIU'] - $Aplus['TIU'], 2) +
                pow($scores['TWK'] - $Aplus['TWK'], 2) +
                pow($scores['TKB'] - $Aplus['TKB'], 2) +
                pow($scores['TW'] - $Aplus['TW'], 2)
            );

            $DjMinus = sqrt(
                pow($scores['TIU'] - $Aminus['TIU'], 2) +
                pow($scores['TWK'] - $Aminus['TWK'], 2) +
                pow($scores['TKB'] - $Aminus['TKB'], 2) +
                pow($scores['TW'] - $Aminus['TW'], 2)
            );

            $Dj = $DjPlus / ($DjPlus + $DjMinus);
            $preferences[$candidateId] = $Dj;
        }

        //dd($preferences);

        // Normalize Dj
        $totalDj = array_sum($preferences);
        foreach ($preferences as $candidateId => $Dj) {
            $preferences[$candidateId] = ($Dj / $totalDj) * 1000;
        }

        // Step 7: Ranking semua candidates
        arsort($preferences);

        // menyimpan rank candidate ke dalam database
        foreach ($preferences as $candidateId => $ranking) {
            CandidateRanking::updateOrCreate(
                ['user_id' => $candidateId],
                ['ranking' => $ranking]
            );
        }

        $rankingData = $candidates->map(function ($candidate, $index) use ($preferences) {
            $finalScore = $preferences[$candidate->id] ?? 0;
            return [
                'id' => $candidate->id,
                'name' => $candidate->name,
                'tiu_score' => $candidate->testResults->where('test_id', 1)->first()?->score,
                'twk_score' => $candidate->testResults->where('test_id', 2)->first()?->score,
                'tkb_score' => $candidate->testResults->where('test_id', 3)->first()?->score,
                'tw_score' => $candidate->testResults->where('test_id', 4)->first()?->score,
                'final_score' => $finalScore,
            ];
        })->sortByDesc('final_score')
            ->values()
            ->map(function ($candidate, $index) {
                $candidate['is_accepted'] = $index < 20;
                return $candidate;
            });

        //Meneruskan data ke view
        return Inertia::render('SubDashboard/candidate_ranking', [
            'candidates' => $rankingData
        ]);
    }
}
