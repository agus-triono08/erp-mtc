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
      <!-- <select v-model="selectedKondisi" class="btn btn-sm border p-2 rounded mr-2">
        <option value="">Semua Kondisi</option>
        <option v-for="kondisi in kondisiOptions" :key="kondisi" :value="kondisi">{{ kondisi }}</option>    
        <option value="OK">OK</option>
        <option value="Rusak">Rusak</option>
        <option value="Musnah">Musnah</option>
        <option value="Hilang">Hilang</option>
        <option value="Error">Error</option>      
      </select>  -->
      <input v-model="search" type="text" placeholder="Search..." class="btn btn-sm border p-2 rounded w-1/3" />
    </div>
    <div class="table-responsive p-3">
      <table class="table table-border no-border table-custom" style="border-radius: 5px;">
        <thead class="bg-table">
          <tr>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('no_peminjaman')" style="color: #000;">
              No Permintaan
              <span v-if="sortKey === 'no_peminjaman'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('no_seri.tools.nama')" style="color: #000;">
              Nama Alat/Mesin
              <span v-if="sortKey === 'no_seri.tools.nama'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('total')" style="color: #000;">
              Total
              <span v-if="sortKey === 'total'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <!-- <th class="text-center p-2 border cursor-pointer" @click="sortBy('no_seri')" style="color: #000;">
              No Seri
              <span v-if="sortKey === 'no_seri'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th> -->
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('tanggal_pinjam')" style="color: #000;">
              Tgl Permintaan
              <span v-if="sortKey === 'tanggal_pinjam'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('diminta_oleh')" style="color: #000;">
              Diminta Oleh
              <span v-if="sortKey === 'diminta_oleh'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('divisi')" style="color: #000;">
              Divisi
              <span v-if="sortKey === 'divisi'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <!-- <th class="text-center p-2 border cursor-pointer" @click="sortBy('tanggal_kembali')" style="color: #000;">
              Tgl Kembali
              <span v-if="sortKey === 'tanggal_kembali'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>             -->
            <!-- <th class="text-center p-2 border cursor-pointer" @click="sortBy('kondisi')" style="color: #000;">
              Kondisi
              <span v-if="sortKey === 'kondisi'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th> -->
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('status')" style="color: #000;">
              Status
              <span v-if="sortKey === 'status'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border" style="color: #000;">Aksi</th>
          </tr>
        </thead>
        <tbody v-if="paginatedData.length === 0">
          <tr>
            <td class="text-center" colspan="9">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody>
          <tr v-for="item in paginatedData" :key="item.id">
            <td class="text-center p-2 border">{{ item.no_permintaan || '-' }}</td>
            <td class="text-center p-2 border">{{ item.tools.nama || '-' }}</td>
            <td class="text-center p-2 border">{{ item.total }}</td>
            <!-- <td class="text-center p-2 border">{{ item.no_seri.no_seri || '-' }}</td> -->
            <td class="text-center p-2 border">{{ item.tgl_permintaan || '-' }}</td>
            <td class="text-center p-2 border">-</td>
            <td class="text-center p-2 border">-</td>
            <!-- <td class="text-center p-2 border">{{ item.tgl_kembali ||'-'}}</td> -->
            <!-- <td class="text-center">
              <div 
                class="btn-sts"
                  :class="{'status-active': item.no_seri.kondisi === 'OK', 
                          'status-error': item.no_seri.kondisi === 'Error',
                          'status-rusak': item.no_seri.kondisi === 'Rusak',
                          'status-hilang': item.no_seri.kondisi === 'Hilang',
                          'status-dipinjam': item.no_seri.kondisi === 'Musnah',
                }"
              >
                {{ item.no_seri.kondisi || '-' }}
              </div>
            </td> -->
            <td>
              <div
                class="status-pill parent-element"
                :class="{
                  'status-active': item.status_kondisi === 'Selesai',
                  'status-error': item.status_kondisi === 'Menunggu Diambil',
                  'status-rusak': item.status_kondisi === 'Ditolak',
                  'status-musnah': item.status_kondisi === 'Digunakan',
                  'status-hilang': item.status_kondisi === 'Belum Diproses',
                }"
              >
                {{ item.status_kondisi || '-' }}
              </div>
            </td>
            <td>
              <button
                class="btn btn-sm"
                type="button"
                id="dropdownMenuButton"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
              >
                <i class="fas fa-ellipsis-v"></i>
              </button>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" @click="openDetailModal(item.no_seri)">
                  <i class="fas fa-eye text-info"></i> Detail
                </a>
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
    
    <!-- Modal Detail No Seri -->
    <div v-if="isDetailModalOpen" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content" style="max-height: 90vh; overflow-y: auto;">
          <div class="modal-header">
            <h5 class="modal-title"><b>Detail No Seri</b></h5>
            <button type="button" class="close" @click="closeDetailModal">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead class="thead-light">
                <tr>
                  <th>No Seri</th>
                  <th>Kondisi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in selectedNoSeri" :key="index">
                  <td>{{ item.no_seri }}</td>
                  <td>{{ item.kondisi }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" @click="closeDetailModal">Tutup</button>
          </div>
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
      permintaanData: [],
      search: '',
      selectedNama: '',
      selectedKondisi: '',
      sortKey: '',
      sortDirection: 'asc',
      currentPage: 1,
      itemsPerPage: 10,
      isDetailModalOpen: false,
      selectedNoSeri: [],
    };
  },
  computed: {
    // namaOptions() {
    //   const names = this.permintaanData.map(item => item.no_seri && item.no_seri.tools.nama);
    //   return [...new Set(names)];
    // },
    // kondisiOptions() {
    //   const kondisi = this.permintaanData.map(item => item.tools.no_seri.kondisi);
    //   return [...new Set(kondisi)];
    // },
    filteredData() {
      let data = this.permintaanData.filter(item => {
        return (
          // (!this.selectedNama || item.no_seri && item.no_seri.tools.nama === this.selectedNama) &&
          // (!this.selectedKondisi || item.no_seri.kondisi === this.selectedKondisi) &&
          // (item.no_seri.no_seri.toLowerCase().includes(this.search.toLowerCase()) ||
          (item.tools.nama.toLowerCase().includes(this.search.toLowerCase()) ||
          item.no_peminjaman.toLowerCase().includes(this.search.toLowerCase()))
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
      const res = await fetch('/api/v1/permintaan');
      const data = await res.json();
      this.permintaanData = data.all;
    },
    exportToExcel() {
      const worksheet = XLSX.utils.json_to_sheet(
        this.filteredData.map(item => ({
          'No Permintaan': item.no_permintaan,
          'Nama Alat/Mesin': item.tools.nama,
          'Total': item.total,
          // 'No Seri': item.no_seri.no_seri,
          'Tanggal Permintaan': item.tgl_permintaan,
          'Dipinjam Oleh': '-',
          'Divisi': '-',
          // 'Tanggal Kembali': item.tgl_kembali,
          // 'Kondisi': item.no_seri.kondisi,
          'Status': item.status_kondisi,
        }))
      );
      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, worksheet, 'Permintaan');
      XLSX.writeFile(workbook, 'Riwayat_Permintaan.xlsx');
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
    },
    openDetailModal(noseriList) {
      this.selectedNoSeri = noseriList;
      this.isDetailModalOpen = true;
    },
    closeDetailModal() {
      this.isDetailModalOpen = false;
      this.selectedNoSeri = [];
    },
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
