<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\KategoriMerek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KategoriMerekController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kategorimerek = KategoriMerek::all();
        return response()->json($kategorimerek);
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
        $kategorimerek = new KategoriMerek();
        $kategorimerek->merek_id = $request->merek_id;
        $kategorimerek->kategori_id = $request->kategori_id;
        $kategorimerek->save();
        return response()->json($kategorimerek);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $kategorimerek = KategoriMerek::find($id);
        return response()->json($kategorimerek);
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {
    //     $kategorimerek = KategoriMerek::find($id);
    //     if ($kategorimerek){
    //         return response()->json($kategorimerek);
    //     } else{
    //         return response()->json(['message' => 'Data tidak ditemukan'], 404);
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
        $kategorimerek = KategoriMerek::find($id);
        $kategorimerek->merek_id = $request->merek_id;
        $kategorimerek->kategori_id = $request->kategori_id;
        $kategorimerek->save();
        return response()->json($kategorimerek);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $kategorimerek = KategoriMerek::find($id);

        if (!$kategorimerek) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $kategorimerek->produk()->delete();

        $kategorimerek->delete();

        return response()->json(['message' => 'Kategori Merek berhasil dihapus']);
    }
    
    public function check(Request $request)
    {
        $kategoriId = $request->kategori_id;
        $merekId = $request->merek_id;

        $existing = KategoriMerek::where('kategori_id', $kategoriId)
                                ->where('merek_id', $merekId)
                                ->first();

        if ($existing) {
            return response()->json(['id' => $existing->id], 200);
        } else {
            return response()->json(['id' => null], 200);
        }
    }
}
