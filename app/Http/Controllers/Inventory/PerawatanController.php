<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\Perawatan;
use App\Models\Inventory\NoSeri;
use App\Models\Inventory\Tools;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerawatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $perawatan = Perawatan::with('noSeri.tools')->get();
    //     return response()->json($perawatan);
    // }
    public function index(Request $request)
    {
        $bulanSekarang = date('m');
        $tahunSekarang = date('Y');
        $bulanBesok = date('m', strtotime('+1 month'));
        $tahunBesok = date('Y', strtotime('+1 month'));
        
        if ($request->has('all')) {
            $perawatan = Perawatan::with('noSeri.tools')
                ->orderBy('updated_at', 'desc') // Urutkan berdasarkan aktivitas terakhir
                ->get();
        } elseif ($request->has('bulan_besok')) {
            $perawatan = Perawatan::with('noSeri.tools')
                ->whereMonth('tgl_perawatan', $bulanBesok)
                ->whereYear('tgl_perawatan', $tahunBesok)
                ->get();
        } else {
            $perawatan = Perawatan::with('noSeri.tools')
                ->whereMonth('tgl_perawatan', $bulanSekarang)
                ->whereYear('tgl_perawatan', $tahunSekarang)
                ->get();
        }

        return response()->json($perawatan);
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
        // Validasi awal
        $validated = $request->validate([
            'tgl_perawatan' => 'required|date',
            'no_seri_id' => 'required|exists:no_seri,id',
        ]);

        // Ambil NoSeri dan Tool terkait
        $noSeri = NoSeri::find($validated['no_seri_id']);
        if (!$noSeri) {
            return response()->json(['message' => 'No seri tidak ditemukan.'], 404);
        }

        $tool = Tools::find($noSeri->tools_id);
        if (!$tool) {
            return response()->json(['message' => 'Tool tidak ditemukan.'], 404);
        }

        // Generate nomor perawatan
        $noPerawatan = 'JP' . str_pad($noSeri->id, 8, '0', STR_PAD_LEFT);

        // Cek apakah perawatan ini sudah ada (optional: berdasarkan no_perawatan atau kombinasi)
        $exists = Perawatan::where('no_perawatan', $noPerawatan)
            ->where('tgl_perawatan', $validated['tgl_perawatan'])
            ->first();

        if ($exists) {
            return response()->json([
                'message' => 'Perawatan untuk alat ini pada tanggal tersebut sudah tercatat.',
            ], 409);
        }

        // Simpan data perawatan
        $perawatan = Perawatan::create([
            'tgl_perawatan' => $validated['tgl_perawatan'],
            'no_seri_id' => $noSeri->id,
            'no_perawatan' => $noPerawatan,
            'nama_tool' => $tool->nama,
        ]);

        return response()->json([
            'message' => 'Data perawatan berhasil disimpan.',
            'data' => $perawatan
        ], 201);
    }
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'tgl_perawatan' => 'required|date',
    //         'no_seri_id' => 'required|exists:no_seri,id',            
    //     ]);

    //     $noSeri = NoSeri::find($request->no_seri_id);
    //     $tool = Tools::find($noSeri->tools_id);

    //     $noPerawatan = 'JP' . str_pad($request->no_seri_id, 8, '0', STR_PAD_LEFT);

    //     Perawatan::create([
    //         'tgl_perawatan' => $request->tgl_perawatan,
    //         'no_seri_id' => $request->no_seri_id,
    //         'no_perawatan' => $noPerawatan,
    //         'nama_tool' => $tool->nama,
    //     ]);

    //     return response()->json([
    //         'message' => 'Data perawatan berhasil disimpan',
    //         'no_perawatan' => $noPerawatan,
    //         'nama_tool' => $tool->nama,
    //         'tgl_perawatan' => $request->tgl_perawatan,
    //         'no_seri_id' => $noSeri->id,
    //     ]);
    // }

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
