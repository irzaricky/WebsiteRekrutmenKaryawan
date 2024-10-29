<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsHRD
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the authenticated user has the HRD role
        if (Auth::check() && Auth::user()->role === 'HRD') {
            return $next($request);
        }

        // Redirect Candidate users or unauthenticated users
        return redirect()->route('home'); // Adjust if needed
    }
}
