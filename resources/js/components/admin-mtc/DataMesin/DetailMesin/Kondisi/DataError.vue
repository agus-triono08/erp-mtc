<template>
    <div class="container-fluid" style="margin-top: 30px;">
      <!-- Modal Input Data -->
      <div id="app" class="modal-input" :class="{'is-visible': showModalInput}" >
        <div class="modal-content-input">
          <input-alat-error @tutup-modal="tutupModal"></input-alat-error>
        </div>
      </div>
  
      <div class="row align-items-center justify-content-end mr-3 mt-3 mb-4">      
        <!-- Tambah Data -->
        <button class="btn btn-sm btn-outline-primary mr-2 ml-1" @click="tambahDataError">
          <i class="fa fa-plus-circle"></i> Tambah Data
        </button>
        <!-- Search -->
        <div class="search-wrapper">
          <div class="input-group">
            <input type="text" placeholder="search..." class="form-control"
              v-model="searchQuery"
              @input="debouncedFetchAlats"/>
            </div>
          </div>
      </div>
      <div class="table-responsive text-wrape">
        <table class="table table-border no-border table-custom" style="overflow-x: auto;">
          <thead>
            <tr class="bg-table">
              <th class="text-center text-black-1 tr-center">#</th>
              <th class="text-center text-black-1 tr-center">No. Seri Mesin</th>            
              <th class="text-center text-black-1">PIC</th>
              <th class="text-center text-black-1">Tgl Error</th>
              <th class="text-center text-black-1">Tgl Selesai</th>
              <th class="text-center text-black-1">Layout</th>
              <th class="text-center text-black-1">Detail Error</th>
              <th class="text-center text-black-1">Kondisi</th>
              <th class="text-center text-black-1">Aksi</th>
            </tr>
          </thead>
          <tbody v-if="errors.length===0">
            <tr>
              <td colspan="9" class="text-center">Tidak Ada Data</td>
            </tr>
          </tbody>
          <tbody v-for="(error, index) in errors" :key="index">
            <tr class="text-center">
              <td class="text-center">{{ index + 1 }}</td>
              <td class="text-center">{{ error.no_seri_mesin ? error.no_seri_mesin.no_seri_mesin : '-' }}</td>
              <!--<td class="text-center">{{ error.stok_error || '-' }}</td>-->
              <td class="text-center">{{ error.staff_analisa ? error.staff_analisa.nama_staff : '-' }}</td>
              <td class="text-center">{{ error.tanggal_error || '-' }}</td>
              <td class="text-center">{{ error.tanggal_perbaikan || 'Belum Selesai Diperbaiki' }}</td>
              <td class="text-center">{{ error.layout ? error.layout.nama_layout : '-' }}</td>
              <td class="text-center text-wrap">{{ error.deskripsi_error || '-' }}</td>            
              <td 
                class="text-center status-pill parent-element"
                style="margin-top: 10px;"
                :class="{
                          'status-active': error.no_seri_mesin.status === 'Ready', 
                          'status-rusak': error.no_seri_mesin.status === 'Rusak', 
                          'status-error': error.no_seri_mesin.status === 'Error'}"
              >{{ error.no_seri_mesin ? error.no_seri_mesin.status : '-' }}</td>
              <td>
                <button class="btn btn-plus btn-sm">
                  <i class="fas fa-edit"></i>
                </button>
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
    props: {
      kodeMesin: String
    },
    data() {
      return {
        staff: {
          nama_staff:''
        },
        errors: [], // Menyimpan data error
        showModalInput: false, // Tambahkan variabel untuk mengontrol tampilan modal input
        searchQuery: '', // Variabel untuk menyimpan query pencarian
        rowsPerPage: 10,
        currentPage: 1,
      };
    },
    computed: {
      totalPages() {
        return Math.ceil(this.errors.length / this.rowsPerPage);
      },
      paginationInfo() {
        const start = (this.currentPage - 1) * this.rowsPerPage + 1;
        const end = Math.min(this.currentPage * this.rowsPerPage, this.errors.length);
        return `Showing ${start} to ${end} of ${this.errors.length} entries`;
      },
      paginatedData() {
        const start = (this.currentPage - 1) * this.rowsPerPage;
        const end = start + this.rowsPerPage;
        return this.errors.slice(start, end);
      },
      filteredData() {
        return this.errors.filter(error => {
          const searchQueryLower = this.searchQuery.toLowerCase();
          const noSeriAlat = error.no_seri_alat && error.no_seri_alat.no_seri_alat;
          const namaStaff = error.staff_analisa && error.staff_analisa.nama_staff;
          const Layout = error.layout && error.layout.nama_layout;
          const detailError = error.deskripsi_error;

          return(
            (noSeriAlat && noSeriAlat.toLowerCase().includes(searchQueryLower)) ||
            (namaStaff && namaStaff.toLowerCase().includes(searchQueryLower)) ||
            (Layout && Layout.toLowerCase().includes(searchQueryLower)) ||
            (detailError && detailError.toLowerCase().includes(searchQueryLower))
          );
        });
      }
    },
    methods: {
      async fetchMesinError() {
        try {
          const kodeMesin = this.kodeMesin; // Kode alat di URL
          //console.log(this.kodeMesin);
          const response = await axios.get(`/api/mesins/errors/${kodeMesin}`);
          this.errors = response.data; // Menyimpan data alat
          //console.log(''); // Debug data
        } catch (error) {
          console.error("Error fetching mesin error detail:", error);
          //alert("Gagal memuat detail data alat error.");
        }
      },
      debouncedFetchAlats: _.debounce(function () {
        this.fetchMesinError();
      }, 300),
      tambahDataError() {
        this.showModalInput = true;
      },
      tutupModal() {
        this.showModalInput = false;
      },
      sortStokError(order) {
        this.errors.sort((a, b) => {
          if (order === 'asc') {
            return a.stok_error - b.stok_error;
          } else {
            return b.stok_error - a.stok_error;
          }
        });
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
    },
    mounted() {
      this.fetchMesinError();
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
  
    .text-wrap {
      white-space: normal; /* Atau gunakan pre-wrap jika ingin mempertahankan spasi */
      word-wrap: break-word; /* Memungkinkan kata untuk terputus jika terlalu panjang */
      overflow-wrap: break-word; /* Memastikan kata panjang terputus */
    }
  
    .modal-input {
      display: none; /* Sembunyikan modal secara default */
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5); /* Latar belakang transparan */
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
  
    .modal-input.is-visible {
      display: flex; /* Tampilkan modal saat is-visible aktif */
    }
  
    .modal-content-input {
      background-color: white;
      padding: 20px;
      border-radius: 20px;
      max-width: max-content;
      width: 100%;
    }
  </style>