<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlatController extends Controller
{
    // Data dummy
    private $dataDummy = [
        [
            'id' => 1,
            'kode_alat' => '1-C3-B0-2-01',
            'nama_alat' => 'Clamp',
            'jenis' => 'Alat',
            'merek_alat' => 'BISON',
            'tipe_alat' => 'Plastik',
            'unit_alat' => 'Unit',
            'stok_awal' => 12, // Stok awal
            'stok_akhir' => 12, // Stok akhir (akan diupdate)
            'lokasi_penyimpanan' => 'Gudang A',
            'kategori' => 'CLAMP',
            'status' => 'active',
            'harga_total' => null,
            'no_seri_alat' => '1234567890',
            'tahun_masuk' => '2020',
            'pembelian' => 'local',
            'sumber_alat' => 'Stok Lama',
            'vendor' => 'Beli Online',
            'fungsi' => null,
            'deskripsi' => 'Clamp untuk berbagai kebutuhan.',
            'gambar' => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//104/MTA-60139885/oem_oem_full01.jpg',
        ],
        [
            'id' => 2,
            'kode_alat' => '1-C3-B0-3-01',
            'nama_alat' => 'Clamp',
            'jenis' => 'Alat',
            'merek_alat' => 'BISON',
            'tipe_alat' => '8"',
            'unit_alat' => 'Unit',
            'stok_awal' => 5, // Stok awal
            'stok_akhir' => 5, // Stok akhir (akan diupdate)
            'lokasi_penyimpanan' => 'Gudang B',
            'kategori' => 'CLAMP',
            'status' => 'active',
            'harga' => 'Rp. 30.000',
            'no_seri_alat' => '1122wscj122',
            'tahun_masuk' => '2020',
            'pembelian' => 'local',
            'sumber_alat' => 'Stok Lama',
            'vendor' => 'Beli Online',
            'fungsi' => null,
            'deskripsi' => 'Clamp untuk berbagai kebutuhan.',
            'gambar' => 'https://down-id.img.susercontent.com/file/id-11134207-7r98t-llsm9985w7n857',
        ],
        [
            'id' => 3,
            'kode_alat' => '2-B1-B0-1-02',
            'nama_alat' => 'Bor Impact',
            'jenis' => 'Mesin',
            'merek_alat' => 'Bosch',
            'tipe_alat' => 'GSB 10 RE Professional',
            'unit_alat' => 'Unit',
            'stok_awal' => 1,
            'stok_akhir' => 1,
            'lokasi_penyimpanan' => 'Gudang C',
            'kategori' => 'BOR',
            'no_seri_bawaan' => '0 601 216 1K1',
            'pembelian' => 'import',
            'sumber_alat' => 'Stok Baru',
            'vendor' => 'Beli Online',
            'deskripsi' => 'Bor impact Bosch GSB 10 RE, 10mm, 18V, 2Ah, 1.5kg',
            'fungsi' => 'Buat mengebor kayu, beton, baja, dll.',
            'gambar' => 'https://www.bosch-pt.co.id/id/id/ocsmedia/7669-54/application-image/1434x828/bor-impact-gsb-10-re-06012161k1.png',
        ]
    ];

    //Data dummy untuk lokasi penyimpanan
    private $dataDummyLokasiPenyimpanan = [
        [
            'id' => 1,
            'nama_lokasi' => 'Gudang A',
        ]
    ];

    //Data dummy untuk No.Seri Alat
    private $dataDummySeri = [
        [
            'id' => 1,
            'no_seri_alat' => '1122wscj121',
            'kode_alat' => '1-C3-B0-2-01',
            'id_layout' => 1,
            'stok' => 1,
            'harga' => 5000,
            'tanggal_masuk' => '2024-01-01',
            'status' => 'Error'
        ],
        [
            'id' => 2,
            'no_seri_alat' => '1122wscj122',
            'kode_alat' => '1-C3-B0-2-01',
            'harga' => 5000,
            'stok' => 1,
            'status' => 'Error'
        ],
        [
            'id' => 3,
            'no_seri_alat' => '1122wscj123',
            'kode_alat' => '1-C3-B0-2-01',
            'harga' => 5000,
            'stok' => 1,
            'status' => 'OK'
        ],
        [
            'id' => 4,
            'no_seri_alat' => '1122wscj124',
            'kode_alat' => '1-C3-B0-2-01',
            'harga' => 5000,
            'stok' => 1,
            'status' => 'Rusak'
        ],
        [
            'id' => 5,
            'no_seri_alat' => '1122wscj125',
            'kode_alat' => '1-C3-B0-2-01',
            'harga' => 5000,
            'stok' => 1,
            'status' => 'Rusak'
        ],
        [
            'id' => 6,
            'no_seri_alat' => '1122wscj126',
            'kode_alat' => '1-C3-B0-2-01',
            'harga' => 5000,
            'stok' => 1,
            'status' => 'OK'
        ],        
        [
            'id' => 7,
            'no_seri_alat' => '1122wscj127',
            'kode_alat' => '1-C3-B0-2-01',
            'harga' => 5000,
            'stok' => 1,
            'status' => 'Hilang'
        ],
        [
            'id' => 8,
            'no_seri_alat' => '1122wscj128',
            'kode_alat' => '1-C3-B0-2-01',
            'harga' => 5000,
            'stok' => 1,
            'status' => 'OK'
        ],
        [
            'id' => 9,
            'no_seri_alat' => '1122wscj129',
            'kode_alat' => '1-C3-B0-2-01',
            'harga' => 5000,
            'stok' => 1,
            'status' => 'OK'
        ],
        [
            'id' => 10,
            'no_seri_alat' => '1122wscj130',
            'kode_alat' => '1-C3-B0-2-01',
            'harga' => 5000,
            'stok' => 1,
            'status' => 'OK'
        ],
        [
            'id' => 11,
            'no_seri_alat' => '1122wscj131',
            'kode_alat' => '1-C3-B0-2-01',
            'harga' => 5000,
            'stok' => 1,
            'status' => 'OK'
        ],
        [
            'id' => 12,
            'no_seri_alat' => '1122wscj132',
            'kode_alat' => '1-C3-B0-2-01',
            'harga' => 5000,
            'stok' => 1,
            'status' => 'OK'
        ],
        [
            'id' => 13,
            'no_seri_alat' => 'B001',
            'kode_alat' => '2-B1-B0-1-02',
            'id_layout' => 1,
            'tanggal_masuk' => '2025-01-01',
            'harga' => 1800000,
            'stok' => 1,
            'status' => 'OK'
        ],
    ];

    //Data dummy untuk peminjaman alat
    private $dataDummyPeminjaman = [
        [
            'id' => 1,
            'no_pinjam' => 'P001',
            'id_no_seri_alat' => 8,
            'id_alat' => 1,
            'kode_alat' => '1-C3-B0-2-01',
            'id_user' => 1,
            'stok' => 1,
            'tanggal_pinjam' => '2025-01-20',
            'tanggal_kembali' => '2025-01-31',
            'detail_peminjaman' => 'Peminjaman alat untuk kegiatan',
            'status' => 'Sedang Dipinjam'
        ],
    ];

    //Data dummy untuk permintaan alat
    private $dataDummyPermintaan = [
        [
            'id' => 1,
            'id_user' => 1,
            'no_permintaan' => 'PR001',
            'kode_alat' => '1-C3-B0-2-01',
            'id_no_seri_alat' => 3,
            'id_alat' => 1,
            'jumlah' => 1,
            'tanggal_permintaan' => '2025-01-01',
            'status' => 'Proses',
            'keterangan' => 'Permintaan alat untuk kebutuhan proyek',
        ],
    ];

    //Data dummy untuk perawatan alat
    private $dataDummyPerawatan = [
        [
            'id' => 1,
            'no_rawat' => 'R001',
            'id_no_seri_alat' => 10,
            'id_alat' => 1,
            'id_staff' => 1,
            'detail_perawatan' => 'Perawatan alat untuk memperbaiki kerusakan',
            'tanggal_perawatan' => '2022-01-19',
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '09:00:00',
            'status' => 'Belum',            
        ],
    ];

    // Data dummy untuk tabel staff
    private $dataDummyStaff = [
        [
            'id' => 1,
            'nama_staff' => 'John Doe',
            'jabatan' => 'Teknisi',
            'departemen' => 'Teknik',
        ],
        [
            'id' => 2,
            'nama_staff' => 'Jane Doe',
            'jabatan' => 'Supervisor',
            'departemen' => 'Manajemen',
        ],
    ];

    //Data dummy untuk tabel pengguna
    private $dataDummyPengguna = [
        [
            'id' => 1,
            'nama_pengguna' => 'Gus Tri',
            'username' => 'gus.tri',
            'password' => 'password123',
            'level' => 'staff',
            'divisi' => 'Teknik',
        ]
    ];

    // Data dummy untuk tabel error
    private $dataDummyError = [
        [
            'id' => 1,
            'kode_alat' => '1-C3-B0-2-01',
            'id_alat' => 1,
            'id_staff_analisa' => 1,
            'id_staff_perbaikan' => 2,
            'tanggal_error' => '2025-02-01',
            'tanggal_perbaikan' => null,
            'stok_error' => 1,
            'deskripsi_error' => 'Sensor tidak berfungsi',
            'deskripsi_perbaikan' => 'Sensor diganti',            
            'id_no_seri_alat' => 1,
        ],
        [
            'id' => 2,
            'kode_alat' => '1-C3-B0-2-01', // Alat yang sama dengan ID sebelumnya
            'id_staff_analisa' => 2,
            'id_staff_perbaikan' => 1,
            'tanggal_error' => '2022-02-10',
            'tanggal_perbaikan' => null,
            'stok_error' => 1,
            'deskripsi_error' => 'Koneksi kabel terputus',
            'deskripsi_perbaikan' => 'Kabel diganti',            
            'id_no_seri_alat' => 2,
        ]
    ];
    
    //Data dummy untuk tabel rusak detail
    private $dataDummyRusak = [
        [
            'id' => 1,
            'id_alat' => 1,
            'kode_alat' => '1-C3-B0-2-01',
            'id_staff_kerusakan' => 1,
            'tanggal_kerusakan' => '2025-01-01',
            'deskripsi_kerusakan' => 'Sensor rusak',
            'stok_kerusakan' => 1,
            'id_no_seri_alat' => 4,
        ],
        [
            'id' => 2,
            'kode_alat' => '1-C3-B0-2-01',
            'id_staff_kerusakan' => 2,
            'tanggal_kerusakan' => '2025-02-10',
            'deskripsi_kerusakan' => 'Koneksi kabel terputus',
            'stok_kerusakan' => 1,
            'id_no_seri_alat' => 5,
        ]
    ];

    //Data dummy untuk tabel musnah detail
    private $dataDummyMusnah = [
        [
            'id' => 1,
            'kode_alat' => '1-C3-B0-2-01',
            'id_staff_musnah' => 1,
            'tanggal_musnah' => '2025-01-01',
            'deskripsi_musnah' => 'Baterai lemah',
            'stok_musnah' => 1,
            'status' => 'Proses',
            'fileUrl' => 'https://library.uns.ac.id/wp-content/uploads/2016/04/EBOOK.pdf',
            'isImage' => false,
            'id_no_seri_alat' => 6,
        ]
    ];

    //Data dummy untuk tabel hilang detail
    private $dataDummyHilang = [
        [
            'id' => 1,
            'kode_alat' => '1-C3-B0-2-01',
            'id_alat' => 1,
            'id_user_hilang' => 1,
            'id_divisi_hilang' => 1,
            'tanggal_hilang' => '2025-01-01' ,
            'tanggal_ganti' => null,
            'detail_hilang' => 'Sengaja hilang',
            'detail_ganti' => null,
            'stok_hilang' => 1,
            'id_no_seri_alat' => 7,
        ]
    ];

    //Data dummy riwayat Alat
    private $dataDummyRiwayatAlat = [
        [
            'id' => 1,
            'id_no_seri_alat' => 1,
            'id_staff' => 1,
            'id_layout' => 1,
            'kondisi' => 'Error',
            'tanggal' => '2025-02-01',
            'keterangan' => 'Sensor tidak berfungsi.'
        ],
    ];

    // Index: Tampilkan semua data alat dengan stok yang sudah dikurangi error
    public function index()
    {
        // Menghitung stok setelah dikurangi error
        $dataWithUpdatedStok = collect($this->dataDummy)->map(function ($alat) {
            // Ambil semua error terkait alat ini
            $totalStokError = collect($this->dataDummyError)->where('kode_alat', $alat['kode_alat'])->sum('stok_error');
            $totalStokRusak = collect($this->dataDummyRusak)->where('kode_alat', $alat['kode_alat'])->sum('stok_kerusakan');
            $totalStokMusnah = collect($this->dataDummyMusnah)->where('kode_alat', $alat['kode_alat'])->sum('stok_musnah');
            $totalStokHilang = collect($this->dataDummyHilang)->where('kode_alat', $alat['kode_alat'])->sum('stok_hilang');
            $totalStokPermintaan = collect($this->dataDummyPermintaan)->where('kode_alat', $alat['kode_alat'])->sum('jumlah');
            $totalStokPeminjaman = collect($this->dataDummyPeminjaman)->where('kode_alat', $alat['kode_alat'])->sum('stok');
            
            // Hitung stok akhir
            $alat['stok_akhir'] = $alat['stok_awal'] - ($totalStokError + $totalStokRusak + $totalStokMusnah + $totalStokHilang + $totalStokPermintaan + $totalStokPeminjaman);
            if ($alat['stok_akhir'] < 0) {
                $alat['stok_akhir'] = 0; // Pastikan stok tidak negatif
            }

            return $alat;
        });

        return response()->json([
            'data' => $dataWithUpdatedStok,
        ]);
    }

    // Store: Tambahkan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_alat' => 'required|string|max:255',
            'merek_alat' => 'required|string|max:255',
            'tipe_alat' => 'nullable|string',
            'unit_alat' => 'nullable|string',
            'stok_awal' => 'required|integer', // Hanya stok_awal yang diinput
            'lokasi_penyimpanan' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'harga' => 'required|string|max:255',
            'no_seri_alat' => 'required|string|max:255',
            'tahun_masuk' => 'nullable|string',
            'pembelian' => 'nullable|string',
            'sumber_alat' => 'nullable|string',
            'vendor' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|string',
        ]);

        // Tambahkan stok_akhir dengan nilai yang sama seperti stok_awal
        $newAlat = [
            'id' => count($this->dataDummy) + 1,
            'stok_akhir' => $validated['stok_awal'], // Stok akhir diisi otomatis
        ] + $validated;

        $this->dataDummy[] = $newAlat;

        return response()->json([
            'message' => 'Data alat berhasil ditambahkan.',
            'data' => $newAlat,
        ], 201);
    }

    // Show: Tampilkan detail data berdasarkan ID
    public function show($id)
    {
        // Cari alat berdasarkan ID
        $alat = collect($this->dataDummy)->firstWhere('id', $id);

        if (!$alat) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Ambil semua error terkait alat ini dan hitung total stok error
        $totalStokError = collect($this->dataDummyError)
                            ->where('kode_alat', $alat['kode_alat'])
                            ->sum('stok_error');

        $totalStokRusak = collect($this->dataDummyRusak)
                            ->where('kode_alat', $alat['kode_alat'])
                            ->sum('stok_kerusakan');

        $totalStokMusnah = collect($this->dataDummyMusnah)
                            ->where('kode_alat', $alat['kode_alat'])
                            ->sum('stok_musnah');
        
        $totalStokHilang = collect($this->dataDummyHilang)
                            ->where('kode_alat', $alat['kode_alat'])
                            ->sum('stok_hilang');

        $totalStokPermintaan = collect($this->dataDummyPermintaan)
                                -> where('kode_alat', $alat['kode_alat'])
                                -> sum('jumlah');

        $totalStokPeminjaman = collect($this->dataDummyPeminjaman)
                                -> where('kode_alat', $alat['kode_alat'])
                                -> sum('stok');

        // Ambil semua harga terkait alat ini dan hitung total harga
        $totalHarga = collect($this->dataDummySeri)
        ->where('kode_alat', $alat['kode_alat'])
        ->sum('harga');

        // Kurangi stok alat dengan total stok error
        $alat['stok_akhir'] = $alat['stok_awal'] - ($totalStokError + $totalStokRusak + $totalStokMusnah + $totalStokHilang + $totalStokPermintaan + $totalStokPeminjaman);

        // Tambahkan total harga ke dalam data alat
        $alat['harga_total'] = $totalHarga > 0 ? $totalHarga : null;

        // Pastikan stok tidak negatif
        if ($alat['stok_akhir'] < 0) {
            $alat['stok_akhir'] = 0; // Pastikan stok tidak negatif
        }

        // Kembalikan data alat yang sudah diperbarui
        return response()->json($alat);
    }


    // Edit: Ambil data untuk diedit
    public function edit($id)
    {
        $alat = collect($this->dataDummy)->firstWhere('id', $id);

        if (!$alat) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => $alat,
        ]);
    }

    // Update: Perbarui data berdasarkan ID
    public function update(Request $request, $id)
    {
        $alatKey = collect($this->dataDummy)->search(fn($item) => $item['id'] == $id);

        if ($alatKey === false) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama_alat' => 'required|string|max:255',
            'merek_alat' => 'required|string|max:255',
            'tipe_alat' => 'nullable|string',
            'unit_alat' => 'nullable|string',
            'stok' => 'required|integer',
            'lokasi_penyimpanan' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'harga' => 'required|string|max:255',
            'no_seri_alat' => 'required|string|max:255',
            'tahun_masuk' => 'nullable|string',
            'pembelian' => 'nullable|string',
            'sumber_alat' => 'nullable|string',
            'vendor' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|string',
        ]);

        $this->dataDummy[$alatKey] = array_merge($this->dataDummy[$alatKey], $validated);

        return response()->json([
            'message' => 'Data alat berhasil diperbarui.',
            'data' => $this->dataDummy[$alatKey],
        ]);
    }

    // Destroy: Hapus data berdasarkan ID
    public function destroy($id)
    {
        $key = collect($this->dataDummy)->search(fn($item) => $item['id'] == $id);

        if ($key === false) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        unset($this->dataDummy[$key]);

        return response()->json(['message' => 'Data alat berhasil dihapus.']);
    }

    // Index: Tampilkan semua data staff
    public function indexStaff()
    {
        return response()->json([
            'data' => $this->dataDummyStaff,
        ]);
    }

    // Store: Tambahkan data staff baru ```php
    public function storeStaff(Request $request)
    {
        $validated = $request->validate([
            'nama_staff' => 'required|string',
            'jabatan' => 'required|string',
            'departemen' => 'required|string',
        ]);

        $newStaff = [
            'id' => count($this->dataDummyStaff) + 1,
        ] + $validated;

        $this->dataDummyStaff[] = $newStaff;

        return response()->json([
            'message' => 'Data staff berhasil ditambahkan.',
            'data' => $newStaff,
        ], 201);
    }

    // Show: Tampilkan detail data staff berdasarkan ID
    public function showStaff($id)
    {
        $staff = collect($this->dataDummyStaff)->firstWhere('id', $id);

        if (!$staff) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($staff);
    }

    // Edit: Ambil data staff untuk diedit
    public function editStaff($id)
    {
        $staff = collect($this->dataDummyStaff)->firstWhere('id', $id);

        if (!$staff) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => $staff,
        ]);
    }

    // Update: Perbarui data staff berdasarkan ID
    public function updateStaff(Request $request, $id)
    {
        $staffKey = collect($this->dataDummyStaff)->search(fn($item) => $item['id'] == $id);

        if ($staffKey === false) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama_staff' => 'required|string',
            'jabatan' => 'required|string',
            'departemen' => 'required|string',
        ]);

        $this->dataDummyStaff[$staffKey] = array_merge($this->dataDummyStaff[$staffKey], $validated);

        return response()->json([
            'message' => 'Data staff berhasil diperbarui.',
            'data' => $this->dataDummyStaff[$staffKey],
        ]);
    }

    // Destroy: Hapus data staff berdasarkan ID
    public function destroyStaff($id)
    {
        $key = collect($this->dataDummyStaff)->search(fn($item) => $item['id'] == $id);

        if ($key === false) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        unset($this->dataDummyStaff[$key]);

        return response()->json(['message' => 'Data staff berhasil dihapus.']);
    }

    // Index: Tampilkan semua data error
    public function indexError()
    {
        return response()->json([
            'data' => $this->dataDummyError,
        ]);
    }

    // Store: Tambahkan data error baru
    public function storeError(Request $request)
    {
        $validated = $request->validate([
            'kode_alat' => 'required|string',
            'id_staff_analisa' => 'required|integer',
            'id_staff_perbaikan' => 'required|integer',
            'tanggal_error' => 'required|date',
            'tanggal_perbaikan' => 'nullable|date',
            'deskripsi_error' => 'required|string',
            'deskripsi_perbaikan' => 'nullable|string',
            'stok_error' => 'required|integer',
            'id_no_seri_alat' => 'required|integer', // ID no seri alat yang error
        ]);

        // Cari no seri alat berdasarkan ID
        $noSeriKey = collect($this->dataDummySeri)->search(fn($item) => $item['id'] == $validated['id_no_seri_alat']);

        if ($noSeriKey === false) {
            return response()->json(['error' => 'No seri alat tidak ditemukan'], 404);
        }

        // Ubah status no seri alat menjadi "Error"
        $this->dataDummySeri[$noSeriKey]['status'] = 'Error';

        // Tambahkan data error baru
        $newError = [
            'id' => count($this->dataDummyError) + 1,
        ] + $validated;

        $this->dataDummyError[] = $newError;

        // Update stok pada data alat
        $alatKey = collect($this->dataDummy)->search(fn($item) => $item['kode_alat'] == $newError['kode_alat']);
        if ($alatKey !== false) {
            $this->dataDummy[$alatKey]['stok_akhir'] -= $newError['stok_error'];
            if ($this->dataDummy[$alatKey]['stok_akhir'] < 0) {
                $this->dataDummy[$alatKey]['stok_akhir'] = 0;
            }
        }

        return response()->json([
            'message' => 'Data error berhasil ditambahkan.',
            'data' => $newError,
        ], 201);
    }

    // Show: Tampilkan detail data error berdasarkan ID
    public function showError($id)
    {
        $error = collect($this->dataDummyError)->firstWhere('id', $id);
    
        if (!$error) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
    
        // Ambil data staff untuk PIC Analisa dan PIC Perba ikan
        $staffAnalisa = collect($this->dataDummyStaff)->firstWhere('id', $error['id_staff_analisa']);
        $staffPerbaikan = collect($this->dataDummyStaff)->firstWhere('id', $error['id_staff_perbaikan']);
    
        // Gabungkan data error dengan data staff
        $error['staff_analisa'] = $staffAnalisa;
        $error['staff_perbaikan'] = $staffPerbaikan;
    
        return response()->json($error);
    }

    // ByKodeAlat
    public function getErrorsByKodeAlat($kodeAlat)
    {
        $errors = collect($this->dataDummyError)->filter(function ($error) use ($kodeAlat) {
            return $error['kode_alat'] === $kodeAlat;
        })->map(function ($error) {
            $error['staff_analisa'] = collect($this->dataDummyStaff)->firstWhere('id', $error['id_staff_analisa']);
            $error['staff_perbaikan'] = collect($this->dataDummyStaff)->firstWhere('id', $error['id_staff_perbaikan']);
            $error['no_seri_alat'] = collect($this->dataDummySeri)->firstWhere('id', $error['id_no_seri_alat']);
            return $error;
        });

        if ($errors->isEmpty()) {
            return response()->json(['error' => 'Tidak ada error untuk kode alat ini'], 404);
        }

        return response()->json($errors->values());
    }

    // Edit: Ambil data error untuk diedit
    public function editError($id)
    {
        $error = collect($this->dataDummyError)->firstWhere('id', $id);

        if (!$error) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        //Ambil data staff, no seri alat, dan alat
        $alat = collect($this->dataDummy)->firstWhere('id', $error['id_alat']);
        $noSeriAlat = collect($this->dataDummySeri)->firstWhere('id', $error['id_no_seri_alat']);
        $staffAnalisa = collect($this->dataDummyStaff)->firstWhere('id', $error['id_staff_analisa']);

        //Gabungkan data alat error
        $error['alat'] = $alat;
        $error['no_seri_alat'] = $noSeriAlat;
        $error['staff_analisa'] = $staffAnalisa;

        return response()->json([
            'data' => $error,
        ]);
    }

    // Update: Perbarui data error berdasarkan ID
    public function updateError(Request $request, $id)
    {
        $errorKey = collect($this->dataDummyError)->search(fn($item) => $item['id'] == $id);

        if ($errorKey === false) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'kode_alat' => 'required|string',
            'id_staff_analisa' => 'required|integer',
            'id_staff_perbaikan' => 'required|integer',
            'tanggal_error' => 'required|date',
            'tanggal_perbaikan' => 'nullable|date',
            'deskripsi_error' => 'required|string',
            'deskripsi_perbaikan' => 'nullable|string',
            'stok_error' => 'required|integer',
            'id_no_seri_alat' => 'required|integer', // ID no seri alat yang error
        ]);

        // Cari no seri alat berdasarkan ID
        $noSeriKey = collect($this->dataDummySeri)->search(fn($item) => $item['id'] == $validated['id_no_seri_alat']);

        if ($noSeriKey === false) {
            return response()->json(['error' => 'No seri alat tidak ditemukan'], 404);
        }

        // Ubah status no seri alat menjadi "Error"
        $this->dataDummySeri[$noSeriKey]['status'] = 'Error';

        // Update data error
        $this->dataDummyError[$errorKey] = array_merge($this->dataDummyError[$errorKey], $validated);

        // Update stok pada data alat
        $alatKey = collect($this->dataDummy)->search(fn($item) => $item['kode_alat'] == $this->dataDummyError[$errorKey]['kode_alat']);
        if ($alatKey !== false) {
            $this->dataDummy[$alatKey]['stok_akhir'] -= $this->dataDummyError[$errorKey]['stok_error'];
            if ($this->dataDummy[$alatKey]['stok_akhir'] < 0) {
                $this->dataDummy[$alatKey]['stok_akhir'] = 0;
            }
        }

        return response()->json([
            'message' => 'Data error berhasil diperbarui.',
            'data' => $this->dataDummyError[$errorKey],
        ]);
    }

    // Destroy: Hapus data error berdasarkan ID
    public function destroyError($id)
    {
        $key = collect($this->dataDummyError)->search(fn($item) => $item['id'] == $id);

        if ($key === false) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Update stok pada data alat sebelum menghapus
        $stokError = $this->dataDummyError[$key]['stok_error'];
        $alatKey = collect($this->dataDummy)->search(fn($item) => $item['kode_alat'] == $this->dataDummyError[$key]['kode_alat']);
        if ($alatKey !== false) {
            $this->dataDummy[$alatKey]['stok'] += $stokError;
        }

        unset($this->dataDummyError[$key]);

        return response()->json(['message' => 'Data error berhasil dihapus.']);
    }

    //Edit: Ambil Data rusak untuk diedit
    public function editRusak($id){
        $rusak = collect($this->dataDummyRusak)->firstWhere('id', $id);
        if (!$rusak) {
            return response()->json(['error' => 'Data rusak tidak ditemukan'], 404);
        }

        //Ambil data staff, no seri, dan alat
        $alat = collect($this->dataDummy)->firstWhere('id', $rusak['id_alat']);
        $noSeriAlat = collect($this->dataDummySeri)->firstWhere('id', $rusak['id_no_seri_alat']);
        $staff = collect($this->dataDummyStaff)->firstWhere('id', $rusak['id_staff_kerusakan']);

        //Gabungkan data alat rusak
        $rusak['alat'] = $alat;
        $rusak['no_seri_alat'] = $noSeriAlat;
        $rusak['staff'] = $staff;

        return response()->json([
            'data' => $rusak,
        ]);

    }

    //ByKodeAlat Rusak
    public function getRusakByKodeAlat($kodeAlat) {
        $datarusak = collect($this->dataDummyRusak)->filter(function($rusak) use ($kodeAlat) {
            return $rusak['kode_alat'] === $kodeAlat;
        })->map(function ($rusak) {
            $rusak['staff_kerusakan'] = collect($this->dataDummyStaff)->firstWhere('id', $rusak['id_staff_kerusakan']);
            $rusak['no_seri_alat'] = collect($this->dataDummySeri)->firstWhere('id', $rusak['id_no_seri_alat']);            
            return $rusak;
        });

        if ($datarusak->isEmpty()) {
            return response()->json(['error' => 'Data rusak tidak ditemukan'], 404);
        }

        return response()->json($datarusak->values());
    }

    //ByKodeAlat Musnah
    public function getMusnahByKodeAlat($kodeAlat) {
        $datamusnah = collect($this->dataDummyMusnah)->filter(function($musnah) use ($kodeAlat) {
            return $musnah['kode_alat'] === $kodeAlat;
        })->map(function ($musnah) {
            $musnah['staff_pemusnahan'] = collect($this->dataDummyStaff)->firstWhere('id', $musnah['id_staff_musnah']);
            $musnah['no_seri_alat'] = collect($this->dataDummySeri)->firstWhere('id', $musnah['id_no_seri_alat']);
            return $musnah;
        });

        if ($datamusnah->isEmpty()) {
            return response()->json(['error' => 'Data musnah tidak ditemukan'], 404);
        }

        return response()->json($datamusnah->values());
    }

    // Index: Tampilkan semua data pengguna
    public function indexPengguna()
    {
        return response()->json([
            'data' => $this->dataDummyPengguna,
        ]);
    }

    // Show: Tampilkan detail data pengguna berdasarkan ID
    public function showPengguna($id)
    {
        $user = collect($this->dataDummyPengguna)->firstWhere('id', $id);

        if (!$user) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($user);
    }

    //Edit: Data Hilang
    public function editHilang($id){
        $hilang = collect($this->dataDummyHilang)->firstWhere('id', $id);

        if (!$hilang) {
            return response()->json(['error' => 'Data hilang tidak ditemukan'], 404);
        }

        //Ambil data pengguna, no seri dan alat
        $alat = collect($this->dataDummy)->firstWhere('id', $hilang['id_alat']);
        $noSeriAlat = collect($this->dataDummySeri)->firstWhere('id', $hilang['id_no_seri_alat']);
        $pengguna = collect($this->dataDummyPengguna)->firstWhere('id', $hilang['id_user_hilang']);
        $divsiPengguna = collect($this->dataDummyPengguna)->firstWhere('id', $hilang['id_divisi_hilang']);

        //Gabungkan data alat rusak
        $rusak['alat'] = $alat;
        $rusak['no_seri_alat'] = $noSeriAlat;
        $rusak['pengguna'] = $pengguna;
        $rusak['divisi_pengguna'] = $divsiPengguna;

        return response()->json([
            'data' => $rusak,
        ]);
    }

    //ByKodeAlat Hilang
    public function getHilangByKodeAlat($kodeAlat) {
        $datahilang = collect($this->dataDummyHilang)->filter(function($hilang) use ($kodeAlat) {
            return $hilang['kode_alat'] === $kodeAlat;
        })->map(function ($hilang) {
            $hilang['user_penghilang'] = collect($this->dataDummyPengguna)->firstWhere('id', $hilang['id_user_hilang']);
            $hilang['divisi_penghilang'] = collect($this->dataDummyPengguna)->firstWhere('id', $hilang['id_divisi_hilang']);
            $hilang['no_seri_alat'] = collect($this->dataDummySeri)->firstWhere('id', $hilang['id_no_seri_alat']);
            return $hilang;
        });

        if ($datahilang->isEmpty()) {
            return response()->json(['error' => 'Data hilang tidak ditemukan'], 404);
        }

        return response()->json($datahilang->values());
    }

    //Get Lokasi Penyimpanan
    public function getLokasiPenyimpanan()
    {
        $lokasiPenyimpanan = collect($this->dataDummy)->filter(function ($item) {
            return !empty($item['lokasi_penyimpanan']);
        })->pluck('lokasi_penyimpanan')->map(function ($item) {
            return explode('","', $item);
        })->flatten()->unique()->values();

        return response()->json([
            'data' => $lokasiPenyimpanan,
        ]);
    }

    //Index: Tampilkan semua data peminjaman alat 
    public function indexPeminjaman()
    {
        // Gabungkan data peminjaman dengan data alat dan pengguna
        $dataPeminjamanAlat = collect($this->dataDummyPeminjaman)->map(function ($peminjamanalat) {
            // Ambil data pengguna berdasarkan ID pengguna
            $pengguna = collect($this->dataDummyPengguna)->firstWhere('id', $peminjamanalat['id_user']);

            // Gabungkan data peminjaman dengan data alat dan pengguna
            $peminjamanalat['pengguna'] = $pengguna;

            return $peminjamanalat;
        });

        return response()->json([
            'data' => $dataPeminjamanAlat,
        ]);
    }

    // Show: Tampilkan detail data peminjaman alat berdasarkan ID
    public function showPeminjaman($id)
    {
        // Cari data peminjaman berdasarkan ID
        $peminjamanalat = collect($this->dataDummyPeminjaman)->firstWhere('id', $id);

        if (!$peminjamanalat) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Ambil data alat berdasarkan ID alat
        $alat = collect($this->dataDummy)->firstWhere('id', $peminjamanalat['id_alat']);
        // Ambil data pengguna berdasarkan ID pengguna
        $pengguna = collect($this->dataDummyPengguna)->firstWhere('id', $peminjamanalat['id_user']);

        // Gabungkan data peminjaman dengan data alat dan pengguna
        $peminjamanalat['alat'] = $alat;
        $peminjamanalat['pengguna'] = $pengguna;

        return response()->json($peminjamanalat);
    }

    // ByKodeAlat No Seri Belum digunakan: Tampilkan detail no Seri berdasarkan kode alat
    public function getNoSeriBelumDigunakanByKodeAlat($kodeAlat) {
        $datanoSeri = collect($this->dataDummySeri)
            ->where('kode_alat', $kodeAlat)
            ->whereNotIn('id', collect($this->dataDummyPermintaan)->pluck('id_no_seri_alat'))
            ->whereNotIn('id', collect($this->dataDummyMusnah)->pluck('id_no_seri_alat'))
            ->map(function ($noseri) {
                if (isset($noseri['id_layout'])) {
                    $noseri['layout'] = collect($this->dataDummyLokasiPenyimpanan)->firstWhere('id', $noseri['id_layout']);
                }
                return $noseri;
            })
            ->sortBy('id')
            ->values();

        if ($datanoSeri->isEmpty()) {
            return response()->json(['error' => 'Data no seri tidak ditemukan'], 404);
        }

        return response()->json($datanoSeri);
    }

    //index: Tampilkan semua data No Seri alat
    public function indexNoSeri() {
        return response()->json([
            'data' => $this->dataDummySeri,
        ]);
    }

    public function getNoSeriByKodeAlat($kode_alat)
    {
        $no_seri = array_filter($this->dataDummySeri, function ($item) use ($kode_alat) {
            return $item['kode_alat'] == $kode_alat;
        });

        return response()->json($no_seri);
    }

    //Edit: No Seri Belum Digunakan
    public function editNoSeriBelumDigunakan($id)
    {
        $datanoSeri = collect($this->dataDummySeri)->firstWhere('id', $id);

        if (!$datanoSeri) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => $datanoSeri,
        ]);
    }

    //Index: Permintaan Alat
    public function indexPermintaan () {
        $dataPermintaan = collect($this->dataDummyPermintaan)->map(function($permintaan) {
            $pengguna = collect($this->dataDummyPengguna)->firstWhere('id', $permintaan['id_user']);
            $alat = collect($this->dataDummy)->firstWhere('id' , $permintaan['id_alat']);

            $permintaan['pengguna'] = $pengguna;
            $permintaan['alat'] = $alat;

            return $permintaan;
        });
        return response()->json([
            'data' => $dataPermintaan,
            ]);
    }

    // Show: Tampilkan data Permintaan Alat
    public function showPermintaan ($id) {
        $dataPermintaan = collect($this->dataDummyPermintaan)->firstWhere('id', $id);

        if(!$dataPermintaan) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $alat = collect($this->dataDummy)->firstWhere('id', $dataPermintaan['id_alat']);
        $pengguna = collect($this->dataDummyPengguna)->firstWhere('id', $dataPermintaan['id_user']);

        $dataPermintaan['alat'] = $alat;
        $dataPermintaan['pengguna'] = $pengguna;
        
        return response()->json($dataPermintaan);
    }

    // ByNoPermintaan: Permintaan By No Permintaan 
    public function byNoPermintaan ($noPermintaan) {
        $dataPermintaan = collect($this->dataDummyPermintaan)->filter(function($permintaan) use ($noPermintaan) {
            return $permintaan['no_permintaan'] == $noPermintaan && $permintaan;
        })->map(function($permintaan) {
            $pengguna = collect($this->dataDummyPengguna)->firstWhere('id', $permintaan['id_user']);
            $alat = collect($this->dataDummy)->firstWhere('id' , $permintaan['id_alat']);
            $permintaan['no_seri_alat'] = collect($this->dataDummySeri)->firstWhere('id', $permintaan['id_no_seri_alat']);
            return $permintaan;
        });

        if ($dataPermintaan->isEmpty()) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($dataPermintaan);
    }

    //ByKodeAlat : Tampilkan detail data permintaan alat berdasarkan no seri alat
    public function getPermintaanAlatByKodeAlat($kodeAlat) {
        $dataPermintaanAlat = collect($this->dataDummyPermintaan)->filter(function($permintaanalat) use ($kodeAlat) {
            return $permintaanalat['kode_alat'] == $kodeAlat && $permintaanalat;
        })->map(function ($permintaanalat){
            $permintaanalat['no_seri_alat'] = collect($this->dataDummySeri)->firstWhere('id', $permintaanalat['id_no_seri_alat']);
            $permintaanalat['pemohon'] = collect($this->dataDummyPengguna)->firstWhere('id', $permintaanalat['id_user']);
            return $permintaanalat;
        });

        if ($dataPermintaanAlat->isEmpty()) {
            return response()->json(['error' => 'Data permintaan alat tidak ditemukan'], 404);
        }

        return response()->json($dataPermintaanAlat);
        
    }

    //Edit : Detail Rincian Permintaan Alat
    public function editRincianPermintaanAlat($id) {
        $dataPermintaanAlat = collect($this->dataDummyPermintaan)->firstWhere('id', $id);

        if(!$dataPermintaanAlat) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        //Ambil data pengguna dan no seri alat
        $pengguna = collect($this->dataDummyPengguna)->firstWhere('id', $dataPermintaanAlat['id_user']);
        $noSeriAlat = collect($this->dataDummySeri)->firstWhere('id', $dataPermintaanAlat['id_no_seri_alat']);

        //Gabungkan data permintaan alat dengan data pengguna dan no seri alat
        $dataPermintaanAlat['pengguna'] = $pengguna;
        $dataPermintaanAlat['no_seri_alat'] = $noSeriAlat;

        return response()->json([
            'data' => $dataPermintaanAlat,
        ]);
    }

    //ByKodeAlat : Tampilkan detail data peminjaman alat berdasarkan no seri alat
    public function getPeminjamanAlatByKodeAlat($kodeAlat) {
        $dataPeminjamanAlat = collect($this->dataDummyPeminjaman)->filter(function($peminjamanalat) use ($kodeAlat) {
            return $peminjamanalat['kode_alat'] == $kodeAlat && $peminjamanalat;
        })->map(function ($peminjamanalat){
            $peminjamanalat['no_seri_alat'] = collect($this->dataDummySeri)->firstWhere('id', $peminjamanalat['id_no_seri_alat']);
            $peminjamanalat['pengguna'] = collect($this->dataDummyPengguna)->firstWhere('id', $peminjamanalat['id_user']);
            return $peminjamanalat;
        });

        if ($dataPeminjamanAlat->isEmpty()) {
            return response()->json(['error' => 'Data peminjaman alat tidak ditemukan'], 404);
        }

        return response()->json($dataPeminjamanAlat);
    }

    //ByNoPinjam : Tampilkan detail data peminjaman alat berdasarkan no pinjam
    public function getPeminjamanAlatByNoPinjam($noPinjam) {
        $dataPeminjamanAlat = collect($this->dataDummyPeminjaman)->filter(function($peminjamanalat) use ($noPinjam) {
            return $peminjamanalat['no_pinjam'] == $noPinjam && $peminjamanalat;
        })->map(function($peminjamanalat){
            $peminjamanalat['alat'] = collect($this->dataDummy)->firstWhere('id', $peminjamanalat['id_alat']);
            $peminjamanalat['no_seri_alat'] = collect($this->dataDummySeri)->firstWhere('id', $peminjamanalat['id_no_seri_alat']);
            $peminjamanalat['pengguna'] = collect($this->dataDummyPengguna)->firstWhere('id', $peminjamanalat['id_user']);
            return $peminjamanalat;
        });

        if ($dataPeminjamanAlat->isEmpty()) {
            return response()->json(['error' => 'Data peminjaman alat tidak ditemukan'], 404);
        }

        return response()->json($dataPeminjamanAlat);
    }

    //Edit Peminjaman : Detail Rincian Peminjaman Alat
    public function editRincianPeminjamanAlat($id) {
        $dataPeminjamanAlat = collect($this->dataDummyPeminjaman)->firstWhere('id', $id);

        if (!$dataPeminjamanAlat) {
            return response()->json(['error' => 'Data peminjaman alat tidak ditemukan'], 404);
        }

        //Ambil data alat, pengguna, dan no seri alat
        $alat = collect($this->dataDummy)->firstWhere('id', $dataPeminjamanAlat['id_alat']);
        $pengguna = collect($this->dataDummyPengguna)->firstWhere('id', $dataPeminjamanAlat['id_user']);
        $noSeriAlat = collect($this->dataDummySeri)->firstWhere('id', $dataPeminjamanAlat['id_no_seri_alat']);
        
        //Gabungkan data peminjaman alat
        $dataPeminjamanAlat['alat'] = $alat;
        $dataPeminjamanAlat['pengguna'] = $pengguna;
        $dataPeminjamanAlat['noseri'] = $noSeriAlat;

        return response()->json([
            'data' => $dataPeminjamanAlat,
        ]);
    }

    //Index: Perawatan Alat
    public function indexPerawatanAlat() {
        $dataPerawatanAlat = collect($this->dataDummyPerawatan)->map(function($perawatanlat){
            $noSeriAlat = collect($this->dataDummySeri)->firstWhere('id', $perawatanlat['id_no_seri_alat']);
            $staff = collect($this->dataDummyStaff)->firstWhere('id', $perawatanlat['id_staff']);
            $alat = collect($this->dataDummy)->firstWhere('id', $perawatanlat['id_alat']);

            if ($noSeriAlat && $staff && $alat) {
                $perawatanlat['no_seri_alat'] = $noSeriAlat;
                $perawatanlat['staff'] = $staff;
                $perawatanlat['alat'] = $alat;
            }

            return $perawatanlat;
        });

        if ($dataPerawatanAlat->isEmpty()) {
            return response()->json(['error' => 'Data perawatan alat tidak ditemukan'], 404);
        }

        return response()->json(['data' => $dataPerawatanAlat->toArray()]);
    }

    //Edit: Detail Rincian Perawatan Alat
    public function editPerawatanAlat($id) {
        $dataPerawatanAlat = collect($this->dataDummyPerawatan)->firstWhere('id', $id);

        if (!$dataPerawatanAlat) {
            return response()->json(['error' => 'Data perawatan alat tidak ditemukan'], 404);
        }

        //Ambil data alat, staff, dan no seri alat
        $alat = collect($this->dataDummy)->firstWhere('id', $dataPerawatanAlat['id_alat']);
        $staff = collect($this->dataDummyStaff)->firstWhere('id', $dataPerawatanAlat['id_staff']);
        $noSeri = collect($this->dataDummySeri)->firstWhere('id', $dataPerawatanAlat['id_no_seri_alat']);

        //Gabungkan data perawatan alat
        $dataPerawatanAlat['alat'] = $alat;
        $dataPerawatanAlat['staff'] = $staff;
        $dataPerawatanAlat['noseri'] = $noSeri;

        return response()->json([
            'data' => $dataPerawatanAlat,
        ]);
    }

    //Edit Rincian Perawatan Alat
    public function editRincianPerawatanAlat($id) {
        $dataPerawatanAlat = collect($this->dataDummyPerawatan)->firstWhere('id', $id);

        if(!$dataPerawatanAlat) {
            return response()->json(['error' => 'Data perawatan alat tidak ditemukan'], 404);
        }

        //Ambil data alat, staff, dan no seri alat
        $alat = collect($this->dataDummy)->firstWhere('id', $dataPerawatanAlat['id_alat']);
        $staff = collect($this->dataDummyStaff)->firstWhere('id', $dataPerawatanAlat['id_staff']);
        $noSeri = collect($this->dataDummySeri)->firstWhere('id', $dataPerawatanAlat['id_no_seri_alat']);

        //Gabungkan data perawatan alat
        $dataPerawatanAlat['alat'] = $alat;
        $dataPerawatanAlat['staff'] = $staff;
        $dataPerawatanAlat['noseri'] = $noSeri;

        return response()->json([
            'data' => $dataPerawatanAlat,
        ]);
    }

    //Show: Tampilkan detail data peminjaman alat berdasarkan ID
    public function showPerawatanAlat($id) {
        $dataPerawatanAlat = collect($this->dataDummyPerawatan)->firstWhere('id', $id);

        if (!$dataPerawatanAlat) {
            return response()->json(['error' => 'Data perawatan alat tidak ditemukan'], 404);
        }

        ///Ambil data alat, staff, dan no seri alat
        $alat = collect($this->dataDummy)->firstWhere('id', $dataPerawatanAlat['id_alat']);
        $staff = collect($this->dataDummyStaff)->firstWhere('id', $dataPerawatanAlat['id_staff']);
        $noSeri = collect($this->dataDummySeri)->firstWhere('id', $dataPerawatanAlat['id_no_seri_alat']);

        //Gabungkan data perawatan alat
        $dataPerawatanAlat['alat'] = $alat;
        $dataPerawatanAlat['staff'] = $staff;
        $dataPerawatanAlat['noseri'] = $noSeri;

        return response()->json($dataPerawatanAlat);
    }

    //ByNoPerawatan : Tampilkan detail data perawatan alat berdasarkan no perawatan
    public function getPerawatanAlatByNoPerawatan($noRawat) {
        $dataPerawatanAlat = collect($this->dataDummyPerawatan)->filter(function($perawatan) use ($noRawat) {
            return $perawatan['no_rawat'] == $noRawat && $perawatan;
        })->map(function($perawatan){
            $perawatan['noseri'] = collect($this->dataDummySeri)->firstWhere('id', $perawatan['id_no_seri_alat']);
            $perawatan['alat'] = collect($this->dataDummy)->firstWhere('id', $perawatan['id_alat']);
            $perawatan['staff'] = collect($this->dataDummyStaff)->firstWhere('id', $perawatan['id_staff']);
            return $perawatan;
        });

        if($dataPerawatanAlat->isEmpty()) {
            return response()->json(['error' => 'Data perawatan alat tidak ditemukan'], 404);
        }
        return response()->json($dataPerawatanAlat);
    }

    //ByNoSeri
    public function getRiwayatAlatByNoSeri($noSeri) {
        $dataRiwayatAlat = collect($this->dataDummyRiwayatAlat)->filter(function($riwayat) use ($noSeri) {
            return optional(collect($this->dataDummySeri)->firstWhere('id', $riwayat['id_no_seri_alat']))['no_seri_alat'] === $noSeri;
        })->map(function ($riwayat) {
            $noseriData = collect($this->dataDummySeri)->firstWhere('id', $riwayat['id_no_seri_alat']);
            $riwayat['no_seri_alat'] = $noseriData['no_seri_alat'] ?? null; // Ambil hanya no_seri_alat
            $riwayat['staff'] = collect($this->dataDummyStaff)->firstWhere('id', $riwayat['id_staff']);
            $riwayat['error'] = collect($this->dataDummyError)->firstWhere('id', $riwayat['id_error'] ?? null);
            $riwayat['rusak'] = collect($this->dataDummyRusak)->firstWhere('id', $riwayat['id_rusak'] ?? null);
            $riwayat['musnah'] = collect($this->dataDummyMusnah)->firstWhere('id_no_seri_alat', $riwayat['id_no_seri_alat']);
            $riwayat['hilang'] = collect($this->dataDummyHilang)->firstWhere('id_no_seri_alat', $riwayat['id_no_seri_alat']);            
            return $riwayat;
        });
    
        if ($dataRiwayatAlat->isEmpty()) {
            return response()->json(['error' => 'Data riwayat alat tidak ditemukan'], 404);
        }
    
        return response()->json($dataRiwayatAlat);
    }    

    //Show: Tampilkan Riwayat Alat
    public function showRiwayatAlat($id) {
        $dataRiwayatAlat = collect($this->dataDummyRiwayatAlat)->firstWhere('id', $id);
        if (!$dataRiwayatAlat) {
            return response()->json(['error' => 'Data riwayat alat tidak ditemukan'], 404);
        }
        $noseri = collect($this->dataDummySeri)->firstWhere('id', $dataRiwayatAlat['id_no_seri_alat']);
        $staff = collect($this->dataDummyStaff)->firstWhere('id', $dataRiwayatAlat['id_staff']);
        $layout = collect($this->dataDummyLokasiPenyimpanan)->firstWhere('id', $dataRiwayatAlat['id_layout']);
        $dataRiwayatAlat['noseri'] = $noseri;
        $dataRiwayatAlat['staff'] = $staff;
        $dataRiwayatAlat['layout'] = $layout;
        return response()->json($dataRiwayatAlat);
    }

    //Index: Tampilkan semua data riwayat alat 
    public function indexRiwayatAlat()
    {
        // Gabungkan data riwayat alat dengan data alat, no seri alat, staff, peminjaman alat, permintaan alat, perawatan alat, error alat, rusak alat, musnah alat, dan hilang alat
        $dataRiwayatAlat = collect($this->dataDummyRiwayatAlat)->map(function ($riwayatalat) {
            // Ambil data alat berdasarkan ID alat
            $alat = collect($this->dataDummy)->firstWhere('id', $riwayatalat['id_alat']);

            // Ambil data no seri alat berdasarkan ID no seri alat
            $noSeriAlat = collect($this->dataDummySeri)->firstWhere('id', $riwayatalat['id_no_seri_alat']);

            // Ambil data staff berdasarkan ID staff
            $staff = collect($this->dataDummyStaff)->firstWhere('id', $riwayatalat['id_staff']);

            $pengguna = collect($this->dataDummyPengguna)->firstWhere('id', $riwayatalat['id_pengguna']);

            // Ambil data peminjaman alat berdasarkan ID no seri alat
            $peminjamanAlat = collect($this->dataDummyPeminjaman)->firstWhere('id_no_seri_alat', $riwayatalat['id_no_seri_alat']);

            // Ambil data permintaan alat berdasarkan ID no seri alat
            $permintaanAlat = collect($this->dataDummyPermintaan)->firstWhere('id_no_seri_alat', $riwayatalat['id_no_seri_alat']);

            // Ambil data perawatan alat berdasarkan ID no seri alat
            $perawatanAlat = collect($this->dataDummyPerawatan)->firstWhere('id_no_seri_alat', $riwayatalat['id_no_seri_alat']);

            // Ambil data error alat berdasarkan ID no seri alat
            $errorAlat = collect($this->dataDummyError)->firstWhere('id_no_seri_alat', $riwayatalat['id_no_seri_alat']);

            // Ambil data rusak alat berdasarkan ID no seri alat
            $rusakAlat = collect($this->dataDummyRusak)->firstWhere('id_no_seri_alat', $riwayatalat['id_no_seri_alat']);

            // Ambil data musnah alat berdasarkan ID no seri alat
            $musnahAlat = collect($this->dataDummyMusnah)->firstWhere('id_no_seri_alat', $riwayatalat['id_no_seri_alat']);

            // Ambil data hilang alat berdasarkan ID no seri alat
            $hilangAlat = collect($this->dataDummyHilang)->firstWhere('id_no_seri_alat', $riwayatalat['id_no_seri_alat']);

            // Gabungkan data riwayat alat dengan data alat, no seri alat, staff, peminjaman alat, permintaan alat, perawatan alat, error alat, rusak alat, musnah alat, dan hilang alat
            $riwayatalat['alat'] = $alat;
            $riwayatalat['no_seri_alat'] = $noSeriAlat;
            $riwayatalat['staff'] = $staff;
            $riwayatalat['pengguna'] = $pengguna;
            $riwayatalat['peminjaman_alat'] = $peminjamanAlat;
            $riwayatalat['permintaan_alat'] = $permintaanAlat;
            $riwayatalat['perawatan_alat'] = $perawatanAlat;
            $riwayatalat['error_alat'] = $errorAlat;
            $riwayatalat['rusak_alat'] = $rusakAlat;
            $riwayatalat['musnah_alat'] = $musnahAlat;
            $riwayatalat['hilang_alat'] = $hilangAlat;

            return $riwayatalat;
        });

        return response()->json([
            'data' => $dataRiwayatAlat,
        ]);
    }

}