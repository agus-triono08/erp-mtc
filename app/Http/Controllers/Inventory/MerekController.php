<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\Merek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MerekController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mereks = Merek::all();
        return response()->json($mereks);
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
    //     $merek = new Merek();
    //     $nama_merek = $request->input('nama_merek');
    //     if ($nama_merek == 'NO MEREK') {
    //         $merek->kode_merek = 'XX';
    //     } else {
    //         $inisial = substr($nama_merek, 0, 1); // ambil 1 digit inisial awal
    //         $hexa = dechex(crc32($nama_merek)); // ambil hexa dari inputan nama_merek
    //         $hexa_numerik = preg_replace('/[a-fA-F]/', '', $hexa); // hapus huruf-huruf dari hexa
    //         if (!empty($hexa_numerik)) {
    //             $merek->kode_merek = $inisial . substr($hexa_numerik, 0, 1); // gabungkan inisial dan hexa numerik
    //         } else {
    //             // handle jika $hexa_numerik kosong
    //             $merek->kode_merek = $inisial . '0'; // contoh: mengisi dengan nilai default
    //         }
    //     }
    //     $merek->nama_merek = $nama_merek;
    //     $merek->save();
    //     return response()->json($merek);
    // }
    public function store(Request $request)
    {
        $merek = new Merek();
        $nama_merek = $request->input('nama_merek');
        
        // Mendapatkan inisial dari nama merek (huruf pertama)
        $inisial = strtoupper(substr($nama_merek, 0, 1)); // ambil 1 digit inisial awal dan pastikan huruf kapital
        
        // Mendapatkan urutan yang sudah ada berdasarkan inisial
        $urutanTerakhir = Merek::where('nama_merek', 'like', $inisial . '%')
                                ->count(); // Menghitung berapa banyak merek yang memiliki inisial yang sama
        
        // Kode merek terdiri dari inisial dan urutan yang dihasilkan
        $kode_merek = $inisial . $urutanTerakhir;

        // Jika nama merek adalah "NO MEREK", maka kode_merek = 'XX'
        if ($nama_merek == 'NO MEREK') {
            $kode_merek = 'XX';
        }

        // Menyimpan data merek
        $merek->kode_merek = $kode_merek;
        $merek->nama_merek = $nama_merek;
        $merek->save();

        return response()->json($merek);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $merek = Merek::find($id);
        if ($merek) {
            return response()->json($merek);
        } else {
            return response()->json(['message' => 'Merek tidak ditemukan'], 404);
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
    //     $merek = Merek::find($id);
    //     if ($merek) {
    //         return response()->json($merek);
    //     } else {
    //         return response()->json(['message' => 'Merek tidak ditemukan'], 404);
    //     }
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
    //     $merek = Merek::find($id);
    //     if ($merek) {
    //         $nama_merek = $request->input('nama_merek');
    //         if ($nama_merek == 'NO MEREK') {
    //             $merek->kode_merek = 'XX';
    //         } else {
    //             $inisial = substr($nama_merek, 0, 1); // ambil 1 digit inisial awal
    //             $hexa = dechex(crc32($nama_merek)); // ambil hexa dari inputan nama_merek
    //             $hexa_numerik = preg_replace('/[a-fA-F]/', '', $hexa); // hapus huruf-huruf dari hexa
    //             $merek->kode_merek = $inisial . substr($hexa_numerik, 0, 1); // gabungkan inisial dan hexa numerik
    //         }
    //         $merek->nama_merek = $nama_merek;
    //         $merek->save();
    //         return response()->json($merek);
    //     } else {
    //         return response()->json(['message' => 'Merek tidak ditemukan'], 404);
    //     }
    // }
    public function update(Request $request, $id)
    {
        $merek = Merek::find($id);

        if (!$merek) {
            return response()->json(['error' => 'Merek tidak ditemukan'], 404);
        }

        $nama_merek = $request->input('nama_merek');

        // Jika nama merek berubah, maka perlu mengupdate kode merek
        if ($nama_merek != $merek->nama_merek) {
            // Mendapatkan inisial dari nama merek (huruf pertama)
            $inisial = strtoupper(substr($nama_merek, 0, 1)); // ambil 1 digit inisial awal dan pastikan huruf kapital

            // Mendapatkan urutan yang sudah ada berdasarkan inisial
            $urutanTerakhir = Merek::where('nama_merek', 'like', $inisial . '%')
                                    ->where('id', '!=', $id) // Jangan hitung merek yang sedang diupdate
                                    ->count(); // Menghitung berapa banyak merek yang memiliki inisial yang sama

            // Kode merek terdiri dari inisial dan urutan yang dihasilkan
            $kode_merek = $inisial . $urutanTerakhir;

            // Jika nama merek adalah "NO MEREK", maka kode_merek = 'XX'
            if ($nama_merek == 'NO MEREK') {
                $kode_merek = 'XX';
            }

            // Menyimpan data merek
            $merek->kode_merek = $kode_merek;
        }

        $merek->nama_merek = $nama_merek;
        $merek->save();

        return response()->json($merek);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $merek = Merek::find($id);
        if ($merek) {
            $merek->delete();
            return response()->json(['message' => 'Merek berhasil dihapus']);
        } else {
            return response()->json(['message' => 'Merek tidak ditemukan'], 404);
        }
    }
}
