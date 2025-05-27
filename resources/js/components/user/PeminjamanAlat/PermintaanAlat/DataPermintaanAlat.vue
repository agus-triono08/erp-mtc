<template>
  <div class="row align-items-center justify-content-end mr-3 mt-3 mb-2">
    <!-- Modal Input Data -->
    <div id="app" class="modal" :class="{'is-visible': showModalInput}">
      <div class="modal-content">
        <user-input-permintaan @tutup-modal="tutupModal" @refresh-data="fetchPermintaan"></user-input-permintaan>
      </div>
    </div>
    <div class="d-flex justify-content-between mb-2">
      <!-- Tambah Data -->
      <button class="btn btn-sm btn-outline-primary mr-2 ml-1" @click="tambahData">
        <i class="fa fa-plus-circle"></i> Tambah Data
      </button>
      <!-- Status Filter with Checkboxes -->
      <!-- <div class="status-filter-wrapper">
        <button
            class="btn btn-sm btn-primary-1 mr-2"
            type="button"
            id="filterDropdown"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          ><i class="fas fa-filter"></i> Filter</button>
        <div class="dropdown-menu p-3" aria-labelledby="filterDropdown" style="border-radius: 8px; width: 250px;" @click.stop>
          <div v-for="status in statusOptions" :key="status" >
            <label>
            <input 
              type="checkbox" 
              :value="status" 
              v-model="selectedStatuses" 
            />
            {{ status }}</label>
          </div>
        </div>
      </div> -->

      <button
        class="btn btn-sm btn-primary-1 mr-2"
        type="button"
        id="filterDropdown"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
      ><i class="fas fa-filter"></i> Filter</button>
      <!-- Tambahkan checkbox untuk memilih status -->
      <div class="dropdown-menu p-3" aria-labelledby="filterDropdown" style="border-radius: 8px; width: 250px;" @click.stop>
        <label for="Status"><b>Status</b></label>
        <div v-for="sts in statusOptions" :key="sts">
          <label>
            <input type="checkbox" :value="sts" v-model="selectedStatuses" />
            {{ sts }}
          </label>
        </div>
      </div>

      <!-- Search -->
      <div class="search-wrapper">
        <div class="input-group">
          <input 
            type="text" 
            placeholder="search..." 
            class="form-control"
            v-model="searchQuery"
            @input="debouncedFetchAlats"
          />
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-border no-border table-custom text-wrape" style="overflow-x: auto;">
        <thead>
          <tr class="text-center bg-table">
            <th class="text-black-1" @click="sortBy('no_permintaan')">No Permintaan <i class="fas" :class="{'fa-sort-up': sortKey === 'no_permintaan' && !reverse, 'fa-sort-down': sortKey === 'no_permintaan' && reverse}"></i></th>
            <th class="text-black-1" @click="sortBy('tgl_permintaan')">Tgl Permintaan <i class="fas" :class="{'fa-sort-up': sortKey === 'tgl_permintaan' && !reverse, 'fa-sort-down': sortKey === 'tgl_permintaan' && reverse}"></i></th>
            <th class="text-black-1" @click="sortBy('total')">Total <i class="fas" :class="{'fa-sort-up': sortKey === 'total' && !reverse, 'fa-sort-down': sortKey === 'total' && reverse}"></i></th>
            <th class="text-black-1" @click="sortBy('pengguna.nama_pengguna')">Nama <i class="fas" :class="{'fa-sort-up': sortKey === 'pengguna.nama_pengguna' && !reverse, 'fa-sort-down': sortKey === 'pengguna.nama_pengguna' && reverse}"></i></th>
            <th class="text-black-1" @click="sortBy('pengguna.divisi')">Divisi <i class="fas" :class="{'fa-sort-up': sortKey === 'pengguna.divisi' && !reverse, 'fa-sort-down': sortKey === 'pengguna.divisi' && reverse}"></i></th>          
            <th class="text-black-1" @click="sortBy('status')">Status <i class="fas" :class="{'fa-sort-up': sortKey === 'status' && !reverse, 'fa-sort-down': sortKey === 'status' && reverse}"></i></th>              
            <th class="text-black-1">Aksi</th>
          </tr>
        </thead>
        <tbody v-if="filteredData.length===0">
          <tr>
            <td colspan="10" class="text-center text-black-1">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="(permintaan, index) in filteredData" :key="permintaan.id">
          <tr class="text-center">
            <td>{{ permintaan.no_permintaan || '-' }}</td>
            <td>{{ permintaan.tgl_permintaan || '-' }}</td>
            <td>{{ permintaan.total || '-' }}</td>
            <td>{{ permintaan.users && permintaan.users.nama || '-' }}</td>
            <td>{{ permintaan.users && permintaan.users.divisi && permintaan.users.divisi.divisi || '-' }}</td>       
            <td>
              <div
                class="btn-sts"
                :class="{
                  'status-active': permintaan.status === 'Selesai',
                  'status-error': permintaan.status === 'Menunggu Diambil',
                  'status-rusak': permintaan.status === 'Ditolak',
                  'status-musnah': permintaan.status === 'Digunakan',
                  'status-hilang': permintaan.status === 'Belum Diproses',
                }"
              >
                {{ permintaan.status || '-' }}
              </div>
            </td>                 
            <td>
              <div class="dropdown text-center">
                <button
                  class="btn btn-sm"
                  type="button"
                  id="dropdownMenuButton"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                >
 <i class="fa fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <a class="dropdown-item" @click="viewDetail(permintaan.id)">
                    <i class="fas fa-eye text-info"></i> Detail
                  </a>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="d-flex justify-content-between align-items-center mt-3 mb-3" style="border-radius: 10px; background-color: #f3f4f6; height: 50px; color: #000;">
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
import axios from 'axios';
import { values } from 'lodash';

export default {
  data() {
    return {
      searchQuery: '',
      dataPermintaanAlat: [],
      rowsPerPage: 10,
      currentPage: 1,
      sortKey: '',
      reverse: false,
      selectedStatuses: [],
      showModalInput: false,
      // statusOptions: ['Selesai', 'Menunggu Diambil', 'Ditolak', 'Digunakan', 'Belum Diproses'],
    }
  },
  computed: {
    statusOptions() {
      const status = this.dataPermintaanAlat.map(item => item.status);
      return [...new Set(status)];
    },
    totalPages() {
      return Math.ceil(this.dataPermintaanAlat.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(this.currentPage * this.rowsPerPage, this.dataPermintaanAlat.length);
      return `Showing ${start} to ${end} of ${this.dataPermintaanAlat.length} entries`;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = this.currentPage * this.rowsPerPage;
      return this.dataPermintaanAlat.slice(start, end);
    },
    filteredData() {
      let data = this.paginatedData;
      if (this.searchQuery) {
        data = data.filter(permintaan => {
          return (
            permintaan.kode_alat?.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            permintaan.no_permintaan?.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            permintaan.pengguna?.nama_pengguna?.toLowerCase().includes(this.searchQuery.toLowerCase())
          );
        });
      }
      if (this.selectedStatuses.length) {
        data = data.filter(permintaan => this.selectedStatuses.includes(permintaan.status));
      }
      if (this.sortKey) {
        data.sort((a, b) => {
          const modifier = this.reverse ? -1 : 1;
          if (a[this.sortKey] < b[this.sortKey]) return -1 * modifier;
          if (a[this.sortKey] > b[this.sortKey]) return 1 * modifier;
          return 0;
        });
      }
      return data;
    }
  },
  methods: {
    async fetchPermintaan() {
      const res = await fetch('/api/v1/permintaan');
      const data = await res.json();
      this.dataPermintaanAlat = data.by_status;
    },
    debouncedFetchAlats: _.debounce(function () {
      this.fetchPermintaan();
    }, 300),
    viewDetail(id) {
      this.$router.push(`/user/permintaan/detail/${id}`);
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
    sortBy(key) {
      this.reverse = (this.sortKey === key) ? !this.reverse : false;
      this.sortKey = key;
    },
    tambahData() {
      this.showModalInput = true;
    },
    tutupModal() {
      this.showModalInput = false;
    },
  },
  mounted() {
    this.fetchPermintaan();
  }
}
</script>