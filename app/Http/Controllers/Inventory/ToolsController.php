<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\Tools;
use App\Models\Inventory\Jenis;
use App\Models\Inventory\Kategori;
use App\Models\Inventory\Merek;
use App\Models\Inventory\KategoriMerek;
use App\Models\Inventory\Tipe;
use App\Models\Inventory\NoSeri;
use App\Models\Inventory\NoSeriLog;
use App\Models\Inventory\Perawatan;
use App\Models\Layout;
use App\Models\User;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ToolsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $tools = Tools::with([
            'jenis.kategori.merek.tipe'
        ])->whereHas('jenis', function ($q) use ($request) {
            if ($request->jenis_id) {
                $q->where('id', $request->jenis_id);
            }
    
            if ($request->kategori_id) {
                $q->whereHas('kategori', function ($q2) use ($request) {
                    $q2->where('id', $request->kategori_id);
                });
            }
    
            if ($request->merek_id) {
                $q->whereHas('kategori.merek', function ($q3) use ($request) {
                    $q3->where('id', $request->merek_id);
                });
            }
    
            if ($request->tipe_id) {
                $q->whereHas('kategori.merek.tipe', function ($q4) use ($request) {
                    $q4->where('id', $request->tipe_id);
                });
            }
        })->orderBy('updated_at', 'desc')->get();
    
        return response()->json($tools);
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
        $request->validate([
            'jenis_id' => 'required|exists:jenis,id',
            'nama' => 'required',
            'stok_awal' => 'nullable|integer',
            'unit' => 'nullable|string',
            'harga_total' => 'nullable|numeric',
            'pembelian' => 'nullable|string',
            'sumber' => 'nullable|string',
            'vendor' => 'nullable|string',
            'fungsi' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'jadwal_perawatan' => 'nullable|numeric',
            'kategori_id' => 'required|exists:kategori,id',
            'merek_id' => 'required|exists:merek,id',
            'tipe_id' => 'required|exists:tipe,id',
            'layout_id' => 'required|exists:layouts,id',
            'users_id' => 'nullable|exists:users,id',
            'waktu_perawatan' => 'nullable|integer|min:0',
            'jumlah_orang_perawatan' => 'nullable|integer|min:0',
            'jadwal_mulai_perawatan' => 'nullable|date', // Validasi baru
        ]);
        
        $jenis = Jenis::find($request->jenis_id);
        $kategori = Kategori::find($request->kategori_id);
        $merek = Merek::find($request->merek_id);
        $tipe = Tipe::find($request->tipe_id);
        $layout = Layout::find($request->layout_id);
        $user = User::find($request->users_id);
        
        // Kode untuk penentuan kode alat tetap seperti sebelumnya
        $prefix = "{$jenis->kode_jenis}-{$kategori->kode_kategori}-{$merek->kode_merek}-{$tipe->kode_tipe}";
        $lastTool = Tools::where('kode', 'like', "$prefix-%")->orderByDesc('id')->first();
        $nextNumber = $lastTool ? (int)substr($lastTool->kode, -3) + 1 : 1;
        $kode = $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        $data = $request->except('kode');
        $data['kode'] = $kode;
        $data['jenis_id'] = $jenis->id;
        if ($request->jumlah_orang_perawatan > 0) {
            $waktuPerawatan = ($request->waktu_perawatan * ($request->stok_awal ?? 1)) / ($request->jumlah_orang_perawatan ?? 1);
        } else {
            // Atur nilai default untuk $waktuPerawatan jika $request->jumlah_orang_perawatan bernilai 0
            $waktuPerawatan = $request->waktu_perawatan * ($request->stok_awal ?? 1);
        }
        // $waktuPerawatan = ($request->waktu_perawatan * ($request->stok_awal ?? 1)) / ($request->jumlah_orang_perawatan ?? 1);
        $data['waktu_perawatan'] = $waktuPerawatan; // Menggunakan hasil perhitungan waktu perawatan
        // $data['waktu_perawatan'] = $request->waktu_perawatan;
        // $data['waktu_perawatan'] = ($request->waktu_perawatan * ($request->stok_awal ?? 1)) / ($request->jumlah_orang_perawatan ?? 1);
        $data['jumlah_orang_perawatan'] = $request->jumlah_orang_perawatan;

        // Menangani upload gambar (jika ada)
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();
            $path = "tools/{$filename}.{$extension}";
            Storage::disk('public')->put($path, file_get_contents($file));
            $data['gambar'] = $path;
        }
        
        // Membuat alat baru
        $tool = Tools::create($data);
        $tool->stok_akhir = $tool->stok_awal;
        $tool->save();
        
        // Proses stok dan penentuan kategori
        $stok = $tool->stok_awal ?? 1;
        $inisial_kategori = strtoupper(Str::limit(preg_replace('/[^A-Za-z]/', '', $kategori->nama_kategori), 2, ''));
        $kode_kategori = str_pad($tipe->id, 2, '0', STR_PAD_LEFT);
        
        // $waktuPerNoSeri = (int) $tool->waktu_perawatan;
        $waktuPerNoSeri = $request->waktu_perawatan;
        $jumlahOrang = max((int) $tool->jumlah_orang_perawatan, 1);
        $jadwalPerawatan = (int) $request->jadwal_perawatan;  // Interval perawatan yang dimasukkan oleh user (dalam bulan)
        
        // ID pengguna yang akan membuat perawatan
        $userId = auth()->id() ?? $request->users_id ?? 1; 
        
        // if ($jadwalPerawatan > 0 && $waktuPerNoSeri > 0 && $jumlahOrang > 0) {
        //     // Hitung waktu perawatan dengan interval
        //     // $waktuPerNoSeriEffisien = ceil($waktuPerNoSeri / $jumlahOrang);
        //     if ($jumlahOrang > 0) {
        //         // $waktuPerNoSeriEffisien = ceil($waktuPerNoSeri / $jumlahOrang);
        //         $waktuPerNoSeriEffisien = $waktuPerNoSeri;
        //     } else {
        //         // Atur nilai default untuk $waktuPerNoSeriEffisien jika $jumlahOrang bernilai 0
        //         $waktuPerNoSeriEffisien = $waktuPerNoSeri;
        //     }
        if ($jadwalPerawatan > 0 && $waktuPerNoSeri > 0 && $jumlahOrang > 0) {
            // Hitung waktu perawatan dengan interval
            $waktuPerNoSeriEffisien = $jumlahOrang > 0 
                ? $waktuPerNoSeri 
                : $waktuPerNoSeri;

            // $startTime = Carbon::createFromTime(8, 0); // Mulai dari jam 8:00
            // Gunakan jadwal_mulai_perawatan jika ada, atau default jam 8:00
            $startTime = $request->jadwal_mulai_perawatan 
            ? Carbon::parse($request->jadwal_mulai_perawatan)
            : Carbon::createFromTime(8, 0);

            for ($i = 0; $i < $stok; $i++) {
                $nomorUrut = str_pad($i + 1, 6, '0', STR_PAD_LEFT);
                $no_seri = $inisial_kategori . $kode_kategori . $nomorUrut;

                [$waktuMulai, $waktuSelesai] = $this->advanceWorkTime($startTime, $waktuPerNoSeriEffisien );

                // Buat record NoSeri
                $noSeriRecord = NoSeri::create([
                    'tools_id' => $tool->id,
                    'layout_id' => $layout->id,
                    'no_seri' => $no_seri,
                    'no_seri_default' => null,
                    'tanggal_masuk' => now(),
                    'harga' => $tool->harga_total ? $tool->harga_total / $stok : null,
                ]);

                // Buat log kondisi awal OK
                NoSeriLog::create([
                    'no_seri_id' => $noSeriRecord->id,
                    'old_kondisi' => null,
                    'new_kondisi' => 'OK',
                    'changed_at' => now(),
                    'changed_by' => $userId,
                ]);

                // Buat perawatan pertama
                $noPerawatan = 'JP' . str_pad($i + 1, 8, '0', STR_PAD_LEFT);

                Perawatan::create([
                    'no_seri_id' => $noSeriRecord->id,
                    'users_id' => $user->id ?? null,
                    'no_perawatan' => $noPerawatan,
                    'tgl_perawatan' => $waktuMulai,
                    'waktu_perawatan' => gmdate('H:i:s', $waktuPerNoSeri * 60),
                ]);

                // Buat perawatan berulang selama tahun ini
                $currentDate = $waktuSelesai->copy()->addMonths($jadwalPerawatan);
                while ($currentDate->year == now()->year) {
                    Perawatan::create([
                        'no_seri_id' => $noSeriRecord->id,
                        'users_id' => $user->id ?? null,
                        'no_perawatan' => 'JP' . str_pad($i + 1, 8, '0', STR_PAD_LEFT),
                        'tgl_perawatan' => $currentDate,
                        'waktu_perawatan' => gmdate('H:i:s', $waktuPerNoSeri * 60),
                    ]);
                    $currentDate->addMonths($jadwalPerawatan);
                }

                // Update start time untuk alat berikutnya
                $startTime = $waktuSelesai->copy();
            }
        } else {
            // Proses jika tidak ada perawatan terjadwal
            for ($i = 0; $i < $stok; $i++) {
                $nomorUrut = str_pad($i + 1, 6, '0', STR_PAD_LEFT);
                $no_seri = $inisial_kategori . $kode_kategori . $nomorUrut;
                
                // Buat record NoSeri
                $noSeriRecord = NoSeri::create([
                    'tools_id' => $tool->id,
                    'layout_id' => $layout->id,
                    'no_seri' => $no_seri,
                    'no_seri_default' => null,
                    'tanggal_masuk' => now(),
                    'harga' => $tool->harga_total ? $tool->harga_total / $stok : null,
                ]);
                
                // Buat log kondisi awal OK
                NoSeriLog::create([
                    'no_seri_id' => $noSeriRecord->id,
                    'old_kondisi' => null,
                    'new_kondisi' => 'OK',
                    'changed_at' => now(),
                    'changed_by' => $userId,
                ]);
            }
        }
        
        return response()->json($tool->load('jenis.kategori.merek.tipe'), 201);
    }

    protected function advanceWorkTime(Carbon $currentStart, $durationMinutes)
    {
        $baseDate = $currentStart->copy()->startOfDay(); // tanggal hari itu

        $workPeriods = [
            ['start' => $baseDate->copy()->setTime(8, 0), 'end' => $baseDate->copy()->setTime(12, 0)],
            ['start' => $baseDate->copy()->setTime(13, 0), 'end' => $baseDate->copy()->setTime(17, 0)],
        ];

        while (true) {
            foreach ($workPeriods as $period) {
                $start = $period['start']->copy()->setDateFrom($currentStart);
                $end = $period['end']->copy()->setDateFrom($currentStart);

                if ($currentStart->lt($start)) {
                    $currentStart = $start->copy();
                }

                if ($currentStart->between($start, $end, true)) {
                    $available = $currentStart->diffInMinutes($end, false);

                    if ($available >= $durationMinutes) {
                        $finish = $currentStart->copy()->addMinutes($durationMinutes);
                        return [$currentStart->copy(), $finish];
                    } else {
                        $durationMinutes -= $available;
                        $currentStart = $end->copy(); // move to next session
                    }
                }
            }

            // Tambah hari, mulai lagi jam 08:00
            $currentStart = $currentStart->copy()->addDay()->setTime(8, 0);
        }
    }
    
    // Helper method
    // protected function nextWorkTime(Carbon $startTime, $durationMinutes)
    // {
    //     $morningStart = Carbon::createFromTime(8, 0);
    //     $morningEnd = Carbon::createFromTime(12, 0);
    //     $afternoonStart = Carbon::createFromTime(13, 0);
    //     $afternoonEnd = Carbon::createFromTime(17, 0);
    
    //     if ($startTime->lt($morningStart)) {
    //         $startTime = $morningStart->copy();
    //     }
    
    //     if ($startTime->between($morningEnd, $afternoonStart)) {
    //         $startTime = $afternoonStart->copy();
    //     }
    
    //     if ($startTime->gte($afternoonEnd)) {
    //         $startTime = $morningStart->copy()->addDay();
    //     }
    
    //     $endTime = $startTime->copy()->addMinutes($durationMinutes);
    
    //     if ($startTime->lt($morningEnd) && $endTime->gt($morningEnd)) {
    //         $overflow = $endTime->diffInMinutes($morningEnd);
    //         $startTime = $afternoonStart->copy();
    //         $endTime = $startTime->copy()->addMinutes($overflow);
    //     } elseif ($startTime->lt($afternoonEnd) && $endTime->gt($afternoonEnd)) {
    //         $overflow = $endTime->diffInMinutes($afternoonEnd);
    //         $startTime = $morningStart->copy()->addDay();
    //         $endTime = $startTime->copy()->addMinutes($overflow);
    //     }
    
    //     return [$startTime, $endTime];
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function show($id)
    {
        $tool = Tools::with([
            'jenis.kategori.merek.tipe',
            'layout' // Tambahkan relasi layout
        ])->findOrFail($id);
    
        return response()->json($tool);
    }     

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
        $tool = Tools::findOrFail($id);

        // Validasi hanya untuk field yang dapat diedit
        $request->validate([
            'pembelian' => 'nullable|string',
            'unit' => 'nullable|string',
            'sumber' => 'nullable|string',
            'vendor' => 'nullable|string',
            'fungsi' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'jadwal_perawatan' => 'nullable|numeric',
            'waktu_perawatan' => 'nullable|integer|min:0',
            'jumlah_orang_perawatan' => 'nullable|integer|min:0',
            'jadwal_mulai_perawatan' => 'nullable|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'note_perubahan_jadwal' => 'nullable|string',
        ]);

        // Ambil data yang diizinkan untuk diubah
        $data = $request->only(['pembelian', 'unit', 'sumber', 'vendor', 'fungsi', 'deskripsi', 
                            'jadwal_perawatan', 'waktu_perawatan', 'jumlah_orang_perawatan', 'note_perubahan_jadwal']);

        // Menangani upload gambar (jika ada)
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($tool->gambar && Storage::disk('public')->exists($tool->gambar)) {
                Storage::disk('public')->delete($tool->gambar);
            }
            
            $file = $request->file('gambar');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();
            $path = "tools/{$filename}.{$extension}";
            Storage::disk('public')->put($path, file_get_contents($file));
            $data['gambar'] = $path;
        }

        // Update data alat
        $tool->update($data);

        // Jika ada perubahan pada jadwal perawatan atau waktu perawatan
        if ($request->has(['jadwal_perawatan', 'waktu_perawatan', 'jumlah_orang_perawatan'])) {
            $this->updateMaintenanceSchedule($tool, $request);
        }

        // Mengembalikan response dengan data lengkap termasuk relasi
        return response()->json($tool->load('jenis.kategori.merek.tipe', 'layout'));
    }

    protected function updateMaintenanceSchedule($tool, $request)
    {
        // Jika tidak ada jadwal perawatan, tidak perlu melakukan apa-apa
        if (empty($request->jadwal_perawatan) || empty($request->waktu_perawatan)) {
            return;
        }

        $jadwalPerawatan = (int) $request->jadwal_perawatan;
        $waktuPerNoSeri = (int) $request->waktu_perawatan;
        $jumlahOrang = max((int) $request->jumlah_orang_perawatan, 1);
        
        // Gunakan jadwal_mulai_perawatan jika ada, atau gunakan tanggal sekarang
        $startTime = $request->jadwal_mulai_perawatan 
            ? Carbon::parse($request->jadwal_mulai_perawatan)
            : now();

        // ID pengguna yang akan membuat perawatan
        $userId = auth()->id() ?? $request->users_id ?? 1;

        // Dapatkan semua nomor seri yang terkait dengan alat ini
        $noSeriRecords = NoSeri::where('tools_id', $tool->id)->get();

        foreach ($noSeriRecords as $noSeriRecord) {
            // Hapus semua perawatan yang belum dilakukan (masih akan datang)
            Perawatan::where('no_seri_id', $noSeriRecord->id)
                    ->where('tgl_perawatan', '>', now())
                    ->delete();

            // Hitung waktu perawatan
            $waktuPerNoSeriEffisien = $jumlahOrang > 0 ? $waktuPerNoSeri : $waktuPerNoSeri;

            // Buat perawatan pertama
            $noPerawatan = 'JP' . str_pad($noSeriRecord->id, 8, '0', STR_PAD_LEFT);

            // Hitung waktu mulai dan selesai
            [$waktuMulai, $waktuSelesai] = $this->advanceWorkTime($startTime, $waktuPerNoSeriEffisien);

            Perawatan::create([
                'no_seri_id' => $noSeriRecord->id,
                'users_id' => $userId,
                'no_perawatan' => $noPerawatan,
                'tgl_perawatan' => $waktuMulai,
                'waktu_perawatan' => gmdate('H:i:s', $waktuPerNoSeri * 60),
            ]);

            // Buat perawatan berulang selama tahun ini
            $currentDate = $waktuSelesai->copy()->addMonths($jadwalPerawatan);
            while ($currentDate->year == now()->year) {
                Perawatan::create([
                    'no_seri_id' => $noSeriRecord->id,
                    'users_id' => $userId,
                    'no_perawatan' => $noPerawatan,
                    'tgl_perawatan' => $currentDate,
                    'waktu_perawatan' => gmdate('H:i:s', $waktuPerNoSeri * 60),
                ]);
                $currentDate->addMonths($jadwalPerawatan);
            }

            // Update start time untuk alat berikutnya
            $startTime = $waktuSelesai->copy();
        }
    }
    // public function update(Request $request, $id)
    // {
    //     $tool = Tools::findOrFail($id);

    //     // Validasi hanya untuk field yang dapat diedit
    //     $request->validate([
    //         'pembelian' => 'nullable|string',
    //         'unit' => 'nullable|string',
    //         'sumber' => 'nullable|string',
    //         'vendor' => 'nullable|string',
    //         'fungsi' => 'nullable|string',
    //         'deskripsi' => 'nullable|string',
    //         'jadwal_perawatan' => 'nullable|numeric',
    //         'waktu_perawatan' => 'nullable|integer|min:0',
    //         'jumlah_orang_perawatan' => 'nullable|integer|min:0',
    //         'jadwal_mulai_perawatan' => 'nullable|date', // Validasi baru
    //     ]);

    //     // Ambil data yang diizinkan untuk diubah
    //     $data = $request->only(['pembelian', 'unit', 'sumber', 'vendor', 'fungsi', 'deskripsi']);

    //     // Update data alat
    //     $tool->update($data);

    //     // Mengembalikan response dengan data lengkap termasuk relasi
    //     return response()->json($tool->load('jenis.kategori.merek.tipe', 'layout'));
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Ambil data tools yang akan dihapus
        $tools = Tools::find($id);

        // Validasi apakah data tools ditemukan
        if (!$tools) {
            return response()->json(['error' => 'Data tidak ditemukan.'], 404);
        }

        // Hapus file gambar jika ada
        if ($tools->gambar) {
            Storage::delete('public/images/' . $tools->gambar);
        }

        // Hapus data tools
        $tools->delete();

        // Kembalikan pesan sukses
        return response()->json(['message' => 'Data berhasil dihapus.']);
    }

    public function getNoSeriByTool($toolId)
    {
        return NoSeri::where('tools_id', $toolId)
            ->select('id', 'no_seri')
            ->get();
    }

    public function apiLowStockTools()
    {
        $lowStockTools = Tools::where('stok_akhir', '<=', 1)
            ->orderBy('stok_akhir', 'asc')
            ->with(['jenis', 'kategori', 'tipe'])
            ->get();

        $totalLowStock = $lowStockTools->count();

        return response()->json([
            'success' => true,
            'data' => $lowStockTools,
            'total_low_stock' => $totalLowStock,
            'message' => 'Data tools dengan stok rendah berhasil diambil'
        ]);
    }

    public function listLowStockTools()
    {
        try {
            $stok = Tools::with(['noSeri'])
                ->where('stok_akhir', '<=', 1)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Daftar stok yang kurang sama dengan 1',
                'data' => $stok
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }
}


