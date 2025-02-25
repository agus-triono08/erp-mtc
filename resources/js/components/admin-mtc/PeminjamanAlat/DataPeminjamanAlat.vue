<template>
  <div class="container-fluid" style="margin-top: 30px;">
    <!-- Modal Input Data -->
    <!--<div id="app" class="modal-input" :class="{'is-visible': showModalInput}" @click.self="tutupModal">
      <div class="modal-content-input">
        <input-peminjaman-alat @tutup-modal="tutupModal"></input-peminjaman-alat>
      </div>
    </div>-->

    <h1 class="h3 mb-4 text-gray-800"><b>Peminjaman</b></h1>

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
          <div
            class="dropdown-menu p-3"
            aria-labelledby="filterDropdown"
            style="border-radius: 8px; width: 250px;"
            @click.stop
          >
            <label for="Status"><b>Status</b></label>
            <div v-for="sts in availableStatus" :key="sts">
              <label><input type="checkbox" :value="sts" v-model="filterStatus" /> {{sts}}</label>
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
      </div>
    <div class="table-responsive">
      <table class="table table-border no-border table-custom text-wrape" style="overflow-x: auto;">
        <thead>
          <tr class="bg-table text-center">
            <th class="text-center text-black-1 tr-center">#</th>
            <th class="text-center text-black-1">No. Pinjam</th>           
            <th class="text-center text-black-1">Dipinjam Oleh</th>
            <th class="text-center text-black-1">Divisi</th>
            <th class="text-center text-black-1" style="cursor: pointer; position: relative; vertical-align: middle;">
              Tanggal Pinjam
              <span class="sort-icons">
                <i @click="sortTanggalPinjam('desc')" class="fas fa-sort-up"></i>
                <i @click="sortTanggalPinjam('asc')" class="fas fa-sort-down"></i>
              </span>
            </th>
            <th class="text-center text-black-1" style="cursor: pointer; position: relative; vertical-align: middle;">
              Tanggal Kembali
              <span class="sort-icons">
                <i @click="sortTanggalkembali('desc')" class="fas fa-sort-up"></i>
                <i @click="sortTanggalkembali('asc')" class="fas fa-sort-down"></i>
              </span>
            </th>            
            <th class="text-center text-black-1">Durasi</th>
            <th class="text-center text-black-1">Status</th>
            <th class="text-center text-black-1">Action</th>
          </tr>
        </thead>
        <tbody v-if="filteredData.length===0">
          <tr>
            <td colspan="10" class="text-center text-black-1">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="(peminjaman, index) in filteredData" :key="index">
          <tr class="text-center">
            <td class="text-center">{{ index + 1 }}</td>
            <td class="text-center">{{ peminjaman.no_pinjam || '-' }}</td>            
            <td class="text-center">{{ peminjaman.pengguna ? peminjaman.pengguna.nama_pengguna : '-' }}</td>
            <td class="text-center">{{ peminjaman.pengguna ? peminjaman.pengguna.divisi : '-' }}</td>
            <td class="text-center">{{ peminjaman.tanggal_pinjam || '-' }}</td>
            <td class="text-center">{{ peminjaman.tanggal_kembali || '-' }}</td>
            <td class="text-center">
              {{ durasiData[index] !== '-' ? durasiData[index] + ' Hari' : '-' }} <br>
              <small>
                <i :class="{'fas fa-clock': !durasiDataKembali[index].includes('Hari Lebih'), 'fas fa-exclamation-circle text-danger': durasiDataKembali[index].includes('Hari Lebih')}"></i>
                <span :class="{'text-danger': durasiDataKembali[index].includes('Hari Lebih')}">
                  {{ durasiDataKembali[index] }}
                </span>
              </small>
            </td>
            <td>
              <div
                class="btn-sts"
                :class="{
                  'sts-warning': peminjaman.status === 'Sedang Dipinjam',
                  'sts-info': peminjaman.status === 'Menunggu Persiapan Barang',
                  'sts-success': peminjaman.status === 'Barang Siap Diambil',
                  'sts-secondary': peminjaman.status === 'Selesai'
                }"
              >
                {{ peminjaman.status }}
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
                  <a class="dropdown-item" @click="viewDetail(peminjaman.id)">
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
      user: {
        nama_pengguna: '',
        divisi: '',
      },
      dataPeminjaman: [], // Menyimpan data error
      showModalInput: false, // Tambahkan variabel untuk mengontrol tampilan modal input
      showPeminjaman: true,
      showPermintaan: false,
      showAlat: true,
      showMesin: false,
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
    };
  },
  computed: {
    availableStatus() {
      return [...new Set(this.dataPeminjaman.map(item => item.status))];
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
      totalPages() {
        return Math.ceil(this.dataPeminjaman.length / this.rowsPerPage);
      },
      paginationInfo() {
        const start = (this.currentPage - 1) * this.rowsPerPage + 1;
        const end = Math.min(this.currentPage * this.rowsPerPage, this.dataPeminjaman.length);
        return `Showing ${start} to ${end} of ${this.dataPeminjaman.length} entries`;
      },
      paginatedData() {
        const start = (this.currentPage - 1) * this.rowsPerPage;
        const end = start + this.rowsPerPage;
        return this.dataPeminjaman.slice(start, end);
      },
      filteredData() {
        if (this.searchQuery) {
          return this.paginatedData.filter(peminjaman => {
            return (
              peminjaman.kode_alat.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
              peminjaman.no_pinjam.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
              (peminjaman.pengguna && peminjaman.pengguna.nama_pengguna.toLowerCase().includes(this.searchQuery.toLowerCase()))
            );
          });
        } else {
          return this.paginatedData;
        }
      }
    },
  methods: {
    async fetchAlatPeminjaman() {
      try {
      const response = await axios.get(`/api/peminjaman`, {
        params: {
          search: this.searchQuery
        }
      });
      this.dataPeminjaman = response.data.data.map((peminjaman) => ({
        id: peminjaman.id,
        id_alat: peminjaman.id_alat,
        id_user: peminjaman.id_user,
        kode_alat: peminjaman.kode_alat,
        no_pinjam: peminjaman.no_pinjam,
        stok_dipinjam: peminjaman.stok_dipinjam,
        tanggal_pinjam: peminjaman.tanggal_pinjam,
        tanggal_kembali: peminjaman.tanggal_kembali,
        keterangan: peminjaman.keterangan,
        status: peminjaman.status,
        alat: peminjaman.alat,
        pengguna: peminjaman.pengguna,
      })); // Menyimpan data alat
      //console.log(this.dataPeminjaman); // Debug data
    } catch (error) {
      console.error("Error fetching alat error detail:", error);
      //alert("Gagal memuat detail data alat error.");
    }
  },
  debouncedFetchAlats: _.debounce(function () {
    this.fetchAlatPeminjaman();
  }, 300),
    tambahPeminjamanAlat() {
      this.showModalInput = true;
    },
    tutupModal() {
      this.showModalInput = false;
    },
    sortJumlah(order) {
      this.dataPeminjaman.sort((a, b) => {
        if (order === "asc") {
          return a.stok_dipinjam - b.stok_dipinjam;
        } else {
          return b.stok_dipinjam - a.stok_dipinjam;
        }
      });
    },
    sortTanggalPinjam(order) {
      this.dataPeminjaman.sort((a, b) => {
        if (order === "asc") {
          return new Date(a.tanggal_pinjam) - new Date(b.tanggal_pinjam);
        } else {
          return new Date(b.tanggal_pinjam) - new Date(a.tanggal_pinjam);
        }
      });
    },
    sortTanggalkembali(order) {
      this.dataPeminjaman.sort((a, b) => {
        if (order === "asc") {
          return new Date(a.tanggal_kembali) - new Date(b.tanggal_kembali);
        } else {
          return new Date(b.tanggal_kembali) - new Date(a.tanggal_kembali);
        }
      });
    },
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
    this.fetchAlatPeminjaman();
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