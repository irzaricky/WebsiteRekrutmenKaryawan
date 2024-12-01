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
        $candidateDetail = $user->candidateDetail;

        // Redirect if profile not complete
        if (!$candidateDetail || !$candidateDetail->nik) {
            return redirect()->route('candidate.profile')
                ->with('message', 'Please complete your profile before uploading documents.');
        }

        return Inertia::render('Candidate/candidate_upload', [
            'title' => 'Upload Document',
            'candidateDetail' => $candidateDetail
        ]);
    }

    public function store_files(Request $request)
    {
        try {
            $user = Auth::user();
            $candidateDetail = $user->candidateDetail;

            // Check each file if it's already accepted
            foreach (['photo', 'cv', 'certificate'] as $field) {
                if ($request->hasFile($field)) {
                    $statusField = $field . '_status';
                    if ($candidateDetail && $candidateDetail->$statusField === 'accepted') {
                        return redirect()->back()->with([
                            'message' => ucfirst($field) . ' has been accepted and cannot be modified.',
                            'type' => 'error'
                        ]);
                    }
                }
            }

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

            $messages = [];

            // Handle file uploads
            foreach (['photo', 'cv', 'certificate'] as $field) {
                if ($request->hasFile($field)) {
                    try {
                        $this->handleFileUpload(
                            $request,
                            $field,
                            "candidate-{$field}s",
                            $user
                        );
                        $messages[] = ucfirst($field) . ' uploaded successfully.';
                    } catch (\Exception $e) {
                        $messages[] = "Failed to upload {$field}: " . $e->getMessage();
                    }
                }
            }

            // Refresh user data to ensure we have latest changes
            $user->refresh();

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

        // Status field name
        $fieldStatus = $field . '_status';

        // Update or create candidate detail with both path and status
        $updateData = [
            $fieldPath => $filePath,
        ];

        // Only set status to pending if it's a new upload or status is null
        if (!$existingDetail || !$existingDetail->$fieldStatus) {
            $updateData[$fieldStatus] = 'pending';
        }

        CandidateDetail::updateOrCreate(
            ['user_id' => $user->id],
            $updateData
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

        // Check if file is accepted
        $statusField = $request->type . '_status';
        if ($candidateDetail->$statusField === 'accepted') {
            return response()->json([
                'message' => 'This file has been accepted and cannot be deleted'
            ], 403);
        }

        $field = $request->type . '_path';
        $path = $candidateDetail->$field;

        if ($path && Storage::exists($path)) {
            Storage::delete($path);
            $candidateDetail->$field = null;
            $candidateDetail->$statusField = null; // Reset status when file is deleted
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

        // Get fresh data by first resolving the relationship
        $candidateDetail = $user->candidateDetail()->first();

        if ($candidateDetail) {
            $candidateDetail = $candidateDetail->fresh();
        }

        return Inertia::render('Candidate/FileSubmissionStatus', [
            'title' => 'File Status',
            'candidateDetail' => $candidateDetail
        ]);
    }
}