<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->status !== 'active') {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}