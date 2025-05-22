<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\NoSeri;
use App\Models\Inventory\NoSeriLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class NoSeriLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $logs = NoSeriLog::with(['noSeri.tools', 'noSeri.layout'])
                ->orderBy('changed_at', 'desc')
                ->get();

            if ($logs->isEmpty()) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            // Transformasi data untuk menampilkan deskripsi_cek sesuai perubahan
            $logs->transform(function ($log) {
                return [
                    'id' => $log->id,
                    'no_seri_id' => $log->no_seri_id,
                    'no_seri' => $log->noSeri->no_seri ?? null,
                    'nama_tool' => $log->noSeri->tools->nama ?? null,
                    'layout' => $log->noSeri->layout->nama ?? null,
                    'old_kondisi' => $log->old_kondisi,
                    'new_kondisi' => $log->new_kondisi,
                    'changed_at' => Carbon::parse($log->changed_at)->format('Y-m-d H:i:s'),
                    'changed_by' => $log->changed_by,
                    'deskripsi_cek' => ($log->old_kondisi !== $log->new_kondisi) ? $log->noSeri->deskripsi_cek : null,
                    'tanggal_pengecekan' => $log->noSeri->tgl_pengecekan ?? null,
                    'kondisi_sekarang' => $log->noSeri->kondisi ?? null,
                    'status_perubahan' => $log->noSeri->status_perubahan ?? null
                ];
            });

            return response()->json($logs);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // public function index()
    // {
    //     try {
    //         $logs = NoSeriLog::with('noSeri.tools')
    //             ->orderBy('changed_at', 'desc')->get();

    //         if ($logs->isEmpty()) {
    //             return response()->json(['message' => 'Data tidak ditemukan'], 404);
    //         }

    //         // Ubah format tanggal pada kolom changed_at
    //         $logs->transform(function ($log) {
    //             $log->changed_at = Carbon::parse($log->changed_at)->format('Y-m-d');
    //             return $log;
    //         });

    //         return response()->json($logs);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Terjadi kesalahan'], 500);
    //     }
    // }

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
        //
    }

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
        $noseri = NoSeri::findOrFail($id);
        $oldKondisi = $noseri->kondisi;
        $newKondisi = $request->input('kondisi');

        if ($oldKondisi !== $newKondisi) {
            // update Kondisi
            $noseri->update(['kondisi' => $newKondisi]);

            // Simpan Log Perubahan
            NoSeriLog::create([
                'no_seri_id' => $noseri->id,
                'old_kondisi' => $oldKondisi,
                'new_kondisi' => $newKondisi,
                'changed_at' => now(),
            ]);
        }

        return response ()->json(['message' => 'Kondisi Diperbaharui']);
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

    public function getLogs($noSeriId)
    {
        $logs = NoSeriLog::where('no_seri_id', $noSeriId)
                        ->orderBy('changed_at', 'desc')
                        ->get();

        return response()->json($logs);
    }
}
