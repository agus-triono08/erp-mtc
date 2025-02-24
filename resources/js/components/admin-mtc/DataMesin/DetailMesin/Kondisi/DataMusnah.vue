<template>
    <div class="container-fluid" style="margin-top: 30px;">
  
      <!--- Modal Input data -->
      <div id="app" class="modal-input" :class="{'is-visible': showModalInput}" @click.self="tutupModal">
        <div class="modal-content-input">
          <input-alat-musnah @tutup-modal="tutupModal"></input-alat-musnah>
        </div>
      </div>
  
      <div class="row align-items-center justify-content-end mr-3 mt-3 mb-4">      
        <!-- Tambah Data -->
        <button class="btn btn-sm btn-outline-primary mr-2 ml-1" @click="tambahDataMusnah">
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
              <th class="text-center text-black-1">No. Seri Mesin</th>
              <!--<th class="text-center text-black-1">Stok Musnah</th>-->
              <th class="text-center text-black-1">PIC Pemusnahan</th>
              <th class="text-center text-black-1">Tanggal Pemusnahan</th>
              <th class="text-center text-black-1">Deskripsi Pemusnahan</th>
              <th class="text-center text-black-1">Dokument Pendukung</th>
              <th class="text-center text-black-1">Aksi</th>
            </tr>
          </thead>
          <tbody v-if="datamusnah.length===0">
            <tr>
              <td colspan="7" class="text-center">Tidak Ada Data</td>
            </tr>
          </tbody>
          <tbody v-for="(musnah, index) in datamusnah" :key="index">
            <tr>
              <td class="text-center">{{ index + 1 }}</td>
              <td class="text-center">{{ musnah.no_seri_mesin ? musnah.no_seri_mesin.no_seri_mesin : '-' }}</td>
              <!--<td class="text-center">{{ musnah.stok_musnah || '-' }}</td>-->
              <td class="text-center">{{ musnah.staff_pemusnahan ? musnah.staff_pemusnahan.nama_staff : '-' }}</td>
              <td class="text-center">{{ musnah.tanggal_musnah || '-' }}</td>
              <td class="text-center">{{ musnah.deskripsi_musnah || '-' }}</td>
              <td class="text-center">
                <div v-if="musnah.fileUrl">
                  <img v-if="musnah.isImage" :src="musnah.fileUrl" width="100%" height="200" style="cursor: zoom-in;"/>
                  <iframe v-else :src="musnah.fileUrl" width="100%" height="200" style="cursor: zoom-in;"></iframe>
                </div>
              </td>
              <td>
              <button class="btn btn-sm btn-outline-primary mr-2">
                <i class="fas fa-print"></i>
              </button>
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
  import axios from "axios";
  
  export default {
    props: {
      kodeMesin: String
    },
    data() {
      return {
        staff: {
          nama_staff: '',
        },
        datamusnah: [
          {
            no_seri_mesin: { no_seri_mesin: 'LMN654321' },
            staff_pemusnahan: { nama_staff: 'Bob Smith' },
            tanggal_musnah: '2025-02-22',
            deskripsi_musnah: 'Mesin tidak dapat diperbaiki, komponen utama rusak parah',
            fileUrl: 'https://tehniq.com/cdn/shop/products/Mesin-Bor-Listrik-NLG-BD-450-VR-Electric-Drill_800x_crop_center.jpg?v=1619591051', // Contoh file URL gambar
            isImage: true
          },
        ],            
        isImage: false,
        showModalInput: false,
        currentPage: 1,
        rowsPerPage: 10,
      };
    },
    computed: {
      paginatedMesins() {
        const start = (this.currentPage - 1) * this.rowsPerPage;
        return this.datamusnah.slice(start, start + this.rowsPerPage);
      },
      totalPages() {
        return Math.ceil(this.datamusnah.length / this.rowsPerPage);
      },
      paginationInfo() {
        if (!this.datamusnah.length) return '0-0 of 0';
        const start = (this.currentPage - 1) * this.rowsPerPage + 1;
        const end = Math.min(start + this.rowsPerPage - 1, this.datamusnah.length);
        return `Showing ${start} to ${end} of ${this.datamusnah.length} entries`;
      },
    },
    methods: {
      async fetchMesinMusnah() {
        try {
          const kodeMesin = this.kodeMesin;
          //console.log(this.kodemesin);
          const response = await axios.get(`/api/mesins/musnah/${kodeMesin}`);
          this.datamusnah = response.data;
        } catch (error) {
          console.error("Error fetching detail mesin musnah : ", error);
          //alert("Gagal memuat detail data alat musnah.");
        }
      }, 
      tambahDataMusnah() {
        this.showModalInput = true;
      },
      tutupModal() {
        this.showModalInput = false;
      },
      prevPage() {
        if (this.currentPage > 1) this.currentPage--;
      },
      nextPage() {
        if (this.currentPage < this.totalPages) this.currentPage++;
      },
    },
    mounted() {
      this.fetchMesinMusnah();
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