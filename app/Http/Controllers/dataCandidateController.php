<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TestResult;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DataCandidateController extends Controller // Ubah ke huruf kapital
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
}
