<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\Kategori;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $kategoris = Kategori::all();
    //     return response()->json($kategoris);
    // }

    public function index() {
        return Kategori::with([
            'jenis',
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
        $kategori = new Kategori();
        $nama_kategori = $request->input('nama_kategori');
        $jenis_id = $request->input('jenis_id');

        //Mendapatkan inisial dari nama kategori (huruf pertama)
        $inisial = strtoupper(substr($nama_kategori, 0, 1)); //Ambil 1 digit inisial awal dan pastikan huruf kapital

        // Mendapatkan urutan yang sudah ada berdasarkan inisial
        $urutanTerakhir = Kategori::where('nama_kategori', 'like', $inisial . '%')
                                ->count(); // Menghitung berapa banyak kategori yang memiliki inisial yang sama

        // Kode kategori terdiri dari inisial dan urutan yang dihasilkan
        $kode_kategori = $inisial . $urutanTerakhir;

        // Jika nama kategori adalah "NO KATEGORI", maka kode_kategori = 'XX'
        if ($nama_kategori == 'NO KATEGORI') {
            $kode_kategori = 'XX';
        }

        $kategori->kode_kategori = $kode_kategori;
        $kategori->nama_kategori = $nama_kategori;
        $kategori->jenis_id = $jenis_id;
        $kategori->save();

        return response()->json($kategori);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $kategori = Kategori::find($id);
        if ($kategori) {
            return response()->json($kategori);
        } else {
            return response()->json(['message' => 'Kategori tidak dapat ditemukan'], 404);
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
    //     $kategori = Kategori::find($id);
    //     if ($kategori) {
    //         return response()->json($kategori);
    //     } else {
    //         return response()->json(['message' => 'Kategori tidak dapat ditemukan'], 404);
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
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        $nama_kategori = $request->input('nama_kategori');
        $jenis_id = $request->input('jenis_id');

        // Jika nama kategori berubah, maka perbarui kode kategori
        if ($kategori->nama_kategori != $nama_kategori) {
            // Mendapatkan inisial dari nama kategori (huruf pertama)
            $inisial = strtoupper(substr($nama_kategori, 0, 1)); //Ambil 1 digit inisial awal dan pastikan huruf kapital

            // Mendapatkan urutan yang sudah ada berdasarkan inisial
            $urutanTerakhir = Kategori::where('nama_kategori', 'like', $inisial . '%')
                                    ->where('id', '!=', $id)
                                    ->count(); // Menghitung berapa banyak kategori yang memiliki inisial yang sama

            // Kode kategori terdiri dari inisial dan urutan yang dihasilkan
            $kode_kategori = $inisial . $urutanTerakhir;

            // Jika nama kategori adalah "NO KATEGORI", maka kode_kategori = 'XX'
            if ($nama_kategori == 'NO KATEGORI') {
                $kode_kategori = 'XX';
            }

            $kategori->kode_kategori = $kode_kategori;
        }

        $kategori->nama_kategori = $nama_kategori;
        $kategori->jenis_id = $jenis_id;
        $kategori->save();

        return response()->json($kategori);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        // Hapus produk-produk yang terkait dengan kategori
        $kategori->produk()->delete();

        $kategori->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}
