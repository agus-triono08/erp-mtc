<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\NoSeri;
use App\Models\Inventory\Peminjaman;
use App\Models\Inventory\Tools;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Ambil semua data peminjaman beserta tools dan no_seri jika status = Dipinjam
        $all = Peminjaman::with([
                'tools',
                'noSeri' => function ($query) {
                    $query->select('no_seri.id', 'no_seri.no_seri', 'no_seri.kondisi', 'no_seri.kondisi_after')
                        // ->where('kondisi_after', 'Dipinjam')
                        ->orderBy('no_seri.created_at', 'asc');
                }
            ])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($peminjaman) {
                // Cek status
                if ($peminjaman->status !== 'Dipinjam') {
                    $peminjaman->no_seri = []; // Kosongkan no_seri
                }
                return $peminjaman;
            });

        // Ambil data by status
        $byStatus = Peminjaman::with([
                'tools',
                'noSeri' => function ($query) {
                    $query->select('no_seri.id', 'no_seri.no_seri', 'no_seri.kondisi', 'no_seri.kondisi_after')
                        ->orderBy('no_seri.created_at', 'asc');
                }
            ])
            ->orderByRaw("FIELD(status, 'Belum Diproses', 'Menunggu Diambil', 'Dipinjam', 'Ditolak', 'Selesai')")
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($peminjaman) {
                if ($peminjaman->status !== 'Dipinjam') {
                    $peminjaman->no_seri = []; // Kosongkan no_seri
                }
                return $peminjaman;
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
            'tgl_pinjam' => 'required|date',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_pinjam',
            'status' => 'nullable|string',
            'total' => 'required|integer|min:1',
        ]);

        // Ambil nomor urutan terakhir
        $lastPeminjaman = Peminjaman::orderBy('id', 'desc')->first();
        $lastNumber = 0;

        if ($lastPeminjaman && preg_match('/PJ(\d{8})/', $lastPeminjaman->no_peminjaman, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $newNumber = $lastNumber + 1;
        $no_peminjaman = 'PJ' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);

        // Siapkan data peminjaman
        $status = $request->status ?? 'Belum Diproses';

        $data = [
            'tools_id' => $request->tools_id,
            'no_peminjaman' => $no_peminjaman,
            'tgl_pinjam' => $request->tgl_pinjam,
            'tgl_kembali' => $request->tgl_kembali,
            'status' => $status,
            'status_kondisi' => $status,
            'total' => $request->total,
        ];

        // Buat peminjaman
        $peminjaman = Peminjaman::create($data);

        // Ambil tools dan no_seri
        $tools = Tools::with('noSeri')->findOrFail($request->tools_id);

        // Ambil ID no_seri yang sudah digunakan
        $usedNoSeriIds = DB::table('permintaan_no_seri')->pluck('no_seri_id')
                            ->merge(DB::table('peminjaman_no_seri')->pluck('no_seri_id'))
                            ->toArray();

        // Ambil no_seri yang tersedia
        $availableNoSeri = $tools->noSeri->where('kondisi', 'OK')
                            ->whereNull('kondisi_after')
                            ->whereNotIn('id', $usedNoSeriIds)
                            ->take($request->total);

        if ($availableNoSeri->count() < $request->total) {
            return response()->json([
                'message' => 'Jumlah no seri yang tersedia tidak cukup atau sudah digunakan.',
            ], 400);
        }

        $noSeriIds = [];

        foreach ($availableNoSeri as $noseri) {
            $noSeriIds[] = $noseri->id;

            // Update kondisi_after dan tanggal_kondisi untuk semua no seri yang diambil
            $noseri->update([
                'kondisi_after' => $status,
                'tanggal_kondisi' => $request->tgl_pinjam,
            ]);
        }

        // Hubungkan peminjaman dengan no seri
        $peminjaman->noSeri()->attach($noSeriIds);

        // Update stok tools kalau status peminjaman 'Dipinjam'
        if ($status === 'Dipinjam') {
            $tools->stok_akhir = max(0, $tools->stok_akhir - $request->total);
            $tools->save();
        }

        return response()->json([
            'message' => 'Data peminjaman berhasil disimpan.',
            'data' => $peminjaman->load('noSeri'),
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
        $peminjaman = Peminjaman::with('tools.noSeri')->find($id);
        if ($peminjaman)
        {
            return response()->json($peminjaman);        
        } else
        {
            return response()->json(['message' => 'Peminjaman not found'], 404);
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

    public function getPeminjaman($kodeAlat)
    {
        $peminjaman = Peminjaman::whereHas('tools', function ($query) use ($kodeAlat) {
                $query->where('kode', $kodeAlat)
                    ->whereHas('noSeri', function ($q) {
                        $q->where('kondisi_after', 'Dipinjam');
                    });
            })
            ->with(['tools','noSeri' => function ($query) {
                $query->where('kondisi_after', 'Dipinjam')
                    ->orderBy('peminjaman_no_seri.created_at'); // Mengurutkan berdasarkan created_at pada pivot
            }])
            ->get()
            ->map(function ($peminjaman) {
                // Jika status peminjaman bukan 'Dipinjam', kita kosongkan no_seri
                if ($peminjaman->status !== 'Dipinjam') {
                    $peminjaman->tools->noSeri = [];
                }
                return $peminjaman;
            });

        if ($peminjaman->isEmpty()) {
            return response()->json(['message' => 'Peminjaman not found'], 404);
        }

        return response()->json($peminjaman);
    }

    public function getNoPeminjaman($noPinjam)
    {
        // Ambil data peminjaman berdasarkan no_peminjaman
        $peminjaman = Peminjaman::where('no_peminjaman', $noPinjam)
            ->with([
                'tools',
                'noSeri' => function ($query) {
                    // Ambil no_seri yang kondisinya 'Dipinjam' dan urutkan berdasarkan created_at
                    $query->select('no_seri.id', 'no_seri.no_seri', 'no_seri.kondisi', 'no_seri.kondisi_after', 'no_seri.tanggal_kondisi', 'no_seri.reject_reason');
                        // ->where('kondisi_after', 'Dipinjam')
                        // ->orderBy('no_seri.created_at', 'desc');
                }
            ])
            ->get()
            ->map(function ($peminjaman) {
                // Jika status peminjaman bukan 'Dipinjam', kosongkan noSeri
                if ($peminjaman->status !== 'Dipinjam') {
                    $peminjaman->noSeri = []; // Kosongkan noSeri
                }
                return $peminjaman;
            });

        // Jika data peminjaman tidak ditemukan
        if ($peminjaman->isEmpty()) {
            return response()->json(['message' => 'Peminjaman not found'], 404);
        }

        return response()->json($peminjaman);
    }

    public function getPengajuanNoPeminjaman($noPinjam)
    {
        // Ambil data peminjaman berdasarkan no_peminjaman
        $peminjaman = Peminjaman::where('no_peminjaman', $noPinjam)
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
            ->map(function ($peminjaman) {
                // Jika status peminjaman bukan 'Dipinjam', kosongkan noSeri
                if ($peminjaman->status !== 'Ditolak') {
                    $peminjaman->noSeri = []; // Kosongkan noSeri
                }
                return $peminjaman;
            });

        // Jika data peminjaman tidak ditemukan
        if ($peminjaman->isEmpty()) {
            return response()->json(['message' => 'Peminjaman not found'], 404);
        }

        return response()->json($peminjaman);
    }


}
