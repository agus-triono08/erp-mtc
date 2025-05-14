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
    
    
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'jenis_id' => 'required|exists:jenis,id',
    //         'nama' => 'required',
    //         'stok_awal' => 'nullable|integer',
    //         'unit' => 'nullable|string',
    //         'harga_total' => 'nullable|numeric',
    //         'pembelian' => 'nullable|string',
    //         'sumber' => 'nullable|string',
    //         'vendor' => 'nullable|string',
    //         'fungsi' => 'nullable|string',
    //         'deskripsi' => 'nullable|string',
    //         'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //         'jadwal_perawatan' => 'nullable|numeric',
    //         'kategori_id' => 'required|exists:kategori,id',
    //         'merek_id' => 'required|exists:merek,id',
    //         'tipe_id' => 'required|exists:tipe,id',
    //         'layout_id' => 'required|exists:layouts,id',
    //         'users_id' => 'nullable|exists:users,id',
    //         'waktu_perawatan' => 'nullable|integer|min:0',
    //         'jumlah_orang_perawatan' => 'nullable|integer|min:0',
    //     ]);
    
    //     $jenis = Jenis::find($request->jenis_id);
    //     $kategori = Kategori::find($request->kategori_id);
    //     $merek = Merek::find($request->merek_id);
    //     $tipe = Tipe::find($request->tipe_id);
    //     $layout = Layout::find($request->layout_id);
    //     $user = User::find($request->users_id);
    
    //     $prefix = "{$jenis->kode_jenis}-{$kategori->kode_kategori}-{$merek->kode_merek}-{$tipe->kode_tipe}";
    //     $lastTool = Tools::where('kode', 'like', "$prefix-%")->orderByDesc('id')->first();
    //     $nextNumber = $lastTool ? (int)substr($lastTool->kode, -3) + 1 : 1;
    //     $kode = $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    
    //     $data = $request->except('kode');
    //     $data['kode'] = $kode;
    //     $data['jenis_id'] = $jenis->id;
    //     $data['waktu_perawatan'] = $request->waktu_perawatan;
    //     $data['jumlah_orang_perawatan'] = $request->jumlah_orang_perawatan;
    
    //     if ($request->hasFile('gambar')) {
    //         $file = $request->file('gambar');
    //         $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
    //         $extension = $file->getClientOriginalExtension();
    //         $path = "tools/{$filename}.{$extension}";
    //         Storage::disk('public')->put($path, file_get_contents($file));
    //         $data['gambar'] = $path;
    //     }
    
    //     $tool = Tools::create($data);
    //     $tool->stok_akhir = $tool->stok_awal;
    //     $tool->save();
    
    //     $stok = $tool->stok_awal ?? 1;
    //     $inisial_kategori = strtoupper(Str::limit(preg_replace('/[^A-Za-z]/', '', $kategori->nama_kategori), 2, ''));
    //     $kode_kategori = str_pad($tipe->id, 2, '0', STR_PAD_LEFT);
    
    //     $waktuPerNoSeri = (int) $tool->waktu_perawatan;
    //     $jumlahOrang = max((int) $tool->jumlah_orang_perawatan, 1);
    //     $jadwalPerawatan = (int) $request->jadwal_perawatan;
    
    //     // $userId = auth()->id() ?? $request->users_id;
    //     $userId = auth()->id() ?? $request->users_id ?? 1; // fallback ID 1 atau ID admin sistem
    
    //     if ($jadwalPerawatan > 0 && $waktuPerNoSeri > 0 && $jumlahOrang > 0) {
    //         $waktuPerNoSeriEffisien = ceil($waktuPerNoSeri / $jumlahOrang);
    //         $startTime = Carbon::createFromTime(8, 0);
    
    //         for ($i = 0; $i < $stok; $i++) {
    //             $nomorUrut = str_pad($i + 1, 6, '0', STR_PAD_LEFT);
    //             $no_seri = $inisial_kategori . $kode_kategori . $nomorUrut;
    
    //             [$waktuMulai, $waktuSelesai] = $this->nextWorkTime($startTime, $waktuPerNoSeriEffisien);
    
    //             $noSeriRecord = NoSeri::create([
    //                 'tools_id' => $tool->id,
    //                 'layout_id' => $layout->id,
    //                 'no_seri' => $no_seri,
    //                 'no_seri_default' => null,
    //                 'tanggal_masuk' => now(),
    //                 'harga' => $tool->harga_total ? $tool->harga_total / $stok : null,
    //             ]);
    
    //             // Buat log kondisi awal OK
    //             NoSeriLog::create([
    //                 'no_seri_id' => $noSeriRecord->id,
    //                 'old_kondisi' => null,
    //                 'new_kondisi' => 'OK',
    //                 'changed_at' => now(),
    //                 'changed_by' => $userId,
    //             ]);
    
    //             $noPerawatan = 'JP' . str_pad($i + 1, 8, '0', STR_PAD_LEFT);
    
    //             Perawatan::create([
    //                 'no_seri_id' => $noSeriRecord->id,
    //                 'users_id' => $user->id ?? null,
    //                 'no_perawatan' => $noPerawatan,
    //                 'tgl_perawatan' => $waktuMulai,
    //                 'waktu_perawatan' => gmdate('H:i:s', $waktuPerNoSeriEffisien * 60),
    //             ]);

    //             // Menghitung perawatan berdasarkan interval (misalnya setiap 1 bulan, 3 bulan, dll)
    //             $currentDate = $waktuSelesai;
    //             while ($currentDate->year == now()->year) {
    //                 $currentDate->addMonths($jadwalPerawatan); // Tambahkan interval perawatan
                    
    //                 // Buat perawatan berulang
    //                 Perawatan::create([
    //                     'no_seri_id' => $noSeriRecord->id,
    //                     'users_id' => $user->id ?? null,
    //                     'no_perawatan' => 'JP' . str_pad($i + 1, 8, '0', STR_PAD_LEFT), 
    //                     'tgl_perawatan' => $currentDate,
    //                     'waktu_perawatan' => gmdate('H:i:s', $waktuPerNoSeriEffisien * 60),
    //                 ]);
    //             }
    
    //             $startTime = $waktuSelesai;
    //         }
    //     } 
    //     // if ($jadwalPerawatan > 0 && $waktuPerNoSeri > 0 && $jumlahOrang > 0) {
    //     //     $waktuPerNoSeriEffisien = ceil($waktuPerNoSeri / $jumlahOrang);
    //     //     $startDate = Carbon::createFromTime(8, 0);
    //     //     $nowYear = $startDate->year;
        
    //     //     for ($i = 0; $i < $stok; $i++) {
    //     //         $nomorUrut = str_pad($i + 1, 6, '0', STR_PAD_LEFT);
    //     //         $no_seri = $inisial_kategori . $kode_kategori . $nomorUrut;
        
    //     //         $noSeriRecord = NoSeri::create([
    //     //             'tools_id' => $tool->id,
    //     //             'layout_id' => $layout->id,
    //     //             'no_seri' => $no_seri,
    //     //             'no_seri_default' => null,
    //     //             'tanggal_masuk' => now(),
    //     //             'harga' => $tool->harga_total ? $tool->harga_total / $stok : null,
    //     //         ]);
        
    //     //         NoSeriLog::create([
    //     //             'no_seri_id' => $noSeriRecord->id,
    //     //             'old_kondisi' => null,
    //     //             'new_kondisi' => 'OK',
    //     //             'changed_at' => now(),
    //     //             'changed_by' => $userId,
    //     //         ]);
        
    //     //         // Menghitung perawatan berdasarkan interval (misalnya setiap 1 bulan, 3 bulan, dll)
    //     //         $currentDate = $waktuSelesai;
    //     //         while ($currentDate->year == now()->year) {
    //     //             $currentDate->addMonths($intervalPerawatan); // Tambahkan interval perawatan
        
    //     //             Perawatan::create([
    //     //                 'no_seri_id' => $noSeriRecord->id,
    //     //                 'users_id' => $user->id ?? null,
    //     //                 'no_perawatan' => $noPerawatan,
    //     //                 'tgl_perawatan' => $currentDate,
    //     //                 'waktu_perawatan' => gmdate('H:i:s', $waktuPerNoSeriEffisien * 60),
    //     //             ]);
        
    //     //             $scheduleDate = $scheduleDate->addDays($jadwalPerawatan);
    //     //             $counter++;
    //     //         }
    //     //     }
    //     // }        
    //     else {
    //         for ($i = 0; $i < $stok; $i++) {
    //             $nomorUrut = str_pad($i + 1, 6, '0', STR_PAD_LEFT);
    //             $no_seri = $inisial_kategori . $kode_kategori . $nomorUrut;
    
    //             $noSeriRecord = NoSeri::create([
    //                 'tools_id' => $tool->id,
    //                 'layout_id' => $layout->id,
    //                 'no_seri' => $no_seri,
    //                 'no_seri_default' => null,
    //                 'tanggal_masuk' => now(),
    //                 'harga' => $tool->harga_total ? $tool->harga_total / $stok : null,
    //             ]);
    
    //             NoSeriLog::create([
    //                 'no_seri_id' => $noSeriRecord->id,
    //                 'old_kondisi' => null,
    //                 'new_kondisi' => 'OK',
    //                 'changed_at' => now(),
    //                 'changed_by' => $userId,
    //             ]);
    //         }
    //     }
    
    //     return response()->json($tool->load('jenis.kategori.merek.tipe'), 201);
    // } 
    
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
        $waktuPerawatan = ($request->waktu_perawatan * ($request->stok_awal ?? 1)) / ($request->jumlah_orang_perawatan ?? 1);
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
        
        if ($jadwalPerawatan > 0 && $waktuPerNoSeri > 0 && $jumlahOrang > 0) {
            // Hitung waktu perawatan dengan interval
            $waktuPerNoSeriEffisien = ceil($waktuPerNoSeri / $jumlahOrang);
            $startTime = Carbon::createFromTime(8, 0); // Mulai dari jam 8:00

            // Loop untuk stok dan penjadwalan perawatan
            for ($i = 0; $i < $stok; $i++) {
                $nomorUrut = str_pad($i + 1, 6, '0', STR_PAD_LEFT);
                $no_seri = $inisial_kategori . $kode_kategori . $nomorUrut;
                
                [$waktuMulai, $waktuSelesai] = $this->nextWorkTime($startTime, $waktuPerNoSeriEffisien);

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
                    // 'waktu_perawatan' => gmdate('H:i:s', $waktuPerNoSeriEffisien * 60),
                    'waktu_perawatan' => gmdate('H:i:s', $waktuPerNoSeri * 60),
                ]);

                // Menghitung perawatan berdasarkan interval yang dimasukkan
                // $currentDate = $waktuSelesai->copy();

                $currentDate = $waktuSelesai->copy()->addMonths($jadwalPerawatan);
                while ($currentDate->year == now()->year) {
                    // Buat perawatan berulang
                    Perawatan::create([
                        'no_seri_id' => $noSeriRecord->id,
                        'users_id' => $user->id ?? null,
                        'no_perawatan' => 'JP' . str_pad($i + 1, 8, '0', STR_PAD_LEFT), 
                        'tgl_perawatan' => $currentDate,
                        // 'waktu_perawatan' => gmdate('H:i:s', $waktuPerNoSeriEffisien * 60),
                        'waktu_perawatan' => gmdate('H:i:s', $waktuPerNoSeri * 60),
                    ]);
                    $currentDate->addMonths($jadwalPerawatan); // Tambahkan setelah pengecekan dan insert
                }                
                $startTime = $waktuSelesai; // Update waktu mulai untuk perawatan berikutnya
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
    
    // Helper method
    protected function nextWorkTime(Carbon $startTime, $durationMinutes)
    {
        $morningStart = Carbon::createFromTime(8, 0);
        $morningEnd = Carbon::createFromTime(12, 0);
        $afternoonStart = Carbon::createFromTime(13, 0);
        $afternoonEnd = Carbon::createFromTime(17, 0);
    
        if ($startTime->lt($morningStart)) {
            $startTime = $morningStart->copy();
        }
    
        if ($startTime->between($morningEnd, $afternoonStart)) {
            $startTime = $afternoonStart->copy();
        }
    
        if ($startTime->gte($afternoonEnd)) {
            $startTime = $morningStart->copy()->addDay();
        }
    
        $endTime = $startTime->copy()->addMinutes($durationMinutes);
    
        if ($startTime->lt($morningEnd) && $endTime->gt($morningEnd)) {
            $overflow = $endTime->diffInMinutes($morningEnd);
            $startTime = $afternoonStart->copy();
            $endTime = $startTime->copy()->addMinutes($overflow);
        } elseif ($startTime->lt($afternoonEnd) && $endTime->gt($afternoonEnd)) {
            $overflow = $endTime->diffInMinutes($afternoonEnd);
            $startTime = $morningStart->copy()->addDay();
            $endTime = $startTime->copy()->addMinutes($overflow);
        }
    
        return [$startTime, $endTime];
    }

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
    // public function update(Request $request, $id)
    // {
    //     $tool = Tools::findOrFail($id);

    //     // Validasi hanya untuk field yang dapat diedit
    //     $request->validate([
    //         'pembelian' => 'nullable|string',
    //         'sumber' => 'nullable|string',
    //         'vendor' => 'nullable|string',
    //         'fungsi' => 'nullable|string',
    //         'deskripsi' => 'nullable|string',
    //     ]);

    //     // Ambil data relasi jika perlu untuk menentukan kode
    //     $jenis = $tool->jenis; // Tidak perlu update jenis jika tidak berubah
    //     $kategori = $tool->kategori; // Tidak perlu update kategori jika tidak berubah
    //     $merek = $tool->merek; // Tidak perlu update merek jika tidak berubah
    //     $tipe = $tool->tipe; // Tidak perlu update tipe jika tidak berubah

    //     // Buat ulang kode berdasarkan relasi jika terjadi perubahan
    //     $prefix = "{$jenis->kode_jenis}-{$kategori->kode_kategori}-{$merek->kode_merek}-{$tipe->kode_tipe}";

    //     // Cek apakah prefix-nya berubah
    //     $currentPrefix = implode('-', array_slice(explode('-', $tool->kode), 0, 4));
    //     if ($prefix !== $currentPrefix) {
    //         $lastTool = Tools::where('kode', 'like', "$prefix-%")->orderByDesc('id')->first();
    //         $nextNumber = $lastTool ? (int)substr($lastTool->kode, -3) + 1 : 1;
    //         $kode = $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    //     } else {
    //         $kode = $tool->kode; // kode tetap
    //     }

    //     // Ambil data yang dapat diubah
    //     $data = $request->only(['pembelian', 'sumber', 'vendor', 'fungsi', 'deskripsi']);
    //     $data['kode'] = $kode;

    //     if ($request->hasFile('gambar')) {
    //         // Hapus gambar lama jika ada
    //         if ($tool->gambar) {
    //             Storage::disk('public')->delete($tool->gambar);
    //         }

    //         // Upload gambar baru
    //         $file = $request->file('gambar');
    //         $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
    //         $extension = $file->getClientOriginalExtension();
    //         $path = "tools/{$filename}.{$extension}";
    //         Storage::disk('public')->put($path, file_get_contents($file));
    //         $data['gambar'] = $path;
    //     }

    //     // Update data alat
    //     $tool->update($data);

    //     // Mengembalikan response dengan data lengkap termasuk relasi
    //     return response()->json($tool->load('jenis.kategori.merek.tipe'));
    // }
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
        ]);

        // Ambil data yang diizinkan untuk diubah
        $data = $request->only(['pembelian', 'unit', 'sumber', 'vendor', 'fungsi', 'deskripsi']);

        // Update data alat
        $tool->update($data);

        // Mengembalikan response dengan data lengkap termasuk relasi
        return response()->json($tool->load('jenis.kategori.merek.tipe', 'layout'));
    }

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
}


