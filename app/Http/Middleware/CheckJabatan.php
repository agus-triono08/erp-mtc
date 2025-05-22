<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckJabatan
{
    public function handle(Request $request, Closure $next, ...$jabatans)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        if (!in_array($user->jabatan_id, $jabatans)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['message' => 'Anda tidak memiliki akses ke halaman tersebut']);
        }

        return $next($request);
    }
}