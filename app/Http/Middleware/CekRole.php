<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekRole
{
    /**
     * Menahan akses ke route tertentu berdasarkan role user.
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Login terlebih dahulu.');
        }

        $user = Auth::user();

        if (in_array($user->role, $roles, true)) {
            return $next($request);
        }

        if ($user->role === 'admin') {
            return redirect()->route('adm');
        }

        if ($user->role === 'pejabat') {
            return redirect()->route('dsn');
        }

        return redirect()->route('mhs');
    }
}
