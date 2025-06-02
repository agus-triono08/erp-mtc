<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\NoSeri;
use App\Models\Inventory\NoSeriLog;
use App\Models\Inventory\Tools;
use App\Models\Inventory\Error;
use App\Models\Inventory\Rusak;
use App\Models\Inventory\Hilang;
use App\Models\Inventory\PermintaanLog;
use App\Models\Inventory\PeminjamanLog;
use App\Models\Inventory\PerubahanPeminjaman;
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
        $noseri = NoSeri::with('tools', 'layout',)                        
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
            'status' => 'required|string|max:255',
            'reason' => 'required|string|max:255',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        }

        // Tambahan validasi manual jika status adalah "Ditolak"
        if ($request->status === 'Ditolak' && empty($request->reason)) {
            return response()->json(['message' => 'Alasan penolakan harus diisi.'], 422);
        }

        // Update NoSeri
        $noseri = NoSeri::findOrFail($request->id);
        $noseri->update([
            'kondisi_after' => $request->status,
            'reject_reason' => $request->reason,
        ]);

        // Update semua Peminjaman terkait NoSeri ini
        foreach ($noseri->peminjaman as $peminjaman) {
            if ($peminjaman) {
                $oldStatus = $peminjaman->status;
                $newStatus = $request->status;
    
                // Simpan log jika status berubah
                if ($oldStatus !== $newStatus) {
                    PeminjamanLog::create([
                        'peminjaman_id' => $peminjaman->id,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'changed_at'  => now(),
                        // 'changed_by'  => auth()->id() ?? 1,
                        'changed_by' => $user->id,
                    ]);
                }
    
                // Update status peminjaman
                $peminjaman->update([
                    'status' => $newStatus,
                    'status_kondisi' => $newStatus,
                    'alasan_penolakan' => $request->reason,
                ]);
            }
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

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        }

        $noseri = NoSeri::findOrFail($request->id);
        $noseri->update([
            'kondisi_after' => $request->status,
            'reject_reason' => $request->reason,
        ]);

        foreach ($noseri->peminjaman as $peminjaman) {
            // Simpan log HANYA jika status berubah menjadi "Menunggu Diambil"
            if ($peminjaman) {
                $oldStatus = $peminjaman->status;
                $newStatus = $request->status;
        
                if ($oldStatus !== $newStatus && $newStatus === 'Menunggu Diambil') {
                    // Simpan log perubahan status
                    PeminjamanLog::create([
                        'peminjaman_id' => $peminjaman->id,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'changed_at'  => now(),
                        // 'changed_by'  => auth()->id() ?? 1, // pastikan user sudah login
                        'changed_by' => $user->id,
                    ]);
                }
        
                // Update status peminjaman
                $peminjaman->update([
                    'status' => $newStatus,
                    'status_kondisi' => $newStatus,
                    'alasan_penolakan' => $request->reason,
                ]);
            }
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

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        }

        foreach ($request->ids as $id) {
            $noseri = NoSeri::findOrFail($id);
            $noseri->update([
                'kondisi_after' => $request->status,
                'reject_reason' => null, // kosongkan alasan penolakan saat update status biasa
            ]);

            foreach ($noseri->peminjaman as $peminjaman) {
                // Simpan log HANYA jika status berubah menjadi "Menunggu Diambil"
                if ($peminjaman) {
                    $oldStatus = $peminjaman->status;
                    $newStatus = $request->status;
            
                    if ($oldStatus !== $newStatus && $newStatus === 'Menunggu Diambil') {
                        // Simpan log perubahan status
                        PeminjamanLog::create([
                            'peminjaman_id' => $peminjaman->id,
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus,
                            'changed_at'  => now(),
                            // 'changed_by'  => auth()->id() ?? 1, // pastikan user sudah login
                            'changed_by' => $user->id,
                        ]);
                    }
            
                    // Update status peminjaman
                    $peminjaman->update([
                        'status' => $newStatus,
                        'status_kondisi' => $newStatus,
                        'alasan_penolakan' => null,
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Semua No Seri dan Peminjaman berhasil diperbarui.']);
    }

    public function rejectPermintaan(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:no_seri,id',
            'status' => 'required|string|max:255',
            'reason' => 'nullable|string|max:255',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        }
    
        // Tambahan validasi manual jika status adalah "Ditolak"
        if ($request->status === 'Ditolak' && empty($request->reason)) {
            return response()->json(['message' => 'Alasan penolakan harus diisi.'], 422);
        }
    
        $noseri = NoSeri::findOrFail($request->id);
        $noseri->update([
            'kondisi_after' => $request->status,
            'reject_reason' => $request->reason,
        ]);
    
        foreach ($noseri->permintaan as $permintaan) {
            if ($permintaan) {
                $oldStatus = $permintaan->status;
                $newStatus = $request->status;
    
                // Simpan log jika status berubah
                if ($oldStatus !== $newStatus) {
                    PermintaanLog::create([
                        'permintaan_id' => $permintaan->id,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'changed_at'  => now(),
                        // 'changed_by'  => auth()->id() ?? 1,
                        'changed_by' => $user->id,
                    ]);
                }
    
                // Update status permintaan
                $permintaan->update([
                    'status' => $newStatus,
                    'status_kondisi' => $newStatus,
                    'alasan_penolakan' => $request->reason,
                ]);
            }
        }
    
        return response()->json(['message' => 'Status No Seri dan Permintaan berhasil diperbarui.']);
    }

    public function updateStatusPermintaan(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:no_seri,id',
            'status' => 'required|string|max:255',
            'reason' => 'nullable|string|max:255',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        }

        $noseri = NoSeri::findOrFail($request->id);
        $noseri->update([
            'kondisi_after' => $request->status,
            'reject_reason' => $request->reason,
        ]);

        foreach ($noseri->permintaan as $permintaan) {
            // Simpan log HANYA jika status berubah menjadi "Menunggu Diambil"
            if ($permintaan) {
                $oldStatus = $permintaan->status;
                $newStatus = $request->status;
        
                if ($oldStatus !== $newStatus && $newStatus === 'Menunggu Diambil') {
                    // Simpan log perubahan status
                    PermintaanLog::create([
                        'permintaan_id' => $permintaan->id,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'changed_at'  => now(),
                        // 'changed_by'  => auth()->id() ?? 1, // pastikan user sudah login
                        'changed_by' => $user->id,
                    ]);
                }
        
                // Update status permintaan
                $permintaan->update([
                    'status' => $newStatus,
                    'status_kondisi' => $newStatus,
                    'alasan_penolakan' => $request->reason,
                ]);
            }
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

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        }

        foreach ($request->ids as $id) {
            $noseri = NoSeri::findOrFail($id);
            $noseri->update([
                'kondisi_after' => $request->status,
                'reject_reason' => null, // kosongkan alasan penolakan saat update status biasa
            ]);

            foreach ($noseri->permintaan as $permintaan) {
                // Simpan log HANYA jika status berubah menjadi "Menunggu Diambil"
                if ($permintaan) {
                    $oldStatus = $permintaan->status;
                    $newStatus = $request->status;
            
                    if ($oldStatus !== $newStatus && $newStatus === 'Menunggu Diambil') {
                        // Simpan log perubahan status
                        PermintaanLog::create([
                            'permintaan_id' => $permintaan->id,
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus,
                            'changed_at'  => now(),
                            // 'changed_by'  => auth()->id() ?? 1, // pastikan user sudah login
                            'changed_by' => $user->id,
                        ]);
                    }
            
                    // Update status permintaan
                    $permintaan->update([
                        'status' => $newStatus,
                        'status_kondisi' => $newStatus,
                        'alasan_penolakan' => null,
                    ]);
                }
            }   
        }

        return response()->json(['message' => 'Semua No Seri dan Permintaan berhasil diperbarui.']);
    }

    public function editLog(Request $request, $id)
    {
        $noseri = NoSeri::findOrFail($id);
        $oldKondisi = $noseri->kondisi;
        $newKondisi = $request->input('kondisi');

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        }

        if ($oldKondisi !== $newKondisi) {
            // update Kondisi
            $noseri->update(['kondisi' => $newKondisi]);

            // Simpan Log Perubahan
            NoSeriLog::create([
                'no_seri_id' => $noseri->id,
                'old_kondisi' => $oldKondisi,
                'new_kondisi' => $newKondisi,
                'changed_at' => now(),
                // 'changed_by' => auth()->user()->id, // tambahkan nilai untuk kolom changed_by
                // 'changed_by' => auth()->id() ?? 1, // ID default (misalnya admin)
                'changed_by' => $user->id,
            ]);
        }

        return response ()->json(['message' => 'Kondisi Diperbaharui']);
    }

    public function updateStatusPermintaanUser(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:no_seri,id',
            'status' => 'required|string|max:255',
            'reason' => 'nullable|string|max:255',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        }

        $noseri = NoSeri::findOrFail($request->id);
        $noseri->update([
            'kondisi_after' => $request->status,
            'reject_reason' => $request->reason,
        ]);

        foreach ($noseri->permintaan as $permintaan) {
            // Simpan log HANYA jika status berubah menjadi "Menunggu Diambil"
            if ($permintaan) {
                $oldStatus = $permintaan->status;
                $newStatus = $request->status;
        
                if ($oldStatus !== $newStatus && $newStatus === 'Digunakan') {
                    // Simpan log perubahan status
                    PermintaanLog::create([
                        'permintaan_id' => $permintaan->id,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'changed_at'  => now(),
                        // 'changed_by'  => auth()->id() ?? 4, // pastikan user sudah login
                        'changed_by'  => $user->id,
                    ]);

                    // Kurangi stok akhir pada tabel tools
                    $tool = Tools::where('id', $noseri->tools_id)->first();
                    if ($tool) {
                        $tool->update([
                            'stok_akhir' => $tool->stok_akhir - 1,
                        ]);
                    }
                }
        
                // Update status permintaan
                $permintaan->update([
                    'status' => $newStatus,
                    'status_kondisi' => $newStatus,
                    'alasan_penolakan' => $request->reason,
                ]);
            }
        }      

        return response()->json(['message' => 'Status No Seri dan Permintaan berhasil diperbarui.']);
    }

    public function bulkUpdateStatusPermintaanUser(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:no_seri,id',
            'status' => 'required|string|max:255',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        };

        foreach ($request->ids as $id) {
            $noseri = NoSeri::findOrFail($id);
            $noseri->update([
                'kondisi_after' => $request->status,
                'reject_reason' => null, // kosongkan alasan penolakan saat update status biasa
            ]);

            foreach ($noseri->permintaan as $permintaan) {
                // Simpan log HANYA jika status berubah menjadi "Menunggu Diambil"
                if ($permintaan) {
                    $oldStatus = $permintaan->status;
                    $newStatus = $request->status;
            
                    if ($oldStatus !== $newStatus && $newStatus === 'Digunakan') {
                        // Simpan log perubahan status
                        PermintaanLog::create([
                            'permintaan_id' => $permintaan->id,
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus,
                            'changed_at'  => now(),
                            // 'changed_by'  => auth()->id() ?? 1, // pastikan user sudah login
                            'changed_by' => $user->id,
                        ]);
                    }
            
                    // Update status permintaan
                    $permintaan->update([
                        'status' => $newStatus,
                        'status_kondisi' => $newStatus,
                        'alasan_penolakan' => null,
                    ]);
                }
            }   
        }

        return response()->json(['message' => 'Semua No Seri dan Permintaan berhasil diperbarui.']);
    }

    public function updateStatusPeminjamanUser (Request $request)
    {
        $request->validate([
            'id' => 'required|exists:no_seri,id',
            'status' => 'required|string|max:255',
            'reason' => 'nullable|string|max:255',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        };

        $noseri = NoSeri::findOrFail($request->id);
        $noseri->update([
            'kondisi_after' => $request->status,
            'reject_reason' => $request->reason,
        ]);

        foreach ($noseri->peminjaman as $peminjaman) {
            // Simpan log HANYA jika status berubah menjadi "Menunggu Diambil"
            if ($peminjaman) {
                $oldStatus = $peminjaman->status;
                $newStatus = $request->status;
            
                if ($oldStatus !== $newStatus && $newStatus === 'Dipinjam') {
                    // Simpan log perubahan status
                    PeminjamanLog::create([
                        'peminjaman_id' => $peminjaman->id,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'changed_at'  => now(),
                        // 'changed_by'  => auth()->id() ?? 4, // pastikan user sudah login
                        'changed_by' => $user->id,
                    ]);

                    // Kurangi stok akhir pada tabel tools
                    $tool = Tools::where('id', $noseri->tools_id)->first();
                    if ($tool) {
                        $tool->update([
                            'stok_akhir' => $tool->stok_akhir - 1,
                        ]);
                    }
                }
            
                // Update status peminjaman
                $peminjaman->update([
                    'status' => $newStatus,
                    'status_kondisi' => $newStatus,
                    'alasan_penolakan' => $request->reason,
                ]);
            }
        }  
        return response()->json(['message' => 'Status No Seri dan Peminjaman berhasil diperbarui.']);
    }

    public function bulkUpdateStatusPeminjamanUser(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:no_seri,id',
            'status' => 'required|string|max:255',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        };

        foreach ($request->ids as $id) {
            $noseri = NoSeri::findOrFail($id);
            $noseri->update([
                'kondisi_after' => $request->status,
                'reject_reason' => null, // kosongkan alasan penolakan saat update status biasa
            ]);

            foreach ($noseri->peminjaman as $peminjaman) {
                // Simpan log HANYA jika status berubah menjadi "Menunggu Diambil"
                if ($peminjaman) {
                    $oldStatus = $peminjaman->status;
                    $newStatus = $request->status;
                    $total = $peminjaman->total;
            
                    if ($oldStatus !== $newStatus && $newStatus === 'Dipinjam') {
                        // Simpan log perubahan status
                        PeminjamanLog::create([
                            'peminjaman_id' => $peminjaman->id,
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus,
                            'changed_at'  => now(),
                            // 'changed_by'  => auth()->id() ?? 1, // pastikan user sudah login
                            'changed_by' => $user->id,
                        ]);

                        // Kurangi stok akhir pada tabel tools
                        $tool = Tools::where('id', $noseri->tools_id)->first();
                        if ($tool) {
                            $tool->update([
                                'stok_akhir' => $tool->stok_akhir - $total,
                            ]);
                        }
                    }
            
                    // Update status peminjaman
                    $peminjaman->update([
                        'status' => $newStatus,
                        'status_kondisi' => $newStatus,
                        'alasan_penolakan' => null,
                    ]);
                }
            }   
        }

        return response()->json(['message' => 'Semua No Seri dan Peminjaman berhasil diperbarui.']);
    }

    public function updateStatusPerubahanPeminjaman(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:no_seri,id',
            'status' => 'required|string|max:255',
            'reason' => 'nullable|string|max:255',
        ]);

        $noseri = NoSeri::findOrFail($request->id);
        $tgl_perubahan = $noseri->tgl_perubahan;

        // Update NoSeri
        $noseri->update([
            'status_perubahan' => $request->status,
            'alasan_penolakan_perubahan' => $request->reason,
        ]);

        // Ambil peminjaman terbaru dari NoSeri
        $peminjaman = $noseri->peminjaman()->latest('peminjaman.created_at')->first();

        if ($peminjaman) {
            // Update tgl_kembali jika tgl_perubahan tersedia
            if ($request->status === 'Disetujui' && $tgl_perubahan) {
                $peminjaman->update([
                    'tgl_kembali' => $tgl_perubahan,
                ]);
            }

            // Ambil perubahan peminjaman terbaru terkait peminjaman ini
            $perubahan = $peminjaman->perubahan()->latest('created_at')->first();

            if ($perubahan) {
                // Update status perubahan peminjaman
                $perubahan->update([
                    'status' => $request->status,
                ]);

                // Cek apakah perubahan peminjaman berhasil diupdate
                if ($perubahan->save()) {
                    // Perubahan peminjaman berhasil diupdate
                    return response()->json(['message' => 'Status perubahan peminjaman berhasil diperbarui.']);
                } else {
                    // Perubahan peminjaman gagal diupdate
                    return response()->json(['message' => 'Gagal memperbarui status perubahan peminjaman.'], 500);
                }
            } else {
                // Tidak ada perubahan peminjaman terbaru
                return response()->json(['message' => 'Tidak ada perubahan peminjaman terbaru.'], 404);
            }
        }

        return response()->json(['message' => 'Status No Seri, Peminjaman, dan Perubahan Peminjaman berhasil diperbarui.']);
    }

    public function bulkUpdateStatusPerubahanPeminjaman(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:no_seri,id',
            'status' => 'required|string|max:255',
        ]);

        foreach ($request->ids as $id) {
            $noseri = NoSeri::findOrFail($id);
            $tgl_perubahan = $noseri->tgl_perubahan;
            $noseri->update([
                'status_perubahan' => $request->status,
                'alasan_penolakan_perubahan' => null,
            ]);

            // Ambil peminjaman terbaru dari NoSeri
            $peminjaman = $noseri->peminjaman()->latest('peminjaman.created_at')->first();

            if ($peminjaman) {
                // Update tgl_kembali jika tgl_perubahan tersedia
                if ($request->status === 'Disetujui' && $tgl_perubahan) {
                    $peminjaman->update([
                        'tgl_kembali' => $tgl_perubahan,
                    ]);
                }

                // Ambil perubahan peminjaman terbaru terkait peminjaman ini
                $perubahan = $peminjaman->perubahan()->latest('created_at')->first();

                if ($perubahan) {
                    // Update status perubahan peminjaman
                    $perubahan->update([
                        'status' => $request->status,
                        'alasan_penolakan' => null,
                    ]);

                    // Cek apakah perubahan peminjaman berhasil diupdate
                    if ($perubahan->save()) {
                        // Perubahan peminjaman berhasil diupdate
                        return response()->json(['message' => 'Status perubahan peminjaman berhasil diperbarui.']);
                    } else {
                        // Perubahan peminjaman gagal diupdate
                        return response()->json(['message' => 'Gagal memperbarui status perubahan peminjaman.'], 500);
                    }
                } else {
                    // Tidak ada perubahan peminjaman terbaru
                    return response()->json(['message' => 'Tidak ada perubahan peminjaman terbaru.'], 404);
                }
            }
        }
    }

    public function rejectPerubahanPeminjaman(Request $request) 
    {
        $request->validate([
            'id' => 'required|exists:no_seri,id',
            'status' => 'required|string|max:255',
            'reason' => 'required|string|max:255',
        ]);

        $noseri = NoSeri::findOrFail($request->id);

        // Update NoSeri
        $noseri->update([
            'status_perubahan' => $request->status,
            'alasan_penolakan_perubahan' => $request->reason,
        ]);

        // Ambil peminjaman terbaru dari NoSeri
        $peminjaman = $noseri->peminjaman()->first();

        // Ambil perubahan peminjaman terbaru terkait peminjaman ini
        $perubahan = $peminjaman->perubahan()->first();

        if ($perubahan) {
            // Update status perubahan peminjaman
            $perubahan->update([
                'status' => $request->status,
                'alasan_penolakan' => $request->reason,
            ]);

            return response()->json(['message' => 'Status perubahan peminjaman berhasil diperbarui.']);
        } else {
            return response()->json(['message' => 'Tidak ada perubahan peminjaman terbaru.'], 404);
        }
    }

    public function cekKondisi(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:no_seri,id',
            'tgl_pengecekan' => 'required|date',
            'kondisi' => 'required|string|max:255',
            'deskripsi_cek' => 'required|string|max:255',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        };

        $noseri = NoSeri::findOrFail($request->id);
        $oldKondisi = $noseri->kondisi;
        $newKondisi = $request->kondisi;

        // Update NoSeri
        $noseri->update([            
            'tgl_pengecekan' => $request->tgl_pengecekan,
            'deskripsi_cek' => $request->deskripsi_cek,
            'kondisi_after' => 'Selesai',
        ]);

        // update Kondisi
        $noseri->update(['kondisi' => $newKondisi]);

        // Simpan Log Perubahan
        NoSeriLog::create([
            'no_seri_id' => $noseri->id,
            'old_kondisi' => $oldKondisi,
            'new_kondisi' => $newKondisi,
            'changed_at' => now(),
            // 'changed_by' => auth()->id() ?? 1,
            'changed_by' => $user->id,
        ]);

        // Jika kondisi Error, simpan ke tabel Error
        if (strtolower($newKondisi) === 'error') {
            // Ambil nomor urutan terakhir
            $lastError = Error::orderBy('id', 'desc')->first();
            $lastNumber = 0;

            if ($lastError && preg_match('/PB(\d{8})/', $lastError->no_perbaikan, $matches)) {
                $lastNumber = (int) $matches[1];
            }

            $newNumber = $lastNumber + 1;
            $no_perbaikan = 'PB' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);

            Error::create([
                'no_seri_id' => $noseri->id,
                'kondisi' => 'Error',
                // 'users_id' => auth()->id() ?? 1,
                'users_id' => $user->id,
                'no_perbaikan' => $no_perbaikan,
                'tgl_perbaikan' => $request->tgl_pengecekan,
            ]);
        }

        // Jika kondisi Rusak, simpan ke tabel Rusak
        if (strtolower($newKondisi) === 'rusak') {
            // Ambil nomor urutan terakhir
            $lastRusak = Rusak::orderBy('id', 'desc')->first();
            $lastNumber = 0;

            if ($lastRusak && preg_match('/KR(\d{8})/', $lastRusak->no_kerusakan, $matches)) {
                $lastNumber = (int) $matches[1];
            }

            $newNumber = $lastNumber + 1;
            $no_kerusakan = 'KR' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);

            Rusak::create([
                'no_seri_id' => $noseri->id,
                'kondisi' => 'Rusak',
                // 'users_id' => auth()->id() ?? 1,
                'users_id' => $user->id,
                'no_kerusakan' => $no_kerusakan,
                'tgl_kerusakan' => $request->tgl_pengecekan,
                'detail_kerusakan' => $request->deskripsi_cek,
            ]);
        }

        if (strtolower($newKondisi) === 'hilang') {
            // Ambil nomor urutan terakhir
            $lastHilang = Hilang::orderBy('id', 'desc')->first();
            $lastNumber = 0;

            if ($lastHilang && preg_match('/KH(\d{8})/', $lastHilang->no_kehilangan, $matches)) {
                $lastNumber = (int) $matches[1];
            }

            $newNumber = $lastNumber + 1;
            $no_kehilangan = 'KH' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);

            Hilang::create([
                'no_seri_id' => $noseri->id,
                'kondisi' => 'Hilang',
                // 'users_id' => auth()->id() ?? 4,
                'users_id' => $user->id,
                'no_kehilangan' => $no_kehilangan,
                'tgl_kehilangan' => $request->tgl_pengecekan,
                'detail_hilang' => $request->deskripsi_cek,
            ]);
        }

        foreach ($noseri->peminjaman as $peminjaman) {
            if ($peminjaman) {
                $oldStatus = $peminjaman->status;
                $newStatus = 'Selesai';

                // Simpan log perubahan status
                PeminjamanLog::create([
                    'peminjaman_id' => $peminjaman->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'changed_at'  => now(),
                    // 'changed_by'  => auth()->id() ?? 4,
                    'changed_by'  => $user->id,
                ]);

                // Kurangi stok akhir pada tabel tools
                // $tool = Tools::find($noseri->tools_id);
                // if ($tool) {
                //     $tool->update([
                //         'stok_akhir' => $tool->stok_akhir + 1,
                //     ]);
                // }

                $peminjaman->update([
                    'status' => $newStatus,
                    'status_kondisi' => $newStatus,
                    'deskripsi_cek' => $request->deskripsi_cek,
                    'tgl_cek' => $request->tgl_pengecekan,
                ]);
            }
        }

        return response()->json(['message' => 'Status No Seri, Peminjaman, dan Perubahan Peminjaman berhasil diperbarui.']);
    }

    public function getToolConditionData()
    {
        $conditions = NoSeri::select('kondisi')
            ->selectRaw('count(*) as total')
            ->groupBy('kondisi')
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [];

        $colors = [
            'OK' => '#169ea8',
            'Error' => '#f6c23e',
            'Rusak' => '#fd7e14',
            'Musnah' => '#e74a3b',
            'Hilang' => '#6610f2',
        ];

        foreach ($conditions as $condition) {
            $labels[] = $condition->kondisi;
            $data[] = $condition->total;
            $backgroundColors[] = $colors[$condition->kondisi] ?? '#6c757d'; // default color if not defined
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'colors' => $backgroundColors,
        ]);
    }


}
