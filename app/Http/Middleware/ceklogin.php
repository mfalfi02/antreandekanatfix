<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekLogin
{
     public function handle(Request $request, Closure $next)
    {
        // kalau belum login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Login terlebih dahulu.');
        }

        return $next($request);
    }

}
