<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CandidateDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CandidateProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $candidateDetail = $user->candidateDetail;

        return Inertia::render('Profile/showCandidate', [
            'title' => 'Profile',
            'user' => $user,
            'candidateDetail' => $candidateDetail
        ]);
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'nik' => 'required|string|size:16|unique:candidate_details,nik,' . $user->id . ',user_id',
                'full_name' => 'required|string|max:255',
                'birth_date' => 'required|date_format:Y-m-d', // Validate date format
                'address' => 'required|string',
                'education_level' => 'required|in:SMA,D3,S1,S2,S3',
                'major' => 'required|string',
                'institution' => 'required|string',
                'graduation_year' => 'required|integer|min:1900|max:' . date('Y')
            ]);

            // Handle file uploads
            $data = $request->except(['cv', 'photo', 'certificate']);

            // Format the date before saving
            $data['birth_date'] = date('Y-m-d', strtotime($request->birth_date));

            if ($request->hasFile('photo')) {
                if ($user->candidateDetail && $user->candidateDetail->photo_path) {
                    Storage::delete($user->candidateDetail->photo_path);
                }
                $data['photo_path'] = $request->file('photo')->store('candidate-photos');
            }

            if ($request->hasFile('cv')) {
                if ($user->candidateDetail && $user->candidateDetail->cv_path) {
                    Storage::delete($user->candidateDetail->cv_path);
                }
                $data['cv_path'] = $request->file('cv')->store('candidate-cvs');
            }

            if ($request->hasFile('certificate')) {
                if ($user->candidateDetail && $user->candidateDetail->certificate_path) {
                    Storage::delete($user->candidateDetail->certificate_path);
                }
                $data['certificate_path'] = $request->file('certificate')->store('candidate-certificates');
            }

            // Update or create candidate details
            $candidateDetail = CandidateDetail::updateOrCreate(
                ['user_id' => $user->id],
                $data
            );

            return redirect()->route('profile.candidate.show')->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update profile: ' . $e->getMessage()]);
        }
    }
}
