<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\NoSeri;
use App\Models\Inventory\Tools;
use App\Models\Layout;
use Carbon\Carbon;
use App\Models\Inventory\Perawatan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoSeriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $noseri = NoSeri::with('tools', 'layout')                        
                        ->orderBy('updated_at', 'desc') // urutkan dari yang terbaru
                        ->get();

        $noseri->transform(function ($item) {
            $item->updated_at = Carbon::parse($item->updated_at)->setTimezone('Asia/Jakarta');
            return $item;
        });

        return response()->json($noseri);
    }

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $kodeAlat)
    {
        $request->validate([
            'layout_id' => 'required|exists:layouts,id',
            'no_seri_default' => 'nullable|string',
            'harga' => 'required|numeric|min:1',
            'kondisi' => 'required|string',
            'stok_awal' => 'required|integer|min:1',
            'jadwal_perawatan' => 'nullable|numeric',
            'users_id' => 'nullable|exists:users,id',
            'waktu_perawatan' => 'nullable|integer|min:0',
            'jumlah_orang_perawatan' => 'nullable|integer|min:0',
        ]);

        $tool = Tools::where('kode', $kodeAlat)->first();
        if (!$tool) {
            return response()->json(['message' => 'Tool not found'], 404);
        }

        $layout = Layout::findOrFail($request->layout_id);
        $stok = $request->stok_awal;
        $harga_per_unit = $request->harga;
        $noSeriList = [];

        $waktuPerNoSeri = (int) $request->waktu_perawatan;
        $jumlahOrang = max((int) $request->jumlah_orang_perawatan, 1);
        $jadwalPerawatan = (int) $request->jadwal_perawatan;

        $startTime = Carbon::createFromTime(8, 0);

        for ($i = 0; $i < $stok; $i++) {
            $prefix = strtoupper(substr($tool->nama, 0, 2));
            $random8 = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $no_seri = $prefix . $random8;

            $noSeri = NoSeri::create([
                'tools_id' => $tool->id,
                'layout_id' => $layout->id,
                'no_seri' => $no_seri,
                'no_seri_default' => $request->no_seri_default,
                'harga' => $harga_per_unit,
                'tanggal_masuk' => now(),
                'tanggal_kondisi' => null,
                'kondisi' => $request->kondisi,
            ]);

            $noSeriList[] = $noSeri;

            if ($jadwalPerawatan > 0 && $waktuPerNoSeri > 0 && $jumlahOrang > 0) {
                $waktuPerNoseriEfisien = ceil($waktuPerNoSeri / $jumlahOrang);

                [$waktuMulai, $waktuSelesai] = $this->nextWorkTime($startTime, $waktuPerNoseriEfisien);

                $noPerawatan = 'JP' . str_pad($i + 1, 8, '0', STR_PAD_LEFT);
                Perawatan::create([
                    'no_seri_id' => $noSeri->id,
                    'users_id' => $request->users_id ?? null,
                    'no_perawatan' => $noPerawatan,
                    'tgl_perawatan' => $waktuMulai,
                    'waktu_perawatan' => gmdate('H:i:s', $waktuPerNoseriEfisien * 60),
                    'kondisi' => $request->kondisi,
                ]);

                $startTime = $waktuSelesai;
            }
        }

        $tool->stok_awal += $stok;
        $tool->stok_akhir += $stok;
        $tool->harga_total += $stok * $harga_per_unit;
        $tool->save();

        return response()->json([
            'message' => 'No seri dan jadwal perawatan berhasil disimpan',
            'stok_awal_baru' => $tool->stok_awal,
            'stok_akhir_baru' => $tool->stok_akhir,
            'harga_total_baru' => $tool->harga_total,
            'no_seri' => $noSeriList
        ], 201);
    }

    // Fungsi pembantu untuk menghitung waktu kerja berikutnya
    private function nextWorkTime(Carbon $start, int $durasiMenit)
    {
        $workStart = Carbon::createFromTime(8, 0);
        $lunchStart = Carbon::createFromTime(12, 0);
        $lunchEnd = Carbon::createFromTime(13, 0);
        $workEnd = Carbon::createFromTime(17, 0);

        $current = $start->copy();

        while (true) {
            $end = $current->copy()->addMinutes($durasiMenit);

            if ($current->between($workStart, $lunchStart) && $end <= $lunchStart) {
                return [$current, $end];
            } elseif ($current->between($lunchEnd, $workEnd) && $end <= $workEnd) {
                return [$current, $end];
            } elseif ($current < $workStart) {
                $current = $workStart->copy();
            } elseif ($current >= $lunchStart && $current < $lunchEnd) {
                $current = $lunchEnd->copy();
            } elseif ($current >= $workEnd) {
                $current = $workStart->copy()->addDay();
            } else {
                $current = $this->nextWorkTime($current->addMinutes(1), $durasiMenit)[0];
            }
        }
    }


    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'tools_id' => 'required|exists:tools,id',
    //         'layout_id' => 'required|exists:layouts,id',
    //         'no_seri_default' => 'nullable|string',
    //         'harga' => 'required|numeric|min:1',
    //         'kondisi' => 'required|string',
    //         'stok_awal' => 'required|integer|min:1',
    //         'jadwal_perawatan' => 'nullable|numeric',
    //         'users_id' => 'nullable|exists:users,id', // untuk PIC perawatan jika ada
    //     ]);

    //     $tool = Tools::findOrFail($request->tools_id);
    //     $layout = Layout::findOrFail($request->layout_id);
    //     $stok = $request->stok_awal;
    //     $harga_per_unit = $request->harga;

    //     $noSeriList = [];

    //     for ($i = 0; $i < $stok; $i++) {
    //         // Buat no_seri
    //         $prefix = strtoupper(substr($tool->nama, 0, 2));
    //         $random8 = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    //         $no_seri = $prefix . $random8;

    //         $noSeri = NoSeri::create([
    //             'tools_id' => $tool->id,
    //             'layout_id' => $layout->id,
    //             'no_seri' => $no_seri,
    //             'no_seri_default' => $request->no_seri_default,
    //             'harga' => $harga_per_unit,
    //             'tanggal_masuk' => now(),
    //             'tanggal_kondisi' => null,
    //             'kondisi' => $request->kondisi,
    //         ]);

    //         // Buat jadwal perawatan (default 12x, tiap interval bulan)
    //         $interval = (int) $request->jadwal_perawatan ?? 1;
    //         $jumlahPerawatan = 12;

    //         for ($j = 0; $j < $jumlahPerawatan; $j++) {
    //             $noPerawatan = 'JP' . str_pad($j + 1, 8, '0', STR_PAD_LEFT);
    //             Perawatan::create([
    //                 'no_seri_id' => $noSeri->id,
    //                 'users_id' => $request->users_id ?? null,
    //                 'no_perawatan' => $noPerawatan,
    //                 'tgl_perawatan' => now()->addMonths($j * $interval),
    //                 'kondisi' => $request->kondisi,
    //             ]);
    //         }

    //         $noSeriList[] = $noSeri;
    //     }

    //     // Update stok dan total harga alat
    //     $tool->stok_awal += $stok;
    //     $tool->stok_akhir += $stok;
    //     $tool->harga_total += $stok * $harga_per_unit;
    //     $tool->save();

    //     return response()->json([
    //         'message' => 'No seri dan jadwal perawatan berhasil disimpan',
    //         'stok_awal_baru' => $tool->stok_awal,
    //         'stok_akhir_baru' => $tool->stok_akhir,
    //         'harga_total_baru' => $tool->harga_total,
    //         'no_seri' => $noSeriList
    //     ], 201);
    // }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $noseri = NoSeri::with('layout', 'tools')->find($id);
        if ($noseri) {
            return response()->json($noseri);
        } else {
            return response()->json(['message' => 'NoSeri not found'], 404);
        }
    }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $tools_id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($tools_id)
    // {
    //     $noseri = NoSeri::where('tools_id', $tools_id)->get();
    //     if ($noseri->isEmpty()) {
    //         return response()->json(['message' => 'Data tidak ditemukan'], 404);
    //     }
    //     return response()->json($noseri);
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     $noseri = NoSeri::findorFail($id);

    //     $request->validate([
    //         'no_seri' => 'nullable|string',
    //         'no_seri_default' => 'nullable|string',
    //         'tanggal_masuk' => 'nullable|date',
    //         'harga' => 'nullable|numeric',
    //         'kondisi' => 'nullable|string',
    //         'layout_id' => 'nullable|exists:layouts,id',
    //     ]);

    //     $data = $request->only(['no_seri', 'no_seri_default', 'tanggal_masuk', 'harga', 'kondisi', 'layout_id']);

    //     $noseri->update($data);

    //     return response()->json(['message' => 'Data berhasil diupdate'], 200);
    // }

    public function update(Request $request, $id)
    {
        $noseri = NoSeri::find($id);

        if (!$noseri) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Simpan harga sebelumnya sebelum diperbarui
        $hargaSebelumnya = $noseri->harga ?? 0;

        $validatedData = $request->validate([
            'no_seri' => 'nullable|string',
            'no_seri_default' => 'nullable|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'harga' => 'nullable|numeric',
            'kondisi' => 'nullable|string',
            'layout_id' => 'nullable|exists:layouts,id',
        ]);

        $noseri->fill($validatedData);
        $noseri->save();

        // Update harga_total pada tabel tools
        $tool = Tools::findOrFail($noseri->tools_id);

        // Jika harga baru tidak null, hitung selisih
        $hargaBaru = $noseri->harga ?? 0;
        $selisihHarga = $hargaBaru - $hargaSebelumnya;

        $tool->harga_total += $selisihHarga;
        $tool->save();

        return response()->json([
            'message' => 'Data berhasil diperbarui',
            'data' => $noseri
        ]);
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

    public function getNoSeri($kodeAlat)
    {
        $tools = Tools::where('kode', $kodeAlat)->first();

        if (!$tools) {
            return response()->json(['message' => 'Tool not found'], 404);
        }

        // Tambahkan relasi layout
        $noseri = NoSeri::with('layout', 'tools')->where('tools_id', $tools->id)->get();

        if ($noseri->isEmpty()) {
            return response()->json(['message' => 'NoSeri not found'], 404);
        }

        return response()->json($noseri);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:no_seri,id',
            'reason' => 'required|string|max:255',
        ]);

        // Update NoSeri
        $noseri = NoSeri::findOrFail($request->id);
        $noseri->update([
            'kondisi_after' => 'Ditolak',
            'reject_reason' => $request->reason,
        ]);

        // Update semua Peminjaman terkait NoSeri ini
        foreach ($noseri->peminjaman as $peminjaman) {
            $peminjaman->update([
                'status' => 'Ditolak',
                'status_kondisi' => 'Ditolak',
                'alasan_penolakan' => $request->reason,
            ]);
        }

        return response()->json(['message' => 'No Seri dan Peminjaman berhasil ditolak.']);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:no_seri,id',
            'status' => 'required|string|max:255',
            'reason' => 'nullable|string|max:255',
        ]);

        $noseri = NoSeri::findOrFail($request->id);
        $noseri->update([
            'kondisi_after' => $request->status,
            'reject_reason' => $request->reason,
        ]);

        foreach ($noseri->peminjaman as $peminjaman) {
            $peminjaman->update([
                'status' => $request->status,
                'status_kondisi' => $request->status,
                'alasan_penolakan' => $request->reason,
            ]);
        }

        return response()->json(['message' => 'Status No Seri dan Peminjaman berhasil diperbarui.']);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:no_seri,id',
            'status' => 'required|string|max:255',
        ]);

        foreach ($request->ids as $id) {
            $noseri = NoSeri::findOrFail($id);
            $noseri->update([
                'kondisi_after' => $request->status,
                'reject_reason' => null, // kosongkan alasan penolakan saat update status biasa
            ]);

            foreach ($noseri->peminjaman as $peminjaman) {
                $peminjaman->update([
                    'status' => $request->status,
                    'status_kondisi' => $request->status,
                    'alasan_penolakan' => null,
                ]);
            }
        }

        return response()->json(['message' => 'Semua No Seri dan Peminjaman berhasil diperbarui.']);
    }

    public function rejectPermintaan(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:no_seri,id',
            'reason' => 'required|string|max:255',
        ]);

        // Update NoSeri
        $noseri = NoSeri::findOrFail($request->id);
        $noseri->update([
            'kondisi_after' => 'Ditolak',
            'reject_reason' => $request->reason,
        ]);

        // Update semua Permintaan terkait NoSeri ini
        foreach ($noseri->permintaan as $permintaan) {
            $permintaan->update([
                'status' => 'Ditolak',
                'status_kondisi' => 'Ditolak',
                'alasan_penolakan' => $request->reason,
            ]);
        }

        return response()->json(['message' => 'No Seri dan Permintaan berhasil ditolak.']);
    }

    public function updateStatusPermintaan(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:no_seri,id',
            'status' => 'required|string|max:255',
            'reason' => 'nullable|string|max:255',
        ]);

        $noseri = NoSeri::findOrFail($request->id);
        $noseri->update([
            'kondisi_after' => $request->status,
            'reject_reason' => $request->reason,
        ]);

        foreach ($noseri->permintaan as $permintaan) {
            $permintaan->update([
                'status' => $request->status,
                'status_kondisi' => $request->status,
                'alasan_penolakan' => $request->reason,
            ]);
        }

        return response()->json(['message' => 'Status No Seri dan Permintaan berhasil diperbarui.']);
    }

    public function bulkUpdateStatusPermintaan(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:no_seri,id',
            'status' => 'required|string|max:255',
        ]);

        foreach ($request->ids as $id) {
            $noseri = NoSeri::findOrFail($id);
            $noseri->update([
                'kondisi_after' => $request->status,
                'reject_reason' => null, // kosongkan alasan penolakan saat update status biasa
            ]);

            foreach ($noseri->permintaan as $permintaan) {
                $permintaan->update([
                    'status' => $request->status,
                    'status_kondisi' => $request->status,
                    'alasan_penolakan' => null,
                ]);
            }
        }

        return response()->json(['message' => 'Semua No Seri dan Permintaan berhasil diperbarui.']);
    }
}
