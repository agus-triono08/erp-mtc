<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\NoSeri;
use App\Models\Inventory\Hilang;
use App\Models\Inventory\Tools;

class HilangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kehilangan = Hilang::with('noSeri.tools')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($kehilangan);
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
            'tgl_kehilangan' => 'required|date',
            'detail_hilang' => 'required|string',
            'status' => 'nullable|string',
            'kondisi' => 'required|string',
        ]);

        // Ambil nomor urutan terakhir
        $lastHilang = Hilang::orderBy('id', 'desc')->first();
        $lastNumber = 0;

        if ($lastHilang && preg_match('/KH(\d{8})/', $lastHilang->no_kehilangan, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $newNumber = $lastNumber + 1;
        $no_kehilangan = 'KH' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);

        $hilang = Hilang::create([
            'no_seri_id' => $request->no_seri_id,
            'no_kehilangan' => $no_kehilangan,
            'tgl_kehilangan' => $request->tgl_kehilangan,
            'detail_hilang' => $request->detail_hilang,
            'kondisi' => $request->kondisi,
            // 'status' => $request->status ?? 'Pending',
            // 'users_id' => auth()->id(),
        ]);

        // Update kondisi pada tabel no_seri
        $noSeri = NoSeri::find($request->no_seri_id);
        if ($noSeri) {
            $noSeri->kondisi = $request->kondisi; // Update kondisi
            $noSeri->save();

            // Kurangi stok_akhir dan harga_total pada tabel tools jika kondisi hilang
            if ($noSeri->tools_id && strtolower($request->kondisi) === 'hilang') {
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
            'message' => 'Data perbaikan berhasil disimpan.',
            'data' => $hilang
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

    public function getHilang($noSeri)
    {
        $noseri = NoSeri::where('no_seri', $noSeri)->first();

        if (!$noseri) {
            return response()->json(['message' => 'No Seri not found'], 404);
        }

        // Cek kondisi
        // if ($noseri->kondisi !== 'Rusak') {
        //     return response()->json(['message' => 'No Seri dalam kondisi baik'], 200);
        // }

        $kehilangan = Hilang::with('noSeri.tools')
            ->where('no_seri_id', $noseri->id)
            ->get();

        if ($kehilangan->isEmpty()) {
            return response()->json(['message' => 'Kehilangan not found'], 404);
        }

        return response()->json($kehilangan);
    }
}
