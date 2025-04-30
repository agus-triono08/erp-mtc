<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\NoSeri;
use App\Models\Inventory\Rusak;

class RusakController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Rusak::with([
            'noSeri.tools'
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
            $noSeri->kondisi = $request->kondisi; // Update kondisi
            $noSeri->save();
        }

        return response()->json([
            'message' => 'Data perbaikan berhasil disimpan.',
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
}
