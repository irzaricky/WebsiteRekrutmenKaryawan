<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Rules\ValidNIK;

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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'nik' => ['required', 'string', 'size:16', new ValidNIK],
            'address' => 'required|string',
            'birth_date' => 'required|date',
        ]);

        try {
            $user = Auth::user();
            $user->update($request->only(['name', 'email']));

            // Update or create HRD details
            $user->hrdDetail()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nik' => $request->nik,
                    'address' => $request->address,
                    'birth_date' => $request->birth_date,
                ]
            );

            return back()->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update profile');
        }
    }
}
