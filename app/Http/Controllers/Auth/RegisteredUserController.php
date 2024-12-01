<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TestResult;
use App\Models\TestsList;
use App\Models\CandidateDetail;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use App\Rules\ValidNIK;

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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active'
        ]);

        // Create initial test scores if user is a Candidate
        if ($request->role === 'Candidate') {
            $tests = TestsList::all();

            foreach ($tests as $test) {
                TestResult::create([
                    'user_id' => $user->id,
                    'test_id' => $test->id,
                    'score' => 0
                ]);
            }

            // Create candidate details record
            CandidateDetail::create([
                'user_id' => $user->id,
                'nik' => $request->nik,
                'full_name' => $request->name,
            ]);
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
