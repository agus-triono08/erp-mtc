<template>
  <div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-1">
      <h1 class="h6 text-teal">
        <i 
          class="fas fa-angle-left text-teal mr-2"
          style="cursor: pointer;"
          @click="goBack"
        > Back to Peminjaman</i>        
      </h1>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h3" style="color: #000;"><b>Detail Peminjaman Alat</b></h1>
      <div class="d-flex align-items-center justify-content-center">
        <div class="card shadow" style="max-width: auto; border-radius: 5px;">
          <div class="card-body text-center" style="border-radius: 5px; height: 30px;">
            <p>
              <span class="m-2" style="color: #169ea8;"> Detail Peminjaman</span>/
              <span class="mt-2 mb-2 mr-2 ml-1" style="color: #e6494b;">{{ peminjaman.no_pinjam }}</span>
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card shadow mb-0" style="border-radius: 10px;">
          <div class="card-body p-0" style="border-radius: 20px;">
            <div class="d-flex justify-content-between align-items-center" style="margin: 10px;">
              <h5 class="m-0 font-weight-bold" style="color: #169ea8;">Peminjaman {{ peminjaman.no_pinjam }}</h5>              
            </div>
            <div class="row m-1">
              <div class="col-2">
                <dd>Nama Pemohon</dd>
                <dt style="color: #000; margin-top: -10px;" class="mb-2">{{ peminjaman.pengguna ? peminjaman.pengguna.nama_pengguna : '-' }}</dt>
                <dd>Divisi</dd>
                <dt style="color: #000; margin-top: -10px;">{{ peminjaman.pengguna ? peminjaman.pengguna.divisi : '-' }}</dt>
              </div>
              <div class="col-2">
                <dd>Tujuan Peminjaman</dd>
                <dt style="color: #000; margin-top: -10px;">{{ peminjaman.detail_peminjaman }}</dt>
              </div>
              <div class="col-2">
                <dd>Tanggal Peminjaman</dd>
                <dt style="color: #000; margin-top: -10px;" class="mb-2">{{ peminjaman.tanggal_pinjam }}</dt>
                <dd>Tanggal Kebutuhan</dd>
                <dt style="color: #000; margin-top: -10px;" class="mb-2">{{ peminjaman.tanggal_kembali }}</dt>
              </div>
              <div class="col-2">
                <dd>Estimasi Pengembalian</dd>
                <dt style="color: #000; margin-top: -10px;">{{ peminjaman.tanggal_kembali }}</dt>
              </div>
              <div class="col-2">
                <dd style="margin-bottom: -2px;">Status</dd>
                <dt class="status-pill parent-element"                
                :class="{
                  'status-active': peminjaman.status == 'Selesai',
                  'status-error': peminjaman.status == 'Barang Siap Diambil',
                  'status-rusak': peminjaman.status == 'Sedang Dipinjam',
                }">{{ peminjaman.status || '-' }}</dt>
              </div>
              <div class="col-2">
                <dd>Durasi</dd>
                <dt style="color: #000; margin-top: -10px;">
                  {{ durasiData !== '-' ? durasiData + ' Hari' : '-' }} <br>
                  <small>
                    <i :class="{'fas fa-clock': !durasiDataKembali.includes('Hari Lebih'), 'fas fa-exclamation-circle text-danger': durasiDataKembali.includes('Hari Lebih')}"></i>
                    <span :class="{'text-danger': durasiDataKembali.includes('Hari Lebih')}">
                      {{ durasiDataKembali }}
                    </span>
                  </small>
                </dt>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <br>
    <!-- Card dengan tombol Detail -->
    <div class="card shadow mb-4" style="border-radius: 20px;">
      <div class="card-header py-3 mb-2" style="border-radius: 20px;">
        <!-- Tombol Peminjaman -->
        <button
          class="btn btn-show m-1"
          :class="{active: showDetailPeminjaman}"
          @click="toggleDetailPeminjaman"
        >
          <span v-if="showDetailPeminjaman">Peminjaman</span>
          <span v-else>Peminjaman</span>
        </button>
      </div>

      <!-- Card Konten Detail -->
      <div id="app" v-if="showDetailPeminjaman && peminjaman.no_pinjam" class="card-body">
        <data-rincian-peminjaman-alat :no-pinjam="peminjaman.no_pinjam"></data-rincian-peminjaman-alat>
      </div>
    </div>    

  </div>
</template>
<script>
import axios from 'axios';

export default {
  props: {
    noPinjam: String,
  },
  data() {
    return {
      peminjaman: {},
      showDetailPeminjaman: true,      
    }
  },
  computed:{
    durasiData() {
      if (this.peminjaman.tanggal_kembali) {
        const tanggalPinjam = new Date(this.peminjaman.tanggal_pinjam);
        const tanggalKembali = new Date(this.peminjaman.tanggal_kembali);
        const selisihHari = Math.abs(tanggalKembali - tanggalPinjam) / (1000 * 60 * 60 * 24);
        return Math.ceil(selisihHari);
      } else {
        return '-';
      }
    },
    durasiDataKembali() {
      if (this.peminjaman.tanggal_kembali) {
        const tanggalTerkini = new Date();
        const tanggalKembali = new Date(this.peminjaman.tanggal_kembali);
        const selisihHari = Math.abs(tanggalKembali - tanggalTerkini);
        const hari = Math.ceil(selisihHari / (1000 * 60 * 60 * 24));

        //Jika tanggal terkininya kurang dari tanggal kembali
        if (tanggalTerkini < tanggalKembali) {
          return hari + ' Hari Lagi';
        } else {
          // Jika tanggal terkininya lebih dari tanggal kembali
          const excessDays = Math.ceil((tanggalTerkini - tanggalKembali) / (1000 * 60 * 60 * 24));
          return excessDays + ' Hari Lebih';
        }
      } else {
        return '-';
      }
    }
  },
  methods: {
    async fetchAlatDetailPeminjaman() {
      try {
        const id = this.$route.params.id;
        const response = await axios.get(`/api/peminjaman/${id}`);
        this.peminjaman = response.data;
        console.log(this.peminjaman)
      } catch (error) {
        alert("Gagal memuat detail alat peminjaman.");
      }
    },
    toggleDetailPeminjaman(){
      this.showDetailPeminjaman = this.showDetailPeminjaman;
    },
    goBack(){
      this.$router.push('/admin-mtc/peminjaman');
    }
  },
  mounted() {
    this.fetchAlatDetailPeminjaman();
  }
}
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

  .text-teal {
    color: #169EA8;
  }

  .btn-show.active {
    background-color: #169EA8; /* Warna tombol saat aktif */
    color: #fff; /* Warna teks tombol saat aktif */
    border: 1px solid #169EA8; /* Tambahkan border agar lebih jelas */
  }

  .btn-show {
    background-color: #fff;
    color: #000;
    border: 1px solid transparent; /* Tambahkan border default */
    transition: background-color 0.3s ease, color 0.3s ease, border 0.3s ease;
  }

  .btn-show:hover {
    background-color: #fff; /* Warna saat hover */
    color: #169EA8;
    border: 1px solid #169EA8;
  }
</style>