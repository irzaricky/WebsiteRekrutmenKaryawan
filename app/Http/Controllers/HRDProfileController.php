<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Rules\ValidNIK;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class HRDProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $hrdDetail = $user->hrdDetail;

        return Inertia::render('HRD/profile', [
            'title' => 'HRD Profile',
            'user' => $user,
            'hrdDetail' => $hrdDetail,
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }


    public function update(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore(Auth::id()),
                ],
                'nik' => [
                    'required',
                    'string',
                    'size:16',
                    Rule::unique('hrd_details')->ignore(Auth::id(), 'user_id'),
                    new ValidNIK,
                ],
                'address' => ['nullable', 'string'],
                'birth_date' => ['nullable', 'date'],
                'profile_image' => ['nullable', 'image', 'max:2048'], // 2MB max
            ]);

            DB::beginTransaction();

            try {
                // 1. Update user basic info
                $user = Auth::user();
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                ]);

                // 2. Update or create HRD details
                $hrdDetail = $user->hrdDetail ?? new \App\Models\HRDDetail();

                if (!$hrdDetail->exists) {
                    $hrdDetail->user_id = $user->id;
                }

                // Handle profile image upload
                if ($request->hasFile('profile_image')) {
                    if ($hrdDetail->profile_image) {
                        Storage::disk('private')->delete($hrdDetail->profile_image);
                    }
                    $path = $request->file('profile_image')->store('profile-images', 'private');
                    $hrdDetail->profile_image = $path;
                }

                // Update HRD details
                $hrdDetail->fill([
                    'nik' => $request->nik,
                    'address' => $request->address,
                    'birth_date' => $request->birth_date ? Carbon::parse($request->birth_date) : null,
                ]);

                $hrdDetail->save();

                DB::commit();

                return redirect()->back()->with('success', 'Profile updated successfully');

            } catch (\Exception $e) {
                DB::rollback();
                \Log::error('Profile update failed: ' . $e->getMessage());
                return redirect()->back()->withErrors(['error' => 'Failed to update profile']);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->errors());
        } catch (\Exception $e) {
            \Log::error('Profile update failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An unexpected error occurred']);
        }
    }

    public function getProfileImage($filename): StreamedResponse
    {
        $user = Auth::user();
        $path = "profile-images/{$filename}";

        if (!Storage::disk('private')->exists($path)) {
            abort(404);
        }

        return Storage::disk('private')->response($path);
    }
}
