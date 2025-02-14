<template>  
    <div class="container-fluid" style="margin-top: 30px;">
      <!-- Modal Input Data -->
      <div id="app" class="modal-input" :class="{'is-visible': showModalInput}" @click.self="tutupModal">
        <div class="modal-content-input">
          <input-alat-rusak @tutup-modal="tutupModal"></input-alat-rusak>
        </div>
      </div>
  
      <div class="row align-items-center justify-content-end mr-3 mt-3 mb-4">      
        <!-- Tambah Data -->
        <button class="btn btn-sm btn-outline-primary mr-2 ml-1" @click="tambahDataRusak">
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
        <table class="table table-border no-border table-custom">
          <thead>
            <tr class="bg-table">
              <th class="text-center text-black-1 tr-center">#</th>
              <th class="text-center text-black-1 tr-center">No. Seri Mesin</th>
              <!--<th class="text-center text-black-1">Stok Rusak</th>-->
              <th class="text-center text-black-1">PIC Analisa Kerusakan</th>
              <th class="text-center text-black-1">Tanggal Kerusakan</th>
              <th class="text-center text-black-1">Layout</th>
              <th class="text-center text-black-1">Detail Kerusakan</th>
              <th class="text-center text-black-1">Kondisi</th>
              <th class="text-center text-black-1">Aksi</th>
            </tr>
          </thead>
          <tbody v-if="datarusak.length===0">
            <tr>
              <td colspan="8" class="text-center">Tidak Ada Data</td>
            </tr>
          </tbody>
          <tbody v-for="(rusak, index) in datarusak" :key="index">
            <tr class="text-center">
              <td class="text-center">{{ index + 1 }}</td>
              <td class="text-center">{{ rusak.no_seri_mesin ? rusak.no_seri_mesin.no_seri_mesin : '-' }}</td>
              <!--<td class="text-center">{{ rusak.stok_kerusakan || '-' }}</td>-->
              <td class="text-center">{{ rusak.staff_kerusakan ? rusak.staff_kerusakan.nama_staff : '-' }}</td>
              <td class="text-center">{{ rusak.tanggal_kerusakan || '-' }}</td>
              <td class="text-center">{{ rusak.lokasi_penyimpanan || '-' }}</td>                      
              <td class="text-center text-wrap">{{ rusak.deskripsi_kerusakan || '-' }}</td>
              <td 
                class="text-center status-pill parent-element"
                style="margin-top: 10px;"
                :class="{
                          'status-active': rusak.no_seri_mesin.status === 'Ready', 
                          'status-rusak': rusak.no_seri_mesin.status === 'Rusak', 
                          'status-error': rusak.no_seri_mesin.status === 'Error'}"
              >{{ rusak.no_seri_mesin ? rusak.no_seri_mesin.status : '-' }}</td>
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
  import axios from 'axios';
  
  export default {
    props: {
      kodeMesin: String
    },
    data() {
      return {
        staff: {
          nama_staff: '',
        },
        datarusak: [],
        showModalInput: false,
        rowsPerPage: 10,
        currentPage: 1,
      };
    },
    computed: {
      totalPages() {
        return Math.ceil(this.datarusak.length / this.rowsPerPage);
      },
      paginationInfo() {
        const start = (this.currentPage - 1) * this.rowsPerPage + 1;
        const end = Math.min(this.currentPage * this.rowsPerPage, this.datarusak.length);
        return `Showing ${start} to ${end} of ${this.datarusak.length} entries`;
      },
      paginatedData() {
        const start = (this.currentPage - 1) * this.rowsPerPage;
        const end = start + this.rowsPerPage;
        return this.datarusak.slice(start, end);
      }
    },
    methods: {
      async fetchMesinRusak() {
        try {
          const kodeMesin = this.kodeMesin;
          //console.log(this.kodeMesin);
          const response = await axios.get(`/api/mesins/rusak/${kodeMesin}`);
          this.datarusak = response.data;
          //console.log("Data alat:", this.datarusak); // Debug data        
        } catch (error) {
          console.error("Error fetching mesin rusak detail:", error);
          //alert("Gagal memuat detail data rusak");
        }
      },
      tambahDataRusak(){
        this.showModalInput = true;
      },
      tutupModal() {
        this.showModalInput = false;
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
      this.fetchMesinRusak();
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
  </style>