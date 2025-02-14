<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TechIssue;

class TechIssueController extends Controller
{
    public function store(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'email' => 'required|email',
            'nama' => 'required|string',
            'jabatan' => 'required|string',
            'bagian' => 'required|string',
            'jenis_sistem' => 'required|string',
            'jenis_permintaan' => 'required|string',
            'keterangan_permasalahan' => 'required|string',
            'lampiran' => 'nullable|file|mimes:jpg,png,pdf',
            'waktu_kebutuhan' => 'nullable|date',
        ]);

        // Menyimpan laporan
        $techIssue = new TechIssue();
        $techIssue->email = $validated['email'];
        $techIssue->nama = $validated['nama'];
        $techIssue->jabatan = $validated['jabatan'];
        $techIssue->bagian = $validated['bagian'];
        $techIssue->jenis_sistem = $validated['jenis_sistem'];
        $techIssue->jenis_permintaan = $validated['jenis_permintaan'];
        $techIssue->keterangan_permasalahan = $validated['keterangan_permasalahan'];

        // Menyimpan lampiran
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('lampiran');
            $techIssue->lampiran = $lampiranPath;
        }

        // Menyimpan waktu kebutuhan
        if ($validated['waktu_kebutuhan']) {
            $techIssue->waktu_kebutuhan = $validated['waktu_kebutuhan'];
        }

        $techIssue->save();

        return response()->json(['message' => 'Laporan berhasil disimpan'], 200);
    }
}
