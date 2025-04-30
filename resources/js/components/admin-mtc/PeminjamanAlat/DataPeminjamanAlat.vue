<template>
  <div class="container-fluid" style="margin-top: 30px;">
    <!-- Modal Input Data -->
    <!--<div id="app" class="modal-input" :class="{'is-visible': showModalInput}" @click.self="tutupModal">
      <div class="modal-content-input">
        <input-peminjaman-alat @tutup-modal="tutupModal"></input-peminjaman-alat>
      </div>
    </div>-->

    <h1 class="h3 mb-4 text-gray-800"><b>Peminjaman dan Permintaan</b></h1>

    <!-- <div class="col-md-12">
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
    </div> -->

    <!-- Data Peminjaman Alat -->
    <div v-if="showAlat">
      <div class="row align-items-center justify-content-start mr-1 mt-3 ml-1">
        <button
          class="btn btn-show m-1"
          :class="{active: showPeminjaman}"
          @click="togglePeminjaman"
        >
          <span v-if="showPeminjaman">Peminjaman</span>
          <span v-else>Peminjaman</span>
        </button>
        <button
          class="btn btn-show m-1"
          :class="{active: showPermintaan}"
          @click="togglePermintaan"
        >
          <span v-if="showPermintaan">Permintaan</span>
          <span v-else>Permintaan</span>
        </button>
      </div>
    </div>
    <div v-if="showPeminjaman" class="mt-4">
      <div class="row align-items-center justify-content-end mr-3 mt-3 mb-2">
        <div class="d-flex justify-content-between mb-2">
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
            <div v-for="sts in kondisiOptions" :key="sts">
              <label>
                <input type="checkbox" :value="sts" @change="updateSelectedStatus($event.target.checked ? sts : '')" />
                {{ sts }}
              </label>
            </div>
          </div>
          <!-- Search -->
          <!-- <div class="search-wrapper">
            <div class="input-group">
              <input 
                type="text" 
                placeholder="search..." 
                class="form-control"
                v-model="searchQuery"
                @input="debouncedFetchAlats"
              />
            </div>
          </div> -->
          <input v-model="search" type="text" placeholder="Search..." class="btn btn-sm border p-2 rounded w-1/3" />
        </div>
      </div>
    <div class="table-responsive">
      <table class="table table-border no-border table-custom text-wrape" style="overflow-x: auto;">
        <thead>
          <tr class="bg-table text-center">
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('no_peminjaman')" style="color: #000;">
              No Peminjaman
              <span v-if="sortKey === 'no_peminjaman'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <!-- <th class="text-center p-2 border cursor-pointer" @click="sortBy('no_seri.tools.nama')" style="color: #000;">
              Nama Alat/Mesin
              <span v-if="sortKey === 'no_seri.tools.nama'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('no_seri')" style="color: #000;">
              No Seri
              <span v-if="sortKey === 'no_seri'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th> -->
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('tanggal_pinjam')" style="color: #000;">
              Tgl Pinjam
              <span v-if="sortKey === 'tanggal_pinjam'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('dipinjam_oleh')" style="color: #000;">
              Dipinjam Oleh
              <span v-if="sortKey === 'dipinjam_oleh'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('divisi')" style="color: #000;">
              Divisi
              <span v-if="sortKey === 'divisi'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('tanggal_kembali')" style="color: #000;">
              Tgl Kembali
              <span v-if="sortKey === 'tanggal_kembali'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>            
            <!-- <th class="text-center p-2 border cursor-pointer" @click="sortBy('kondisi')" style="color: #000;">
              Kondisi
              <span v-if="sortKey === 'kondisi'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th> -->
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('total')" style="color: #000;">
              Total
              <span v-if="sortKey === 'total'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center p-2 border cursor-pointer" @click="sortBy('status')" style="color: #000;">
              Status
              <span v-if="sortKey === 'status'">{{ sortDirection === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="text-center text-black-1">Aksi</th>
          </tr>
        </thead>
        <tbody v-if="paginatedData.length === 0">
          <tr>
            <td class="text-center" colspan="10">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="item in paginatedData" :key="item.id">
          <tr class="text-center">
            <td class="text-center p-2 border">{{ item.no_peminjaman || '-' }}</td>
            <!-- <td class="text-center p-2 border">{{ item.no_seri.tools.nama || '-' }}</td>
            <td class="text-center p-2 border">{{ item.no_seri.no_seri || '-' }}</td> -->
            <td class="text-center p-2 border">{{ item.tgl_pinjam || '-' }}</td>
            <td class="text-center p-2 border">-</td>
            <td class="text-center p-2 border">-</td>
            <td class="text-center p-2 border">{{ item.tgl_kembali ||'-'}}</td>
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
            <td class="text-center p-2 border">{{ item.total }}</td>
            <td>
              <div
                class="btn-sts"
                :class="{
                  'status-active': item.status === 'Selesai',
                  'status-error': item.status === 'Menunggu Diambil',
                  'status-rusak': item.status === 'Ditolak',
                  'status-musnah': item.status === 'Dipinjam',
                  'status-hilang': item.status === 'Belum Diproses',
                }"
              >
                {{ item.status || '-' }}
              </div>
            </td>
            <td class="text-center">
              <div class="dropdown text-center">
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
                  <a class="dropdown-item" @click="viewDetail(item.id)">
                    <i class="fas fa-eye text-info"></i> Detail
                  </a>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
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
  <div v-if="showPermintaan">
      <data-permintaan-alat></data-permintaan-alat>
    </div>
  <!-- Data Peminjaman Mesin-->
  <div v-if="showMesin">
    <peminjaman-mesin></peminjaman-mesin>
  </div>
  </div>
</template>

<script>
import axios from "axios";
import _ from "lodash";

export default {
  props: {
    kodeAlat: String
  },
  data() {
    return {
      // user: {
      //   nama_pengguna: '',
      //   divisi: '',
      // },
      // peminjamanData: [],
      // dataPeminjaman: [], // Menyimpan data error
      showModalInput: false, // Tambahkan variabel untuk mengontrol tampilan modal input
      showPeminjaman: true,
      showPermintaan: false,
      showAlat: true,
      showMesin: false,
      // searchQuery: '',
      // rowsPerPage: 10,
      // currentPage: 1,
      peminjamanData: [],
      search: '',
      selectedNama: '',
      selectedKondisi: '',
      selectedStatus: '',
      sortKey: '',
      sortDirection: 'asc',
      currentPage: 1,
      itemsPerPage: 10
    };
  },
  computed: {
    kondisiOptions() {
      const kondisi = this.peminjamanData.map(item => item.status);
      return [...new Set(kondisi)];
    },
    durasiData() {
      return this.dataPeminjaman.map(peminjamanalat => {
        if (peminjamanalat.tanggal_kembali) {
          const tanggalPinjam = new Date(peminjamanalat.tanggal_pinjam);
          const tanggalKembali = new Date(peminjamanalat.tanggal_kembali);
          const selisihHari = Math.abs(tanggalKembali - tanggalPinjam);
          const hari = Math.ceil(selisihHari / (1000 * 60 * 60 * 24));
          return hari;
        }
      });
    },
    durasiDataKembali() {
      return this.dataPeminjaman.map(peminjamanalat => {
        if (peminjamanalat.tanggal_kembali) {
          const tanggalTerkini = new Date();
          const tanggalKembali = new Date(peminjamanalat.tanggal_kembali);
          const selisihHari = Math.abs(tanggalKembali - tanggalTerkini);
          const hari = Math.ceil(selisihHari / (1000 * 60 * 60 * 24));

          // Jika tanggal terkininya kurang dari tanggal kembali
          if (tanggalTerkini < tanggalKembali) {
            return hari + ' Hari Lagi';
          } else {
            // Jika tanggal terkininya lebih dari tanggal kembali
            const excessDays = Math.ceil((tanggalTerkini - tanggalKembali) / (1000 * 60 * 60 * 24));
            return excessDays + ' Hari Lebih';
          }
        }
      });
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
    filteredData() {
      let data = this.peminjamanData.filter(item => {
        return (
          // (!this.selectedNama || item.no_seri && item.no_seri.tools.nama === this.selectedNama) &&
          (this.selectedStatus === '' || item.status === this.selectedStatus) &&
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
  },
  methods: {
    async fetchData() {
      const res = await fetch('/api/v1/peminjaman');
      const data = await res.json();
      this.peminjamanData = data.by_status;
      // console.log(this.peminjamanData);
    },
    updateSelectedStatus(status) {
      this.selectedStatus = status;
    },
    // async fetchAlatPeminjaman() {
    //   try {
    //     const response = await axios.get(`/api/v1/peminjaman`, {
    //       params: {
    //         search: this.searchQuery
    //       }
    //     });
    //     console.log(response.data); // Log data respons
    //     if (Array.isArray(response.data)) {
    //       this.dataPeminjaman = response.data.map((peminjaman) => ({
    //         id: peminjaman.id,
    //         id_alat: peminjaman.id_alat,
    //         id_user: peminjaman.id_user,
    //         kode_alat: peminjaman.kode_alat,
    //         no_pinjam: peminjaman.no_pinjam,
    //         stok_dipinjam: peminjaman.stok_dipinjam,
    //         tanggal_pinjam: peminjaman.tanggal_pinjam,
    //         tanggal_kembali: peminjaman.tanggal_kembali,
    //         keterangan: peminjaman.keterangan,
    //         status: peminjaman.status,
    //         alat: peminjaman.alat,
    //         pengguna: peminjaman.pengguna,
    //       }));
    //     } else {
    //       console.error("Data respons tidak valid. Harapkan array.");
    //       alert("Gagal memuat data. Silakan coba lagi nanti.");
    //     }
    //   } catch (error) {
    //     console.error("Error fetching alat error detail:", error);
    //     alert("Gagal memuat data. Silakan coba lagi nanti.");
    //   }
    // },
  // debouncedFetchAlats: _.debounce(function () {
  //   this.fetchAlatPeminjaman();
  // }, 300),
    tambahPeminjamanAlat() {
      this.showModalInput = true;
    },
    tutupModal() {
      this.showModalInput = false;
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
    // sortJumlah(order) {
    //   this.dataPeminjaman.sort((a, b) => {
    //     if (order === "asc") {
    //       return a.stok_dipinjam - b.stok_dipinjam;
    //     } else {
    //       return b.stok_dipinjam - a.stok_dipinjam;
    //     }
    //   });
    // },
    // sortTanggalPinjam(order) {
    //   this.dataPeminjaman.sort((a, b) => {
    //     if (order === "asc") {
    //       return new Date(a.tanggal_pinjam) - new Date(b.tanggal_pinjam);
    //     } else {
    //       return new Date(b.tanggal_pinjam) - new Date(a.tanggal_pinjam);
    //     }
    //   });
    // },
    // sortTanggalkembali(order) {
    //   this.dataPeminjaman.sort((a, b) => {
    //     if (order === "asc") {
    //       return new Date(a.tanggal_kembali) - new Date(b.tanggal_kembali);
    //     } else {
    //       return new Date(b.tanggal_kembali) - new Date(a.tanggal_kembali);
    //     }
    //   });
    // },
    toggleAlat() {
      if (!this.showAlat) {
        this.showMesin = false;
        this.showAlat = true;
        this.showPeminjaman = true;
        this.showPermintaan = false;
      }
    },
    toggleMesin() {
      if (!this.showMesin) {
        this.showAlat = false;
        this.showMesin = true;
        this.showPermintaan = false;
        this.showPeminjaman = false;
      }
    },
    togglePeminjaman() {
      if (!this.showPeminjaman) {
        this.showPeminjaman = true;
        this.showPermintaan = false;
      }
    },
    togglePermintaan() {
      if (!this.showPermintaan) {
        this.showPermintaan = true;
        this.showPeminjaman = false;
      }
    },
    viewDetail(id) {
      this.$router.push(`/admin-mtc/data-alat/detail-peminjaman/${id}`);
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
    }
  },
  mounted() {
    this.fetchData();
    // this.fetchAlatPeminjaman();
  }
}
</script>

<style>
  .no-border {
    border: none;
  }

  .no-border th,
  .no-border td {
    border-top: none !important;
    border-bottom: none !important;
  }

  .compact-table th,
  .compact-table td {
    padding: 0.1rem 0.3rem;
  }

  .compact-table tbody tr {
    margin-bottom: 0;
  }

  .compact-table th {
    padding-left: 0.2rem;
    padding-right: 0.2rem;
  }

  .compact-table td {
    padding-left: 0.2rem;
    padding-right: 0.2rem;
  }  

  .sts-warning {
    background-color: rgba(255, 204, 0, 0.1);
    color: #ffcc00;
  }

  .sts-info {
    background-color: rgba(23, 162, 184, 0.1);
    color: #17a2b8;
  }

  .sts-success {
    background-color: rgba(40, 167, 69, 0.1);
    color: #28a745;
  }

  .sts-secondary {
    background-color: rgba(108, 117, 125, 0.1);
    color: #6c757d;
  }

</style>

