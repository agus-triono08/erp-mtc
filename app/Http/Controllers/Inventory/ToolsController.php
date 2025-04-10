<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\Tools;
use App\Models\Inventory\Jenis;
use App\Models\Inventory\Kategori;
use App\Models\Inventory\Merek;
use App\Models\Inventory\KategoriMerek;
use App\Models\Inventory\Tipe;
use App\Models\Inventory\NoSeri;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToolsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     return Tools::with(['jenis', 'kategori', 'merek', 'tipe'])
    //                 ->get()
    //                 ->map(function ($tool) {
    //                     return [
    //                         'id' => $tool->id,
    //                         'kode' => $tool->kode,
    //                         'nama' => $tool->nama,
    //                         'stok_awal' => $tool->stok_awal,
    //                         'stok_akhir' => $tool->stok_akhir,
    //                         'unit' => $tool->unit,
    //                         'harga_total' => $tool->harga_total,
    //                         'pembelian' => $tool->pembelian,
    //                         'sumber' => $tool->sumber,
    //                         'vendor' => $tool->vendor,
    //                         'fungsi' => $tool->fungsi,
    //                         'deskripsi' => $tool->deskripsi,
    //                         'gambar' => $tool->gambar,
    //                         'jadwal_perawatan' => $tool->jadwal_perawatan,
    //                         'nama_jenis' => $tool->jenis->nama_jenis ?? null,
    //                         'nama_kategori' => $tool->kategori->nama_kategori ?? null,
    //                         'nama_merek' => $tool->merek->nama_merek ?? null,
    //                         'nama_tipe' => $tool->tipe->nama_tipe ?? null,
    //                     ];
    //                 });
    // }

    // public function index()
    // {
    //     return Tools::with([
    //         'jenis',
    //         'jenis.kategori',
    //         'jenis.kategori.kategoriMerek.merek',
    //         'jenis.kategori.kategoriMerek.tipe'
    //     ])->get();
    // }

    public function index(Request $request)
    {
        $tools = Tools::with([
            'jenis',
            'jenis.kategori',
            'jenis.kategori.kategoriMerek.merek',
            'jenis.kategori.kategoriMerek.tipe'
        ])
        ->when($request->jenis_id, function ($query) use ($request) {
            $query->where('jenis_id', $request->jenis_id);
        })
        ->when($request->kategori_id, function ($query) use ($request) {
            $query->whereHas('jenis.kategori', function ($q) use ($request) {
                $q->where('id', $request->kategori_id);
            });
        })
        ->when($request->merek_id, function ($query) use ($request) {
            $query->whereHas('jenis.kategori.kategoriMerek', function ($q) use ($request) {
                $q->where('merek_id', $request->merek_id);
            });
        })
        ->when($request->tipe_id, function ($query) use ($request) {
            $query->whereHas('jenis.kategori.kategoriMerek', function ($q) use ($request) {
                $q->where('tipe_id', $request->tipe_id);
            });
        })
        ->get();

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

    // UPDATE 10-04-2025
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'stok_awal' => 'required|integer|min:1',
            'jenis_id' => 'required|exists:jenis,id',
            'kategori_id' => 'required|exists:kategori,id',
            'merek_id' => 'required|exists:merek,id',
            'tipe_id' => 'required|exists:tipe,id',
            'nama' => 'required|string',
            'unit' => 'required|string',
            'pembelian' => 'required|string',
            'sumber' => 'required|string',
            'vendor' => 'nullable|string',
            'fungsi' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        // Ambil model relasi untuk kebutuhan kode
        $jenis = Jenis::findOrFail($request->jenis_id);
        $kategori = Kategori::findOrFail($request->kategori_id);
        $merek = Merek::findOrFail($request->merek_id);
        $tipe = Tipe::findOrFail($request->tipe_id);

        // Generate kode unik
        $count = Tools::count() + 1;
        $nomorUrut = str_pad($count, 3, '0', STR_PAD_LEFT);
        $kode = $jenis->kode_jenis . '-' . $kategori->kode_kategori . '-' . $merek->kode_merek . '-' . $tipe->kode_tipe . '-' . $nomorUrut;

        // Simpan alat
        $tool = Tools::create([
            'jenis_id' => $request->jenis_id,
            'kode' => $kode,
            'nama' => $request->nama,
            'stok_awal' => $request->stok_awal,
            'stok_akhir' => $request->stok_awal,
            'unit' => $request->unit,
            'pembelian' => $request->pembelian,
            'sumber' => $request->sumber,
            'vendor' => $request->vendor,
            'fungsi' => $request->fungsi,
            'deskripsi' => $request->deskripsi,
            'gambar' => null, // optional jika kamu pakai upload file
            'jadwal_perawatan' => null // optional jika belum diinput
        ]);

        // Buat no seri sebanyak stok_awal
        for ($i = 0; $i < $request->stok_awal; $i++) {
            $hurufUnique = strtoupper(substr($kategori->nama_kategori, 0, 2));
            $angkaAcak = rand(1000, 99999999);
            $noSeri = $hurufUnique . $angkaAcak;

            NoSeri::create([
                'tools_id' => $tool->id,
                'no_seri' => $noSeri,
                'no_seri_default' => null,
                'tanggal_masuk' => now(),
            ]);
        }

        // Load relasi sampai ke nama_kategori, nama_merek, dan nama_tipe
        $tool = Tools::with([
            'jenis.kategori.kategoriMerek.merek',
            'jenis.kategori.kategoriMerek.tipe'
        ])->find($tool->id);

        return response()->json($tool, 201);
    }

    // // UPDATE 08-04-2025
    // public function store(Request $request)
    // {
    //     // Validate input
    //     $request->validate([
    //         'stok_awal' => 'required|integer|min:1',
    //         'jenis_id' => 'required',
    //         'kategori_id' => 'required',
    //         'merek_id' => 'required',
    //         'tipe_id' => 'required',
    //     ]);

    //     // $jenis = Jenis::findOrFail($request->jenis_id);
    //     // $kategori = Kategori::findOrFail($request->kategori_id);

    //     // // Cari kategori_merek berdasarkan kategori_id & merek_id
    //     // $kategoriMerek = KategoriMerek::with(['kategori', 'merek', 'tipe'])
    //     // ->findOrFail($request->kategori_merek_id);

    //     // $merek = $kategoriMerek->merek;

    //     // // Cari tipe berdasarkan kategori_merek_id & tipe_id
    //     // $tipe = \App\Models\Inventory\Tipe::where('kategori_merek_id', $kategoriMerek->id)
    //     //     ->where('id', $request->tipe_id)
    //     //     ->firstOrFail();

    //     // $jenis = Jenis::firstOrCreate(['id' => $request->jenis_id]);
    //     // $kategori = Kategori::firstOrCreate(['id' => $request->kategori_id]);
    //     // $merek = Merek::firstOrCreate(['id' => $request->merek_id]);
    //     // $tipe = Tipe::firstOrCreate(['id' => $request->tipe_id]);

    //     // Find related models
    //     $jenis = Jenis::findOrFail($request->jenis_id);
    //     $kategori = Kategori::findOrFail($request->kategori_id);
    //     $merek = Merek::findOrFail($request->merek_id);
    //     $tipe = Tipe::findOrFail($request->tipe_id);

    //     // Generate unique tool code
    //     $count = Tools::count() + 1;
    //     $nomorUrut = str_pad($count, 3, '0', STR_PAD_LEFT);
    //     $kode = $jenis->kode_jenis . '-' . $kategori->kode_kategori . '-' . $merek->kode_merek . '-' . $tipe->kode_tipe . '-' . $nomorUrut;

    //     // Create the tool
    //     $tool = Tools::create(array_merge($request->all(), [
    //         'kode' => $kode,
    //         'stok_akhir' => $request->stok_awal,            
    //     ]));

    //     // Create serial numbers based on stok_awal
    //     for ($i = 0; $i < $request->stok_awal; $i++) {
    //         $hurufUnique = substr($kategori->nama_kategori, 0, 2);
    //         $angkaAcak = rand(1000, 99999999); // Membuat angka acak antara 1000 dan 99999999
    //         $noSeri = $hurufUnique . $angkaAcak;
    //         NoSeri::create([
    //             'tools_id' => $tool->id, // Reference to the tool
    //             'no_seri' => $noSeri, // Generates a unique serial number
    //             'no_seri_default' => null, // Set this if you have a default or specific format
    //             'tanggal_masuk' => now(), // Tambahkan tanggal masuk
    //         ]);
    //     }

    //     // Reload tool with full relations
    //     $tool = Tools::with(['jenis', 'kategori', 'merek', 'tipe'])->find($tool->id);

    //     return response()->json($tool, 201);
    // }

    // // 08-04-2025
    // public function store(Request $request)
    // {
    //     $jenis = Jenis::findOrFail($request->jenis_id);
    //     $kategori = Kategori::findOrFail($request->kategori_id);
    //     $merek = Merek::findOrFail($request->merek_id);
    //     $tipe = Tipe::findOrFail($request->tipe_id);

    //     // Menghitung jumlah tools yang sudah ada
    //     $count = Tools::count() + 1;
    //     $nomorUrut = str_pad($count, 3, '0', STR_PAD_LEFT); // Menjadikan 3 digit (001, 002, ...)

    //     // Format kode
    //     $kode = $jenis->kode_jenis . '-' . $kategori->kode_kategori . '-' . $merek->kode_merek . '-' . $tipe->kode_tipe . '-' . $nomorUrut;

    //     // Simpan data dengan kode baru
    //     $tool = Tools::create(array_merge($request->all(), ['kode' => $kode]));

    //     return response()->json($tool, 201);
    // }

    // public function store(Request $request)
    // {
    //     // Menjalankan query untuk mendapatkan kode dan data lainnya berdasarkan id_tools
    //     $tool = DB::table('tools as t')
    //         ->join('jenis as j', 't.jenis_id', '=', 'j.id')
    //         ->join('kategori as k', 'j.id', '=', 'k.jenis_id')
    //         ->join('kategori_merek as km', 'k.id', '=', 'km.kategori_id')
    //         ->join('merek as m', 'km.merek_id', '=', 'm.id')
    //         ->join('tipe as ty', 'km.id', '=', 'ty.kategori_merek_id')
    //         ->select(
    //             't.kode as tool_code',  // Kolom yang Anda inginkan, yaitu kode alat
    //             't.nama as tool_name',
    //             'j.nama_jenis as jenis_name',
    //             'k.nama_kategori as kategori_name',
    //             'm.nama_merek as merek_name',
    //             'ty.nama_tipe as tipe_name'
    //         )
    //         ->where('t.id', $request->id_tools)  // Mengambil berdasarkan id_tools dari request
    //         ->first(); // Ambil satu hasil saja

    //     // Debugging untuk melihat hasil query
    //     dd($tool); // Lihat hasil query yang dijalankan

    //     // Jika alat tidak ditemukan, beri response error
    //     if (!$tool) {
    //         return response()->json(['error' => 'Tool not found'], 404);
    //     }

    //     // Membuat objek Tools baru untuk disimpan
    //     $tools = new Tools();
    //     $tools->jenis_id = $request->jenis_id;
    //     $tools->kode = $tool->tool_code;  // Mengisi kode secara otomatis dengan nilai dari query
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
        
    //     // Simpan data tools ke database
    //     $tools->save();

    //     // Kembalikan response dengan data tools yang baru disimpan
    //     return response()->json($tools);
    // }

    // public function store(Request $request)
    // {
    //     // Validasi input termasuk gambar
    //     $request->validate([
    //         'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:153600',  // 150MB = 150 * 1024 KB
    //     ]);

    //     // Ambil kode dari tabel terkait berdasarkan ID yang diberikan
    //     $jenis = Jenis::find($request->jenis_id);
    //     $kategori = Kategori::find($request->kategori_id); // Menggunakan kategori_id dari request
    //     $kategoriMerek = KategoriMerek::where('kategori_id', $request->kategori_id)->first(); // Ambil kategori_merek berdasarkan kategori_id
    //     $merek = Merek::find($kategoriMerek->merek_id); // Ambil merek berdasarkan kategori_merek
    //     $tipe = Tipe::find($request->tipe_id); // Menggunakan tipe_id dari request

    //     // Validasi apakah data yang diperlukan ditemukan
    //     if (!$jenis || !$kategori || !$merek || !$tipe) {
    //         return response()->json(['error' => 'Data tidak ditemukan.'], 404);
    //     }

    //     // Ambil nomor urut berdasarkan jumlah data tools yang sudah ada
    //     $nomorUrut = Tools::count() + 1; // Hitung jumlah data tools dan tambah 1

    //     // Gabungkan semua bagian kode (jenis, kategori, merek, tipe, nomor urut)
    //     $kode = $jenis->kode . $kategori->kode . $merek->kode . $tipe->kode . str_pad($nomorUrut, 4, '0', STR_PAD_LEFT);

    //     // Jika gambar ada di dalam request
    //     if ($request->hasFile('gambar')) {
    //         // Ambil file gambar
    //         $file = $request->file('gambar');

    //         // Tentukan nama file dengan format yang unik
    //         $fileName = time() . '_' . $file->getClientOriginalName();

    //         // Simpan file di folder 'public/images'
    //         $filePath = $file->storeAs('public/images', $fileName);

    //         // Membuat objek tools baru dan menyimpan data
    //         $tools = new Tools();
    //         $tools->jenis_id = $request->jenis_id;
    //         $tools->kategori_id = $request->kategori_id;
    //         $tools->kode = $kode; // Assign kode yang telah digabungkan
    //         $tools->nama = $request->nama;
    //         $tools->stok_awal = $request->stok_awal;
    //         $tools->stok_akhir = $request->stok_akhir;
    //         $tools->unit = $request->unit;
    //         $tools->harga_total = $request->harga_total;
    //         $tools->pembelian = $request->pembelian;
    //         $tools->sumber = $request->sumber;
    //         $tools->vendor = $request->vendor;
    //         $tools->fungsi = $request->fungsi;
    //         $tools->deskripsi = $request->deskripsi;
    //         $tools->gambar = $filePath; // Simpan path gambar di database
    //         $tools->jadwal_perawatan = $request->jadwal_perawatan;
    //         $tools->save();

    //         // Kembalikan data tools yang telah disimpan sebagai JSON
    //         return response()->json($tools);
    //     }

    //     // Jika tidak ada gambar yang diupload
    //     return response()->json(['error' => 'Tidak ada file gambar yang diunggah.'], 400);
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     $tools = Tools::find($id);
    //     if ($tools) {
    //         return response()->json($tools);
    //     } else {
    //         return response()->json(['error' => 'Tools tidak ditemukan.'], 404);
    //     }
    // }
    public function show($id)
    {
        $tools = Tools::with([
            'jenis',
            'jenis.kategori',
            'jenis.kategori.kategoriMerek.merek',
            'jenis.kategori.kategoriMerek.tipe'
        ])->find($id);

        if ($tools) {
            return response()->json($tools);
        } else {
            return response()->json(['error' => 'Tools tidak ditemukan.'], 404);
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
    //     //
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
        // Validasi input termasuk gambar
        $request->validate([
            'gambar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:153600',  // 150MB = 150 * 1024 KB
        ]);

        // Ambil data tools yang akan diupdate
        $tools = Tools::find($id);

        // Validasi apakah data tools ditemukan
        if (!$tools) {
            return response()->json(['error' => 'Data tidak ditemukan.'], 404);
        }

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

        // Gabungkan semua bagian kode (jenis, kategori, merek, tipe, nomor urut)
        $kode = $jenis->kode . $kategori->kode . $merek->kode . $tipe->kode . str_pad($tools->nomor_urut, 4, '0', STR_PAD_LEFT);

        // Jika gambar ada di dalam request
        if ($request->hasFile('gambar')) {
            // Ambil file gambar
            $file = $request->file('gambar');

            // Tentukan nama file dengan format yang unik
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Simpan file di folder 'public/images'
            $filePath = $file->storeAs('public/images', $fileName);

            // Update data tools
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

            // Kembalikan data tools yang telah diupdate sebagai JSON
            return response()->json($tools);
        }

        // Jika tidak ada gambar yang diupload
        // Update data tools tanpa gambar
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
        $tools->jadwal_perawatan = $request->jadwal_perawatan;
        $tools->save();

        // Kembalikan data tools yang telah diupdate sebagai JSON
        return response()->json($tools);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Ambil data tools yang akan dihapus
        $tools = Tools::find($id);

        // Validasi apakah data tools ditemukan
        if (!$tools) {
            return response()->json(['error' => 'Data tidak ditemukan.'], 404);
        }

        // Hapus file gambar jika ada
        if ($tools->gambar) {
            Storage::delete('public/images/' . $tools->gambar);
        }

        // Hapus data tools
        $tools->delete();

        // Kembalikan pesan sukses
        return response()->json(['message' => 'Data berhasil dihapus.']);
    }
}


