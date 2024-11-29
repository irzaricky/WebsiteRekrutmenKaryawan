<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CandidateDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Rules\ValidNIK;
use Carbon\Carbon;

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
                'nik' => [
                    'required',
                    'string',
                    'size:16',
                    'unique:candidate_details,nik,' . Auth::id() . ',user_id',
                    new ValidNIK
                ],
                'full_name' => 'required|string|max:255',
                'birth_date' => 'required|date',
                'address' => 'required|string',
                'education_level' => 'required|in:SMA,D3,S1,S2,S3',
                'major' => 'required|string',
                'institution' => 'required|string',
                'graduation_year' => 'required|integer|min:1900|max:' . date('Y')
            ]);

            // Get all request data except files
            $data = $request->except(['cv', 'photo', 'certificate']);

            // Format date only once
            if ($request->birth_date) {
                $data['birth_date'] = Carbon::parse($request->birth_date)->format('Y-m-d');
            }

            // Update or create candidate details
            CandidateDetail::updateOrCreate(
                ['user_id' => $user->id],
                $data
            );

            return redirect()->back()->with('success', 'Profile updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
