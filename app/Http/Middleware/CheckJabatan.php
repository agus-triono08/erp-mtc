<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckJabatan
{
    /**
     * Menangani permintaan yang masuk.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles Format: 'divisi:jabatan' atau wildcard seperti 'divisi:*' atau '*:jabatan'
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // ... [validasi user sama seperti sebelumnya]

        $userDivisi = $user->Divisi->kode;
        $userJabatan = $user->Karyawan->jabatan;

        // Parameter khusus untuk exclude (diawali dengan tanda !)
        foreach ($roles as $role) {
            if (str_starts_with($role, '!')) {
                $excludedRole = substr($role, 1);
                [$excludedDivisi, $excludedJabatan] = explode(':', $excludedRole);
                
                if ($userDivisi === $excludedDivisi && $userJabatan === $excludedJabatan) {
                    Auth::logout();
                    return redirect()->route('login')->withErrors([
                        'message' => 'Akses ditolak untuk jabatan Anda'
                    ]);
                }
            }
        }

        return $next($request);
    }
}