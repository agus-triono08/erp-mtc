<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Pastikan view login Anda sesuai
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Redirect berdasarkan jabatan_id
            if ($user->jabatan_id == 1) {
                $redirectUrl = '/admin-mtc/dashboard';
            } elseif ($user->jabatan_id == 2) {
                $redirectUrl = '/manajer-mtc/dashboard';
            } else {
                $redirectUrl = '/user/dashboard';
            }

            // Jika request menginginkan response JSON (untuk AJAX)
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil',
                    'redirect' => $redirectUrl
                ]);
            }

            return redirect()->intended($redirectUrl);
        }

        // Jika request menginginkan response JSON
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        return back()->withErrors([
            'username' => 'Username atau password salah',
        ])->onlyInput('username');
    }

    protected function authenticated(Request $request, $user)
    {
        switch ($user->jabatan_id) {
            case 1:
                return redirect()->route('dashboard.adminmtc');
            case 2:
                return redirect()->route('dashboard.manajermtc');
            case 3:
                return redirect()->route('dashboard.user-mtc');
            case 4:
                return redirect()->route('dashboard.user');
            default:
                Auth::logout();
                return redirect()->route('login')->withErrors(['message' => 'Role tidak valid']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect('/login');
    }
}