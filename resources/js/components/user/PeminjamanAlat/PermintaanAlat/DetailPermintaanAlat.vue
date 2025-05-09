<template>
  <div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-1 mt-3">
      <h1 class="h6 text-teal">
        <i 
          class="fas fa-angle-left text-teal mr-2"
          style="cursor: pointer;"
          @click="goBack"
        > Back to Permintaan</i>  
      </h1>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h3" style="color: #000;"><b>Detail Permintaan Alat</b></h1>
      <div class="d-flex align-items-center justify-content-center">
        <div class="card shadow" style="max-width: auto; border-radius: 5px;">
          <div class="card-body text-center" style="border-radius: 5px; height: 30px;">
            <p>
              <span class="m-2" style="color: #169ea8;"> Detail Permintaan</span>/
              <span class="mt-2 mb-2 mr-2 ml-1" style="color: #e6494b;">{{ dataPermintaan.no_permintaan || '-'}}</span>
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card shadow mb-0" style="border-radius: 10px;">
          <div class="card-body p-0" style="border-radius: 10px;">
            <div class="d-flex justify-content-between align-items-center" style="margin: 10px;">
              <h5 class="m-0 font-weight-bold" style="color: #169ea8;">Permintaan {{ dataPermintaan.no_permintaan || '-' }}</h5>
            </div>
            <div class="row m-1">
              <div class="col-3">
                <dd>Nama Pemohon</dd>
                <dt style="color: #000; margin-top: -10px;" class="mb-2">{{ dataPermintaan.pengguna ? dataPermintaan.pengguna.nama_pengguna : '-' }}</dt>
                <dd>Divisi</dd>
                <dt style="color: #000; margin-top: -10px;" class="mb-2">{{ dataPermintaan.pengguna ? dataPermintaan.pengguna.divisi : '-' }}</dt>              
              </div>
              <div class="col-3">
                <dd>Tujuan Permintaan</dd>
                <dt style="color: #000; margin-top: -10px;" class="mb-2">{{ dataPermintaan.detail_permintaan || '-' }}</dt>
              </div>
              <div class="col-3">
                <dd>Tanggal Permintaan</dd>
                <dt style="color: #000; margin-top: -10px;" class="mb-2">{{ dataPermintaan.tgl_permintaan || '-' }}</dt>
              </div>
              <div class="col-3">
                <dd>Status</dd>
                <dt>
                  <div
                    class="btn-sts w-50"
                    :class="{
                      'status-active': dataPermintaan.status === 'Selesai',
                      'status-error': dataPermintaan.status === 'Menunggu Diambil',
                      'status-rusak': dataPermintaan.status === 'Ditolak',
                      'status-musnah': dataPermintaan.status === 'Digunakan',
                      'status-hilang': dataPermintaan.status === 'Belum Diproses',
                    }"
                  >
                  {{ dataPermintaan.status || '-' }}
                  </div>
                </dt>
                <dd>Durasi</dd>
                <dt style="color: red; margin-top: -10px;" class="mb-2">{{ durasiData !== '-' ? durasiData + ' Hari' : '-' }}</dt>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <br>

    <!-- Card dengan Tombol Detail -->
    <div class="card shadow mb-4" style="border-radius: 10px;">
      <div class="card-header py-3 mb-2" style="border-radius: 10px;">
        <!-- Tombol Permintaan -->
        <button
          class="btn btn-sm btn-show m-1"
          :class="{active: showRincianPermintaan}"
          @click="togglePengeluaran"
        >
          <span v-if="showRincianPermintaan">Pengeluaran</span>
          <span v-else>Pengeluaran</span>
        </button>
        <!-- Tombol Pengajuan -->
        <button
          class="btn btn-sm btn-show m-1"
          :class="{active: showPengajuan}"
          @click="togglePengajuan"
        >
          <span v-if="showPengajuan">Pengajuan</span>
          <span v-else>Pengajuan</span>
        </button>
      </div>

      <!-- Card Konten Detail -->
      <div class="card_body" id="app" v-if="showRincianPermintaan && dataPermintaan.no_permintaan">
        <user-permintaan-pengeluaran :no-permintaan="dataPermintaan.no_permintaan"></user-permintaan-pengeluaran>
      </div>

      <!-- Card Konten Detail Pengajuan -->
      <div class="card_body" id="app" v-if="showPengajuan && dataPermintaan.no_permintaan">
        <data-pengajuan :no-permintaan="dataPermintaan.no_permintaan"></data-pengajuan>
      </div>
    </div>

  </div>
</template>
<script>
import axios from 'axios';

export default {
  props: {
    noPermintaan: String,
  },
  data() {
    return {
      dataPermintaan: {},
      showRincianPermintaan: true,
      showPengajuan: false,
    }
  },
  computed: {
    durasiData() {
      if (this.dataPermintaan.tgl_permintaan) {
        const tanggalTerkini = new Date();
        const tanggalPermintaan = new Date(this.dataPermintaan.tgl_permintaan);
        const selisih = Math.abs(tanggalTerkini - tanggalPermintaan);
        const hari = Math.floor(selisih / (1000 * 60 * 60 * 24));
        return hari;
      }
    },
  },
  methods: {
    async fetchDetailPermintaan() {
      try {
        const id = this.$route.params.id;
        //console.log(id);
        const response = await axios.get(`/api/v1/permintaan/${id}`);
        this.dataPermintaan = response.data;
        // console.log(this.dataPermintaan);
      } catch (error) {
        alert("Gagal memuat detail alat permintaan.");
      }
    },
    goBack() {
      this.$router.push('/admin-mtc/peminjaman');
    },
    togglePengeluaran() {
      if (!this.showRincianPermintaan) {
        this.showRincianPermintaan = true;
        this.showPengajuan = false;
      }
    },
    togglePengajuan() {
      if (!this.showPengajuan) {
        this.showPengajuan = true;
        this.showRincianPermintaan = false;
      }
    }
  },
  mounted() {
    this.fetchDetailPermintaan();
  }
}
</script>