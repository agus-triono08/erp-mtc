<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\NoSeri;
use App\Models\Inventory\Hilang;
use App\Models\Inventory\HilangActivityBaru;
use App\Models\Inventory\HilangActivityProses;
use App\Models\Inventory\PeminjamanLog;
use App\Models\Inventory\Peminjaman;
use App\Models\Inventory\Tools;
use App\Models\Inventory\NoSeriLog;
use Carbon\Carbon;
use Illuminate\Support\Str;

class HilangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $all = Hilang::with([
            'noSeri.tools',
            'noSeri.layout',
            'users.divisi',
            'users.Karyawan',
            'hilang_activity_baru',
            'hilang_activity_proses'
        ])
        ->orderBy('updated_at', 'desc')
        ->get();

        $byStatusBaru = Hilang::with([
            'noSeri.tools',
            'noSeri.layout',
        ])
        ->where('status', 'Belum')
        ->orderBy('updated_at', 'desc')
        ->get();

        $byStatusProses = Hilang::with([
            'noSeri.tools',
            'noSeri.layout',
        ])
        ->where('status', 'Proses')
        ->orderBy('updated_at', 'desc')
        ->get();

        $byStatusSelesai = Hilang::with([
            'noSeri.tools',
            'noSeri.layout',
            'users.divisi',
            'hilang_activity_baru',
            'hilang_activity_proses'
        ])
        ->where('status', 'Selesai')
        ->orderBy('updated_at', 'desc')
        ->get();

        return response()->json([
            'all' => $all,
            'byStatusBaru' => $byStatusBaru,
            'byStatusProses' => $byStatusProses,
            'byStatusSelesai' => $byStatusSelesai,
        ]);
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
    public function store(Request $request)
    {
        $request->validate([
            'no_seri_id' => 'required|exists:no_seri,id',
            'tgl_kehilangan' => 'required|date',
            'detail_hilang' => 'required|string',
            'status' => 'nullable|string',
            'kondisi' => 'required|string',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        }

        // Ambil nomor urutan terakhir
        $lastHilang = Hilang::orderBy('id', 'desc')->first();
        $lastNumber = 0;

        if ($lastHilang && preg_match('/KH(\d{8})/', $lastHilang->no_kehilangan, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $newNumber = $lastNumber + 1;
        $no_kehilangan = 'KH' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);

        $hilang = Hilang::create([
            'no_seri_id' => $request->no_seri_id,
            'no_kehilangan' => $no_kehilangan,
            'tgl_kehilangan' => $request->tgl_kehilangan,
            'detail_hilang' => $request->detail_hilang,
            'kondisi' => $request->kondisi,
            // 'status' => $request->status ?? 'Pending',
            // 'users_id' => auth()->id(),
            'users_id' => $user->id,
        ]);

        // Update kondisi pada tabel no_seri
        $noSeri = NoSeri::find($request->no_seri_id);
        if ($noSeri) {

            $oldKondisi = $noSeri->kondisi;
            $newKondisi = $request->kondisi;

            if ($oldKondisi !== $newKondisi) {
                // Simpan log perubahan kondisi
                NoSeriLog::create([
                    'no_seri_id'  => $noSeri->id,
                    'old_kondisi' => $oldKondisi,
                    'new_kondisi' => $newKondisi,
                    'changed_at'  => now(),
                    // 'changed_by'  => auth()->id() ?? 1, // pastikan user sudah login
                    'changed_by'  => $user->id,
                ]);
            }

            $noSeri->kondisi = $newKondisi; // Update kondisi
            $noSeri->save();

            // Kurangi stok_akhir dan harga_total pada tabel tools jika kondisi hilang
            if ($noSeri->tools_id && strtolower($newKondisi) === 'hilang') {
                $tool = Tools::find($noSeri->tools_id);
                if ($tool) {
                    if ($tool->stok_akhir > 0) {
                        $tool->stok_akhir -= 1;
                    }
                    if ($noSeri->harga && $tool->harga_total >= $noSeri->harga) {
                        $tool->harga_total -= $noSeri->harga;
                    }
                    $tool->save();
                }
            }
        }

        return response()->json([
            'message' => 'Data perbaikan berhasil disimpan.',
            'data' => $hilang
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
        $kehilangan = Hilang::with([
            'noSeri.tools',
            'noSeri.layout',
            'users.divisi',
            'hilang_activity_baru' => function($q) {
                $q->orderBy('changed_at', 'desc');
            },
            'hilang_activity_proses'=> function($p) {
                $p->orderBy('changed_at', 'desc');
            },
        ])
        ->orderBy('updated_at', 'desc')
        ->findOrFail($id);
        // Get the collection of related models
        $hilangActivityBaru = $kehilangan->hilang_activity_baru;
        // Use the map method on the collection
        $hilangActivityBaru = $hilangActivityBaru->map(function ($item) {
            $item->changed_at = \Carbon\Carbon::parse($item->changed_at)->format('Y-m-d');
            return $item;
        });
        // Update the hilang_activity_baru attribute of the pemus$kehilangan model
        $kehilangan->hilang_activity_baru = $hilangActivityBaru;

        // Get the collection of related models
        $hilangActivityProses = $kehilangan->hilang_activity_proses;
        // Use the map method on the collection
        $hilangActivityProses = $hilangActivityProses->map(function ($item) {
            $item->changed_at = \Carbon\Carbon::parse($item->changed_at)->format('Y-m-d');
            return $item;
        });
        // Update the hilang_activity_proses attribute of the pemus$kehilangan model
        $kehilangan->hilang_activity_proses = $hilangActivityProses;

        return response()->json($kehilangan);
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

    public function getHilang($noSeri)
    {
        $noseri = NoSeri::where('no_seri', $noSeri)->first();

        if (!$noseri) {
            return response()->json(['message' => 'No Seri not found'], 404);
        }

        // Cek kondisi
        // if ($noseri->kondisi !== 'Rusak') {
        //     return response()->json(['message' => 'No Seri dalam kondisi baik'], 200);
        // }

        $kehilangan = Hilang::with('noSeri.tools')
            ->where('no_seri_id', $noseri->id)
            ->get();

        if ($kehilangan->isEmpty()) {
            return response()->json(['message' => 'Kehilangan not found'], 404);
        }

        return response()->json($kehilangan);
    }

    public function addActivity(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:hilang,id',
            'bukti_pertanggung_jawaban' => 'nullable|file|mimes:pdf', // Ini sudah benar, karena kita akan mengirim array file
        ]);

        $kehilangan = Hilang::findOrFail($request->id);

        // Simpan file Bukti Pertanggung Jawaban
        $buktiPJPath = null;
        if ($request->hasfile('bukti_pertanggung_jawaban')) {
            $buktiPJPath = $request->file('bukti_pertanggung_jawaban')->store('kehilangan/pj', 'public');
        }

        // Simpan aktivitas kehilangan ke database
        HilangActivityBaru::create([
            'hilang_id' => $kehilangan->id,
            'bukti_pertanggung_jawaban' => $buktiPJPath,
            'status' => 'Belum',
            'changed_at' => now()->format('Y-m-d'),
        ]);

        return response()->json(['message' => 'Berhasil menyimpan aktivitas baru kehilangan.']);
    }

    public function pengantianDiterima(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:hilang_activity_baru,id', // ID milik RusakActivity
            'status' => 'nullable|string',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        }

        $aktivitas = HilangActivityBaru::findOrFail($request->id); // Ambil aktivitas
        $kehilangan = $aktivitas->hilang; // Ambil relasi ke Hilang
        if (!$kehilangan) {
            return response()->json(['message' => 'Data kehilangan tidak ditemukan'], 404);
        }
        $oldKondisi = $kehilangan->noSeri->kondisi;
        $kehilangan->update([
            'kondisi' => 'Hilang',
            'status' => 'Proses',
        ]);
        $aktivitas->update([
            'status' => 'Diterima',
            'changed_at' => Carbon::today()->format('Y-m-d'),
        ]);

        $kehilangan->noSeri->update([
            'kondisi' => 'Hilang',
        ]);
        $tool = Tools::find($kehilangan->noSeri->tools_id);
        if ($tool) {
            $tool->update([
                'stok_akhir' => $tool->stok_akhir - 1,
            ]);
        }
        NoSeriLog::create([
            'no_seri_id' => $kehilangan->no_seri_id,
            'old_kondisi' => $oldKondisi,
            'new_kondisi' => 'Hilang',
            'changed_at' => Carbon::today()->format('Y-m-d'),
            // 'changed_by' => auth()->id() ?? 1,
            'changed_by' => $user->id,        ]);
        return response()->json(['message' => 'Data berhasil diperbarui']);
    }

    public function pengantianDitolak(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:hilang_activity_baru,id', // ID milik RusakActivity
            'status' => 'nullable|string',
            'alasan_penolakan' => 'nullable|string',
        ]);

        $aktivitas = HilangActivityBaru::findOrFail($request->id); // Ambil aktivitas
        $kehilangan = $aktivitas->hilang; // Ambil relasi ke Hilang

        if (!$kehilangan) {
            return response()->json(['message' => 'Data kehilangan tidak ditemukan'], 404);
        }

        $oldKondisi = $kehilangan->noSeri->kondisi;

        $aktivitas->update([
            'status' => 'Ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
            'changed_at' => Carbon::today()->format('Y-m-d'),
        ]);

        return response()->json(['message' => 'Data berhasil ditolak']);
    }

    public function addActivityProses(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:hilang,id',
            'tgl_penggantian' => 'nullable|date',
            'harga' => 'nullable|numeric',
            'kondisi' => 'nullable|string',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        }

        $kehilangan = Hilang::with('noSeri')->findOrFail($request->id);
        $tools = $kehilangan->noSeri->tools;
        $toolsId = $kehilangan->noSeri->tools_id;
        $oldKondisi = $kehilangan->noSeri->kondisi;
        $layoutId = $tools->layout_id ?? 1;

        // Ambil no_seri lama
        $noSeriLama = $kehilangan->noSeri->no_seri;
        
         // Ambil prefix (bagian awal dari no seri lama)
        $prefix = substr($noSeriLama, 0, -6);

        // Buat no_seri baru dengan format yang sama
        $countExisting = NoSeri::where('tools_id', $tools->id)->count();
        $nextNumber = $countExisting + 1;

        // Ambil nomor seri terakhir untuk tools_id yang sama
        $lastNoSeri = NoSeri::where('tools_id', $tools)
            ->where('no_seri', 'like', $prefix . '%')
            ->orderBy('no_seri', 'desc')
            ->first();

        // Ambil 6 angka terakhir dari nomor seri terakhir, lalu +1
        if ($lastNoSeri) {
            $lastNumber = (int)substr($lastNoSeri->no_seri, -6);
            // $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format ke 6 digit dengan padding nol di depan
        $newSuffix = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        // Gabungkan prefix dengan 6 digit angka baru
        $noSeriBaru = $prefix . $newSuffix;

        

        // Buat data no_seri baru
        $noSeri = NoSeri::create([
            'tools_id' => $tools->id,
            'layout_id' => $layoutId,
            'no_seri' => $noSeriBaru,
            'no_seri_default' => null,
            'tanggal_masuk' => $request->tgl_penggantian,
            'harga' => $request->harga ?? ($tools->harga_total ? $tools->harga_total / ($tools->stok_awal ?: 1) : null),
        ]);

        // Log kondisi OK
        NoSeriLog::create([
            'no_seri_id' => $noSeri->id,
            'old_kondisi' => null,
            'new_kondisi' => $request->kondisi ?? 'OK',
            'changed_at' => now(),
            // 'changed_by' => auth()->id() ?? 4,
            'changed_by' => $user->id,
        ]);

        // Buat activity proses penggantian
        HilangActivityProses::create([
            'hilang_id' => $kehilangan->id,
            'no_seri_old' => $kehilangan->noSeri->no_seri ?? null,
            'no_seri_new' => $noSeri->no_seri,
            'tgl_penggantian' => $request->tgl_penggantian ?? now(),
            'harga' => $request->harga,
            'status' => 'Menunggu Konfirmasi',
            'changed_at' => now(),
        ]);

        // Update stok akhir
        // $tools->increment('stok_akhir');

        return response()->json(['message' => 'Data berhasil diproses', 'no_seri' => $noSeriBaru], 200);
    }

    public function alatDiserahkan(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:hilang_activity_proses,id', // ID milik RusakActivity
            'status' => 'nullable|string',
        ]);
        $aktivitas = HilangActivityProses::findOrFail($request->id); // Ambil aktivitas
        $kehilangan = $aktivitas->hilang; // Ambil relasi ke Hilang
        if (!$kehilangan) {
            return response()->json(['message' => 'Data kehilangan tidak ditemukan'], 404);
        }
        $oldKondisi = $kehilangan->noSeri->kondisi;
        $aktivitas->update([
            'status' => 'Serahkan Alat/Mesin',
            'changed_at' => Carbon::today()->format('Y-m-d'),
        ]);
        return response()->json(['message' => 'Data berhasil diperbarui']);
    }

    public function alatDiterima(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:hilang_activity_proses,id', // ID milik RusakActivity
            'status' => 'nullable|string',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        }

        $aktivitas = HilangActivityProses::findOrFail($request->id); // Ambil aktivitas
        $kehilangan = $aktivitas->hilang; // Ambil relasi ke Hilang
        if (!$kehilangan) {
            return response()->json(['message' => 'Data kehilangan tidak ditemukan'], 404);
        }
        $oldKondisi = $kehilangan->noSeri->kondisi;
        $kehilangan->update([
            'status' => 'Selesai',
        ]);
        $aktivitas->update([
            'status' => 'Diterima',
            'changed_at' => Carbon::today()->format('Y-m-d'),
        ]);
        $tool = Tools::find($kehilangan->noSeri->tools_id);
        if ($tool) {
            $tool->update([
                'stok_akhir' => $tool->stok_akhir + 1,
            ]);
        }
        NoSeriLog::create([
            'no_seri_id' => $kehilangan->no_seri_id,
            'old_kondisi' => $oldKondisi,
            'new_kondisi' => 'Hilang',
            'changed_at' => Carbon::today()->format('Y-m-d'),
            // 'changed_by' => auth()->id() ?? 1,
            'changed_by' => $user->id,
        ]);
        foreach ($kehilangan->noSeri->peminjaman as $peminjaman) {
            if ($peminjaman) {
                $oldStatus = $peminjaman->status;
                $newStatus = 'Selesai';
                // Simpan log perubahan status
                PeminjamanLog::create([
                    'peminjaman_id' => $peminjaman->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'changed_at'  => now(),
                    // 'changed_by'  => auth()->id() ?? 1,
                    'changed_by'  => $user->id,
                ]);
                // Kurangi stok akhir pada tabel tools
                // $tool = Tools::find($kehilangan->noSeri->tools_id);
                // if ($tool) {
                //     $tool->update([
                //         'stok_akhir' => $tool->stok_akhir + 1,
                //     ]);
                // }
                $peminjaman->update([
                    'status' => $newStatus,
                    'status_kondisi' => $newStatus,
                ]);
            }
        }    
        return response()->json(['message' => 'Data berhasil diperbarui']);
    }

    public function countBelum()
    {
        try {
            // Menghitung jumlah peminjaman dengan status 'Belum Diproses'
            $count = Hilang::where('status', 'Belum')->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Jumlah Kehilangan dengan status Belum',
                'count' => $count
    
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function listBelum()
    {
        try {
            $perbaikan = Hilang::with(['noSeri.tools'])
                ->where('status', 'Belum')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Daftar perbaikan belum diproses',
                'data' => $perbaikan
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

}
