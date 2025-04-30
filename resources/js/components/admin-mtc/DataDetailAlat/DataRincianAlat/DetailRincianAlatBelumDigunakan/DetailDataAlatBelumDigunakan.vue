<template>
  <div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-1 mt-2">
      <h1 class="h6 text-teal">
        <i 
          class="fas fa-angle-left text-teal mr-2 mt-2"
          style="cursor: pointer;"
          @click="goBack"
        > Back</i>        
      </h1>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h3" style="color: #000;"><b>Detail Rincian Data Alat</b></h1>
      <div class="d-flex align-items-center justify-content-center">
        <div class="card shadow" style="max-width: auto; border-radius: 5px;">
          <div class="card-body text-center" style="border-radius: 5px; height: 30px;">
            <p>
              <span class="m-2" style="color: #169ea8;">Detail Rincian Alat</span>/
              <span class="mt-2 mb-2 mr-2 ml-1" style="color: #e6494b;">{{ datanoseri.no_seri || '-' }}</span>
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow mb-3" style="border-radius: 20px;">
      <div class="card-body" style="border-radius: 20px">
        <div class="row m-1">
          <div class="col-12 mt-3">
            <h5 class="font-weight-bold" style="color: #169ea8;">No Seri Alat/Mesin {{ datanoseri.no_seri || '-' }}</h5>
          </div>
        </div>
        <div class="row m-1">
          <div class="col-3">
            <dd>Nama Alat/Mesin</dd>
            <dt style="color: #000;" class="mb-2">
              {{ datanoseri.tools && datanoseri.tools.nama ? datanoseri.tools.nama : '-' }}
            </dt>
            <dd>Layout</dd>            
            <dt style="color: #000;" class="mb-2">{{ formatLayout(datanoseri) }}</dt>
          </div>
          <div class="col-3">
            <dd>Tanggal Masuk</dd>
            <dt style="color: #000;" class="mb-2">{{ datanoseri.tanggal_masuk || '-' }}</dt>
          </div>
          <div class="col-3">
            <dd>Harga</dd>
            <dt style="color: #000;" class="mb-2">{{ formatRupiah(datanoseri.harga || '-') }}</dt>
          </div>
          <div class="col-3">
            <dd>Kondisi</dd>
            <dt class="mb-4 text-center status-pill parent-element"      
                :class="{
                          'status-active': datanoseri.kondisi === 'OK', 
                          'status-rusak': datanoseri.kondisi === 'Rusak', 
                          'status-error': datanoseri.kondisi === 'Error',
                          'status-hilang': datanoseri.kondisi === 'Hilang',
                          'status-dipinjam': datanoseri.kondisi === 'Dipinjam'}">{{ datanoseri.kondisi }}</dt>
          </div>
        </div>
      </div>
    </div>
    <br>
    <!-- Card dengan tombol Detail -->
    <div class="card shadow mb-4" style="border-radius: 20px;">
      <div class="card-header py-3 mb-2" style="border-radius: 20px">
        <!-- Tombol Riwayat Error -->
        <button
          class="btn btn-sm btn-show mr-1 ml-1"
          style="border-radius: 5px;"
          :class="{active: showDetail}"
          @click="toggleDetail"
        >
          <span v-if="showDetail">Riwayat Error</span>
          <span v-else>Riwayat Error</span>
        </button>
        <!-- Tombol Riwayat Rusak -->
        <button
          class="btn btn-sm btn-show mr-1 ml-1"
          style="border-radius: 5px;"
          :class="{active: showRusak}"
          @click="toggleRusak"
        >
          <span v-if="showRusak">Riwayat Rusak</span>
          <span v-else>Riwayat Rusak</span>
        </button>
        <!-- Tombol Riwayat Musnah -->
        <button
          class="btn btn-sm btn-show mr-1 ml-1"
          style="border-radius: 5px;"          
          :class="{active: showMusnah}"
          @click="toggleMusnah"
        >
          <span v-if="showMusnah">Riwayat Musnah</span>
          <span v-else>Riwayat Musnah</span>
        </button>
        <!-- Tombol Riwayat Hilang -->
        <button 
          class="btn btn-sm btn-show mr-1 ml-1"
          style="border-radius: 5px;"
          :class="{active: showHilang}"
          @click="toggleHilang"
        >
          <span v-if="showHilang">Riwayat Hilang</span>
          <span v-else>Riwayat Hilang</span>
        </button>
      </div> 

      <!-- Card Konten Detail -->
      <div id="app" v-if="showDetail  && datanoseri.no_seri" class="card-body">
        <detail-rincian-alat-belum-digunakan :no-seri="datanoseri.no_seri"></detail-rincian-alat-belum-digunakan>        
      </div>

      <!-- Card Konten Riwayat Rusak -->
      <div id="app" v-if="showRusak && datanoseri.no_seri" class="card-body">
        <detail-riwayat-rusak :no-seri="datanoseri.no_seri"></detail-riwayat-rusak>        
      </div>

      <!-- Card Konten Riwayat Musnah -->
      <div id="app" v-if="showMusnah && datanoseri.no_seri" class="card-body">
        <detail-riwayat-musnah :no-seri="datanoseri.no_seri"></detail-riwayat-musnah>        
      </div>

      <!-- Card Konten Riwayat Hilang -->
      <div id="app" v-if="showHilang && datanoseri.no_seri" class="card-body">
        <detail-riwayat-hilang :no-seri="datanoseri.no_seri"></detail-riwayat-hilang>        
      </div>
    </div>
    
  </div>
</template>
<script>
import axios from "axios";

export default {
  props: {
    kodeAlat: String,
    id: Number,
    noSeri: String,
  },
  data() {
    return {
      layout: {},
      noseri: {},
      datanoseri: [],
      showDetail: true,
      showRusak: false,
      showHilang: false,
      showMusnah: false,
      searchQuery: '',
    }
  },
  methods: {
    async fetchNoSeri() {
      try {
        const id = this.$route.params.id;
        const response = await axios.get(`/api/v1/noseri/${id}`);
        this.datanoseri = response.data;
        // console.log(this.datanoseri);
      } catch (error) {
        alert("Gagal memuat detail alat peminjaman.");
      }
    },
    formatLayout() {
      if (!this.datanoseri.layout) return '-';
      return `${this.datanoseri.layout.ruang || '-'} | Rak ${this.datanoseri.layout.rak || '-'} | Lantai ${this.datanoseri.layout.lantai || '-'} | ${this.datanoseri.layout.koordinat || '-'}`;
    },
    debouncedFetchNoSeri: _.debounce(function (){
      this.fetchNoSeri();
    }),
    goBack() {
      this.$router.go(-1);
    },
    toggleRusak() {
      this.showRusak = !this.showRusiak;
      this.showDetail = false;
      this.showHilang = false;
      this.showMusnah = false;
    },
    toggleDetail() {
      this.showDetail = !this.showDetail;
      this.showRusak = false;
      this.showHilang = false;
      this.showMusnah = false;
    },
    toggleMusnah() {
      this.showMusnah = !this.showMusnah;
      this.showDetail = false;
      this.showRusak = false;
      this.showHilang = false;
    },
    toggleHilang() {
      this.showHilang = !this.showHilang;
      this.showDetail = false;
      this.showRusak = false;
      this.showMusnah = false;
    },
    formatRupiah(harga) {
      return harga ? new Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(harga) : 'Rp -';
    }
  },
  mounted() {
    this.fetchNoSeri();
  }
}
</script>
<style>
.card {
  border-radius: 20px;
}
.card-body {
  padding: 0;
}
.text-teal {
  color: #169ea8;
}
</style>