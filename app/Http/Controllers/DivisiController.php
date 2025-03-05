<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    public function index()
    {
        $divisis = Divisi::all();
        return response()->json($divisis);
    }

    public function store(Request $request)
    {
        $request->validate([
            'divisi' => 'required|string|max:255',
        ]);

        $divisi = new Divisi();
        $divisi->divisi = $request->input('divisi');
        $divisi->save();

        return response()->json($divisi, 201);
    }

    public function show($id)
    {
        $divisi = Divisi::find($id);
        if (!$divisi) {
            return response()->json(['message' => 'Divisi tidak ditemukan'], 404);
        }
        return response()->json($divisi);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'divisi' => 'required|string|max:255',
        ]);

        $divisi = Divisi::find($id);
        if (!$divisi) {
            return response()->json(['message' => 'Divisi tidak ditemukan'], 404);
        }

        $divisi->divisi = $request->input('divisi');
        $divisi->save();

        return response()->json($divisi);
    }

    public function destroy($id)
    {
        $divisi = Divisi::find($id);
        if (!$divisi) {
            return response()->json(['message' => 'Divisi tidak ditemukan'], 404);
        }
        $divisi->delete();
        return response()->json(['message' => 'Divisi berhasil dihapus']);
    }
}