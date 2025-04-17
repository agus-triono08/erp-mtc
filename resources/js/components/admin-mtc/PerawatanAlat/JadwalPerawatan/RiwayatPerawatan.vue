<template>
  <div class="container-fluid">
    <!-- Loader -->
    <div class="loader" v-if="isLoading">
      <div class="loading-overlay">
        <div class="loading-spinner">
            <span class="sr-only">Loading...</span>          
        </div>
      </div>
    </div>
    <!-- Riwayat Perawatan -->
    <div class="table-responsive p-3">
      <h5 style="color: #000;"><b>Riwayat Perawatan</b></h5>
      <div class="row align-items-center justify-content-end m-4">                
        <!-- Filter Kondisi -->
        <div class="filter-wrapper ml-2">
          <select class="form-control" v-model="filterKondisi" @change="filterData">
            <option value="">Semua Kondisi</option>
            <option value="OK">OK</option>
            <option value="Rusak">Rusak</option>
            <option value="Error">Error</option>
          </select>
        </div>

        <!-- Filter Status -->
        <div class="filter-wrapper ml-2">
          <select class="form-control" v-model="filterStatus" @change="filterData">
            <option value="">Semua Status</option>
            <option value="Selesai Perawatan">Selesai Perawatan</option>
            <option value="Belum Dilakukan Perawatan">Belum Dilakukan Perawatan</option>
            <option value="Dalam Proses Perawatan">Dalam Proses Perawatan</option>
          </select>
        </div>
        <div class="search-wrapper ml-2">
          <div class="input-group">
            <input type="text" placeholder="Search..." class="form-control"
              v-model="search"
              @input="filterData"
            />
          </div>
        </div>
      </div>
      <div class="row align-items-center justify-content-end m-4">
        <div class="filter-wrapper">
          <div class="input-group">
            <input type="date" placeholder="Tanggal Start..." class="form-control"
              v-model="tanggalStart"
              @input="filterData"
            />
            <span class="mx-2">sampai</span>
            <input type="date" placeholder="Tanggal End..." class="form-control"
              v-model="tanggalEnd"
              @input="filterData"
            />
          </div>
        </div>
      </div>
      <table class="table table-custom has-text-centered is-bordered" style="white-space: nowrap" id="riwayat_perawatan_table">
        <thead class="bg-table">
          <tr style="color: #000;" class="text-center">
            <th @click="sortBy('no_perawatan')">
              No. Perawatan
              <i class="fas" :class="{'fa-sort-up': sortKey === 'no_perawatan' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'no_perawatan' && sortDirection === 'desc'}"></i>
            </th>
            <th>Nama Alat/Mesin</th>
            <th>No Seri</th>
            <th>Tanggal Perawatan</th>
            <th>Rentang Waktu</th>
            <!-- <th @click="sortBy('waktu_mulai')">
              Waktu Mulai
              <i class="fas" :class="{'fa-sort-up': sortKey === 'waktu_mulai' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'waktu_mulai' && sortDirection === 'desc'}"></i>
            </th>
            <th @click="sortBy('waktu_selesai')">
              Waktu Selesai
              <i class="fas" :class="{'fa-sort-up': sortKey === 'waktu_selesai' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'waktu_selesai' && sortDirection === 'desc'}"></i>
            </th> -->
            <th>PIC</th>
            <th>Keterangan Perawatan</th>
            <th>Kondisi</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody v-if="paginatedRiwayatPerawatan.length > 0">
          <tr v-for="item in paginatedRiwayatPerawatan" :key="item.id" class="text-center">
            <td>{{ item.no_perawatan }}</td>
            <td>{{ item.no_seri.tools.nama || '-' }}</td>
            <td>{{ item.no_seri.no_seri || '-' }}</td>
            <td>{{ item.tgl_perawatan || '-' }}</td>
            <td>{{ item.rentang_waktu }} <br>
            <small>{{ item.tgl_mulai_perawatan || '-' }} to {{ item.tgl_selesai_perawatan || '-'}}</small> </td>
            <!-- <td>{{ item.waktu_mulai }}</td>
            <td>{{ item.waktu_selesai }}</td> -->
            <td>{{ item.users_id || '-' }}</td>
            <td>{{ item.detail_perawatan || '-' }}</td>
            <td>
              <div 
                class="btn-sts"
                :class="{
                  'status-rusak': item.no_seri.kondisi === 'Rusak',
                  'status-active': item.no_seri.kondisi === 'OK',
                  'status-error': item.no_seri.kondisi === 'Error',
                }">
                {{ item.no_seri.kondisi }}
                </div>
            </td>
            <td>
              <div 
                class="btn-sts"
                :class="{
                  'status-rusak': item.status === 'Belum Dilakukan Perawatan',
                  'status-active': item.status === 'Selesai Perawatan',
                  'status-error': item.status === 'Dalam Proses Perawatan',
                }">
                {{ item.status }}
                </div>
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td :colspan="8">Tidak ada data</td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3 mb-3" style="border-radius: 10px; background-color: #f3f4f6; height: 50px; color: #000;">
      <div class="ml-3">
          Rows per page:
          <span>{{ perPage }}</span>
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
</template>

<script>
import { sortBy } from 'lodash';
import axios from "axios";

export default {
  data() {
    return {
      picOptions: [
        { text: 'John Doe', value: 'John Doe' },
        { text: 'Jane Doe', value: 'Jane Doe' },
        { text: 'Bob Smith', value: 'Bob Smith' },
        { text: 'Alice Johnson', value: 'Alice Johnson' },
      ],
      riwayatPerawatan: [
        // {          
        //   id: 1,
        //   no_perawatan: 'R-01',
        //   nama_alat: 'Bor',
        //   no_seri: 'B-01',
        //   tanggal_perawatan: '2025-03-06',
        //   tanggal_start: '2025-03-04',
        //   tanggal_end: '2025-03-07',
        //   waktu_mulai: '08:00',
        //   waktu_selesai: '11:00',
        //   pic: 'John Doe',
        //   detail: 'Perawatan Rutin',
        //   kondisi: 'OK',
        //   status: 'Selesai'
        // },
        // {          
        //   id: 2,
        //   no_perawatan: 'R-02',
        //   nama_alat: 'Bor',
        //   no_seri: 'B-02',
        //   tanggal_perawatan: '2025-03-13',
        //   tanggal_start: '2025-03-13',
        //   tanggal_end: '2025-03-15',
        //   waktu_mulai: '08:30',
        //   waktu_selesai: '09:45',
        //   pic: 'Jane Doe',
        //   detail: 'Perawatan Rutin',
        //   kondisi: 'Error',
        //   status: 'Selesai'
        // },
        // {                    
        //   id: 3,
        //   no_perawatan: 'R-03',
        //   nama_alat: 'Bor',
        //   no_seri: 'B-03',
        //   tanggal_perawatan: '2025-03-19',
        //   tanggal_start: '2025-03-17',
        //   tanggal_end: '2025-03-21',
        //   waktu_mulai: '10:00',
        //   waktu_selesai: '12:00',
        //   pic: 'Bob Smith',
        //   detail: 'Perawatan Rutin',
        //   kondisi: 'Rusak',
        //   status: 'Selesai'
        // },
        // tambahkan data lainnya
      ],
      status: '',
      currentPage: 1,
      pages: [],
      perPage: 10, // jumlah data per halaman
      search: '',
      tanggalStart: '',
      tanggalEnd: '',
      nama_alat: '',
      no_seri: '',
      isModalOpen: false,
      modalTitle: 'Tambah Jadwal Perawatan',
      sortKey: '',
      sortDirection: 'asc',
      isLoading: false,
      filterKondisi: '',
      filterStatus: '',
    }
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      this.isLoading = true;
      try {
        const params = {
          all: ''
        };
        const response = await axios.get('/api/v1/perawatan', { params });
        this.riwayatPerawatan = response.data;
        // console.log(this.riwayatPerawatan);
      } catch (error) {
        console.error(error);
      } finally {
        this.isLoading = false;
      }
    },
    sortBy(key) {
      this.sortKey = key
      this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc'
      this.riwayatPerawatan.sort((a, b) => {
        if (key === 'tgl_perawatan') {
          const tanggalA = new Date(a[key])
          const tanggalB = new Date(b[key])
          if (this.sortDirection === 'asc') {
            return tanggalA > tanggalB ? 1 : -1
          } else {
            return tanggalA < tanggalB ? 1 : -1
          }
        } else {
          if (this.sortDirection === 'asc') {
            return a[key] > b[key] ? 1 : -1
          } else {
            return a[key] < b[key] ? 1 : -1
          }
        }
      })
    },
    filterData() {
      this.currentPage = 1;
    },
    filterTanggal(tanggal, item) {
      if (item.tgl_mulai_perawatan && item.tgl_selesai_perawatan) {
        return tanggal >= item.tgl_mulai_perawatan && tanggal <= item.tgl_selesai_perawatan
      } else if (item.tgl_mulai_perawatan) {
        return tanggal >= item.tgl_mulai_perawatan
      } else if (item.tgl_selesai_perawatan) {
        return tanggal <= item.tgl_selesai_perawatan
      } else {
        return true
      }
    },
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--
      }
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++
      }
    },
    updatePerPage() {
      this.currentPage = 1
    }
  },
  computed: {
    filteredRiwayatPerawatan() {
      return this.riwayatPerawatanWithRentang.filter(item => {
        const noPerawatan = String(item.no_perawatan || '').toLowerCase();
        const namaAlat = String(item.no_seri?.tools?.nama || '').toLowerCase();
        const noSeri = String(item.no_seri?.no_seri || '').toLowerCase();
        const search = this.search.toLowerCase();

        const matchSearch = noPerawatan.includes(search) || namaAlat.includes(search) || noSeri.includes(search);
        const matchTanggal = (
          (this.tanggalStart === '' || item.tgl_mulai_perawatan >= this.tanggalStart) &&
          (this.tanggalEnd === '' || item.tgl_selesai_perawatan <= this.tanggalEnd)
        );
        const matchKondisi = this.filterKondisi === '' || item.no_seri?.kondisi === this.filterKondisi;
        const matchStatus = this.filterStatus === '' || item.status === this.filterStatus;

        return matchSearch && matchTanggal && matchKondisi && matchStatus;
      }).sort((a, b) => {
        if (!this.sortKey) return 0;
        if (this.sortDirection === 'asc') {
          return a[this.sortKey] > b[this.sortKey] ? 1 : -1;
        } else {
          return a[this.sortKey] < b[this.sortKey] ? 1 : -1;
        }
      });
    },
    totalPages() {
      return Math.ceil(this.filteredRiwayatPerawatan.length / this.perPage)
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.perPage + 1
      const end = Math.min(this.currentPage * this.perPage, this.filteredRiwayatPerawatan.length)
      return `Showing ${start} to ${end} of ${this.filteredRiwayatPerawatan.length} entries`
    },
    paginatedRiwayatPerawatan() {
      const start = (this.currentPage - 1) * this.perPage
      const end = this.currentPage * this.perPage
      return this.filteredRiwayatPerawatan.slice(start, end)
    },
    riwayatPerawatanWithRentang() {
      return this.riwayatPerawatan.map(item => {
        const tanggalStart = new Date(item.tgl_mulai_perawatan);
        const tanggalEnd = new Date(item.tgl_selesai_perawatan);
        const rentangWaktu = Math.abs(tanggalEnd - tanggalStart) / (1000 * 60 * 60 * 24);
        return {
          ...item,
          rentang_waktu: `${rentangWaktu} hari`
        }
      })
    },
  }
}
</script>