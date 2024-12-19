<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TestResult;
use App\Models\TestsList;
use App\Models\CandidateDetail;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use App\Rules\ValidNIK;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'title' => "Register"
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string|in:Candidate,HRD',
            'nik' => ['required', 'string', 'size:16', 'unique:candidate_details', new ValidNIK],
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => 'pending'
            ]);

            // Create candidate detail if role is Candidate
            if ($request->role === 'Candidate') {
                CandidateDetail::create([
                    'user_id' => $user->id,
                    'nik' => $request->nik,
                    'address' => null,
                    'birth_date' => null,
                    'photo_path' => null,
                    'cv_path' => null,
                    'education_level' => null,
                    'major' => null,
                    'institution' => null,
                    'graduation_year' => null,
                    'photo_status' => null,
                    'cv_status' => null,
                    'ijazah_smp_path' => null,
                    'ijazah_sma_path' => null,
                    'ijazah_d3_path' => null,
                    'ijazah_s1_path' => null,
                    'ijazah_s2_path' => null,
                    'ijazah_s3_path' => null,
                    'ijazah_smp_status' => null,
                    'ijazah_sma_status' => null,
                    'ijazah_d3_status' => null,
                    'ijazah_s1_status' => null,
                    'ijazah_s2_status' => null,
                    'ijazah_s3_status' => null
                ]);
            }

            DB::commit();

            event(new Registered($user));
            $user->sendEmailVerificationNotification();
            Auth::login($user);

            return redirect()->route('verification.notice');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
}
