<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MesinController extends Controller
{
    //Data Mesin dummy
    private $datadummyMesin = [
        [
            'id' => 1,
            'kode_mesin' => '2-B1-B0-1-02',
            'nama_mesin' => 'Bor Impact',
            'merek_mesin' => 'Bosch',
            'tipe_mesin' => 'GSB 10 RE Professional',
            'unit_mesin' => 'Unit',
            'stok_awal' => 1,
            'stok_akhir' => 1,
            'harga_mesin' => 1000000,
            'harga_beli' => '5.000.000',
            'tanggal_masuk' => '2020-01-01',            
            'kategori' => 'BOR',
            'no_seri_bawaan' => '0 601 216 1K1',
            'pembelian' => 'Local',
            'sumber_mesin' => 'Stok Lama',
            'vendor' => 'Bosch',
            'fungsi_mesin' => 'Buat mengebor kayu, beton, baja, dll.',
            'keterangan' => 'Bor impact Bosch GSB 10 RE, 10mm, 18V, 2Ah, 1.5kg',
            'gambar' => 'https://www.bosch-pt.co.id/id/id/ocsmedia/7669-54/application-image/1434x828/bor-impact-gsb-10-re-06012161k1.png',            
        ],
    ];

    //No Seri Mesin dummy
    private $datadummyNoSeri = [
        [
            'id' => 1,
            'no_seri' => 'MSN-01',
            'no_seri_default' => '0 601 216 1K1',
            'kode_mesin' => '2-B1-B0-1-02',
            'stok' => 1,
            'harga' => 5000000,
            'tanggal_masuk' => '2025-01-01',
            'lokasi_penyimpanan' => 'Lokasi 1',
            'status' => 'Ready',
        ]
    ];

    //Data dummy riwayat mesin
    private $datadummyRiwayat = [
        [
            'id' => 1,
            'id_alat' => 1,            
            'id_no_seri' => 1,
            'tanggal' => '2025-01-01',
            'id_layout' => 1,
            'tujuan' => null,
            'jenis' => null,
            'id_pengguna' => 1,
            'id_staff' => 1,
            'kondisi' => 'Ready',
            'jumlah' => 1,
        ]
    ];

    //Data Lokasi Penyimpanan dummy
    /**private $datadummyLokasiPenyimpanan = [
        [
            'id' => 1,
            'lokasi' => 'Lokasi 1',
            'kode_lokasi' => 'L1',
        ]
    ];*/

    // Data Dummy Perawatan
    private $datadummyPerawatan = [
        [        
            'id' => 1,
            'no_rawat' => 'R001',
            'id_no_seri_alat' => 10,
            'id_alat' => 1,
            'id_staff' => 1,
            'detail_perawatan' => 'Perawatan alat untuk memperbaiki kerusakan',
            'tanggal_perawatan' => '2022-01-19',
            'status' => 'Belum',                        
        ]
    ];

    //Data Dummy untuk Permintaan Mesin
    private $datadummyPermintaan = [

    ];

    //Data Dummy untuk Pengguna
    private $datadummyPengguna = [
        [
            'id' => 1,
            'nama_pengguna' => 'Gus Tri',
            'username' => 'gus.tri',
            'password' => 'password123',
            'level' => 'staff',
            'divisi' => 'Teknik',
        ],
    ];

    //Data Dummy untuk Peminjaman Mesin
    private $datadummyPeminjaman = [
        /*[
            'id' => 1,
            'no_pinjam' => 'P001',
            'id_no_seri_mesin' => 1,
            'id_mesin' => 1,
            'id_pengguna' => 1,
            'id_lokasi_penyimpanan' => 1,
            'kode_mesin' => '2-B1-B0-1-02',
            'tanggal_pinjam' => '2025-01-01',
            'tanggal_kembali' => '2025-01-15',
            'status' => 'Sedang Dipinjam',
            'stok' => 1,
        ]*/
    ];

    //Data dummy untuk Kondisi Error
    private $datadummyKondisiError = [

    ];

    //Data dummy untuk Kondisi Rusak
    private $datadummyKondisiRusak = [

    ];

    //Data dummy untuk Kondisi Musnah
    private $datadummyKondisiMusnah = [
        
    ];

    //Data Dummy untuk staff
    private $datadummyStaff = [

    ];

    //Index: Menampilkan semua data mesin
    public function index() {
        return response()->json([
            'data' => $this->datadummyMesin,
        ]);
    }

    //Edit: Menampilkan data mesin berdasarkan id
    public function edit($id) {
        $mesin = collect($this->datadummyMesin)->firstWhere('id', $id);

        if (!$mesin) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => $mesin,
        ]);
    }

    //Show: Menampilkan data mesin berdasarkan id
    public function show($id) {
        $mesin = collect($this->datadummyMesin)->firstWhere('id', $id);

        if (!$mesin) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        //Kembalikan data mesin yang sudah di perbaharui
        return response()->json($mesin);
    }

    //ByKodeMesin No Seri Belum Digunakan: Tampilkan detail no seri berdasarkan kode mesin
    public function byKodeMesinNoSeriBelumDigunakan($kodeMesin) {
        $datanoseri = collect($this->datadummyNoSeri)->filter(function($noseri) use ($kodeMesin){
            return $noseri['kode_mesin'] == $kodeMesin && $noseri;
        });

        if($datanoseri->isEmpty()) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($datanoseri);
    }

    //Edit: No Seri Belum Digunakan
    public function editNoSeriBelumDigunakan($id) {
        $datanoSeri = collect($this->datadummyNoSeri)->firstWhere('id', $id);
        
        if (!$datanoSeri) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => $datanoSeri,
        ]);
    }

    //ByKodeMesin : Tampilkan detail data permintaan mesin berdasarkan kode mesin\
    public function byKodeMesinPermintaan($kodeMesin) {
        $dataPermintaanMesin = collect($this->datadummyPermintaan)->filter(function($permintaanmesin) use ($kodeMesin){
            return $permintaanmesin['kode_mesin'] == $kodeMesin && $permintaanmesin;
        })->map(function ($permintaanmesin){
            $permintaanmesin['no_seri_mesin'] = collect($this->datadummyNoSeri)->firstWhere('id', $permintaanmesin['id_no_seri_mesin']);
            $permintaanmesin['pengguna'] = collect($this->datadummyPengguna)->firstWhere('id', $permintaanmesin['id_pengguna']);
            return $permintaanmesin;
        });

        if ($dataPermintaanMesin->isEmpty()) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($dataPermintaanMesin);
    }

    //ByKodeMesin : Tampilkan detail data peminjaman mesin berdasarkan kode mesin
    public function byKodeMesinPeminjaman($kodeMesin) {
        $dataPeminjamanMesin = collect($this->datadummyPeminjaman)->filter(function($peminjamanmesin) use ($kodeMesin){
            return $peminjamanmesin['kode_mesin'] == $kodeMesin && $peminjamanmesin;
        })->map(function($peminjamanmesin) {
            $peminjamanmesin['no_seri_mesin'] = collect($this->datadummyNoSeri)->firstWhere('id', $peminjamanmesin['id_no_seri_mesin']);
            $peminjamanmesin['pengguna'] = collect($this->datadummyPengguna)->firstWhere('id', $peminjamanmesin['id_pengguna']);
            return $peminjamanmesin;
        });

        if ($dataPeminjamanMesin->isEmpty()){
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($dataPeminjamanMesin);
    }

    //ByKodeMesin : Tampilkan detail data error berdasarkan kode mesin
    public function byKodeMesinError($kodeMesin) {
        $errors = collect($this->datadummyKondisiError)->filter(function ($error) use ($kodeMesin) {
            return $error['kode_mesin'] == $kodeMesin;
        })->map(function ($error){
            $error['staff_analisa'] = collect($this->datadummyStaff)->firstWhere('id', $error['id_staff_analisa']);
            $error['staff_perbaikan'] = collect($this->datadummyStaff)->firstWhere('id', $error['id_staff_perbaikan']);
            $error['no_seri_mesin'] = collect($this->datadummyNoSeri)->firstWhere('id', $error['id_no_seri_mesin']);
            return $error;
        });

        if ($errors->isEmpty()) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($errors->values());
    }

    //ByKodeMesin : Tampilkan detail data rusak berdasarkan kode mesin
    public function byKodeMesinRusak($kodeMesin) {
        $dataRusak = collect($this->datadummyKondisiRusak)->filter(function($rusak) use ($kodeMesin){
            return $rusak['kode_mesin'] == $kodeMesin;
        })->map(function($rusak){
            $rusak['no_seri_mesin'] = collect($this->datadummyNoSeri)->firstWhere('id', $rusak['id_no_seri_mesin']);
            $rusak['staff_kerusakan'] = collect($this->datadummyStaff)->firstWhere('id', $rusak['id_staff_kerusakan']);
            return $rusak;
        });

        if ($dataRusak->isEmpty()){
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($dataRusak);
    }

    //ByKodeMesin : Tampilkan detail data musnah berdasarkan kode mesin
    public function byKodeMesinMusnah($kodeMesin) {
        $datamusnah = collect($this->datadummyKondisiMusnah)->filter(function($musnah) use ($kodeMesin){
            return $musnah['kode_mesin'] == $kodeMesin;
        })->map(function($musnah){
            $musnah['no_seri_mesin'] = collect($this->datadummyNoSeri)->firstWhere('id', $musnah['id_no_seri_mesin']);
            $musnah['staff_pemusnahan'] = collect($this->datadummyStaff)->firstWhere('id', $musnah['id_staff_pemusnahan']);
            return $musnah;
        });

        if ($datamusnah->isEmpty()){
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
        return response()->json($datamusnah);
    }

    //Index: Tampilkan semua data peminjaman mesin
    public function indexPeminjamanMesin() {
        $dataPeminjamanMesin = collect($this->datadummyPeminjaman)->map(function($peminjamanmesin){
            $mesin = collect($this->datadummyMesin)->firstWhere('id', $peminjamanmesin['id_mesin']);
            $peminjamanmesin['mesin'] = $mesin;
            $pengguna = collect($this->datadummyPengguna)->firstWhere('id', $peminjamanmesin['id_pengguna']);
            $peminjamanmesin['pengguna'] = $pengguna;
            return $peminjamanmesin;
        });
        return response()->json([
            'data' => $dataPeminjamanMesin,
        ]);
    }

    //Show: Tampilkan Riwayat Mesin
    public function showRiwayatMesin($id) {
        $dataRiwayatMesin = collect($this->datadummyRiwayat)->firstWhere('id', $id);
        if (!$dataRiwayatMesin) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
        $noseri = collect($this->datadummyNoSeri)->firstWhere('id', $dataRiwayatMesin['id_no_seri']);
        $staff = collect($this->datadummyStaff)->firstWhere('id', $dataRiwayatMesin['id_staff']);
        $dataRiwayatMesin['noseri'] = $noseri;
        $dataRiwayatMesin['staff'] = $staff;
        return response()->json($dataRiwayatMesin);
    }

    //Index: Perawatan Mesin
    public function indexPerawatanMesin() {
        $dataPerawatan = collect($this->datadummyPerawatan)->map(function($perawatan) {
            $noseri = collect($this->datadummyNoSeri)->firstWhere('id', $perawatan['id_no_seri']);
            $staff = collect($this->datadummyStaff)->firstWhere('id', $perawatan['id_staff']);
            $mesin = collect($this->datadummyMesin)->firstWhere('id', $perawatan['id_mesin']);

            if (!$noseri || !$staff || !$mesin) {
                $perawatan['noseri'] = $noseri;
                $perawatan['staff'] = $staff;
                $perawatan['mesin'] = $mesin;
            }
            return $perawatan;
        });

        if($dataPerawatan->isEmpty()) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
        return response()->json([
            'data' => $dataPerawatan->toArray()
        ]);
    }
}
