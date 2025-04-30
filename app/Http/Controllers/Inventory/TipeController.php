<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\Tipe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $tipe = Tipe::all();
    //     return response()->json($tipe);
    // }
    public function index() {
        return Tipe::with([
            'kategorimerek',
            'kategorimerek.merek',
            'kategorimerek.kategori',
        ])->get();
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
        $tipe = new Tipe();
        $kategori_merek_id = $request->input('kategori_merek_id');
        $nama_tipe = $request->input('nama_tipe');

        $inisial = strtoupper(substr($nama_tipe, 0, 1));

        $urutanTerakhir = Tipe::where('kode_tipe', 'like', $inisial . '%')
                            ->count();

        $kode_tipe = $inisial . $urutanTerakhir;

        if ($nama_tipe == 'NO TIPE') {
            $kode_tipe = 'XX';
        }

        $tipe->kode_tipe = $kode_tipe;
        $tipe->kategori_merek_id = $kategori_merek_id;
        $tipe->nama_tipe = $nama_tipe;
        $tipe->save();

        return response()->json($tipe);
    }
    // public function store(Request $request)
    // {
    //     $tipe = new Tipe();
    //     $kategori_merek_id = $request->input('kategori_merek_id');
    //     $nama_tipe = $request->input('nama_tipe');

    //     $inisial = strtoupper(substr($nama_tipe, 0, 1));

    //     $urutanTerakhir = Tipe::where('nama_tipe', 'like', $inisial . '%')
    //                         ->count();
        
    //     $kode_tipe = $urutanTerakhir;

    //     if ($nama_tipe == 'NO TIPE') {
    //         $kode_tipe = 'XX';
    //     }

    //     $tipe->kode_tipe = $kode_tipe;
    //     $tipe->kategori_merek_id = $kategori_merek_id;
    //     $tipe->nama_tipe = $nama_tipe;
    //     $tipe->save();

    //     return response()->json($tipe);
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tipe = Tipe::find($id);
        if ($tipe) {
            return response()->json($tipe);
        } else {
            return response()->json(['message' => 'Tipe tidak ditemukan'], 404);
        }
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {
    //     $tipe = Tipe::find($id);
    //     if ($tipe) {
    //         return response()->json($tipe);
    //     } else {
    //         return response()->json(['message' => 'Tipe tidak ditemukan'], 404);
    //     }
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $tipe = Tipe::find($id);

        if (!$tipe) {
            return response()->json(['message' => 'Tipe tidak ditemukan'], 404);
        }

        $kategori_merek_id = $request->input('kategori_merek_id');
        $nama_tipe = $request->input('nama_tipe');

        $inisial = strtoupper(substr($nama_tipe, 0, 1));

        $urutanTerakhir = Tipe::where('kode_tipe', 'like', $inisial . '%')
                            ->count();

        $kode_tipe = $inisial . $urutanTerakhir;

        if ($nama_tipe == 'NO TIPE') {
            $kode_tipe = 'XX';
        }

        $tipe->kode_tipe = $kode_tipe;
        $tipe->kategori_merek_id = $kategori_merek_id;
        $tipe->nama_tipe = $nama_tipe;
        $tipe->save();

        return response()->json($tipe);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tipe = Tipe::find($id);

        if (!$tipe) {
            return response()->json(['message' => 'Tipe tidak ditemukan'], 404);
        }

        // Hapus produk-produk yang terkait dengan tipe
        $tipe->produk()->delete();

        $tipe->delete();

        return response()->json(['message' => 'Tipe berhasil dihapus']);
    }
}
