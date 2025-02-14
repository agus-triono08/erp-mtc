<template>
  <div class="container-fluid">
    <div class="row align-items-center justify-content-end mr-3 mt-4 mb-2">
          <button class="btn btn-sm btn-outline-primary mr-2">
            <i class="fas fa-print"></i>
          </button>
          <div class="search-wrapper">
            <div class="input-group">
              <input type="text" placeholder="search..." class="form-control"
              v-model="searchQuery"
              @input="debouncedFetchAlats"/>              
            </div>
          </div>
        </div>
        <div class="table-responsive p-3">
          <table class="table table-border no-border table-custom" style="overflow-x: auto;">
            <thead>
              <tr class="bg-table text-center">
                <th class="text-center" style="width: 10px; color: #000;"></th>
                <th class="text-center" style="width: 10px; color: #000;">#</th>
                <th class="text-center" style="width: 10px; color: #000;">Kode Alat</th>
                <th class="text-center" style="width: 10px; color: #000;">Jumlah</th>
                <th class="text-center" style="width: 10px; color: #000;">Tgl Peminjaman</th>
                <th class="text-center" style="width: 10px; color: #000;">Tgl Pengembalian</th>
              </tr>
            </thead>
            <tbody v-if="filteredData.length===0">
            <tr>
              <td colspan="6" class="text-center">Tidak Ada Data</td>
            </tr>
          </tbody>
          <tbody v-for="(peminjamanalat, index) in filteredData" :key="index">
          <tr class="text-center">
            <td><button class="btn btn-sm btn-outline-primary"
              :class="{active: Detail_no_seri}"
              @click="toggleDetail_no_seri"
              >
              <span v-if="Detail_no_seri">-</span>
              <span v-else>+</span>
            </button></td>
            <td>{{ index +1 }}</td>
            <td>{{ peminjamanalat.kode_alat }}</td>
            <td>{{ peminjamanalat.stok }}</td>
            <td>{{ peminjamanalat.tanggal_pinjam }}</td>
            <td>{{ peminjamanalat.tanggal_kembali }}</td>
          </tr>
          <tr v-if="Detail_no_seri" style="background: rgb(244, 246, 249);">
            <td colspan="100%">
              <table class="table table-border no-border table-custom" style="overflow-x: auto; background-color: #fff;">
                <thead>
                  <tr class="bg-table text-center">
                    <th class="text-center" style="width: 10px; color: #000;">#</th>
                    <th class="text-center" style="width: 10px; color: #000;">No Seri Alat</th>
                    <th class="text-center" style="width: 10px; color: #000;">Tgl Pengembalian</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="text-center" v-for="(noSeri, index) in dataPeminjamanAlat" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>{{ noSeri.no_seri_alat ? noSeri.no_seri_alat.no_seri_alat : '-' }}</td>
                    <td>{{ noSeri.tanggal_kembali }}</td>
                  </tr>
                </tbody>
              </table>
            </td>
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
</template>
<script>
import axios from "axios";

export default {
  props:{
    noPinjam: String,
  },
  data() {
    return {
      noseri: {
        no_seri_alat: '',
      },
      dataPeminjamanAlat: [],    
      Detail_no_seri: false,
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
    }
  },
  computed: {
    totalPages() {
      return Math.ceil(this.dataPeminjamanAlat.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(this.currentPage * this.rowsPerPage, this.dataPeminjamanAlat.length);
      return `Showing ${start} to ${end} of ${this.dataPeminjamanAlat.length} entries`;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = start + this.rowsPerPage;
      return this.dataPeminjamanAlat.slice(start, end);
    },
    filteredData() {
      if (this.searchQuery) {
        return this.paginatedData.filter(peminjamanalat => {
          return (
            peminjamanalat.kode_alat.toLowerCase().includes(this.searchQuery.toLowerCase())
          );
        });
      } else {
        return this.paginatedData;
      }
    }
  },
  methods: {
    async fetchPeminjamanAlat() {
      try {
        const noPinjam =this.noPinjam;
        console.log(this.noPinjam);
        const response = await axios.get(`/api/peminjaman/alats/nopin/${noPinjam}`);
        this.dataPeminjamanAlat = response.data;
        console.log(this.dataPeminjamanAlat);
      } catch (error) {
        console.error("Error fetching data peminjaman", error);
      }
    },
    toggleDetail_no_seri(){
      this.Detail_no_seri = !this.Detail_no_seri;
    },
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
      }
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
      }
    },
    debouncedFetchAlats: _.debounce(function () {
      this.fetchPeminjamanAlat();
    }, 300),
  },
  mounted() {
    this.fetchPeminjamanAlat();
  }
}
</script>