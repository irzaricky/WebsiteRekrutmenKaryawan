<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CandidateDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Rules\ValidNIK;

class CandidateUploadController extends Controller
{
    private $maxFileSize = 2048;
    private $allowedImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    private $allowedDocTypes = ['application/pdf'];

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

            // Check profile completion first
            if (!$candidateDetail || !$candidateDetail->nik) {
                return redirect()->route('candidate.profile')
                    ->with('message', 'Please complete your profile before uploading documents.');
            }

            // Validate basic files
            $validator = Validator::make($request->all(), [
                'photo' => [
                    'nullable',
                    'file',
                    'image',
                    'max:' . $this->maxFileSize,
                    function ($attribute, $value, $fail) {
                        if ($value && !in_array($value->getMimeType(), $this->allowedImageTypes)) {
                            $fail("Photo must be a JPEG, PNG or JPG image.");
                        }
                    },
                ],
                'cv' => [
                    'nullable',
                    'file',
                    'mimes:pdf',
                    'max:' . $this->maxFileSize
                ]
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $messages = [];

            // Handle basic files (photo, cv)
            foreach (['photo', 'cv'] as $type) {
                if ($request->hasFile($type)) {
                    // Check if file already accepted
                    $statusField = "{$type}_status";
                    if ($candidateDetail->$statusField === 'accepted') {
                        return redirect()->back()->with([
                            'message' => ucfirst($type) . ' has been accepted and cannot be modified.',
                            'type' => 'error'
                        ]);
                    }

                    // Store and update file
                    $path = Storage::putFile("/candidate-{$type}s", $request->file($type));
                    if ($path) {
                        // Delete old file if exists
                        $pathField = "{$type}_path";
                        if ($candidateDetail->$pathField) {
                            Storage::delete($candidateDetail->$pathField);
                        }

                        // Update details
                        $candidateDetail->$pathField = $path;
                        $candidateDetail->$statusField = 'pending';
                        $messages[] = ucfirst($type) . ' uploaded successfully.';
                    }
                }
            }

            // Handle ijazah files based on education level
            if ($candidateDetail->education_level) {
                $requiredIjazah = $this->getRequiredIjazah($candidateDetail->education_level);

                foreach ($requiredIjazah as $ijazah) {
                    $type = "ijazah_{$ijazah}";
                    if ($request->hasFile($type)) {
                        // Validate ijazah file
                        $request->validate([
                            $type => 'file|mimes:pdf|max:' . $this->maxFileSize
                        ]);

                        // Check if already accepted
                        $statusField = "{$type}_status";
                        if ($candidateDetail->$statusField === 'accepted') {
                            continue;
                        }

                        // Store and update file
                        $path = Storage::putFile("/ijazah/{$ijazah}", $request->file($type));
                        if ($path) {
                            // Delete old file if exists
                            $pathField = "{$type}_path";
                            if ($candidateDetail->$pathField) {
                                Storage::delete($candidateDetail->$pathField);
                            }

                            // Update details
                            $candidateDetail->$pathField = $path;
                            $candidateDetail->$statusField = 'pending';
                            $messages[] = "Ijazah " . strtoupper($ijazah) . " uploaded successfully.";
                        }
                    }
                }
            }

            // Save all changes
            $candidateDetail->save();

            return redirect()->back()->with([
                'message' => empty($messages) ? 'No files were uploaded.' : implode(', ', $messages),
                'type' => empty($messages) ? 'warning' : 'success'
            ]);

        } catch (\Exception $e) {
            Log::error("File upload error: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
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
        $fieldPath = "{$field}_path";
        $statusField = "{$field}_status";
        if ($existingDetail && $existingDetail->$fieldPath) {
            Storage::delete($existingDetail->$fieldPath);
        }

        try {
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
        } catch (\Exception $e) {
            Log::error("File upload failed: {$e->getMessage()}");
            throw $e;
        }
    }

    public function getFile($type, $filename)
    {
        $user = Auth::user();
        $candidateUserId = request()->query('candidate_id');

        // For HRD, allow access with candidate_id 
        if ($user->role === 'HRD') {
            $candidateDetail = CandidateDetail::where('user_id', $candidateUserId)->first();
        } else {
            $candidateDetail = $user->candidateDetail;
        }

        if (!$candidateDetail) {
            abort(404);
        }

        // Get expected file path based on type
        $fieldPath = match ($type) {
            'photo' => $candidateDetail->photo_path,
            'cv' => $candidateDetail->cv_path,
            'ijazah_smp' => $candidateDetail->ijazah_smp_path,
            'ijazah_sma' => $candidateDetail->ijazah_sma_path,
            'ijazah_d3' => $candidateDetail->ijazah_d3_path,
            'ijazah_s1' => $candidateDetail->ijazah_s1_path,
            'ijazah_s2' => $candidateDetail->ijazah_s2_path,
            'ijazah_s3' => $candidateDetail->ijazah_s3_path,
            default => null
        };

        if (!$fieldPath) {
            abort(404);
        }

        $expectedPath = match ($type) {
            'photo' => 'candidate-photos/' . $filename,
            'cv' => 'candidate-cvs/' . $filename,
            'ijazah_smp' => 'ijazah/smp/' . $filename,
            'ijazah_sma' => 'ijazah/sma/' . $filename,
            'ijazah_d3' => 'ijazah/d3/' . $filename,
            'ijazah_s1' => 'ijazah/s1/' . $filename,
            'ijazah_s2' => 'ijazah/s2/' . $filename,
            'ijazah_s3' => 'ijazah/s3/' . $filename,
            default => abort(404)
        };

        if ($expectedPath !== $fieldPath) {
            abort(403, 'Unauthorized access');
        }

        if (!Storage::exists($fieldPath)) {
            abort(404);
        }

        return Storage::response($fieldPath);
    }

    public function deleteFile(Request $request)
    {
        $request->validate([
            'type' => [
                'required',
                'in:photo,cv,ijazah_smp,ijazah_sma,ijazah_d3,ijazah_s1,ijazah_s2,ijazah_s3'
            ]
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

        // Get file path field name
        $pathField = $request->type . '_path';
        $path = $candidateDetail->$pathField;

        if ($path && Storage::exists($path)) {
            Storage::delete($path);
            $candidateDetail->$pathField = null;
            $candidateDetail->$statusField = null;
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
            Auth::id(),
            $request->candidate_id,
            null,
            null,
            $candidate->name,
            $candidateDetail->$field ? 'confirm_file' : 'unconfirm_file',
            null,
            null,
            $request->file_type
        );

        return back()->with('message', 'File confirmation status updated');
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

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $candidateDetail = $user->candidateDetail;

            // Basic file validations
            $baseValidations = [
                'photo' => [
                    'nullable',
                    'file',
                    'image',
                    'max:' . $this->maxFileSize,
                    function ($attribute, $value, $fail) {
                        if ($value && !in_array($value->getMimeType(), $this->allowedImageTypes)) {
                            $fail("Photo must be a JPEG, PNG or JPG image.");
                        }
                    },
                ],
                'cv' => [
                    'nullable',
                    'file',
                    'mimes:pdf',
                    'max:' . $this->maxFileSize
                ]
            ];

            // Add ijazah validations based on education level
            if ($candidateDetail->education_level) {
                $requiredIjazah = $this->getRequiredIjazah($candidateDetail->education_level);
                foreach ($requiredIjazah as $ijazah) {
                    $baseValidations["ijazah_$ijazah"] = [
                        'nullable',
                        'file',
                        'mimes:pdf',
                        'max:' . $this->maxFileSize
                    ];
                }
            }

            $request->validate($baseValidations);

            $messages = [];

            // Handle basic files (photo, cv)
            foreach (['photo', 'cv'] as $type) {
                if ($request->hasFile($type)) {
                    $path = $this->storeFile($request->file($type), $type);
                    if ($path) {
                        $this->updateFileStatus($candidateDetail, $type, $path);
                        $messages[] = ucfirst($type) . " uploaded successfully.";
                    }
                }
            }

            // Handle ijazah files
            if ($candidateDetail->education_level) {
                foreach ($requiredIjazah as $ijazah) {
                    if ($request->hasFile("ijazah_$ijazah")) {
                        $path = $this->storeFile(
                            $request->file("ijazah_$ijazah"),
                            "ijazah/$ijazah"
                        );
                        if ($path) {
                            $this->updateFileStatus($candidateDetail, "ijazah_$ijazah", $path);
                            $messages[] = "Ijazah " . strtoupper($ijazah) . " uploaded successfully.";
                        }
                    }
                }
            }

            return redirect()->back()->with([
                'message' => implode(' ', $messages),
                'type' => empty($messages) ? 'warning' : 'success'
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function storeFile($file, $type)
    {
        try {
            return Storage::putFile("/$type", $file);
        } catch (\Exception $e) {
            Log::error("File upload failed: " . $e->getMessage());
            return null;
        }
    }

    private function updateFileStatus($candidateDetail, $type, $path)
    {
        $pathField = "{$type}_path";
        $statusField = "{$type}_status";

        // Delete old file if exists
        if ($candidateDetail->$pathField) {
            Storage::delete($candidateDetail->$pathField);
        }

        $candidateDetail->$pathField = $path;
        $candidateDetail->$statusField = 'pending';
        $candidateDetail->save();
    }

    private function getRequiredIjazah($level)
    {
        return match ($level) {
            'SMA' => ['smp', 'sma'],
            'D3' => ['smp', 'sma', 'd3'],
            'S1' => ['smp', 'sma', 's1'],
            'S2' => ['smp', 'sma', 's1', 's2'],
            'S3' => ['smp', 'sma', 's1', 's2', 's3'],
            default => []
        };
    }

    private function validateFileStatus($candidateDetail, $type)
    {
        $statusField = "{$type}_status";
        return !$candidateDetail || $candidateDetail->$statusField !== 'accepted';
    }
}