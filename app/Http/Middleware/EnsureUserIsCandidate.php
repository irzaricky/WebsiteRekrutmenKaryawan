<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsCandidate
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
        // Check if the authenticated user has the Candidate role
        if (Auth::check() && Auth::user()->role === 'Candidate') {
            return $next($request);
        }

        // Redirect non-Candidate users
        return redirect()->route('home');
    }
}
