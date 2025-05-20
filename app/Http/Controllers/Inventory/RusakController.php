<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\NoSeri;
use App\Models\Inventory\Rusak;
use App\Models\Inventory\Tools;
use App\Models\Inventory\Musnah;
use App\Models\Inventory\RusakActivity;
use App\Models\Inventory\NoSeriLog;
use App\Models\User;
use Carbon\Carbon;


class RusakController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $all = Rusak::with([
            'noSeri.tools',
            'noSeri.layout'
        ])
        ->orderBy('updated_at', 'desc')
        ->get();

        $byStatusBaru = Rusak::with([
            'noSeri.tools',
            'noSeri.layout',
        ])
        ->where('status', 'Belum')
        ->orderBy('updated_at', 'desc')
        ->get();

        return response()->json([
            'all' => $all,
            'byStatusBaru' => $byStatusBaru,
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
            'tgl_kerusakan' => 'required|date',
            'detail_kerusakan' => 'required|string',
            'status' => 'nullable|string',
            'kondisi' => 'required|string',
        ]);

        // Ambil nomor urutan terakhir
        $lastRusak = Rusak::orderBy('id', 'desc')->first();
        $lastNumber = 0;

        if ($lastRusak && preg_match('/KR(\d{8})/', $lastRusak->no_kerusakan, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $newNumber = $lastNumber + 1;
        $no_kerusakan = 'KR' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);

        $rusak = Rusak::create([
            'no_seri_id' => $request->no_seri_id,
            'no_kerusakan' => $no_kerusakan,
            'tgl_kerusakan' => $request->tgl_kerusakan,
            'detail_kerusakan' => $request->detail_kerusakan,
            'kondisi' => $request->kondisi,
            // 'status' => $request->status ?? 'Pending',
            // 'users_id' => auth()->id(),
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
                    'changed_by'  => auth()->id() ?? 1, // pastikan user sudah login
                ]);
            }

            $noSeri->kondisi = $newKondisi; // Update kondisi
            $noSeri->save();
        }

        return response()->json([
            'message' => 'Data kerusakan berhasil disimpan.',
            'data' => $rusak
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
        $kerusakan = Rusak::with([
            'noSeri.tools',
            'noSeri.layout',
            'rusak_activity' => function($q) {
                $q->orderBy('changed_at', 'desc');
            }
        ])
        ->orderBy('updated_at', 'desc')
        ->findOrFail($id);
        // Get the collection of related models
        $rusakActivities = $kerusakan->rusak_activity;
        // Use the map method on the collection
        $rusakActivities = $rusakActivities->map(function ($item) {
            $item->changed_at = \Carbon\Carbon::parse($item->changed_at)->format('Y-m-d');
                // Ambil nama-nama PIC
            if ($item->pic) {
                $picIds = explode(',', $item->pic); // pecah jadi array ID
                $users = \App\Models\User::whereIn('id', $picIds)->pluck('nama', 'id');
                // Urutkan nama sesuai urutan ID dalam pic and join into a string
                $item->nama_pic = collect($picIds)->map(function ($id) use ($users) {
                    return $users[$id] ?? 'Tidak Ditemukan';
                })->join(', ');
            } else {
                $item->nama_pic = '';
            }
            return $item;
        });
        // Update the rusak_activity attribute of the kerusakan model
        $kerusakan->rusak_activity = $rusakActivities;

        return response()->json($kerusakan);
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

    public function getRusak($noSeri)
    {
        $noseri = NoSeri::where('no_seri', $noSeri)->first();

        if (!$noseri) {
            return response()->json(['message' => 'No Seri not found'], 404);
        }

        // Cek kondisi
        // if ($noseri->kondisi !== 'Rusak') {
        //     return response()->json(['message' => 'No Seri dalam kondisi baik'], 200);
        // }

        $kerusakan = Rusak::with('noSeri.tools')
            ->where('no_seri_id', $noseri->id)
            ->get();

        if ($kerusakan->isEmpty()) {
            return response()->json(['message' => 'Kerusakan not found'], 404);
        }

        return response()->json($kerusakan);
    }

    public function addActivity(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:rusak,id',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i',
            'detail_kerusakan' => 'required|string',
            'status' => 'nullable|string',
            'alasan_penolakan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'kondisi' => 'nullable|string',
            'pic' => 'nullable|array',
            'pic.*' => 'integer|exists:users,id',
        ]);

        $kerusakan = Rusak::findOrFail($request->id);

        // Simpan kondisi lama sebelum update
        $oldKondisi = $kerusakan->noSeri->kondisi;

        // Buat Rusak Activity baru
        RusakActivity::create([
            'rusak_id' => $kerusakan->id,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'detail_kerusakan' => $request->detail_kerusakan,
            'status' => 'Menunggu Persetujuan Atasan',
            'kondisi' => 'Rusak',
            'pic' => implode(',', $request->pic ?? []),
            'changed_at' => Carbon::today()->format('Y-m-d'), // Ubah format tanggal
        ]);
    }

    public function pemusnahanDiterima(Request $request) 
    {
        $request->validate([
            'id' => 'required|exists:rusak_activity,id', // ID milik RusakActivity
            'status' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $aktivitas = RusakActivity::findOrFail($request->id); // Ambil aktivitas
        $kerusakan = $aktivitas->rusak; // Ambil relasi ke Rusak

        if (!$kerusakan) {
            return response()->json(['message' => 'Data kerusakan tidak ditemukan'], 404);
        }

        $oldKondisi = $kerusakan->noSeri->kondisi;

        $kerusakan->update([
            'kondisi' => 'Musnah',
            'status' => 'Proses',
        ]);

        $aktivitas->update([
            'kondisi' => 'Musnah',
            'status' => 'Diterima',
            'catatan' => $request->catatan,
            'changed_at' => Carbon::today()->format('Y-m-d'),
        ]);

        $kerusakan->noSeri->update([
            'kondisi' => 'Musnah',
        ]);

        $tool = Tools::find($kerusakan->noSeri->tools_id);
        if ($tool) {
            $tool->update([
                'stok_akhir' => $tool->stok_akhir - 1,
            ]);
        }

        NoSeriLog::create([
            'no_seri_id' => $kerusakan->no_seri_id,
            'old_kondisi' => $oldKondisi,
            'new_kondisi' => 'Musnah',
            'changed_at' => Carbon::today()->format('Y-m-d'),
            'changed_by' => auth()->id() ?? 2,
        ]);

        // Penomoran otomatis untuk PM...
        $lastMusnah = Musnah::orderBy('id', 'desc')->first();
        $lastNumber = 0;

        if ($lastMusnah && preg_match('/PM(\d{8})/', $lastMusnah->no_pemusnahan, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $newNumber = $lastNumber + 1;
        $no_pemusnahan = 'PM' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);

        Musnah::create([
            'no_seri_id' => $kerusakan->no_seri_id,
            'kondisi' => 'Musnah',
            'status' => 'Proses',
            'no_pemusnahan' => $no_pemusnahan,
            'users_id' => auth()->id() ?? 2,
            'tgl_pemusnahan' => Carbon::today()->format('Y-m-d'),
        ]);

        return response()->json(['message' => 'Data berhasil diperbarui']);
    }

    public function pemusnahanDitolak(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:rusak_activity,id', // ID milik RusakActivity
            'status' => 'nullable|string',
            'alasan_penolakan' => 'nullable|string',
        ]);

        $aktivitas = RusakActivity::findOrFail($request->id); // Ambil aktivitas
        $kerusakan = $aktivitas->rusak; // Ambil relasi ke Rusak

        if (!$kerusakan) {
            return response()->json(['message' => 'Data kerusakan tidak ditemukan'], 404);
        }

        $oldKondisi = $kerusakan->noSeri->kondisi;

        $aktivitas->update([
            'kondisi' => 'Rusak',
            'status' => 'Ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
            'changed_at' => Carbon::today()->format('Y-m-d'),
        ]);

        return response()->json(['message' => 'Data berhasil ditolak']);
    }

    public function countBelum()
    {
        try {
            // Menghitung jumlah peminjaman dengan status 'Belum Diproses'
            $count = rusak::where('status', 'Belum')->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Jumlah Rusak dengan status Belum',
                'count' => $count
    
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }
}
