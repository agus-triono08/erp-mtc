<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\NoSeri;
use App\Models\Inventory\NoSeriLog;
use App\Models\User;
// use App\Models\Inventory\Tools;
use App\Models\Inventory\Error;
use App\Models\Inventory\Rusak;
use App\Models\Inventory\ErrorActivity;
use Carbon\Carbon;

class ErrorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $all = Error::with([
            'noSeri.tools',
            'noSeri.layout',
        ])
        ->orderBy('updated_at', 'desc')
        ->get();

        $byStatusBaru = Error::with([
            'noSeri.tools',
            'noSeri.layout',
        ])
        ->where('status', 'Belum')
        ->orderBy('updated_at', 'desc')
        ->get();

        $byStatusProses = Error::with([
            'noSeri.tools',
            'noSeri.layout',
        ])
        ->where('status', 'Proses')
        ->orderBy('updated_at', 'desc')
        ->get();

        $byStatusSelesai = Error::with([
            'noSeri.tools',
            'noSeri.layout',
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
            'tgl_perbaikan' => 'required|date',
            'detail_perbaikan' => 'required|string',
            'status' => 'nullable|string',
            'kondisi' => 'required|string',
        ]);

        // Ambil nomor urutan terakhir
        $lastError = Error::orderBy('id', 'desc')->first();
        $lastNumber = 0;

        if ($lastError && preg_match('/PB(\d{8})/', $lastError->no_perbaikan, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $newNumber = $lastNumber + 1;
        $no_perbaikan = 'PB' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);

        $error = Error::create([
            'no_seri_id' => $request->no_seri_id,
            'no_perbaikan' => $no_perbaikan,
            'tgl_perbaikan' => $request->tgl_perbaikan,
            'detail_perbaikan' => $request->detail_perbaikan,
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

            $noSeri->kondisi = $newKondisi;
            $noSeri->save();
        }

        return response()->json([
            'message' => 'Data perbaikan berhasil disimpan.',
            'data' => $error
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
        $perbaikan = Error::with([
            'noSeri.tools',
            'noSeri.layout',
            'error_activity' => function($q) {
                $q->orderBy('changed_at', 'desc');
            }
        ])
        ->orderBy('updated_at', 'desc')
        ->findOrFail($id);
        // Get the collection of related models
        $errorActivities = $perbaikan->error_activity;
        // Use the map method on the collection
        $errorActivities = $errorActivities->map(function ($item) {
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
        // Update the error_activity attribute of the perbaikan model
        $perbaikan->error_activity = $errorActivities;
        return response()->json($perbaikan);
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

    public function getError($noSeri)
    {
        $noseri = NoSeri::where('no_seri', $noSeri)->first();

        if (!$noseri) {
            return response()->json(['message' => 'No Seri not found'], 404);
        }

        // Cek kondisi
        // if ($noseri->kondisi !== 'Error') {
        //     return response()->json(['message' => 'No Seri dalam kondisi baik'], 200);
        // }

        $perbaikan = Error::with('noSeri.tools')
            ->where('no_seri_id', $noseri->id)
            ->get();

        if ($perbaikan->isEmpty()) {
            return response()->json(['message' => 'Perbaikan not found'], 404);
        }

        return response()->json($perbaikan);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:error,id',
            'status' => 'required|string|max:255',            
        ]);

        $perbaikan = Error::findOrFail($request->id);
        $perbaikan->update([
            'status' => $request->status,
            'tgl_selesai' => Carbon::today(), // hanya tanggal, tanpa waktu
        ]);

        return response()->json(['message' => 'Status berhasil diubah'], 200);
    }

    public function addActivity(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:error,id',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i',
            'detail_perbaikan' => 'required|string',
            'kondisi' => 'required|string',
            'pic' => 'nullable|array',
            'pic.*' => 'integer|exists:users,id',
            'layout' => 'nullable|integer|exists:layouts,id',
        ]);

        $perbaikan = Error::findOrFail($request->id);

        // Simpan kondisi lama sebelum update
        $oldKondisi = $perbaikan->noSeri->kondisi;

        // Update kondisi Error
        $perbaikan->update([
            'kondisi' => $request->kondisi,
        ]);

        // Tandai selesai jika kondisi bukan "Error"
        if (strtolower($request->kondisi) !== 'error') {
            $perbaikan->update([
                'status' => 'Selesai',
                // 'tgl_selesai' => now(),
            ]);
        } else {
            $perbaikan->update([
                'status' => 'Proses',
            ]);
        }

        // Buat Error Activity baru
        ErrorActivity::create([
            'error_id' => $perbaikan->id,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'detail_perbaikan' => $request->detail_perbaikan,
            'kondisi' => $request->kondisi,
            'pic' => implode(',', $request->pic ?? []),
            'changed_at' => Carbon::today()->format('Y-m-d'), // Ubah format tanggal
        ]);

        // Update kondisi NoSeri
        $perbaikan->noSeri->update([
            'kondisi' => $request->kondisi,
        ]);

        // Jika kondisi "Rusak" dan layout tersedia, pindahkan layout
        if (strtolower($request->kondisi) === 'rusak' && $request->layout) {
            $perbaikan->noSeri->update([
                'layout_id' => $request->layout,
            ]);
        }

        // Buat log perubahan kondisi
        NoSeriLog::create([
            'no_seri_id'  => $perbaikan->no_seri_id,
            'old_kondisi' => $oldKondisi,
            'new_kondisi' => $request->kondisi,
            'changed_at'  => Carbon::today(),
            'changed_by'  => auth()->id() ?? 1,
        ]);

        if (strtolower($request->kondisi) === 'rusak') {
            $lastRusak = Rusak::orderBy('id', 'desc')->first();
            $lastNumber = 0;

            if ($lastRusak && preg_match('/KR(\d{8})/', $lastRusak->no_kerusakan, $matches)) {
                $lastNumber = (int) $matches[1];
            }

            $newNumber = $lastNumber + 1;
            $no_kerusakan = 'KR' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);

            Rusak::create([
                'no_seri_id' => $perbaikan->no_seri_id,
                'kondisi' => 'Rusak',
                'users_id' => auth()->id() ?? 1,
                'no_kerusakan' => $no_kerusakan,
                'detail_kerusakan' => $request->detail_perbaikan,
                'tgl_kerusakan' => Carbon::today()->format('Y-m-d'),
            ]);
        }

        return response()->json(['message' => 'Status No Seri, Error, dan Error Activity berhasil diperbarui.']);
    }

    public function countBelum()
    {
        try {
            // Menghitung jumlah peminjaman dengan status 'Belum Diproses'
            $count = Error::where('status', 'Belum')->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Jumlah Perbaikan dengan status Belum',
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
