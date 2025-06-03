<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\Permintaan;
use App\Models\Inventory\PermintaanLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class PermintaanLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $logs = PermintaanLog::with('permintaan.noSeri.tools', 'permintaan.users.divisi', 'permintaan.users.Karyawan')
                ->orderBy('changed_at', 'desc')
                ->get();

            if ($logs->isEmpty()) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            $logs->transform(function ($log) {
                $log->changed_at = Carbon::parse($log->changed_at)->format('Y-m-d');
                return $log;
            });

            return response()->json($logs);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan'], 500);
        }
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
}
