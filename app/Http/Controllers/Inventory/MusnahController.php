<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\NoSeri;
use App\Models\Inventory\Musnah;
use App\Models\Inventory\Tools;

class MusnahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Musnah::with([
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
            // 'users_id' => auth()->id(),
        ]);

        // Update kondisi pada tabel no_seri
        $noSeri = NoSeri::find($request->no_seri_id);
        if ($noSeri) {
            $noSeri->kondisi = $request->kondisi;
            $noSeri->save();

            // Kurangi stok_akhir dan harga_total pada tabel tools jika kondisi musnah
            if ($noSeri->tools_id && strtolower($request->kondisi) === 'musnah') {
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
}
