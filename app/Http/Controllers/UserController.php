<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function get_image($file)
    {
        $path = 'users/' . $file;
        if (Storage::disk('ftp')->exists($path)) {
            $fileStream = Storage::disk('ftp')->readStream($path);
            return response()->stream(
                fn() => fpassthru($fileStream),
                200,
                [
                    'Content-Type' => Storage::disk('ftp')->mimeType($path),
                    'Content-Disposition' => 'inline; filename="' . $file . '"',
                ]
            );
        }
    }

    public function index()
    {
        $all = User::all();

        $byPIC = User::where('divisi_id', 16)
                    ->with('Karyawan')
                    // ->whereNotIn('jabatan_id', [1, 2])
                    ->get();

        return response()->json([
            'all' => $all,
            'byPIC' => $byPIC,
        ]);
    }

    public function store(Request $request)
    {
        $user = new User();
        $user->divisi_id = $request->divisi_id;
        $user->jabatan_id = $request->jabatan_id;
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->nama = $request->nama;
        $user->foto = $request->foto;
        $user->status = $request->status;
        $user->url = $request->url;
        $user->save();
        return response()->json($user);
    }

    public function show($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->divisi_id = $request->divisi_id;
        $user->jabatan_id = $request->jabatan_id;
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->nama = $request->nama;
        $user->foto = $request->foto;
        $user->status = $request->status;
        $user->url = $request->url;
        $user->save();
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(['message' => 'User  berhasil dihapus']);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['username', 'password']);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            Auth::login($user);
            if ($user->jabatan_id == 1) {
                return response()->json(['success' => true, 'message' => 'Login berhasil', 'redirect' => '/admin-mtc/dashboard']);
            } elseif ($user->jabatan_id == 2) {
                return response()->json(['success' => true, 'message' => 'Login berhasil', 'redirect' => '/manajer-mtc/dashboard']);
            } else {
                return response()->json(['success' => true, 'message' => 'Login berhasil', 'redirect' => '/user/dashboard']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Login gagal']);
        }
    }
}
