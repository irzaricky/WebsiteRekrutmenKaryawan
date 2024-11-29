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
    private $maxFileSize = 2048; // 2MB
    private $allowedImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];

    public function index()
    {
        $user = Auth::user();
        return Inertia::render('Candidate/candidate_upload', [
            'title' => 'Upload Document',
            'candidateDetail' => $user->candidateDetail,
            'errors' => session('errors', null)
        ]);
    }

    public function store_files(Request $request)
    {
        try {
            // Validate at least one file is uploaded
            if (!$request->hasAny(['photo', 'cv', 'certificate'])) {
                return redirect()->back()->with([
                    'message' => 'Please upload at least one file.',
                    'type' => 'warning'
                ]);
            }

            $request->validate([
                'photo' => [
                    'nullable',
                    'image',
                    'max:' . $this->maxFileSize,
                    function ($attribute, $value, $fail) {
                        if ($value && !in_array($value->getMimeType(), $this->allowedImageTypes)) {
                            $fail('The photo must be a JPEG, PNG or JPG image.');
                        }
                    },
                ],
                'cv' => 'nullable|mimes:pdf|max:' . $this->maxFileSize,
                'certificate' => 'nullable|mimes:pdf|max:' . $this->maxFileSize,
            ]);

            $user = Auth::user();
            $messages = [];

            // Handle file uploads
            if ($request->hasFile('photo')) {
                try {
                    $this->handleFileUpload($request, 'photo', 'candidate-photos', $user);
                    $messages[] = 'Photo uploaded successfully.';
                } catch (\Exception $e) {
                    $messages[] = 'Failed to upload photo: ' . $e->getMessage();
                }
            }

            if ($request->hasFile('cv')) {
                try {
                    $this->handleFileUpload($request, 'cv', 'candidate-cvs', $user);
                    $messages[] = 'CV uploaded successfully.';
                } catch (\Exception $e) {
                    $messages[] = 'Failed to upload CV: ' . $e->getMessage();
                }
            }

            if ($request->hasFile('certificate')) {
                try {
                    $this->handleFileUpload($request, 'certificate', 'candidate-certificates', $user);
                    $messages[] = 'Certificate uploaded successfully.';
                } catch (\Exception $e) {
                    $messages[] = 'Failed to upload certificate: ' . $e->getMessage();
                }
            }

            return redirect()->back()->with([
                'message' => implode(' ', $messages),
                'type' => empty($messages) ? 'warning' : 'success'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->with([
                'message' => 'Please check your file requirements.',
                'type' => 'error'
            ]);
        }
    }

    public function store_profile(Request $request)
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
                'graduation_year' => 'required|integer|min:1900|max:' . date('Y'),
            ]);

            // Format date 
            $data = $request->all();
            $data['birth_date'] = Carbon::parse($request->birth_date)->format('Y-m-d');

            // Update candidate details
            CandidateDetail::updateOrCreate(
                ['user_id' => $user->id],
                $data
            );

            return redirect()->back()->with('success', 'Profile updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function handleFileUpload($request, $field, $path, $user)
    {
        $file = $request->file($field);
        $existingDetail = $user->candidateDetail;

        // Delete old file if exists
        $fieldPath = $field . '_path';
        if ($existingDetail && $existingDetail->$fieldPath) {
            Storage::delete($existingDetail->$fieldPath);
        }

        // Store new file
        $filePath = $file->store($path);

        // Update or create candidate detail
        CandidateDetail::updateOrCreate(
            ['user_id' => $user->id],
            [$fieldPath => $filePath]
        );
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
            // Menghapus file dari storage server
            Storage::delete($path);
            // Menghapus path file dari database
            $candidateDetail->$field = null;
            $candidateDetail->save();
        }

        return response()->json(['message' => 'File deleted successfully']);
    }

    public function confirmFile(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:users,id',
            'file_type' => 'required|in:photo,cv,certificate'
        ]);

        $candidateDetail = CandidateDetail::where('user_id', $request->candidate_id)->first();

        if (!$candidateDetail) {
            return response()->json(['message' => 'Candidate detail not found'], 404);
        }

        $field = $request->file_type . '_confirmed';
        $currentValue = $candidateDetail->$field;

        // Toggle confirmation status
        $candidateDetail->$field = !$currentValue;
        $candidateDetail->save();

        // Get candidate name
        $candidate = User::find($request->candidate_id);

        // Create history record
        $historyController = new HRDHistoryController();
        $historyController->SimpanAksi(
            Auth::id(), // HRD ID
            $request->candidate_id, // Candidate ID
            null, // Test Result ID
            null, // Test Name
            $candidate->name, // Candidate Name
            $candidateDetail->$field ? 'confirm_file' : 'unconfirm_file', // Action Type
            null, // Previous Score
            null, // New Score
            $request->file_type // File Type
        );

        return back()->with('message', 'File confirmation status updated');
    }

    public function updateFileStatus(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:users,id',
            'file_type' => 'required|in:photo,cv,certificate',
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

        // Get candidate name
        $candidate = User::find($request->candidate_id);

        // Create history record
        $historyController = new HRDHistoryController();
        $historyController->SimpanAksi(
            Auth::id(),
            $request->candidate_id,
            null,
            null,
            $candidate->name,
            'update_file_status',
            null,
            null,
            "{$request->file_type} ({$request->status})"
        );

        return back()->with('message', 'File status updated successfully');
    }

    public function fileStatus()
    {
        $user = Auth::user();
        // Load candidateDetail with all file-related fields
        $candidateDetail = $user->candidateDetail()->select([
            'photo_path',
            'photo_status',
            'cv_path',
            'cv_status',
            'certificate_path',
            'certificate_status'
        ])->first();

        return Inertia::render('Candidate/FileSubmissionStatus', [
            'title' => 'File Status',
            'candidateDetail' => $candidateDetail
        ]);
    }
}