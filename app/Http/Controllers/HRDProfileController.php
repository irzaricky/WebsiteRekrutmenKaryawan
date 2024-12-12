<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HRDProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return Inertia::render('HRD/profile', [
            'title' => 'HRD Profile',
            'user' => $user,
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
        ]);

        try {
            $user = Auth::user();
            $user->update($request->only(['name', 'email']));

            return back()->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update profile');
        }
    }
}
