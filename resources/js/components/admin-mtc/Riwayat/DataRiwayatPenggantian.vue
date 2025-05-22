<template>
  <div class="container-fluid">
    <h1 class="h3 mb-4 mt-4 text-gray-900"><b>Riwayat</b></h1>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="kondisi-tab" data-toggle="tab" role="tab" aria-controls="kondisi" aria-selected="false" :class="{active: $route.name === 'data-riwayat-perkondisi'}" :to="{name: 'data-riwayat-perkondisi'}">Per Kondisi</router-link>
      </li>
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="peminjaman-tab" data-toggle="tab" role="tab" aria-controls="peminjaman" aria-selected="false" :class="{active: $route.name === 'data-riwayat-peminjaman'}" :to="{name: 'data-riwayat-peminjaman'}">Peminjaman</router-link>
      </li>
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="permintaan-tab" data-toggle="tab" role="tab" aria-controls="permintaan" aria-selected="false" :class="{active: $route.name === 'data-riwayat-permintaan'}" :to="{name: 'data-riwayat-permintaan'}">Permintaan</router-link>
      </li>
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="penggantian-tab" data-toggle="tab" role="tab" aria-controls="penggantian" aria-selected="true" :class="{active: $route.name === 'data-riwayat-penggantian'}" :to="{name: 'data-riwayat-penggantian'}">Penggantian Alat/Mesin Hilang</router-link>
      </li>
    </ul>
    <div class="row align-items-center justify-content-end m-3">
      <button @click="exportToExcel" class="btn btn-sm btn-primary-1 mr-2"><i class="fas fa-file-excel"></i> Export Excel</button>    
      <input v-model="search" type="text" placeholder="Search..." class="btn btn-sm border p-2 rounded w-1/3" />
    </div>
    <div class="table-responsive p-3">
      <table class="table table-border no-border table-custom" style="border-radius: 5px;">
        <thead class="bg-table">
          <tr>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('hilang_activity_proses.tgl__penggantian')" style="color: #000;">
              Tgl Penggantian
              <span v-if="sortKey === 'hilang_activity_proses.tgl__penggantian'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('no_kehilangan')" style="color: #000;">
              No Kehilangan
              <span v-if="sortKey === 'no_kehilangan'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('no_seri.tools.nama')" style="color: #000;">
              Nama Alat/Mesin
              <span v-if="sortKey === 'no_seri.tools.nama'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('hilang_activity_proses.no_seri_old')" style="color: #000;">
              No Seri Lama
              <span v-if="sortKey === 'hilang_activity_proses.no_seri_old'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('hilang_activity_proses.no_seri_new')" style="color: #000;">
              No Seri Baru
              <span v-if="sortKey === 'hilang_activity_proses.no_seri_new'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>          
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('tgl_kehilangan')" style="color: #000;">
              Tgl Kehilangan
              <span v-if="sortKey === 'tgl_kehilangan'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('users.nama')" style="color: #000;">
              Dipinjam Oleh
              <span v-if="sortKey === 'users.nama'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('users.divisi.divisi')" style="color: #000;">
              Divisi
              <span v-if="sortKey === 'users.divisi.divisi'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
          </tr>
        </thead>
        <!-- <tbody v-if="paginatedData.length === 0">
          <tr>
            <td class="text-center" colspan="8">Tidak Ada Data</td>
          </tr>
        </tbody> -->
        <!-- <tbody>
          <tr v-for="item in kehilanganData" :key="item.id">
            <td>{{ item.no_kehilangan }}</td>
            <td>{{ item.no_seri.tools.nama }}</td>
            <td>{{ item.hilang_activity_proses[0].no_seri_old }}</td>
            <td>{{ item.hilang_activity_proses[0].no_seri_new }}</td>
            <td>{{ item.tgl_kehilangan }}</td>
            <td>{{ item.users.nama }}</td>
            <td>{{ item.users.divisi.divisi }}</td>
          </tr>
        </tbody> -->
        <tbody v-if="paginatedData.length === 0">
          <tr>
            <td class="text-center" colspan="8">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="item in paginatedData" :key="item.id">
          <tr v-for="(proses, index) in item.hilang_activity_proses" :key="proses.id">
            <td class="text-center p-2 border">{{ proses.tgl_penggantian || '-' }}</td>
            <td class="text-center p-2 border">{{ item.no_kehilangan }}</td>
            <td class="text-center p-2 border">{{ item.no_seri && item.no_seri.tools && item.no_seri.tools.nama || '-' }}</td>
            <td class="text-center p-2 border">{{ proses.no_seri_old || '-' }}</td>            
            <td class="text-center p-2 border">{{ proses.no_seri_new || '-' }}</td>
            <td class="text-center p-2 border">{{ item.tgl_kehilangan }}</td>
            <td class="text-center p-2 border">{{ item.users && item.users.nama || '-' }}</td>
            <td class="text-center p-2 border">{{ item.users && item.users.divisi && item.users.divisi.divisi || '-' }}</td>            
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
      kehilanganData: [],
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
    filteredData() {
      let data = this.kehilanganData.filter(item => {
        return (
          item.no_kehilangan.toLowerCase().includes(this.search.toLowerCase()) ||
          item.no_seri.tools.nama.toLowerCase().includes(this.search.toLowerCase())
        )
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
      const res = await fetch('/api/v1/kehilangan');
      const data = await res.json();
      this.kehilanganData = data.all;
      console.log(this.kehilanganData);
    },
    exportToExcel() {
      // Flatten data for Excel export based on hilang_activity_proses entries
      const exportData = [];
      this.filteredData.forEach(item => {
        if (item.hilang_activity_proses && item.hilang_activity_proses.length > 0) {
          item.hilang_activity_proses.forEach(proses => {
            exportData.push({
              'Tgl Penggantian': proses.tgl_penggantian || '-',
              'No Kehilangan': item.no_kehilangan || '-',
              'Nama Alat/Mesin': item.no_seri?.tools?.nama || '-',
              'No Seri Lama': proses.no_seri_old || '-',
              'No Seri Baru': proses.no_seri_new || '-',
              'Tgl Kehilangan': item.tgl_kehilangan || '-',
              'Dipinjam Oleh': item.users?.nama || '-',
              'Divisi': item.users?.divisi?.divisi || '-',
            });
          });
        } else {
          // in case hilang_activity_proses is empty or missing, still export main row data
          exportData.push({
            'Tgl Penggantian': '-',
            'No Kehilangan': item.no_kehilangan || '-',
            'Nama Alat/Mesin': item.no_seri?.tools?.nama || '-',
            'No Seri Lama': '-',
            'No Seri Baru': '-',
            'Tgl Kehilangan': item.tgl_kehilangan || '-',
            'Dipinjam Oleh': item.users?.nama || '-',
            'Divisi': item.users?.divisi?.divisi || '-',
          });
        }
      });

      const worksheet = XLSX.utils.json_to_sheet(exportData);
      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, worksheet, 'Penggantian_Hilang');
      XLSX.writeFile(workbook, 'Riwayat_Penggantian_Hilang.xlsx');
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
    getNamaAlat(item) {
      const ns = item?.permintaan?.no_seri?.[0];
      return ns?.tools?.nama || '-';
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