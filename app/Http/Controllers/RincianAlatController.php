<?php

namespace App\Http\Controllers;

use App\Models\RincianAlat;
use Illuminate\Http\Request;

class RincianAlatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10);

        $kodeAlat = $request->query('kode_alat', '');
        $query = RincianAlat::query();

        if ($kodeAlat) {
            $query->where('kode_alat', $kodeAlat);
        }

        $data = $query->where('brand', 'like', "%$search%")
            ->orWhere('kode_alat', 'like', "%$search%")
            ->paginate($perPage);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_alat' => 'required|string|exists:alats,kode_alat',
            'brand' => 'required|string',
            'kode_rincian_alat' => 'required|string|unique:rincian_alats',
            'jumlah' => 'required|integer|min:0',
            'kondisi' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
        ]);

        $rincian = new RincianAlat($validated);

        if ($request->hasFile('gambar')) {
            $rincian->gambar = $request->file('gambar')->store('uploads', 'public');
        }

        $rincian->save();

        return response()->json(['message' => 'Data berhasil disimpan!', 'data' => $rincian], 201);
    }

    public function destroy($id)
    {
        $rincian = RincianAlat::findOrFail($id);
        $rincian->delete();

        return response()->json(['message' => 'Data berhasil dihapus!'], 200);
    }
}
