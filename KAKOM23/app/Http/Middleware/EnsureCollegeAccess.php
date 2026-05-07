<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCollegeAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->session()->has('college_id')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
