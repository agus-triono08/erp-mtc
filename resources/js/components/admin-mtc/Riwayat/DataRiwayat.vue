<template>
  <div class="container-fluid">
    <h1 class="h3 mb-4 mt-4 text-gray-900"><b>Riwayat</b></h1>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="kondisi-tab" data-toggle="tab" role="tab" aria-controls="kondisi" aria-selected="true" :class="{active: $route.name === 'data-riwayat-perkondisi'}" :to="{name: 'data-riwayat-perkondisi'}">Per Kondisi</router-link>
      </li>
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="peminjaman-tab" data-toggle="tab" role="tab" aria-controls="peminjaman" aria-selected="false" :class="{active: $route.name === 'data-riwayat-peminjaman'}" :to="{name: 'data-riwayat-peminjaman'}">Peminjaman</router-link>
      </li>
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="permintaan-tab" data-toggle="tab" role="tab" aria-controls="permintaan" aria-selected="false" :class="{active: $route.name === 'data-riwayat-permintaan'}" :to="{name: 'data-riwayat-permintaan'}">Permintaan</router-link>
      </li>
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="penggantian-tab" data-toggle="tab" role="tab" aria-controls="penggantian" aria-selected="false" :class="{active: $route.name === 'data-riwayat-penggantian'}" :to="{name: 'data-riwayat-penggantian'}">Penggantian Alat/Mesin Hilang</router-link>
      </li>
    </ul>
    <div class="row align-items-center justify-content-end m-3">
      <button @click="exportToExcel" class="btn btn-sm btn-primary-1 mr-2"><i class="fas fa-file-excel"></i> Export Excel</button>
      <!-- <select v-model="selectedNama" class="border p-2 rounded">
        <option value="">Semua Alat/Mesin</option>
        <option v-for="nama in namaOptions" :key="nama" :value="nama">{{ nama }}</option>
      </select> -->
      <select v-model="selectedKondisi" class="btn btn-sm border p-2 rounded mr-2">
        <option value="">Semua Kondisi</option>
        <option v-for="kondisi in kondisiOptions" :key="kondisi" :value="kondisi">{{ kondisi }}</option>    
        <!-- <option value="OK">OK</option>
        <option value="Rusak">Rusak</option>
        <option value="Musnah">Musnah</option>
        <option value="Hilang">Hilang</option>
        <option value="Error">Error</option>       -->
      </select> 
      <input v-model="search" type="text" placeholder="Search..." class="btn btn-sm border p-2 rounded w-1/3" />
    </div>
    <div class="table-responsive p-3">
      <table class="table table-border no-border table-custom" style="border-radius: 5px;">
        <thead class="bg-table">
          <tr>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('tanggal_masuk')" style="color: #000;">
              Tanggal Masuk
              <span v-if="sortKey === 'tanggal_masuk'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('tools.nama')" style="color: #000;">
              Nama Alat/Mesin
              <span v-if="sortKey === 'tools.nama'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('no_seri')" style="color: #000;">
              No Seri
              <span v-if="sortKey === 'no_seri'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('kondisi')" style="color: #000;">
              Kondisi
              <span v-if="sortKey === 'kondisi'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
          </tr>
        </thead>
        <tbody v-if="paginatedData.length === 0">
          <tr>
            <td class="text-center" colspan="4">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody>
          <tr v-for="item in paginatedData" :key="item.id">
            <td class="text-center p-2 border">{{ item.tanggal_masuk }}</td>
            <td class="text-center p-2 border">{{ item.tools.nama }}</td>
            <td class="text-center p-2 border">{{ item.no_seri }}</td>
            <td class="text-center">
              <div 
                class="btn-sts"
                  :class="{'status-active': item.kondisi === 'OK', 
                          'status-error': item.kondisi === 'Error',
                          'status-rusak': item.kondisi === 'Rusak',
                          'status-hilang': item.kondisi === 'Hilang',
                          'status-dipinjam': item.kondisi === 'Musnah',
                }"
              >
                {{ item.kondisi || '-' }}
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="d-flex justify-content-between align-items-center mb-3 mt-3" style="border-radius: 10px; background-color: #f3f4f6; height: 50px; color: #000;">
        <div class="ml-3">
          Rows per page:
          <span>{{ paginatedData.length }}</span>
        </div>
        <div class="mr-3">
          <span>{{ paginationInfo }}</span>
          <button
            @click="changePage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="btn btn-sm btn-light"
          >
          <i class="fas fa-angle-left"></i>
          </button>
          <span>  </span>
          <button
            @click="changePage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="btn btn-sm btn-light"
          >
          <i class="fas fa-angle-right"></i>
          </button>
        </div>        
      </div>
    </div>
  </div>
</template>

<script>
import * as XLSX from 'xlsx';

export default {
  data() {
    return {
      noseriData: [],
      search: '',
      selectedNama: '',
      selectedKondisi: '',
      sortKey: '',
      sortDirection: 'asc',
      currentPage: 1,
      itemsPerPage: 10
    };
  },
  computed: {
    namaOptions() {
      const names = this.noseriData.map(item => item.tools.nama);
      return [...new Set(names)];
    },
    kondisiOptions() {
      const kondisi = this.noseriData.map(item => item.kondisi);
      return [...new Set(kondisi)];
    },
    filteredData() {
      let data = this.noseriData.filter(item => {
        return (
          (!this.selectedNama || item.tools.nama === this.selectedNama) &&
          (!this.selectedKondisi || item.kondisi === this.selectedKondisi) &&
          (item.no_seri.toLowerCase().includes(this.search.toLowerCase()) ||
          item.tools.nama.toLowerCase().includes(this.search.toLowerCase()))
        );
      });

      if (this.sortKey) {
        data.sort((a, b) => {
          const getValue = (obj, path) => path.split('.').reduce((o, i) => o?.[i], obj);
          const valA = getValue(a, this.sortKey);
          const valB = getValue(b, this.sortKey);
          if (valA == null) return 1;
          if (valB == null) return -1;
          if (this.sortDirection === 'asc') {
            return valA > valB ? 1 : valA < valB ? -1 : 0;
          } else {
            return valA < valB ? 1 : valA > valB ? -1 : 0;
          }
        });
      }

      return data;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      return this.filteredData.slice(start, start + this.itemsPerPage);
    },
    totalPages() {
      return Math.ceil(this.filteredData.length / this.itemsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.itemsPerPage +1;
      const end = Math.min(this.currentPage * this.itemsPerPage, this.filteredData.length);
      return `Showing ${start} to ${end} of ${this.filteredData.length} entries`;
    },
  },
  methods: {
    async fetchData() {
      const res = await fetch('/api/v1/noseri');
      const data = await res.json();
      this.noseriData = data;
    },
    exportToExcel() {
      const worksheet = XLSX.utils.json_to_sheet(
        this.filteredData.map(item => ({
          'Tanggal Masuk': item.tanggal_masuk,
          'Nama Alat/Mesin': item.tools.nama,
          'No Seri': item.no_seri,
          'Kondisi': item.kondisi
        }))
      );
      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, worksheet, 'Noseri');
      XLSX.writeFile(workbook, 'noseri.xlsx');
    },
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortKey = key;
        this.sortDirection = 'asc';
      }
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    }
  },
  mounted() {
    this.fetchData();
  }
};
</script>

<style scoped>
th, td {
  text-align: left;
}
</style>
