<template>
  <div class="container-fluid mt-3">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-1">
      <h1 class="h6 text-teal">
        <i 
          class="fas fa-angle-left text-teal mr-2" 
          @click="kembali"
          style="cursor: pointer;"
        > Back to Master Data</i>        
      </h1>
    </div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3" style="color: #000;"><b>Detail Master Data</b></h1>
        <div class="d-flex align-items-center justify-content-center">
          <div class="card shadow" style="max-width: auto; border-radius: 10px;">
            <div class="card-body text-center" style="border-radius: 10px; height: 30px;">
              <p>
                <span class="m-2" style="color: #169ea8;"> Detail Master data</span>/ 
                <span class="mt-2 mb-2 mr-2 ml-1" style="color: #e6494b;">{{ alat.kode_alat || '-'}}</span>
              </p>
            </div>
          </div>
        </div>
      </div>        

      <div class="row" style="display: flex; align-items: stretch;">
        <!-- Card untuk Gambar (Kiri) -->
        <div class="col-md-3">
          <div class="card shadow" style="border-radius: 10px; height: 100%;">
            <div class="card-body text-center" style="border-radius: 10px;"> 
              <div class="image-container" style="width: 100%; height: 220px; overflow: hidden; border-radius: 10px;">
                <img 
                  :src="alat.gambar" 
                  class="img-fluid shadow-sm hover-effect" 
                  alt="Gambar Alat" 
                  style="width: 100%; height: 100%; object-fit: cover; border-radius: 0;" 
                />
              </div>                        
            </div>
          </div>
        </div>

        <!-- Card untuk Detail Alat (Kanan) -->
        <div class="col-md-9">
          <div class="card shadow mb-0" style="border-radius: 20px; height: 100%;">
            <div class="card-body p-0" style="border-radius: 20px;"> 
              <div class="d-flex justify-content-between align-items-center" style="margin: 10px;">
                <h5 class="m-0 font-weight-bold" style="color: #169ea8;">Tool Information</h5>
                <button 
                  class="btn btn-plus btn-sm"
                  @click="goToEditPage"                  
                >
                  <i class="fas fa-pencil-alt"></i>
                </button>
              </div>
              <!-- Tabel Detail -->
              <table class="table table-hover no-border compact-table ml-2">              
                <thead>
                  <tr>
                    <td>Nama </td>
                    <td class="ml-4">Kode </td>
                  </tr>
                  <tr>
                    <th style="color: #000; font-size: x-large;">
                      {{ alat.nama_alat || '-' }}
                    </th>
                    <th class="ml-4" style="color: #000; font-size: x-large;">
                      {{ alat.kode_alat || '-' }}
                    </th>
                  </tr>
                </thead>
              </table>
              <table class="table no-border compact-table ml-2">              
                <thead>
                  <tr>
                    <th class="text-left">Harga Total Pembelian</th>
                  </tr>                
                </thead>
                <tbody>
                  <tr>
                    <td>{{ formatRupiah(alat.harga_total) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>


    <br>
    <!-- Card dengan tombol Detail -->
    <div class="card shadow mb-4" style="border-radius: 20px;">
      <div class="card-header py-3 mb-2" style="border-radius: 20px;">
        <!-- Tombol Detail -->
        <button 
          class="btn btn-show m-1"
          :class="{ active: showDetail }" 
          @click="toggleDetail"
        >
          <span v-if="showDetail">Detail</span>
          <span v-else>Detail</span>
        </button>
        <!-- Tombol Rincian Alat -->
        <!--<button 
          class="btn btn-show"
          :class="{ active: showRincianAlat }" 
          @click="toggleRincianAlat"
        >
          <span v-if="showRincianAlat">Rincian Alat</span>
          <span v-else>Rincian Alat</span>
        </button>-->
        <!-- Tombol Detail Alat Error -->
        <!-- <button
          class="btn btn-show-error m-1"
          :class="{ active: showRincianAlatError }"
          @click="toggleRincianAlatError"
        >
          <span v-if="showRincianAlatError">Detail Data Error</span>
          <span v-else>Detail Data Error</span>
        </button> -->
        <!-- Tombol Detail Alat Rusak -->
        <!-- <button
          class="btn btn-show-rusak m-1"
          :class="{ active: showRincianAlatRusak }"
          @click="toggleRincianAlatRusak"
          >
          <span v-if="showRincianAlatRusak">Detail Data Rusak</span>
          <span v-else>Detail Data Rusak</span>
        </button> -->
        <!-- Tombol Detail Alat Musnah -->
        <!-- <button
          class="btn btn-show-musnah m-1"
          :class="{ active: showRincianAlatMusnah }"
          @click="toggleRincianAlatMusnah"
        >
        <span v-if="showRincianAlatMusnah">Detail Data Musnah</span>
        <span v-else>Detail Data Musnah</span>
        </button> -->
        <!-- Tombol Detail Alat Hilang -->
        <!-- <button
          class="btn btn-show-hilang m-1"
          :class="{ active: showRincianAlatHilang }"
          @click="toggleRincianAlatHilang"
        >
          <span v-if="showRincianAlatHilang">Detail Data Hilang</span>
          <span v-else>Detail Data Hilang</span>
        </button> -->
      </div>
      <!-- Konten Detail -->
      <div class="card-body ml-2" v-if="showDetail" style="border-radius: 20px;">
          <div class="col-md-12">
            <button 
              class="btn btn-show ml-2 mb-4 mr-1 mt-3"
              :class="{ active: showDetailAlat }" 
              @click="toggleDetailAlat"
            >
              <span v-if="showDetailAlat">Detail Data Master</span>
              <span v-else>Detail Data Master</span>
            </button>
            <button
              class="btn btn-show ml-1 mb-4 mr-1 mt-3"
              :class="{ active: showRincianAlat }"
              @click="toggleRincianAlat"
            >
              <span v-if="showRincianAlat">Detail Rincian Data Master</span>
              <span v-else>Detail Rincian Data Master</span>
            </button>
          </div>
        <div v-if="showDetailAlat" class="mt-3">
        <div class="col-md-4">
            <table class="table table-hover no-border compact-table">
              <tbody>
                <tr>
                  <td>Kategori</td>
                  <th style="color: #000;">{{ alat.kategori || '-' }}</th>
                </tr>
                <tr>                  
                  <td>Merek</td>
                  <th style="color: #000;">{{ alat.merek_alat || '-' }}</th>
                </tr>
                <tr>
                  <td>Type/Size</td>
                  <th style="color: #000;">{{ alat.tipe_alat || '-' }}</th>
                </tr>
                <tr>
                  <td>Available Stok</td>
                  <th style="color: #000">{{ alat.stok_akhir || '-' }}</th>
                </tr>
                <tr>
                  <td>Produk</td>
                  <th style="color: #000;">{{ alat.pembelian || '-' }}</th>
                </tr>
                <tr>
                  <td>Satuan</td>
                  <th style="color: #000">{{ alat.unit_alat || '-' }}</th>
                </tr>
                <tr>
                  <td>Sumber</td>
                  <th style="color: #000;">{{ alat.sumber_alat || '-' }}</th>
                </tr>
                <tr>
                  <td>Vendor</td>
                  <th style="color: #000;">{{ alat.vendor || '-' }}</th>
                </tr>                               
              </tbody>
            </table>
          </div>
          <div class="col-md-12">
            <table class="table table-hover no-border compact-table">
              <thead>
                <tr>                 
                  <th style="color: #000">Fungsi</th>                  
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{{ alat.fungsi || '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-md-12">
            <table class="table table-hover no-border compact-table">
              <thead>
                <tr>                 
                  <th style="color: #000">Deskripsi</th>                  
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{{ alat.deskripsi || '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- Konten Detail Rincian Alat -->
        <div id="app" class="card-body" v-if="showRincianAlat" style="border-radius: 20px;">
          <rincian-alat :kode-alat="alat.kode_alat"></rincian-alat>
        </div>
      </div>
      <!-- Konten Detail Alat Rusak -->
      <div id="app" class="card-body" v-if="showRincianAlatRusak" style="border-radius: 20px;">
        <alat-rusak :kode-alat="alat.kode_alat"></alat-rusak>
      </div>
      <!-- Konten Detail Alat Musnah -->
      <div id="app" class="card-body" v-if="showRincianAlatMusnah" style="border-radius: 20px;">
        <alat-musnah :kode-alat="alat.kode_alat"></alat-musnah>
      </div>
      <!-- Konten Detail Alat Error -->
      <div id="app" class="card-body" v-if="showRincianAlatError" style="border-radius: 20px;">
        <alat-error :kode-alat="alat.kode_alat"></alat-error>
      </div>
      <!-- Konten Detail Alat Hilang -->
      <div id="app" class="card-body" v-if="showRincianAlatHilang" style="border-radius: 20px;">
        <alat-hilang :kode-alat="alat.kode_alat"></alat-hilang>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: {
    kodeAlat: String
  },
  data() {
    return {
      alat: {}, // Menyimpan data alat
      showDetail: true, // Mengontrol tampilan detail
      showDetailAlat: true,
      showRincianAlat: false, //Mengontrol tampilan rincian alat
      showRincianAlatError: false, //Mengontrol tampilan alat error
      showRincianAlatRusak: false, //Mengontrol tampilan alat rusak
      showRincianAlatMusnah: false, //Mengontrol tampilan alat musnah
      showRincianAlatHilang: false, //Mengontrol tampilan alat hilang
    };
  },
  methods: {
    async fetchAlatDetail() {
      try {
        const id = this.$route.params.id; // ID alat di URL
        const response = await axios.get(`/api/alats/${id}`);
        this.alat = response.data; // Menyimpan data alat
        //console.log("Data alat:", this.alat); // Debug data
      } catch (error) {
        //console.error("Error fetching alat detail:", error);
        alert("Gagal memuat detail alat.");
      }
    },
    formatRupiah(harga_total) {
      return harga_total ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(harga_total) : 'Rp -';
    },
    kembali() {
      this.$router.push('/admin-mtc/data-alat'); // Navigasi kembali
    },
    goToEditPage() {
      const id = this.$route.params.id;
      this.$router.push(`/admin-mtc/data-alat/edit/${id}`);
    },
    toggleDetail() {
    // Menutup rincian alat jika dibuka, lalu membuka atau menutup detail
    this.showRincianAlatRusak = false;
    this.showRincianAlat = false;
    this.showRincianAlatError = false;
    this.showRincianAlatMusnah = false;
    this.showRincianAlatHilang = false;
    this.showDetailAlat = true;
    this.showDetail = !this.showDetail;
  },
  toggleDetailAlat(){
    this.showRincianAlat = false;
    this.showDetailAlat = !this.showDetailAlat;
  },
  toggleRincianAlat() {
    this.showDetailAlat = false;
    this.showRincianAlat = !this.showRincianAlat;
  },
  toggleRincianAlatRusak() {
    // Menutup rincian alat dan detail jika dibuka, lalu membuka atau menutup alat rusak
    this.showDetail = false;
    this.showRincianAlat = false;
    this.showRincianAlatError = false;
    this.showRincianAlatMusnah = false;
    this.showRincianAlatHilang = false;
    this.showRincianAlatRusak = !this.showRincianAlatRusak;
  },
  toggleRincianAlatMusnah() {
    // Menutup rincian alat dan detail jika dibuka, lalu membuka
    this.showDetail = false;
    this.showRincianAlat = false;
    this.showRincianAlatError = false;
    this.showRincianAlatRusak = false;
    this.showRincianAlatHilang = false;
    this.showRincianAlatMusnah = !this.showRincianAlatMusnah;
  },
  toggleRincianAlatError() {
    // Menutup rincian alat dan detail jika dibuka, lalu membuka
    this.showDetail = false;
    this.showRincianAlat = false;    
    this.showRincianAlatRusak = false;
    this.showRincianAlatMusnah = false;
    this.showRincianAlatHilang = false;
    this.showRincianAlatError = !this.showRincianAlatError;
  },
  toggleRincianAlatHilang() {
    // Menutup rincian alat dan detail jika dibuka, lalu membuka
    this.showDetail = false;
    this.showRincianAlat = false;
    this.showRincianAlatError = false;
    this.showRincianAlatRusak = false;
    this.showRincianAlatMusnah = false;
    this.showRincianAlatHilang = !this.showRincianAlatHilang;
  }
  },
  mounted() {
    this.fetchAlatDetail();
    this.showDetail = true; // Menampilkan detail alat saat halaman pertama kali dimuat
  },
};
</script>

<style scoped>
  /* Mengubah bentuk ujung card agar lebih melengkung */
  .card {
    border-radius: 20px; /* Menambahkan border-radius untuk membuat sudut lebih melengkung */
  }

  /* Menghilangkan jarak antar card-body */
  .card-body {
    padding: 0; /* Menghilangkan padding pada card-body */
  }

  /* Gaya untuk efek hover gambar */
  .image-container {
    position: relative;
    overflow: hidden;
  }

  .hover-effect {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .hover-effect:hover {
    transform: scale(1.1); /* Perbesar gambar */
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3); /* Tambahkan bayangan */
    z-index: 10; /* Membuatnya muncul di depan */
    position: relative;
  }

  /* Gaya tombol Edit */
  .btn-icon-split .icon {
    padding: 0.5rem;
    background-color: #f6c23e;
    border-right: 1px solid #e6b31e;
  }

  .btn-icon-split:hover .icon {
    background-color: #e6b31e;
  }
  /* Gaya tombol Edit berbentuk bulat */
  .btn-plus {
    background-color: #169EA8;
    color: #fff;
    width: 45px; /* Lebar tombol */
    height: 45px; /* Tinggi tombol sama dengan lebar */
    border-radius: 50%; /* Membuat tombol berbentuk bulat */
    display: flex; /* Menggunakan flexbox untuk memusatkan ikon */
    align-items: center; /* Vertikal tengah */
    justify-content: center; /* Horizontal tengah */
    border: none; /* Menghilangkan border default */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Tambahkan bayangan */
    transition: background-color 0.3s ease, transform 0.3s ease;
  }

  .btn-plus:hover {
    background-color: #22d3e0; /* Warna saat hover */
    transform: scale(1); /* Efek memperbesar */
    color: #fff;
  }

  .btn-plus .icon {
    font-size: 1.25rem; /* Ukuran ikon */
  }

  .no-border {
    border: none; /* Menghilangkan border tabel */
  }

  .no-border th, 
  .no-border td {
    border-top: none !important; /* Menghilangkan garis atas pada setiap sel */
    border-bottom: none !important; /* Menghilangkan garis bawah pada setiap sel */
  }

  .compact-table th,
  .compact-table td {
    padding: 0.1rem 0.3rem; /* Atur padding agar jarak kanan kiri lebih rapat */
  }

  .compact-table tbody tr {
    margin-bottom: 0; /* Hilangkan margin tambahan antar baris */
  }

  .compact-table th {
    padding-left: 0.2rem; /* Jarak kiri sedikit lebih rapat untuk th */
    padding-right: 0.2rem; /* Jarak kanan sedikit lebih rapat untuk th */
  }

  .compact-table td {
    padding-left: 0.2rem; /* Jarak kiri sedikit lebih rapat untuk td */
    padding-right: 0.2rem; /* Jarak kanan sedikit lebih rapat untuk td */
  }
  .btn-show.active {
    background-color: #169EA8; /* Warna tombol saat aktif */
    color: #fff; /* Warna teks tombol saat aktif */
    border: 1px solid #169EA8; /* Tambahkan border agar lebih jelas */
  }

  .btn-show {
    background-color: #fff;
    color: #169EA8;
    border: 1px solid transparent; /* Tambahkan border default */
    transition: background-color 0.3s ease, color 0.3s ease, border 0.3s ease;
  }

  .btn-show:hover {
    background-color: #169EA8; /* Warna saat hover */
    color: #fff;
    border: 1px solid #169EA8;
  }

  .btn-show-rusak {
    background-color: #fff;
    color: #EB5A3C;
    border: 1px solid transparent;
    transition: background-color 0.3s ease, color 0.3s ease, border 0.3s ease;
  }

  .btn-show-rusak.active {
    background-color: #EB5A3C; /* Warna tombol saat aktif */
    color: #fff; /* Warna teks tombol saat aktif */
    border: 1px solid #EB5A3C; /* Tambahkan border agar lebih jelas*/
  }

  .btn-show-rusak:hover {
    background-color: #EB5A3C;
    color: #fff;
    border: 1px solid #EB5A3C;
  }

  .btn-show-musnah {
    background-color: #fff;
    color: #e6494b;
    border: 1px solid transparent;
    transition: background-color 0.3s ease, color 0.3s ease, border 0.3s ease;
  }

  .btn-show-musnah.active {
    background-color: #e6494b; /* Warna tombol saat aktif */
    color: #fff; /* Warna teks tombol saat aktif */
    border: 1px solid #e6494b; /* Tambahkan border agar lebih jelas */
  }

  .btn-show-musnah:hover {
    background-color: #e6494b;
    color: #fff;
    border: 1px solid #e6494b;
  }

  .btn-show-error {
    background-color: #fff;
    color: #ffac32;
    border: 1px solid transparent;
    transition: background-color 0.3s ease, color 0.3s ease, border 0.3s ease;
  }

  .btn-show-error.active {
    background-color: #ffac32; /* Warna tombol saat aktif */
    color: #fff; /* Warna teks tombol saat aktif */
    border: 1px solid #ffac32; /* Tambahkan border agar lebih jelas */
  }

  .btn-show-error:hover {
    background-color: #ffac32;
    color: #fff;
    border: 1px solid #ffac32;
  }

  .btn-show-hilang {
    background-color: #fff;
    color: #8E1616;
    border: 1px solid transparent;
    transition: background-color 0.3s ease, color 0.3s ease, border 0.3s ease;
  }

  .btn-show-hilang.active {
    background-color: #8E1616; /* Warna tombol saat aktif */
    color: #fff; /* Warna teks tombol saat aktif */
    border: 1px solid #8E1616; /* Tambahkan border agar lebih jelas */
  }

  .btn-show-hilang:hover {
    background-color: #8E1616;
    color: #fff;
    border: 1px solid #8E1616;
  }

  .status-active {
    background-color: rgba(40, 167, 69, 0.1); /* Hijau dengan transparansi */
    color: rgba(40, 167, 69); 
  }

  .status-rusak {
    background-color: rgba(220, 53, 69, 0.1); /* Merah dengan transparansi */
    color: rgba(220, 53, 69);
  }

  .status-error {
    background-color: rgba(255, 193, 7, 0.1); /* Kuning dengan transparansi */
    color: rgba(255, 193, 7);
  }

  .status-hilang {
    background-color: rgba(142, 22, 22, 0.1); /* Merah Tua dengan transparansi */
    color: rgba(142, 22, 22);
  }

  /* Styling untuk status pill */
  .status-pill {
    margin: auto; /* Tengahkan baik vertikal maupun horizontal */
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: bold;
    border-radius: 20px;
    text-align: center;
    padding: 0.2em 0.6em;
    height: 1rem;
  }

  .text-teal {
    color: #169EA8;
  }
</style>

