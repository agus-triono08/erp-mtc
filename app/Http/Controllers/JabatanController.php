<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatans = Jabatan::all();
        return response()->json($jabatans);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jabatan' => 'required|string|max:255',
        ]);

        $jabatan = new Jabatan();
        $jabatan->jabatan = $request->input('jabatan');
        $jabatan->save();

        return response()->json($jabatan, 201);
    }

    public function show($id)
    {
        $jabatan = Jabatan::find($id);
        if (!$jabatan) {
            return response()->json(['message' => 'Jabatan tidak ditemukan'], 404);
        }
        return response()->json($jabatan);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jabatan' => 'required|string|max:255',
        ]);

        $jabatan = Jabatan::find($id);
        if (!$jabatan) {
            return response()->json(['message' => 'Jabatan tidak ditemukan'], 404);
        }

        $jabatan->jabatan = $request->input('jabatan');
        $jabatan->save();

        return response()->json($jabatan);
    }

    public function destroy($id)
    {
        $jabatan = Jabatan::find($id);
        if (!$jabatan) {
            return response()->json(['message' => 'Jabatan tidak ditemukan'], 404);
        }
        $jabatan->delete();
        return response()->json(['message' => 'Jabatan berhasil dihapus']);
    }
}