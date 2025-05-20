<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\Perawatan;
use App\Models\Inventory\NoSeri;
use App\Models\User;
use App\Models\Inventory\NoSeriLog;
use App\Models\Inventory\Tools;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class PerawatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $perawatan = Perawatan::with('noSeri.tools')->get();
    //     return response()->json($perawatan);
    // }
    public function index(Request $request)
    {
        $bulanSekarang = date('m');
        $tahunSekarang = date('Y');
        $bulanBesok = date('m', strtotime('+1 month'));
        $tahunBesok = date('Y', strtotime('+1 month'));
        
        if ($request->has('all')) {
            $perawatan = Perawatan::with('noSeri.tools', 'users')
                ->orderBy('updated_at', 'desc') // Urutkan berdasarkan aktivitas terakhir
                ->get();
        } elseif ($request->has('bulan_besok')) {
            $perawatan = Perawatan::with('noSeri.tools.jenis.kategori.merek.tipe', 'users')
                ->whereMonth('tgl_perawatan', $bulanBesok)
                ->whereYear('tgl_perawatan', $tahunBesok)
                ->get();
        } else {
            $perawatan = Perawatan::with('noSeri.tools', 'users:id,nama,username')
                ->whereMonth('tgl_perawatan', $bulanSekarang)
                ->whereYear('tgl_perawatan', $tahunSekarang)
                ->get();
        }

        return response()->json($perawatan);
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
    public function store(Request $request)
    {
        // Normalisasi input agar string kosong jadi null
        $request->merge([
            'waktu_perawatan' => $request->waktu_perawatan !== '' ? $request->waktu_perawatan : null,
        ]);

        // Validasi input: waktu dalam format "HH:MM"
        $validated = $request->validate([
            'tgl_perawatan' => 'required|date',
            'waktu_perawatan' => 'nullable|date_format:H:i',
            'no_seri_id' => 'required|exists:no_seri,id',
        ]);

        // Simpan waktu asli dalam format TIME untuk tabel Perawatan
        $waktuTime = $validated['waktu_perawatan'] 
            ? $validated['waktu_perawatan'] . ':00' 
            : null;

        // Ambil NoSeri dan Tool terkait
        $noSeri = NoSeri::find($validated['no_seri_id']);
        $tool = Tools::find($noSeri->tools_id);

        if (!$tool) {
            return response()->json(['message' => 'Tool tidak ditemukan.'], 404);
        }

        // Generate nomor perawatan
        $noPerawatan = 'JP' . str_pad($noSeri->id, 8, '0', STR_PAD_LEFT);

        // Cek duplikat
        $exists = Perawatan::where('no_perawatan', $noPerawatan)
            ->where('tgl_perawatan', $validated['tgl_perawatan'])
            ->first();

        if ($exists) {
            return response()->json([
                'message' => 'Perawatan untuk alat ini pada tanggal tersebut sudah tercatat.',
            ], 409);
        }

        // Simpan data perawatan
        $perawatan = Perawatan::create([
            'tgl_perawatan' => $validated['tgl_perawatan'],
            'waktu_perawatan' => $waktuTime,
            'no_seri_id' => $noSeri->id,
            'no_perawatan' => $noPerawatan,
            'nama_tool' => $tool->nama,
        ]);

        // Tambahkan waktu ke tools.waktu_perawatan (dalam satuan menit misalnya)
        if (!is_null($validated['waktu_perawatan'])) {
            // Ubah waktu H:i menjadi total menit
            [$jam, $menit] = explode(':', $validated['waktu_perawatan']);
            $totalMenitBaru = ((int) $jam * 60) + (int) $menit;

            // Tambahkan ke existing waktu perawatan (integer)
            $tool->waktu_perawatan = ($tool->waktu_perawatan ?? 0) + $totalMenitBaru;
            $tool->save();
        }

        return response()->json([
            'message' => 'Data perawatan berhasil disimpan.',
            'data' => $perawatan
        ], 201);
    }
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'tgl_perawatan' => 'required|date',
    //         'no_seri_id' => 'required|exists:no_seri,id',            
    //     ]);

    //     $noSeri = NoSeri::find($request->no_seri_id);
    //     $tool = Tools::find($noSeri->tools_id);

    //     $noPerawatan = 'JP' . str_pad($request->no_seri_id, 8, '0', STR_PAD_LEFT);

    //     Perawatan::create([
    //         'tgl_perawatan' => $request->tgl_perawatan,
    //         'no_seri_id' => $request->no_seri_id,
    //         'no_perawatan' => $noPerawatan,
    //         'nama_tool' => $tool->nama,
    //     ]);

    //     return response()->json([
    //         'message' => 'Data perawatan berhasil disimpan',
    //         'no_perawatan' => $noPerawatan,
    //         'nama_tool' => $tool->nama,
    //         'tgl_perawatan' => $request->tgl_perawatan,
    //         'no_seri_id' => $noSeri->id,
    //     ]);
    // }

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

    public function statusPelaksanaan(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:perawatan,id',
            'status' => 'nullable|string',
            'pic' => 'required|array',
            'pic.*' => 'integer|exists:users,id',
        ]);

        $perawatan = Perawatan::findOrFail($request->id);

        if (strtolower($request->status) !== 'belum di lakukan perawatan') {
            $perawatan->update([
                'status' => 'Dalam Proses Perawatan',
                'pic' => implode(',', $request->pic),
                // 'users_id' => implode(',', $request->pic),
                // 'pic' => $request->pic,
                'tgl_mulai_perawatan' => now()->format('Y-m-d'),
                'waktu_mulai' => Carbon::now('Asia/Jakarta')->format('H:i:s')
            ]);
             // simpan PIC ke tabel pivot
            $perawatan->users()->sync($request->pic);   // <─ di sinilah multiple ID disimpan
        } else {
            $perawatan->update([
                'status' => 'Belum Dilakukan Perawatan',
            ]);
            // kosongkan PIC jika perlu
            $perawatan->users()->detach();
        }
    
        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function statusSelesai(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:perawatan,id',
            'detail_perawatan' => 'nullable|string',
            'kondisi' => 'nullable|string',
        ]);

        $perawatan = Perawatan::findOrFail($request->id);

        $oldKondisi = $perawatan->noSeri->kondisi;

        if (strtolower($request->status) !== 'belum di lakukan perawatan') {
            $perawatan->update([
                'status' => 'Selesai Perawatan',
                'detail_perawatan' => $request->detail_perawatan,
                'tgl_selesai_perawatan' => now()->format('Y-m-d'),
                'waktu_selesai' => Carbon::now('Asia/Jakarta')->format('H:i:s')
            ]);
        } else {
            $perawatan->update([
                'status' => 'Dalam Proses Perawatan',
            ]);
        }

        $perawatan->noSeri->update([
            'kondisi' => $request->kondisi,
        ]);

        NoSeriLog::create([
            'no_seri_id' => $perawatan->no_seri_id,
            'old_kondisi' => $oldKondisi,
            'new_kondisi' => $request->kondisi,
            'changed_at' => Carbon::today()->format('Y-m-d'),
            'changed_by' => auth()->id() ?? 1,
        ]);

        return response()->json(['message' => 'Data berhasil diperbarui']);
    }

    public function countBelum()
    {
        try {
            // Menghitung jumlah peminjaman dengan status 'Belum Diproses'
            $count = Perawatan::where('status', 'Belum Dilakukan Perawatan')->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Jumlah Perawatan yang Belum Dilakukan Perawatan',
                'count' => $count
    
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProgressData(): JsonResponse
    {
        // Hitung total semua perawatan
        $totalPerawatan = Perawatan::count();
        
        // Jika tidak ada data perawatan, set semua nilai ke 0
        if ($totalPerawatan === 0) {
            $progressData = [
                'belum_dilakukan' => 0,
                'dalam_proses' => 0,
                'selesai' => 0,
                'total' => 0
            ];
        } else {
            // Hitung jumlah per status
            $belumDilakukan = Perawatan::where('status', 'Belum Dilakukan Perawatan')->count();
            $dalamProses = Perawatan::where('status', 'Dalam Proses Perawatan')->count();
            $selesai = Perawatan::where('status', 'Selesai Perawatan')->count();
            
            // Hitung persentase
            $progressData = [
                'belum_dilakukan' => round(($belumDilakukan / $totalPerawatan) * 100, 2),
                'dalam_proses' => round(($dalamProses / $totalPerawatan) * 100, 2),
                'selesai' => round(($selesai / $totalPerawatan) * 100, 2),
                'total' => $totalPerawatan,
                'counts' => [
                    'belum_dilakukan' => $belumDilakukan,
                    'dalam_proses' => $dalamProses,
                    'selesai' => $selesai
                ]
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $progressData,
            'message' => 'Data progress perawatan berhasil diambil'
        ]);
    }
}
