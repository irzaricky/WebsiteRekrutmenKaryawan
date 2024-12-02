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
    private function getTestScore($candidate, $testId)
    {
        return $candidate->testResults->where('test_id', $testId)->first()?->score ?? 0;
    }

    // Add helper method to check for zero scores
    private function hasZeroScores($candidate)
    {
        return $this->getTestScore($candidate, 1) === 0 ||
            $this->getTestScore($candidate, 2) === 0 ||
            $this->getTestScore($candidate, 3) === 0 ||
            $this->getTestScore($candidate, 4) === 0;
    }

    private function calculateRankingData()
    {
        // Step 1: Mengambil semua data hasil test dari candidate
        $candidates = User::where('role', 'Candidate')->with('testResults')->get();

        // Step 2: Decision Matrix with null handling
        $decisionMatrix = [];
        foreach ($candidates as $candidate) {
            $decisionMatrix[$candidate->id] = [
                'TIU' => pow($this->getTestScore($candidate, 1), 2),
                'TWK' => pow($this->getTestScore($candidate, 2), 2),
                'TKB' => pow($this->getTestScore($candidate, 3), 2),
                'TW' => pow($this->getTestScore($candidate, 4), 2),
            ];
        }

        // Step 3: Normalized Decision Matrix
        $decisionMatrixTotal = [
            'TIU' => pow(array_sum(array_column($decisionMatrix, 'TIU')), 0.5),
            'TWK' => pow(array_sum(array_column($decisionMatrix, 'TWK')), 0.5),
            'TKB' => pow(array_sum(array_column($decisionMatrix, 'TKB')), 0.5),
            'TW' => pow(array_sum(array_column($decisionMatrix, 'TW')), 0.5),
        ];

        $normalizedMatrix = [];
        foreach ($candidates as $candidate) {
            $normalizedMatrix[$candidate->id] = [
                'TIU' => $this->getTestScore($candidate, 1) / $decisionMatrixTotal['TIU'],
                'TWK' => $this->getTestScore($candidate, 2) / $decisionMatrixTotal['TWK'],
                'TKB' => $this->getTestScore($candidate, 3) / $decisionMatrixTotal['TKB'],
                'TW' => $this->getTestScore($candidate, 4) / $decisionMatrixTotal['TW'],
            ];
        }

        // Step 4: Weighted Matrix
        $weights = [
            'TIU' => 0.3,
            'TWK' => 0.1,
            'TKB' => 0.2,
            'TW' => 0.4,
        ];

        $weightedMatrix = [];
        foreach ($normalizedMatrix as $candidateId => $scores) {
            $weightedMatrix[$candidateId] = [
                'TIU' => $scores['TIU'] * $weights['TIU'],
                'TWK' => $scores['TWK'] * $weights['TWK'],
                'TKB' => $scores['TKB'] * $weights['TKB'],
                'TW' => $scores['TW'] * $weights['TW'],
            ];
        }

        // Step 5: Calculate A+ and A-
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

        // Step 6: Calculate preferences
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

        // Normalize preferences
        $totalDj = array_sum($preferences);
        foreach ($preferences as $candidateId => $Dj) {
            $preferences[$candidateId] = ($Dj / $totalDj) * 1000;
        }

        // Create ranking data
        return $candidates->map(function ($candidate) use ($preferences) {
            $finalScore = $preferences[$candidate->id] ?? 0;
            return [
                'id' => $candidate->id,
                'name' => $candidate->name,
                'tiu_score' => $this->getTestScore($candidate, 1),
                'twk_score' => $this->getTestScore($candidate, 2),
                'tkb_score' => $this->getTestScore($candidate, 3),
                'tw_score' => $this->getTestScore($candidate, 4),
                'final_score' => $finalScore,
            ];
        })->sortByDesc('final_score')->values();
    }

    public function calculateRanking()
    {
        $rankingData = $this->calculateRankingData()->map(function ($candidate, $index) {
            // Mark as not accepted if any score is 0, otherwise check ranking position
            $candidate['is_accepted'] = !($candidate['tiu_score'] === 0 ||
                $candidate['twk_score'] === 0 ||
                $candidate['tkb_score'] === 0 ||
                $candidate['tw_score'] === 0) &&
                $index < 20;
            return $candidate;
        });

        //Meneruskan data ke view
        return Inertia::render('HRD/candidate_ranking', [
            'title' => 'Candidate Rankings',
            'candidates' => $rankingData
        ]);
    }

    public function getDashboardAnalytics()
    {
        $candidates = User::where('role', 'Candidate')->with('testResults')->get();

        // Update average calculations to use helper method
        $averageScores = [
            'TIU' => round($candidates->avg(fn($c) => $this->getTestScore($c, 1)), 2),
            'TWK' => round($candidates->avg(fn($c) => $this->getTestScore($c, 2)), 2),
            'TKB' => round($candidates->avg(fn($c) => $this->getTestScore($c, 3)), 2),
            'TW' => round($candidates->avg(fn($c) => $this->getTestScore($c, 4)), 2)
        ];

        // Calculate rankings first
        $rankings = $this->calculateRankingData();

        // Filter out candidates with zero scores before taking top 20
        $acceptedCandidates = $rankings->filter(function ($candidate) {
            return !($candidate['tiu_score'] === 0 ||
                $candidate['twk_score'] === 0 ||
                $candidate['tkb_score'] === 0 ||
                $candidate['tw_score'] === 0);
        })->take(20);

        return [
            'title' => 'Dashboard',
            'averageScores' => $averageScores,
            'statistics' => [
                'total_candidates' => $candidates->count(),
                'accepted_candidates' => $acceptedCandidates->count(),
                'acceptance_rate' => round(($acceptedCandidates->count() / $candidates->count()) * 100, 2),
                'accepted_list' => $acceptedCandidates
            ]
        ];
    }
}
