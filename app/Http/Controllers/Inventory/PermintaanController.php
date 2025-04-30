<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\NoSeri;
use App\Models\Inventory\Permintaan;
use App\Models\Inventory\Tools;
use Illuminate\Support\Facades\DB;

class PermintaanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Ambil semua data permintaan beserta tools dan no_seri jika status = Digunakan
        $all = Permintaan::with([
                'tools',
                'noSeri' => function ($query) {
                    $query->select('no_seri.id', 'no_seri.no_seri', 'no_seri.kondisi', 'no_seri.kondisi_after')
                        // ->where('kondisi_after', 'Digunakan')
                        ->orderBy('no_seri.created_at', 'asc');
                }
            ])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($permintaan) {
                // Cek status
                if ($permintaan->status !== 'Digunakan') {
                    $permintaan->no_seri = []; // Kosongkan no_seri
                }
                return $permintaan;
            });

        // Ambil data by status
        $byStatus = Permintaan::with([
                'tools',
                'noSeri' => function ($query) {
                    $query->select('no_seri.id', 'no_seri.no_seri', 'no_seri.kondisi', 'no_seri.kondisi_after')
                        ->orderBy('no_seri.created_at', 'asc');
                }
            ])
            ->orderByRaw("FIELD(status, 'Belum Diproses', 'Menunggu Diambil', 'Digunakan', 'Ditolak', 'Selesai')")
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($permintaan) {
                if ($permintaan->status !== 'Digunakan') {
                    $permintaan->no_seri = []; // Kosongkan no_seri
                }
                return $permintaan;
            });

        return response()->json([
            'all' => $all,
            'by_status' => $byStatus,
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
            'tools_id' => 'required|exists:tools,id',
            'tgl_permintaan' => 'required|date',
            'status' => 'nullable|string',
            'total' => 'required|integer|min:1',
        ]);

        // Ambil nomor urutan terakhir
        $lastPermintaan = Permintaan::orderBy('id', 'desc')->first();
        $lastNumber = 0;

        if ($lastPermintaan && preg_match('/PM(\d{8})/', $lastPermintaan->no_permintaan, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $newNumber = $lastNumber + 1;
        $no_permintaan = 'PM' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);

        // Siapkan data permintaan
        $status = $request->status ?? 'Belum Diproses';

        $data = [
            'tools_id' => $request->tools_id,
            'no_permintaan' => $no_permintaan,
            'tgl_permintaan' => $request->tgl_permintaan,
            'status' => $status,
            'status_kondisi' => $status,
            'total' => $request->total, // ✅ Ini ditambahkan
        ];

        // Buat Permintaan
        $permintaan = Permintaan::create($data);

        // Ambil tools yang sesuai dengan tools_id
        $tools = Tools::with('noSeri')->find($request->tools_id);

        // Ambil ID no_seri yang sudah digunakan
        $usedNoSeriIds = DB::table('peminjaman_no_seri')->pluck('no_seri_id')
                            ->merge(DB::table('permintaan_no_seri')->pluck('no_seri_id'))
                            ->toArray();

        // Cari no_seri yang sesuai dengan kondisi 'OK' dan kondisi_after null
        // $availabeleNoSeri = $tools->noSeri->where('kondisi', 'OK')
        //                                 ->whereNull('kondisi_after')
        //                                 ->take($request->total);
                                    
        // Ambil no_seri yang tersedia
        $availableNoSeri = $tools->noSeri->where('kondisi', 'OK')
                            ->whereNull('kondisi_after')
                            ->whereNotIn('id', $usedNoSeriIds)
                            ->take($request->total);

        // Jika jumlah no_seri yang ditemukan kurang dari yang diminta
        if ($availableNoSeri->count() < $request->total) 
        {
            return response()
            ->json(['message' => 'Jumlah no seri yang tersedia tidak cukup atau sudah digunakan.'], 400);
        }

        $noSeriIds = [];

        // Proses permintaan no_seri
        foreach ($availableNoSeri as $noseri)
        {
            $noSeriIds[] = $noseri->id;

            // Update kondisi_after dan tanggal_kondisi untuk semua no seri yang diambil
            $noseri->update([
                'kondisi_after' => $status,
                'tanggal_kondisi' => $request->tgl_permintaan,
            ]);

            // if ($request->status == 'Digunakan')
            // {
            //     // Update kondisi_after untuk no_seri yang dipinta
            //     $noseri->update([
            //         'kondisi_after' => 'Digunakan',
            //         'tanggal_kondisi' => $request->tgl_permintaan,
            //     ]);
            // }            
        }

        // Hubungkan ke Pivot
        $permintaan->noSeri()->attach($noSeriIds);

        // Kurangi stok_akhir pada tools hanya kalau sattus = Digunakan
        if($status == 'Digunakan')
        {
            $tools->stok_akhir = max(0, $tools->stok_akhir - $request->total); //Hindari nilai negative
            $tools->save();
        }        

        return response()->json([
            'message' => 'Data permintaan berhasil disimpan.',
            'data' => $permintaan
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
        $permintaan = Permintaan::with('tools.noSeri')->find($id);
        if ($permintaan)
        {
            return response()->json($permintaan);
        } else {
            return response()->json(['message' => 'Permintaan not found'], 404);
        }
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

    public function getPermintaan($kodeAlat)
    {
        $permintaan = Permintaan::whereHas('tools', function ($query) use ($kodeAlat) {
                $query->where('kode', $kodeAlat)
                    ->whereHas('noSeri', function ($q) {
                        $q->where('kondisi_after', 'Digunakan');
                    });
            })
            ->with(['tools','noSeri' => function ($query) {
                $query->where('kondisi_after', 'Digunakan')
                    ->orderBy('permintaan_no_seri.created_at'); // Mengurutkan berdasarkan created_at pada pivot
            }])
            ->get()
            ->map(function ($permintaan) {
                // Jika status permintaan bukan 'Digunakan', kita kosongkan no_seri
                if ($permintaan->status !== 'Digunakan') {
                    $permintaan->tools->noSeri = [];
                }
                return $permintaan;
            });

        if ($permintaan->isEmpty()) {
            return response()->json(['message' => 'Permintaan not found'], 404);
        }

        return response()->json($permintaan);
    }

    public function getNoPermintaan($noPermintaan)
    {
        // Ambil data permintaan berdasarkan no_permintaan
        $permintaan = Permintaan::where('no_permintaan', $noPermintaan)
            ->with([
                'tools', 'noSeri' => function ($query) {
                    $query->select('no_seri.id', 'no_seri.no_seri', 'no_seri.kondisi', 'no_seri.kondisi_after', 'no_seri.tanggal_kondisi', 'no_seri.reject_reason');
                }
            ])
            ->get()
            ->map(function ($permintaan) {
                // Jika status permintaan bukan 'Digunakan', kosongkan noSeri
                if ($permintaan->status !== 'Digunakan') {
                    $permintaan->noSeri = [];
                }
                return $permintaan;
            });

        // Jika data permintaan tidak ditemukan
        if ($permintaan->isEmpty()) {
            return response()->json(['message' => 'Permintaan not found'], 404);
        }

        return response()->json($permintaan);
    }

    public function getPengajuanNoPermintaan($noPermintaan)
    {
        // Ambil data permintaan berdasarkan no_permintaan
        $permintaan = Permintaan::where('no_permintaan', $noPermintaan)
            ->with([
                'tools',
                'noSeri' => function ($query) {
                    // Ambil no_seri yang kondisinya 'Dipinjam' dan urutkan berdasarkan created_at
                    $query->select('no_seri.id', 'no_seri.no_seri', 'no_seri.kondisi', 'no_seri.kondisi_after', 'no_seri.tanggal_kondisi', 'no_seri.reject_reason')
                        ->where('kondisi_after', 'Ditolak');
                        // ->orderBy('no_seri.created_at', 'desc');
                }
            ])
            ->get()
            ->map(function ($permintaan) {
                // Jika status permintaan bukan 'Dipinjam', kosongkan noSeri
                if ($permintaan->status !== 'Ditolak') {
                    $permintaan->noSeri = []; // Kosongkan noSeri
                }
                return $permintaan;
            });

        // Jika data permintaan tidak ditemukan
        if ($permintaan->isEmpty()) {
            return response()->json(['message' => 'Permintaan not found'], 404);
        }

        return response()->json($permintaan);
    }

}
