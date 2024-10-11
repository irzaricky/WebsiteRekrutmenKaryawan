<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /** Show the registration page */
    public function register()
    {
        return view('auth.register');
    }

    /** Store a new user */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $register = new User();
            $register->name = $request->name;
            $register->email = $request->email;
            $register->join_date = Carbon::now()->toDayDateTimeString();
            $register->role_name = 'User Normal';
            $register->status = 'Active';
            $register->password = Hash::make($request->password);
            $register->save();

            Toastr()->success('Account created successfully :)', 'Success');
            return redirect('login');
        } catch (\Exception $e) {
            Log::error($e);
            Toastr()->error('Failed to create account :)', 'Error');
            return redirect()->back();
        }
    }
}