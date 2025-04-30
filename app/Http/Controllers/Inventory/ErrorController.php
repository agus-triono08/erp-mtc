<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\NoSeri;
// use App\Models\Inventory\Tools;
use App\Models\Inventory\Error;

class ErrorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Error::with([
            'noSeri.tools',
        ])->get();
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
            $noSeri->kondisi = $request->kondisi; // Update kondisi
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

}
