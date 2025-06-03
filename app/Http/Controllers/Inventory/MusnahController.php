<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\NoSeri;
use App\Models\Inventory\Musnah;
use App\Models\Inventory\MusnahActivity;
use App\Models\User;
use App\Models\Inventory\Tools;
use App\Models\Inventory\NoSeriLog;
use Illuminate\Support\Facades\Storage;

class MusnahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        $all = Musnah::with([
            'noSeri.tools',
            'noSeri.layout',
            'musnah_activity'
        ])
        ->orderBy('updated_at', 'desc')
        ->get();

        $byStatusProses = Musnah::with([
            'noSeri.tools',
            'noSeri.layout',
            'musnah_activity'
        ])
        ->where('status', 'Proses')
        ->orderBy('updated_at', 'desc')
        ->get();

        $byStatusSelesai = Musnah::with([
            'noSeri.tools',
            'noSeri.layout',
            'musnah_activity'
        ])
        ->where('status', 'Selesai')
        ->orderBy('updated_at', 'desc')
        ->get();

        return response()->json([
            'all' => $all,
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
            'tgl_pemusnahan' => 'required|date',
            'detail_pemusnahan' => 'required|string',
            'status' => 'nullable|string',
            'kondisi' => 'required|string',
        ]);

        // Ambil nomor urutan terakhir
        $lastMusnah = Musnah::orderBy('id', 'desc')->first();
        $lastNumber = 0;

        if ($lastMusnah && preg_match('/PM(\d{8})/', $lastMusnah->no_pemusnahan, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $newNumber = $lastNumber + 1;
        $no_pemusnahan = 'PM' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);

        $musnah = Musnah::create([
            'no_seri_id' => $request->no_seri_id,
            'no_pemusnahan' => $no_pemusnahan,
            'tgl_pemusnahan' => $request->tgl_pemusnahan,
            'detail_pemusnahan' => $request->detail_pemusnahan,
            'kondisi' => $request->kondisi,
            // 'status' => $request->status ?? 'Pending',
            // 'users_id' => (),
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
                    'changed_by'  => auth()->id ?? 1, // pastikan user sudah login
                ]);
            }

            $noSeri->kondisi = $newKondisi;
            $noSeri->save();

            // Kurangi stok_akhir dan harga_total pada tabel tools jika kondisi musnah
            if ($noSeri->tools_id && strtolower($newKondisi) === 'musnah') {
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
            'message' => 'Data pemusnahan berhasil disimpan.',
            'data' => $musnah
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
        $pemusnahan = Musnah::with([
            'noSeri.tools',
            'noSeri.layout',
            'users',
            'musnah_activity' => function($q) {
                $q->orderBy('changed_at', 'desc');
            }
        ])
        ->orderBy('updated_at', 'desc')
        ->findOrFail($id);
        // Get the collection of related models
        $musnahActivities = $pemusnahan->musnah_activity;
        // Use the map method on the collection
        $musnahActivities = $musnahActivities->map(function ($item) {
            $item->changed_at = \Carbon\Carbon::parse($item->changed_at)->format('Y-m-d');
            return $item;
        });
        // Update the musnah_activity attribute of the pemus$pemusnahan model
        $pemusnahan->musnah_activity = $musnahActivities;

        return response()->json($pemusnahan);
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

    public function getMusnah($noSeri)
    {
        $noseri = NoSeri::where('no_seri', $noSeri)->first();

        if (!$noseri) {
            return response()->json(['message' => 'No Seri not found'], 404);
        }

        // Cek kondisi
        // if ($noseri->kondisi !== 'Musnah') {
        //     return response()->json(['message' => 'No Seri dalam kondisi baik'], 200);
        // }

        $pemusnahan = Musnah::with('noSeri.tools')
            ->where('no_seri_id', $noseri->id)
            ->get();

        if ($pemusnahan->isEmpty()) {
            return response()->json(['message' => 'Kerusakan not found'], 404);
        }

        return response()->json($pemusnahan);
    }

    public function addActivity(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:musnah,id',
            'dokumen_pemusnahan' => 'required|array', // Ini sudah benar, karena kita akan mengirim array file
            'dokumen_pemusnahan.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048', // Validasi tiap file
            'berita_acara' => 'nullable|file|mimes:pdf',
            'detail_pemusnahan' => 'nullable|string',
        ]);

        $pemusnahan = Musnah::findOrFail($request->id);

        $pemusnahan->update([
            'status' => 'Selesai',
        ]);

        // Proses file dokumen_pemusnahan yang merupakan array
        $dokumenPaths = [];
        if ($request->hasFile('dokumen_pemusnahan')) {
            foreach ($request->file('dokumen_pemusnahan') as $file) {
                // Simpan setiap file ke folder storage
                $dokumenPaths[] = $file->store('pemusnahan/dokumen', 'public');
            }
        }

        // Simpan file berita acara, jika ada
        $baPath = null;
        if ($request->hasFile('berita_acara')) {
            $baPath = $request->file('berita_acara')->store('pemusnahan/ba', 'public');
        }

        // Simpan aktivitas pemusnahan ke database
        MusnahActivity::create([
            'musnah_id' => $pemusnahan->id,
            'dokumen_pemusnahan' => json_encode($dokumenPaths), // Menyimpan array file paths dalam format JSON
            'berita_acara' => $baPath,
            'detail_pemusnahan' => $request->detail_pemusnahan,
            'status' => 'Selesai',
            'changed_at' => now()->format('Y-m-d'),
        ]);

        return response()->json(['message' => 'Berhasil menyimpan aktivitas pemusnahan.']);
    }

    public function countSelesai()
    {
        try {
            // Menghitung jumlah peminjaman dengan status 'Belum Diproses'
            $count = Musnah::where('status', 'Proses')->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Jumlah Pemusnahan dengan status Selesai',
                'count' => $count
    
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function listSelesai()
    {
        try {
            $pemusnahan = Musnah::with(['noSeri.tools'])
                ->where('status', 'Proses')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Daftar pemusnahan belum selesai',
                'data' => $pemusnahan
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }
}
