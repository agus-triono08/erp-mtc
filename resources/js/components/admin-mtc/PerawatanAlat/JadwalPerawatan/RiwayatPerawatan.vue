<template>
  <div class="container-fluid">
    <!-- Riwayat Perawatan -->
    <div class="table-responsive p-3">
      <h5 style="color: #000;"><b>Riwayat Perawatan</b></h5>
      <div class="row align-items-center justify-content-end m-3">        
        <div class="filter-wrapper">
          <div class="input-group">
            <input type="date" placeholder="Tanggal Start..." class="form-control"
              v-model="tanggalStart"
            />
            <span class="mx-2">sampai</span>
            <input type="date" placeholder="Tanggal End..." class="form-control"
              v-model="tanggalEnd"
            />
          </div>
        </div>
        <div class="search-wrapper ml-2">
          <div class="input-group">
            <input type="text" placeholder="Cari..." class="form-control"
              v-model="search"
            />
          </div>
        </div>
      </div>
      <table class="table table-custom has-text-centered is-bordered" style="white-space: nowrap" id="riwayat_perawatan_table">
        <thead class="bg-table">
          <tr style="color: #000;" class="text-center">
            <th @click="sortBy('nama_alat')">
              Nama Alat/Mesin
              <i class="fas" :class="{'fa-sort-up': sortKey === 'nama_alat' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'nama_alat' && sortDirection === 'desc'}"></i>
            </th>
            <th @click="sortBy('no_seri')">
              No Seri
              <i class="fas" :class="{'fa-sort-up': sortKey === 'no_seri' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'no_seri' && sortDirection === 'desc'}"></i>
            </th>
            <th @click="sortBy('tanggal_perawatan')">
              Tanggal Perawatan
              <i class="fas" :class="{'fa-sort-up': sortKey === 'tanggal_perawatan' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'tanggal_perawatan' && sortDirection === 'desc'}"></i>
            </th>
            <th @click="sortBy('waktu_mulai')">
              Waktu Mulai
              <i class="fas" :class="{'fa-sort-up': sortKey === 'waktu_mulai' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'waktu_mulai' && sortDirection === 'desc'}"></i>
            </th>
            <th @click="sortBy('waktu_selesai')">
              Waktu Selesai
              <i class="fas" :class="{'fa-sort-up': sortKey === 'waktu_selesai' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'waktu_selesai' && sortDirection === 'desc'}"></i>
            </th>
            <th @click="sortBy('pic')">
              PIC
              <i class="fas" :class="{'fa-sort-up': sortKey === 'pic' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'pic' && sortDirection === 'desc'}"></i>
            </th>
            <th @click="sortBy('detail')">
              Keterangan Perawatan
              <i class="fas" :class="{'fa-sort-up': sortKey === 'detail' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'detail' && sortDirection === 'desc'}"></i>
            </th>
            <th @click="sortBy('kondisi')">
              Kondisi
              <i class="fas" :class="{'fa-sort-up': sortKey === 'kondisi' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'kondisi' && sortDirection === 'desc'}"></i>
            </th>
            <th @click="sortBy('status')">
              Status
              <i class="fas" :class="{'fa-sort-up': sortKey === 'status' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'status' && sortDirection === 'desc'}"></i>
            </th>
          </tr>
        </thead>
        <tbody v-if="paginatedRiwayatPerawatan.length > 0">
          <tr v-for="item in paginatedRiwayatPerawatan" :key="item.id" class="text-center">
            <td>{{ item.nama_alat }}</td>
            <td>{{ item.no_seri }}</td>
            <td>{{ item.tanggal_perawatan }}</td>
            <td>{{ item.waktu_mulai }}</td>
            <td>{{ item.waktu_selesai }}</td>
            <td>{{ item.pic }}</td>
            <td>{{ item.detail }}</td>
            <td>
              <div 
                class="btn-sts"
                :class="{
                  'status-rusak': item.kondisi === 'Rusak',
                  'status-active': item.kondisi === 'OK',
                  'status-error': item.kondisi === 'Error',
                }">
                {{ item.kondisi }}
                </div>
            </td>
            <td>
              <div 
                class="btn-sts"
                :class="{
                  'status-rusak': item.status === 'Belum Selesai',
                  'status-active': item.status === 'Selesai',
                  'status-error': item.status === 'Error',
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
        {          
          id: 1,
          no_perawatan: 'R-01',
          nama_alat: 'Bor',
          no_seri: 'B-01',
          tanggal_perawatan: '2025-03-06',
          waktu_mulai: '08:00',
          waktu_selesai: '11:00',
          pic: 'John Doe',
          detail: 'Perawatan Rutin',
          kondisi: 'OK',
          status: 'Selesai'
        },
        {          
          id: 2,
          no_perawatan: 'R-02',
          nama_alat: 'Bor',
          no_seri: 'B-02',
          tanggal_perawatan: '2025-03-13',
          waktu_mulai: '08:30',
          waktu_selesai: '09:45',
          pic: 'Jane Doe',
          detail: 'Perawatan Rutin',
          kondisi: 'Error',
          status: 'Selesai'
        },
        {                    
          id: 3,
          no_perawatan: 'R-03',
          nama_alat: 'Bor',
          no_seri: 'B-03',
          tanggal_perawatan: '2025-03-19',
          waktu_mulai: '10:00',
          waktu_selesai: '12:00',
          pic: 'Bob Smith',
          detail: 'Perawatan Rutin',
          kondisi: 'Rusak',
          status: 'Selesai'
        },
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
    }
  },
  methods: {
    sortBy(key) {
      this.sortKey = key
      this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc'
    },
    filterTanggal(tanggal) {
      if (this.tanggalStart && this.tanggalEnd) {
        return tanggal >= this.tanggalStart && tanggal <= this.tanggalEnd
      } else if (this.tanggalStart) {
        return tanggal >= this.tanggalStart
      } else if (this.tanggalEnd) {
        return tanggal <= this.tanggalEnd
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
      return this.riwayatPerawatan.filter(item => {
        return Object.values(item).some(value => {
          return String(value).toLowerCase().includes(this.search.toLowerCase())
        }) && this.filterTanggal(item.tanggal_perawatan)
      }).sort((a, b) => {
        if (this.sortDirection === 'asc') {
          return a[this.sortKey] > b[this.sortKey] ? 1 : -1
        } else {
          return a[this.sortKey] < b[this.sortKey] ? 1 : -1
        }
      })
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
    }
  }
}
</script>