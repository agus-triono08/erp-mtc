<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\Jenis;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JenisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jenis = Jenis::all();
        return response()->json($jenis);
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
        $jenis = new Jenis();
        $jenis->nama_jenis = $request->input('nama_jenis');

        $kodeJenisMap = [
            'ALAT' => 1,
            'MESIN' => 2,
            // Anda dapat menambahkan jenis lainnya di sini
        ];

        $jenis->kode_jenis = $kodeJenisMap[$request->input('nama_jenis')] ?? null;

        if (!$jenis->kode_jenis) {
            // Anda dapat menambahkan kode untuk menangani kasus lainnya
            // Misalnya, mengembalikan respons error jika nama_jenis tidak dikenal
            return response()->json(['error' => 'Nama jenis tidak dikenal'], 400);
        }

        $jenis->save();
        return response()->json($jenis, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jenis = Jenis::find($id);
        if ($jenis) {
            return response()->json($jenis);
        } else {
            return response()->json(['error' => 'Jenis tidak ditemukan'], 404);
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
    //     $jenis = Jenis::find($id);
    //     if ($jenis) {
    //         return response()->json($jenis);
    //     } else {
    //         return response()->json(['error' => 'Jenis tidak ditemukan'], 404);
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
        $jenis = Jenis::find($id);

        if (!$jenis) {
            return response()->json(['error' => 'Jenis tidak ditemukan'], 404);
        }

        if ($request->input('nama_jenis')) {
            $jenis->nama_jenis = $request->input('nama_jenis');

            $kodeJenisMap = [
                'ALAT' => 1,
                'MESIN' => 2,
                // Anda dapat menambahkan jenis lainnya di sini
            ];

            $jenis->kode_jenis = $kodeJenisMap[$request->input('nama_jenis')] ?? $jenis->kode_jenis;

            if (!$jenis->kode_jenis) {
                // Anda dapat menambahkan kode untuk menangani kasus lainnya
                // Misalnya, mengembalikan respons error jika nama_jenis tidak dikenal
                return response()->json(['error' => 'Nama jenis tidak dikenal'], 400);
            }
        }

        $jenis->save();
        return response()->json($jenis, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jenis = Jenis::find($id);

        if (!$jenis) {
            return response()->json(['error' => 'Jenis tidak ditemukan'], 404);
        }

        $jenis->delete();
        return response()->json(['message' => 'Jenis berhasil dihapus'], 200);
    }
}
