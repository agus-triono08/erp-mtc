<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\NoSeri;
use App\Models\User;
use App\Models\Inventory\Peminjaman;
use App\Models\Inventory\PeminjamanLog;
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
        $user = auth()->user();

        // Cek apakah user divisi MTC dan jabatan Supervisor / Manager
        $isMtcSupervisorOrManager = strtolower(optional($user->Divisi)->kode) === 'mtc' &&
            in_array(strtolower(optional($user->Karyawan)->jabatan), ['supervisor', 'manager']);

        $baseQuery = Peminjaman::with([
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
            'tgl_pinjam' => 'required|date',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_pinjam',
            'detail_peminjaman' => 'required|string',
            'status' => 'nullable|string',
            'total' => 'required|integer|min:1',
        ]);

        $user = auth()->user();

        if(!$user) {
            return response()->json([
                'message' => 'Anda harus login untuk membuat peminjaman',
            ], 401);
        }

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
            'detail_peminjaman' => $request->detail_peminjaman,
            'status' => $status,
            'status_kondisi' => $status,
            'total' => $request->total,
            'users_id' => $user->id,
            // 'users_id' => auth()->id(),
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
        $availableNoSeri = $tools->noSeri()
                                ->where('kondisi', 'OK')
                                ->where(function ($query) {
                                    $query->whereNull('kondisi_after')
                                        ->orWhere('kondisi_after', 'Selesai');
                                })
                                ->whereNotIn('id', $usedNoSeriIds)
                                ->take($request->total)
                                ->get();

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

        if ($status === 'Belum Diproses') {
            PeminjamanLog::create([
                'peminjaman_id' => $peminjaman->id,
                'old_status' => null,
                'new_status' => $status,
                'changed_at' => now(),
                'changed_by' => $user->id,
                // 'changed_by' => auth()->id(),
            ]);
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
        $peminjaman = Peminjaman::with('tools.noSeri', 'users.divisi', 'users.Karyawan')->find($id);
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
                    $query->select('no_seri.id', 'no_seri.no_seri', 'no_seri.kondisi', 'no_seri.kondisi_after', 'no_seri.tanggal_kondisi', 'no_seri.reject_reason', 'no_seri.tgl_pengecekan', 'no_seri.deskripsi_cek');
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


    // Peminjaman Belum Diproses
    public function countBelumDiproses()
    {
        try {
            $count = Peminjaman::where('status', 'Belum Diproses')->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Jumlah peminjaman dengan status Belum Diproses',
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
            $peminjaman = Peminjaman::with(['users.Karyawan', 'tools', 'noSeri'])
                ->where('status', 'Belum Diproses')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Daftar peminjaman belum diproses',
                'data' => $peminjaman
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Bisa gunakan ini buat chart Dasboard
    public function monthlyCompletedLoans(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        // Inisialisasi data untuk semua bulan
        $monthlyData = array_fill(0, 12, 0);
        
        // Query untuk data aktual
        $data = Peminjaman::select(
                DB::raw('MONTH(tgl_pinjam) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('status', 'Selesai')
            ->whereYear('tgl_pinjam', $year)
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
            'message' => 'Data peminjaman selesai tahun ' . $year . ' berhasil diambil'
        ]);
    }

    public function monthlyAllStatus(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        // Inisialisasi data untuk semua bulan
        $result = [
            'belum_diproses' => array_fill(0, 12, 0),
            'menunggu_diambil' => array_fill(0, 12, 0),
            'dipinjam' => array_fill(0, 12, 0),
            'ditolak' => array_fill(0, 12, 0),
            'selesai' => array_fill(0, 12, 0),
        ];
        
        // Query untuk semua status
        $data = Peminjaman::select(
                DB::raw('MONTH(tgl_pinjam) as month'),
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('tgl_pinjam', $year)
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
            'dipinjam' => $result['dipinjam'],
            'ditolak' => $result['ditolak'],
            'selesai' => $result['selesai'],
            'year' => $year,
            'message' => 'Data peminjaman tahun ' . $year . ' berhasil diambil'
        ]);
    }

    public function availableYears()
    {
        $years = Peminjaman::select(DB::raw('YEAR(tgl_pinjam) as year'))
            ->where('status', 'Selesai')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return response()->json([
            'years' => $years->toArray(),
            'message' => 'Daftar tahun tersedia berhasil diambil'
        ]);
    }

    // Alternatif: Jika Anda ingin data dalam format yang berbeda
    public function monthlyCompletedLoansAlternative()
    {
        $currentYear = date('Y');
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $data = Peminjaman::select(
                DB::raw('MONTH(tgl_pinjam) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('status', 'Selesai')
            ->whereYear('tgl_pinjam', $currentYear)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Inisialisasi array dengan 0 untuk semua bulan
        $monthlyCounts = array_fill(0, 12, 0);

        foreach ($data as $item) {
            $monthlyCounts[$item->month - 1] = $item->total;
        }

        return response()->json([
            'labels' => $months,
            'data' => $monthlyCounts,
            'year' => $currentYear,
            'message' => 'Data peminjaman selesai tahun ' . $currentYear . ' berhasil diambil'
        ]);
    }

    // Peminjaman Lewat Dari Proses tanggal Peminjaman
    public function countOverdueLoans()
    {
        try {
            $today = now()->format('Y-m-d');
            $count = Peminjaman::where('status', 'Dipinjam')
                ->whereDate('tgl_kembali', '<', $today)
                ->count();
                
            return response()->json([
                'success' => true,
                'message' => 'Jumlah peminjaman yang sudah melewati tanggal kembali',
                'count' => $count
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function listOverDate()
    {
        try {
            $today = now()->format('Y-m-d');
            $peminjaman = Peminjaman::with(['users.Karyawan', 'tools', 'noSeri'])
                ->where('status', 'Dipinjam')
                ->whereDate('tgl_kembali', '<', $today)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar tanggal peminjaman lewat',
                'data' => $peminjaman
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }


}
