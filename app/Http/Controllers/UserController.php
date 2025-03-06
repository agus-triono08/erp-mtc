<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
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
