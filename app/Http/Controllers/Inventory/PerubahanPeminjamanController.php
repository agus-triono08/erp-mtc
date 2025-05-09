<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Peminjaman;
use App\Models\Inventory\PerubahanPeminjaman;
use App\Models\Inventory\NoSeri;
use Illuminate\Support\Facades\DB;

class PerubahanPeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $noPinjam)
    {
        $request->validate([
            'tgl_kembali' => 'required|date',
            'keterangan_perubahan' => 'required|string',
            'status' => 'nullable|string',
        ]);

        // Mencari data peminjaman berdasarkan noPinjam
        $peminjaman = Peminjaman::where('no_peminjaman', $noPinjam)->first();
        if (!$peminjaman) {
            return response()->json(['message' => 'Peminjaman not found'], 404);
        }

        // Mendapatkan peminjaman_id dari objek peminjaman yang ditemukan
        $peminjaman_id = $peminjaman->id;

        $lastPerubahanPeminjaman = PerubahanPeminjaman::orderBy('id', 'desc')->first();
        $lastNumber = 0;

        if ($lastPerubahanPeminjaman && preg_match('/PB(\d{8})/', $lastPerubahanPeminjaman->no_perubahan, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $newNumber = $lastNumber + 1;
        $no_perubahan = 'PB' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);
        $status = $request->status ?? 'Belum Diproses';

        // Simpan perubahan peminjaman tanpa harus menginputkan peminjaman_id secara manual
        $perubahan = PerubahanPeminjaman::create([
            'peminjaman_id' => $peminjaman_id,
            'no_perubahan' => $no_perubahan,
            'tgl_kembali' => $request->tgl_kembali,
            'keterangan_perubahan' => $request->keterangan_perubahan,
            'status' => $status,
        ]);

        // Ambil no_seri yang terkait dengan peminjaman, dan update field status_perubahan
        $noSeriList = $peminjaman->noSeri; // dari relasi belongsToMany

        foreach ($noSeriList as $noSeri) {
            $noSeri->update([
                'status_perubahan' => $status, // atau bisa disesuaikan: $request->keterangan_perubahan, dll.
                'tgl_perubahan' => $request->tgl_kembali,
            ]);
        }

        return response()->json([
            'message' => 'Perubahan peminjaman berhasil disimpan',
            'data' => $perubahan
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getPerubahanNoPeminjaman($noPinjam)
    {
        $perubahan = PerubahanPeminjaman::whereHas('peminjaman', function ($query) use ($noPinjam) {
            $query->where('no_peminjaman', $noPinjam);
        })->with('peminjaman.tools', 'peminjaman.noSeri')->get();

        return response()->json($perubahan);
    }

    public function rejectPerubahan(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:perubahan_peminjaman,id',
            'reason' => 'required|string',
        ]);

        $perubahan = PerubahanPeminjaman::find($request->id);
        $perubahan->status = 'Ditolak';
        $perubahan->alasan_penolakan = $request->reason;
        $perubahan->save();

        // Ambil semua no_seri dari peminjaman terkait
        $peminjaman = $perubahan->peminjaman;

        foreach ($peminjaman->noSeri as $noSeri) {
            $noSeri->update([
                'status_perubahan' => 'Ditolak',
            ]);
        }

        return response()->json([
            'message' => 'Perubahan peminjaman ditolak dan status NoSeri diperbarui.',
            'data' => $perubahan
        ]);
    }

}
