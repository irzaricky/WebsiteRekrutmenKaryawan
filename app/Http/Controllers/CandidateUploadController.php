<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CandidateDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CandidateUploadController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return Inertia::render('SubDashboard/candidate_upload', [
            'title' => 'Upload Document',
            'candidateDetail' => $user->candidateDetail
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16|unique:candidate_details,nik,' . Auth::id() . ',user_id',
            'full_name' => 'required|string|max:255',
            'address' => 'required|string',
            'birth_date' => 'required|date',
            'education_level' => 'required|in:SMA,D3,S1,S2,S3',
            'major' => 'required|string',
            'institution' => 'required|string',
            'graduation_year' => 'required|integer|min:1900|max:' . date('Y'),
            'photo' => 'nullable|image|max:2048',
            'cv' => 'nullable|mimes:pdf|max:2048',
            'certificate' => 'nullable|mimes:pdf|max:2048'
        ]);

        $user = Auth::user();
        $data = $request->except(['photo', 'cv', 'certificate']);

        // Handle file uploads
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('candidate-photos');
        }
        if ($request->hasFile('cv')) {
            $data['cv_path'] = $request->file('cv')->store('candidate-cvs');
        }
        if ($request->hasFile('certificate')) {
            $data['certificate_path'] = $request->file('certificate')->store('candidate-certificates');
        }

        // Update or create candidate details
        CandidateDetail::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return redirect()->back()->with('message', 'Data berhasil disimpan');
    }
}