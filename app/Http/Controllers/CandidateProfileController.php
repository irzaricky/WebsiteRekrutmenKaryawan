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

        return Inertia::render('Candidate/profile', [
            'title' => 'Profile',
            'user' => $user,
            'candidateDetail' => $candidateDetail
        ]);
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            // Base validation
            $baseValidation = [
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
            ];

            // Get required ijazah based on education level
            $requiredIjazah = $this->getRequiredIjazah($request->education_level);

            // Add validation rules for required ijazah
            foreach ($requiredIjazah as $ijazah) {
                $fileField = "ijazah_{$ijazah}";
                $existingFile = CandidateDetail::where('user_id', $user->id)
                    ->whereNotNull("{$fileField}_path")
                    ->exists();

                if (!$existingFile) {
                    $baseValidation[$fileField] = 'required|file|mimes:pdf|max:2048';
                }
            }

            $request->validate($baseValidation);

            // Prepare data for update
            $data = $request->only([
                'nik',
                'birth_date',
                'address',
                'education_level',
                'major',
                'institution',
                'graduation_year'
            ]);

            // Format date
            if ($request->birth_date) {
                $data['birth_date'] = Carbon::parse($request->birth_date)->format('Y-m-d');
            }

            // Handle ijazah file uploads
            foreach ($requiredIjazah as $ijazah) {
                $fileField = "ijazah_{$ijazah}";
                if ($request->hasFile($fileField)) {
                    if ($user->candidateDetail?->{"{$fileField}_path"}) {
                        Storage::delete($user->candidateDetail->{"{$fileField}_path"});
                    }

                    $path = $request->file($fileField)->store("ijazah/{$ijazah}", 'private');
                    $data["{$fileField}_path"] = $path;
                    $data["{$fileField}_status"] = 'pending';
                }
            }

            // Update or create candidate details
            CandidateDetail::updateOrCreate(
                ['user_id' => $user->id],
                $data
            );

            return back()->with('success', 'Profile updated successfully');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function getRequiredIjazah($educationLevel)
    {
        return match ($educationLevel) {
            'SMA' => ['smp', 'sma'],
            'D3' => ['smp', 'sma', 'd3'],
            'S1' => ['smp', 'sma', 's1'],
            'S2' => ['smp', 'sma', 's1', 's2'],
            'S3' => ['smp', 'sma', 's1', 's2', 's3'],
            default => []
        };
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $candidateDetail = $user->candidateDetail;

            // Check if education fields can be modified
            if ($candidateDetail) {
                $hasFilesPendingOrAccepted = $this->hasFilesPendingOrAccepted($candidateDetail);

                // If trying to change education details while files are pending/accepted
                if (
                    $hasFilesPendingOrAccepted && (
                        $candidateDetail->education_level !== $request->education_level ||
                        $candidateDetail->major !== $request->major ||
                        $candidateDetail->institution !== $request->institution ||
                        $candidateDetail->graduation_year != $request->graduation_year
                    )
                ) {
                    return back()->withErrors([
                        'education' => 'Education details cannot be modified while files are pending or accepted'
                    ]);
                }
            }

            // Base validation
            $request->validate([
                'education_level' => 'required|in:SMA,D3,S1,S2,S3',
                // ... other validations
            ]);

            // Get required documents based on education level
            $requiredDocs = match ($request->education_level) {
                'SMA' => ['smp', 'sma'],
                'D3' => ['smp', 'sma', 'd3'],
                'S1' => ['smp', 'sma', 's1'],
                'S2' => ['smp', 'sma', 's1', 's2'],
                'S3' => ['smp', 'sma', 's1', 's2', 's3'],
                default => []
            };

            // Validate required files
            $fileValidationRules = [];
            foreach ($requiredDocs as $doc) {
                if (
                    !$request->hasFile("ijazah_files.$doc") &&
                    !CandidateDetail::where('user_id', $user->id)
                        ->whereNotNull("ijazah_{$doc}_path")
                        ->exists()
                ) {
                    $fileValidationRules["ijazah_files.$doc"] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
                }
            }

            $request->validate($fileValidationRules);

            // Store files and update paths
            $data = $request->except('ijazah_files');

            foreach ($requiredDocs as $doc) {
                if ($request->hasFile("ijazah_files.$doc")) {
                    $path = $request->file("ijazah_files.$doc")->store('ijazah', 'private');
                    $data["ijazah_{$doc}_path"] = $path;
                    $data["ijazah_{$doc}_status"] = 'pending';
                }
            }

            // Update candidate details
            CandidateDetail::updateOrCreate(
                ['user_id' => $user->id],
                $data
            );

            return back()->with('success', 'Profile updated successfully');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function hasUploadedEducationFiles($candidateDetail)
    {
        // Check for any education related file paths
        $educationFiles = [
            $candidateDetail->ijazah_smp_path,
            $candidateDetail->ijazah_sma_path,
            $candidateDetail->ijazah_d3_path,
            $candidateDetail->ijazah_s1_path,
            $candidateDetail->ijazah_s2_path,
            $candidateDetail->ijazah_s3_path
        ];

        // Return true if any education files exist
        return collect($educationFiles)->filter()->isNotEmpty();
    }

    private function hasFilesPendingOrAccepted($candidateDetail)
    {
        $requiredFiles = $this->getRequiredIjazah($candidateDetail->education_level);

        foreach ($requiredFiles as $file) {
            $statusField = "ijazah_{$file}_status";
            if (
                $candidateDetail->$statusField &&
                in_array($candidateDetail->$statusField, ['pending', 'accepted'])
            ) {
                return true;
            }
        }

        return false;
    }
}
