<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\NoSeri;
use App\Models\Inventory\Permintaan;
use App\Models\Inventory\Tools;
use App\Models\Inventory\PermintaanLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PermintaanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        // Cek apakah user divisi MTC dan jabatan Supervisor / Manager
        $isMtcSupervisorOrManager = strtolower(optional($user->Divisi)->kode) === 'mtc' &&
            in_array(strtolower(optional($user->Karyawan)->jabatan), ['supervisor', 'manager']);

        $baseQuery = Permintaan::with([
            'tools',
            'users.Karyawan',
            'users.divisi',
            'noSeri' => function ($query) {
                $query->select('no_seri.id', 'no_seri.no_seri', 'no_seri.kondisi', 'no_seri.kondisi_after')
                    ->orderBy('no_seri.created_at', 'asc');
            }
        ])
        ->when(!$isMtcSupervisorOrManager, function ($query) use ($user) {
            $query->where('users_id', $user->id); // Filter hanya data miliknya sendiri
        });

        // Ambil semua
        $all = (clone $baseQuery)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($peminjaman) {
                if ($peminjaman->status !== 'Dipinjam') {
                    $peminjaman->no_seri = [];
                }
                return $peminjaman;
            });

        // Ambil berdasarkan status
        $byStatus = (clone $baseQuery)
            ->orderByRaw("FIELD(status, 'Belum Diproses', 'Menunggu Diambil', 'Dipinjam', 'Ditolak', 'Selesai')")
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($peminjaman) {
                if ($peminjaman->status !== 'Dipinjam') {
                    $peminjaman->no_seri = [];
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
            'tgl_permintaan' => 'required|date',
            'detail_permintaan' => 'required|string',
            'status' => 'nullable|string',
            'total' => 'required|integer|min:1',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat permintaan.'
            ], 401);
        }

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
            'users_id' => $user->id, // Tambahkan user_id dari user yang login
            'tools_id' => $request->tools_id,
            'no_permintaan' => $no_permintaan,
            'tgl_permintaan' => $request->tgl_permintaan,
            'detail_permintaan' => $request->detail_permintaan,
            'status' => $status,
            'status_kondisi' => $status,
            'total' => $request->total,
        ];

        // Buat Permintaan
        $permintaan = Permintaan::create($data);

        // Ambil tools dan data noseri terkait
        $tools = Tools::with('noSeri')->find($request->tools_id);

        $usedNoSeriIds = DB::table('peminjaman_no_seri')->pluck('no_seri_id')
            ->merge(DB::table('permintaan_no_seri')->pluck('no_seri_id'))
            ->toArray();

        $availableNoSeri = $tools->noSeri->where('kondisi', 'OK')
            ->whereNull('kondisi_after')
            ->whereNotIn('id', $usedNoSeriIds)
            ->take($request->total);

        if ($availableNoSeri->count() < $request->total) {
            return response()->json([
                'message' => 'Jumlah no seri yang tersedia tidak cukup atau sudah digunakan.'
            ], 400);
        }

        $noSeriIds = [];

        foreach ($availableNoSeri as $noseri) {
            $noSeriIds[] = $noseri->id;

            $noseri->update([
                'kondisi_after' => $status,
                'tanggal_kondisi' => $request->tgl_permintaan,
            ]);
        }

        $permintaan->noSeri()->attach($noSeriIds);

        // Update stok jika status Digunakan
        if ($status == 'Digunakan') {
            $tools->stok_akhir = max(0, $tools->stok_akhir - $request->total);
            $tools->save();
        }

        if ($status === 'Belum Diproses') {
            PermintaanLog::create([
                'permintaan_id' => $permintaan->id,
                'old_status' => null,
                'new_status' => $status,
                'changed_at' => now(),
                'changed_by' => $user->id, // Gunakan ID user yang login
            ]);
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
        $permintaan = Permintaan::with('tools.noSeri', 'users.divisi', 'users.Karyawan')->find($id);
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

    public function countBelumDiproses()
    {
        try {
            // Menghitung jumlah peminjaman dengan status 'Belum Diproses'
            $count = Permintaan::where('status', 'Belum Diproses')->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Jumlah permintaan dengan status Belum Diproses',
                'count' => $count
    
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function listBelumDiproses()
    {
        try {
            $permintaan = Permintaan::with(['users', 'tools', 'noSeri'])
                ->where('status', 'Belum Diproses')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Daftar permintaan belum diproses',
                'data' => $permintaan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function monthlyCompletedLoansAlternative(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        // Inisialisasi data untuk semua bulan
        $monthlyData = array_fill(0, 12, 0);
        
        // Query untuk data aktual
        $data = Permintaan::select(
                DB::raw('MONTH(tgl_permintaan) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('status', 'Digunakan')
            ->whereYear('tgl_permintaan', $year)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
        
        // Isi data aktual ke array
        foreach ($data as $item) {
            $monthlyData[$item->month - 1] = $item->total;
        }
        
        return response()->json([
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            'data' => $monthlyData,
            'year' => $year,
            'message' => 'Data permintaan selesai tahun ' . $year . ' berhasil diambil'
        ]);
    }

    public function monthlyAllStatus(Request $request)
    {
        $year = $request->input('year', date('Y'));

        // Inisialisasi data untuk semua bulan
        $result = [
            'belum_diproses' => array_fill(0, 12, 0),
            'menunggu_diambil' => array_fill(0, 12, 0),
            'digunakan' => array_fill(0, 12, 0),
            'ditolak' => array_fill(0, 12, 0),
        ];

        // Query untuk semua status
        $data = Permintaan::select(
            DB::raw('MONTH(tgl_permintaan) as month'),
            'status',
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('tgl_permintaan', $year)
        ->groupBy('month', 'status')
        ->orderBy('month', 'asc')
        ->get();

        // Isi data aktual ke array
        foreach ($data as $item) {
            $monthIndex = $item->month - 1;
            $statusKey = strtolower(str_replace(' ', '_', $item->status));
            
            if (array_key_exists($statusKey, $result)) {
                $result[$statusKey][$monthIndex] = $item->total;
            }
        }

        return response()->json([
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            'belum_diproses' => $result['belum_diproses'],
            'menunggu_diambil' => $result['menunggu_diambil'],
            'digunakan' => $result['digunakan'],
            'ditolak' => $result['ditolak'],
            'year' => $year,
            'message' => 'Data permintaan tahun ' . $year . ' berhasil diambil'
        ]);
    }

    public function availableYears()
    {
        $years = Permintaan::select(DB::raw('YEAR(tgl_permintaan) as year'))
            ->where('status', 'Digunakan')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return response()->json([
            'years' => $years->toArray(),
            'message' => 'Daftar tahun tersedia berhasil diambil'
        ]);
    }

}
