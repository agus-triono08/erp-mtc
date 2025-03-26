<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\Tools;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToolsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tools = Tools::all();
        return response()->json($tools);
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
    // public function store(Request $request)
    // {
    //     $tools = new Tools();
    //     $tools->jenis_id = $request->jenis_id;
    //     $tools->kode = $request->kode;
    //     $tools->nama = $request->nama;
    //     $tools->stok_awal = $request->stok_awal;
    //     $tools->stok_akhir = $request->stok_akhir;
    //     $tools->unit = $request->unit;
    //     $tools->harga_total = $request->harga_total;
    //     $tools->pembelian = $request->pembelian;
    //     $tools->sumber = $request->sumber;
    //     $tools->vendor = $request->vendor;
    //     $tools->fungsi = $request->fungsi;
    //     $tools->deskripsi = $request->deskripsi;
    //     $tools->gambar = $request->gambar;
    //     $tools->jadwal_perawatan = $request->jadwal_perawatan;
    //     $tools->save();
    //     return response()->json($tools);
    // }

    public function store(Request $request)
    {
        // Validasi input termasuk gambar
        $request->validate([
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:153600',  // 150MB = 150 * 1024 KB
        ]);

        // Ambil kode dari tabel terkait berdasarkan ID yang diberikan
        $jenis = Jenis::find($request->jenis_id);
        $kategori = Kategori::find($request->kategori_id); // Menggunakan kategori_id dari request
        $kategoriMerek = KategoriMerek::where('kategori_id', $request->kategori_id)->first(); // Ambil kategori_merek berdasarkan kategori_id
        $merek = Merek::find($kategoriMerek->merek_id); // Ambil merek berdasarkan kategori_merek
        $tipe = Tipe::find($request->tipe_id); // Menggunakan tipe_id dari request

        // Validasi apakah data yang diperlukan ditemukan
        if (!$jenis || !$kategori || !$merek || !$tipe) {
            return response()->json(['error' => 'Data tidak ditemukan.'], 404);
        }

        // Ambil nomor urut berdasarkan jumlah data tools yang sudah ada
        $nomorUrut = Tools::count() + 1; // Hitung jumlah data tools dan tambah 1

        // Gabungkan semua bagian kode (jenis, kategori, merek, tipe, nomor urut)
        $kode = $jenis->kode . $kategori->kode . $merek->kode . $tipe->kode . str_pad($nomorUrut, 4, '0', STR_PAD_LEFT);

        // Jika gambar ada di dalam request
        if ($request->hasFile('gambar')) {
            // Ambil file gambar
            $file = $request->file('gambar');

            // Tentukan nama file dengan format yang unik
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Simpan file di folder 'public/images'
            $filePath = $file->storeAs('public/images', $fileName);

            // Membuat objek tools baru dan menyimpan data
            $tools = new Tools();
            $tools->jenis_id = $request->jenis_id;
            $tools->kategori_id = $request->kategori_id;
            $tools->kode = $kode; // Assign kode yang telah digabungkan
            $tools->nama = $request->nama;
            $tools->stok_awal = $request->stok_awal;
            $tools->stok_akhir = $request->stok_akhir;
            $tools->unit = $request->unit;
            $tools->harga_total = $request->harga_total;
            $tools->pembelian = $request->pembelian;
            $tools->sumber = $request->sumber;
            $tools->vendor = $request->vendor;
            $tools->fungsi = $request->fungsi;
            $tools->deskripsi = $request->deskripsi;
            $tools->gambar = $filePath; // Simpan path gambar di database
            $tools->jadwal_perawatan = $request->jadwal_perawatan;
            $tools->save();

            // Kembalikan data tools yang telah disimpan sebagai JSON
            return response()->json($tools);
        }

        // Jika tidak ada gambar yang diupload
        return response()->json(['error' => 'Tidak ada file gambar yang diunggah.'], 400);
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
}
