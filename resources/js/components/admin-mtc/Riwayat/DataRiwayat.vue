<template>
  <div class="container-fluid">
    <h1 class="h3 mb-4 mt-4 text-gray-900"><b>Riwayat</b></h1>
    <div class="col-md-12">
      <button 
        class="btn btn-show m-1"
        :class="{active: showAlat}"
        @click="toggleAlat"
      >
        <span v-if="showAlat">Alat</span>
        <span v-else>Alat</span>
      </button>
      <button 
        class="btn btn-show m-1"
        :class="{active: showMesin}"
        @click="toggleMesin"
      >
        <span v-if="showMesin">Mesin</span>
        <span v-else>Mesin</span>
      </button>
    </div>
    <div v-if="showAlat">
    <div class="row align-items-center justify-content-end mr-3 mt-4 mb-2">
      <div class="ml-2" style="border-radius: 5px;">
        <div class="input-group">
          <label class="mr-2" style="color: #000;"><b>Tujuan Divisi:</b></label>
          <select class="form-control" v-model="tujuanDivisiFilter" style="border-radius: 5px;">
            <option value="">Semua</option>
            <option v-for="(tujuan, index) in tujuanDivisiOptions" :key="index" :value="tujuan">{{ tujuan }}</option>
          </select>
          <label class="ml-2 mr-2" style="color: #000;"><b>Jenis:</b></label>
          <select class="form-control" v-model="jenisFilter" style="border-radius: 5px;">
            <option value="">Semua</option>
            <option v-for="(jenis, index) in jenisOptions" :key="index" :value="jenis">{{ jenis }}</option>
          </select>
          <label class="ml-2 mr-2" style="color: #000;"><b>Kondisi:</b></label>
          <select class="form-control" v-model="kondisiFilter" style="border-radius: 5px;">
            <option value="">Semua</option>
            <option v-for="(kondisi, index) in kondisiOptions" :key="index" :value="kondisi">{{ kondisi }}</option>
          </select>
        </div>
      </div>      
          <div class="search-wrapper col-3">
            <div class="input-group">
              <input type="text" placeholder="Search..." class="form-control"
                v-model="searchQuery"
                @input="debouncedFetchNoSeri"
              />            
            </div>
          </div>
        </div>
    <div class="row align-items-center justify-content-end mr-3 mt-4 mb-2">
      <div class="ml-2">
        <div class="input-group">
          <label class="mr-2" style="color: #000;"><b>Tanggal Awal:</b></label>
          <input type="date" class="form-control" v-model="tanggalAwal" style="border-radius: 5px;"/>
          <label class="ml-2 mr-2" style="color: #000;"><b>Tanggal Akhir:</b></label>
          <input type="date" class="form-control" v-model="tanggalAkhir" style="border-radius: 5px;">          
        </div>
      </div>
    </div>
        <div class="table-responsive p-3">
          <table class="table table-border no-border table-custom" style="overflow-x: auto;">
            <thead>
              <tr class="bg-table text-center">
                <th class="text-center" style="width: 10px; color: #000;">#</th>
                <th class="text-center" style="width: 10px; color: #000;">Tgl</th>
                <th class="text-center" style="width: 10px; color: #000;">PIC</th>
                <th class="text-center" style="width: 10px; color: #000;">Tujuan Divisi</th>
                <th class="text-center" style="width: 10px; color: #000;">Kode Alat</th>                
                <th class="text-center" style="width: 10px; color: #000;">Kondisi</th>                                
              </tr>
            </thead>
            <tbody v-if="filteredData.length===0">
              <tr class="text-center">
                <td colspan="8">Tidak Ada Data</td>
              </tr>
            </tbody>
            <tbody v-for="(riwayat, index) in filteredData" :key="index">
              <tr class="text-center">
                <td>{{ index + 1 }}</td>
                <td>{{ riwayat.tanggal || '-' }} <br> <small style="color: #444;"><i class="fas fa-clock"></i> {{ durasiData[index] !== '-' ? durasiData[index] + 'Hari' : '-' }}</small></td>                
                <td>{{ riwayat.PIC ? riwayat.PIC.nama_staff : '-' }}</td>     
                <td>{{ riwayat.pengguna ? riwayat.pengguna.divisi : '-' }}</td>
                <td>{{ riwayat.alat ? riwayat.alat.kode_alat : '-' }}</td>                
                <td>{{ riwayat.noSeri ? riwayat.noSeri.status : '-' }}</td>
              </tr>
            </tbody>
          </table>
          <!-- Pagination -->
          <div class="d-flex justify-content-between align-items-center mb-3 mt-3" style="border-radius: 10px; background-color: #f3f4f6; height: 50px; color: #000;">
            <div class="ml-3">
              Rows per page:
              <span>{{ rowsPerPage }}</span>
            </div>
            <div class="mr-3">          
              <span>{{ paginationInfo }}</span>
              <button @click="prevPage" :disabled="currentPage === 1" class="btn btn-sm btn-light">
                <i class="fas fa-angle-left"></i>
              </button>
              <span>  </span>
              <button @click="nextPage" :disabled="currentPage === totalPages" class="btn btn-sm btn-light">
                <i class="fas fa-angle-right"></i>
              </button>
            </div>
          </div>
        </div>
    </div>
    <div v-if="showMesin">

    </div>
  </div>
</template>
<script>
import axios from 'axios';
export default {
  props: {
    noSeri: String,
  },
  data() {
    return {
      searchQuery: '',
      datariwayat: [],
      rowsPerPage: 10,
      currentPage: 1,
      tanggalAwal: '',
      tanggalAkhir: '',
      tujuanDivisiFilter: '',
      jenisFilter: '',
      kondisiFilter: '',
      tujuanDivisiOptions: [],
      jenisOptions: [],
      kondisiOptions: [],
      showAlat: true,
      showMesin: false,
      }
  },
  computed: {
    durasiData() {
      return this.datariwayat.map(noseri => {
        if (noseri.tanggal) {
          const tanggal = new Date(noseri.tanggal);
          const tanggalTerkini = new Date();
          const durasi = tanggalTerkini - tanggal;
          const hari = Math.floor(durasi / (1000 * 60 * 60 * 24));
          return hari;
        }
        return '-';
      });
    },
    totalPages() {
      return Math.ceil(this.datariwayat.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(this.currentPage * this.rowsPerPage, this.datariwayat.length);
      return `Showing ${start} to ${end} of ${this.datariwayat.length} entries`;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = this.currentPage * this.rowsPerPage;
      return this.datariwayat.slice(start, end);
    },
    filteredData() {
      if (this.searchQuery || this.tanggalAwal || this.tanggalAkhir || this.tujuanDivisiFilter || this.jenisFilter || this.kondisiFilter) {
        return this.paginatedData.filter(datariwayat => {
          const tanggalMatch = this.tanggalAwal && this.tanggalAkhir
            ? datariwayat.tanggal >= this.tanggalAwal && datariwayat.tanggal <= this.tanggalAkhir
            : true;
          const searchMatch = this.searchQuery
            ? (
              (datariwayat.no_seri && datariwayat.no_seri.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
              (datariwayat.tujuan && datariwayat.tujuan.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
              (datariwayat.nama_peminjam && datariwayat.nama_peminjam.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
              (datariwayat.staff && datariwayat.staff.nama_staff && datariwayat.staff.nama_staff.toLowerCase().includes(this.searchQuery.toLowerCase()))
            )
            : true;
          const tujuanDivisiMatch = this.tujuanDivisiFilter
            ? datariwayat.tujuan === this.tujuanDivisiFilter
            : true;
          const jenisMatch = this.jenisFilter
            ? datariwayat.jenis === this.jenisFilter
            : true;
          const kondisiMatch = this.kondisiFilter
            ? datariwayat.kondisi === this.kondisiFilter
            : true;
          return tanggalMatch && searchMatch && tujuanDivisiMatch && jenisMatch && kondisiMatch;
        });
      } else {
        return this.paginatedData;
      }
    }
  },
  methods: {
    async fetchNoSeriAlat() {
      try {
        const response = await axios.get(`/api/riwayat/alats`);
        this.datariwayat = response.data.data.map((riwayat)=> ({
          id: riwayat.id,
          id_alat: riwayat.id_alat,
          id_pengguna: riwayat.id_pengguna,
          no_seri: riwayat.id_no_seri_alat,
          layout: riwayat.id_layout,
          pic: riwayat.id_staff,
          jumlah: riwayat.jumlah,
          tanggal: riwayat.tanggal,
          PIC: riwayat.staff,
          noSeri: riwayat.no_seri_alat,
          alat: riwayat.alat,
          pengguna: riwayat.pengguna,
        }));
        this.tujuanDivisiOptions = [...new Set(this.datariwayat.map(datariwayat => datariwayat.tujuan))];
        this.jenisOptions = [...new Set(this.datariwayat.map(datariwayat => datariwayat.jenis))];
        this.kondisiOptions = [...new Set(this.datariwayat.map(datariwayat => datariwayat.kondisi))];
        console.log(this.datariwayat);
      } catch (error) {
        console.error(error);
      }
    },
    debouncedFetchNoSeri: _.debounce(function () {
      this.fetchNoSeriAlat();
    }, 300),
    prevPage () {
      if (this.currentPage > 1) {
        this.currentPage--;
      }
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
      }
    },
    toggleAlat() {
      this.showMesin = false;
      this.showAlat = !this.showAlat;
    },
    toggleMesin() {
      this.showAlat = false;
      this.showMesin = !this.showMesin;
    },
  },
  mounted() {
    this.fetchNoSeriAlat();
  }
}
</script>