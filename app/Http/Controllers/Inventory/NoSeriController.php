<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\NoSeri;
use App\Models\Inventory\Tools;
use App\Models\Layout;
use App\Models\Inventory\Perawatan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoSeriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $noseri = NoSeri::with('tools', 'layout')
                        ->orderBy('updated_at', 'desc') // urutkan dari yang terbaru
                        ->get();

        return response()->json($noseri);
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
    public function store(Request $request)
    {
        $request->validate([
            'tools_id' => 'required|exists:tools,id',
            'layout_id' => 'required|exists:layouts,id',
            'no_seri_default' => 'nullable|string',
            'harga' => 'required|numeric|min:1',
            'kondisi' => 'required|string',
            'stok_awal' => 'required|integer|min:1',
            'jadwal_perawatan' => 'nullable|numeric',
            'users_id' => 'nullable|exists:users,id', // untuk PIC perawatan jika ada
        ]);

        $tool = Tools::findOrFail($request->tools_id);
        $layout = Layout::findOrFail($request->layout_id);
        $stok = $request->stok_awal;
        $harga_per_unit = $request->harga;

        $noSeriList = [];

        for ($i = 0; $i < $stok; $i++) {
            // Buat no_seri
            $prefix = strtoupper(substr($tool->nama, 0, 2));
            $random8 = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $no_seri = $prefix . $random8;

            $noSeri = NoSeri::create([
                'tools_id' => $tool->id,
                'layout_id' => $layout->id,
                'no_seri' => $no_seri,
                'no_seri_default' => $request->no_seri_default,
                'harga' => $harga_per_unit,
                'tanggal_masuk' => now(),
                'tanggal_kondisi' => null,
                'kondisi' => $request->kondisi,
            ]);

            // Buat jadwal perawatan (default 12x, tiap interval bulan)
            $interval = (int) $request->jadwal_perawatan ?? 1;
            $jumlahPerawatan = 12;

            for ($j = 0; $j < $jumlahPerawatan; $j++) {
                $noPerawatan = 'JP' . str_pad($j + 1, 8, '0', STR_PAD_LEFT);
                Perawatan::create([
                    'no_seri_id' => $noSeri->id,
                    'users_id' => $request->users_id ?? null,
                    'no_perawatan' => $noPerawatan,
                    'tgl_perawatan' => now()->addMonths($j * $interval),
                    'kondisi' => $request->kondisi,
                ]);
            }

            $noSeriList[] = $noSeri;
        }

        // Update stok dan total harga alat
        $tool->stok_awal += $stok;
        $tool->stok_akhir += $stok;
        $tool->harga_total += $stok * $harga_per_unit;
        $tool->save();

        return response()->json([
            'message' => 'No seri dan jadwal perawatan berhasil disimpan',
            'stok_awal_baru' => $tool->stok_awal,
            'stok_akhir_baru' => $tool->stok_akhir,
            'harga_total_baru' => $tool->harga_total,
            'no_seri' => $noSeriList
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
        $noseri = NoSeri::with('layout', 'tools')->find($id);
        if ($noseri) {
            return response()->json($noseri);
        } else {
            return response()->json(['message' => 'NoSeri not found'], 404);
        }
    }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $tools_id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($tools_id)
    // {
    //     $noseri = NoSeri::where('tools_id', $tools_id)->get();
    //     if ($noseri->isEmpty()) {
    //         return response()->json(['message' => 'Data tidak ditemukan'], 404);
    //     }
    //     return response()->json($noseri);
    // }

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
    //     $noseri = NoSeri::findorFail($id);

    //     $request->validate([
    //         'no_seri' => 'nullable|string',
    //         'no_seri_default' => 'nullable|string',
    //         'tanggal_masuk' => 'nullable|date',
    //         'harga' => 'nullable|numeric',
    //         'kondisi' => 'nullable|string',
    //         'layout_id' => 'nullable|exists:layouts,id',
    //     ]);

    //     $data = $request->only(['no_seri', 'no_seri_default', 'tanggal_masuk', 'harga', 'kondisi', 'layout_id']);

    //     $noseri->update($data);

    //     return response()->json(['message' => 'Data berhasil diupdate'], 200);
    // }

    public function update(Request $request, $id)
    {
        $noseri = NoSeri::find($id);

        if (!$noseri) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Log data yang diterima
        \Log::info('Data yang diterima untuk update:', $request->all());

        $validatedData = $request->validate([
            'no_seri' => 'nullable|string',
            'no_seri_default' => 'nullable|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'harga' => 'nullable|numeric',
            'kondisi' => 'nullable|string',
            'layout_id' => 'nullable|exists:layouts,id',
        ]);

        $noseri->fill($validatedData);
        $noseri->save();

        return response()->json([
            'message' => 'Data berhasil diperbarui',
            'data' => $noseri
        ]);
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

    public function getNoSeri($kodeAlat)
    {
        $tools = Tools::where('kode', $kodeAlat)->first();

        if (!$tools) {
            return response()->json(['message' => 'Tool not found'], 404);
        }

        // Tambahkan relasi layout
        $noseri = NoSeri::with('layout')->where('tools_id', $tools->id)->get();

        if ($noseri->isEmpty()) {
            return response()->json(['message' => 'NoSeri not found'], 404);
        }

        return response()->json($noseri);
    }
}
