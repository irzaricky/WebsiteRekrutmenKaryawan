<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Rules\ValidNIK;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HRDProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $hrdDetail = $user->hrdDetail;

        // Format the birth date
        if ($hrdDetail && $hrdDetail->birth_date) {
            $hrdDetail->birth_date = $hrdDetail->birth_date->format('Y-m-d');
        }

        // Add image URL with proper error handling
        if ($hrdDetail && $hrdDetail->profile_image) {
            try {
                $filename = basename($hrdDetail->profile_image);
                $hrdDetail->profile_image_url = route('hrd.profile.image', $filename);
            } catch (\Exception $e) {
                \Log::error('Error generating profile image URL: ' . $e->getMessage());
                $hrdDetail->profile_image_url = null;
            }
        }

        $user->hrd_detail = $hrdDetail;
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
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . Auth::id(),
                'nik' => ['required', 'string', 'size:16', new ValidNIK],
                'address' => 'required|string',
                'birth_date' => 'required|date',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $user = Auth::user();
            $user->update($request->only(['name', 'email']));

            $data = [
                'nik' => $request->nik,
                'address' => $request->address,
                'birth_date' => $request->birth_date,
            ];

            if ($request->hasFile('profile_image')) {
                if ($user->hrdDetail?->profile_image) {
                    Storage::disk('private')->delete($user->hrdDetail->profile_image);
                }

                $path = $request->file('profile_image')->store('profile-images', 'private');
                if (!$path) {
                    throw new \Exception('Failed to store profile image');
                }
                $data['profile_image'] = $path;
            }

            $hrdDetail = $user->hrdDetail()->updateOrCreate(
                ['user_id' => $user->id],
                $data
            );

            // Generate image URL after update
            if ($hrdDetail->profile_image) {
                $filename = basename($hrdDetail->profile_image);
                $hrdDetail->profile_image_url = route('hrd.profile.image', $filename);
                $user->hrd_detail = $hrdDetail;
            }

            return back()->with([
                'success' => 'Profile updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            \Log::error('Profile update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update profile: ' . $e->getMessage());
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
