<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CandidateDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Rules\ValidNIK;

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
            'nik' => [
                'required',
                'string',
                'size:16',
                'unique:candidate_details,nik,' . Auth::id() . ',user_id',
                new ValidNIK
            ],
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

    public function getFile($type, $filename)
    {
        // Get current logged in user
        $user = Auth::user();

        // Get candidate ID from filename or request
        $candidateUserId = request()->query('candidate_id');

        // If user is HRD, allow access with candidate_id
        if ($user->role === 'HRD') {
            $candidateDetail = CandidateDetail::where('user_id', $candidateUserId)->first();
        } else {
            // For candidates, only allow access to their own files
            $candidateDetail = $user->candidateDetail;
        }

        if (!$candidateDetail) {
            abort(404);
        }

        // Rest of the validation and file serving logic
        $field = $type . '_path';
        $expectedPath = $candidateDetail->$field;

        $path = "";
        switch ($type) {
            case 'photo':
                $path = 'candidate-photos/' . $filename;
                break;
            case 'cv':
                $path = 'candidate-cvs/' . $filename;
                break;
            case 'certificate':
                $path = 'candidate-certificates/' . $filename;
                break;
            default:
                abort(404);
        }

        if ($expectedPath !== $path) {
            abort(403, 'Unauthorized access');
        }

        if (!Storage::exists($path)) {
            abort(404);
        }

        return Storage::response($path);
    }

    public function deleteFile(Request $request)
    {
        $request->validate([
            'type' => 'required|in:photo,cv,certificate'
        ]);

        $user = Auth::user();
        $candidateDetail = $user->candidateDetail;

        if (!$candidateDetail) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $field = $request->type . '_path';
        $path = $candidateDetail->$field;

        if ($path && Storage::exists($path)) {
            Storage::delete($path);
            $candidateDetail->$field = null;
            $candidateDetail->save();
        }

        return response()->json(['message' => 'File deleted successfully']);
    }
}