<template>  
  <div class="container-fluid" style="margin-top: 30px;">
    <!-- Modal Input Data -->
    <div id="app" class="modal-input" :class="{'is-visible': showModalInput}" @click.self="tutupModal">
      <div class="modal-content-input">
        <input-alat-rusak @tutup-modal="tutupModal"></input-alat-rusak>
      </div>
    </div>

    <!-- Modal Edit Data -->
    <div id="app" class="modal-input" :class="{'is-visible' :showModalEdit}">
      <div class="modal-content-input">
        <edit-alat-rusak @tutup-modal="tutupModal" :id="idEdit"></edit-alat-rusak>
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
            <th class="text-center text-black-1 tr-center">No. Seri Alat</th>
            <!--<th class="text-center text-black-1">Stok Rusak</th>-->
            <th class="text-center text-black-1">PIC Analisa Kerusakan</th>
            <th class="text-center text-black-1">Tanggal Kerusakan</th>
            <th class="text-center text-black-1">Layout</th>
            <th class="text-center text-black-1">Detail Kerusakan</th>
            <th class="text-center text-black-1">Kondisi</th>
            <th class="text-center text-black-1">Aksi</th>
          </tr>
        </thead>
        <tbody v-if="filteredData.length===0">
          <tr>
            <td colspan="8" class="text-center">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="(rusak, index) in filteredData" :key="index">
          <tr class="text-center">
            <td class="text-center">{{ index + 1 }}</td>
            <td class="text-center">{{ rusak.no_seri_alat ? rusak.no_seri_alat.no_seri_alat : '-' }}</td>
            <!--<td class="text-center">{{ rusak.stok_kerusakan || '-' }}</td>-->
            <td class="text-center">{{ rusak.staff_kerusakan ? rusak.staff_kerusakan.nama_staff : '-' }}</td>
            <td class="text-center">{{ rusak.tanggal_kerusakan || '-' }}</td>
            <td class="text-center">{{ rusak.lokasi_penyimpanan || '-' }}</td>                      
            <td class="text-center text-wrap">{{ rusak.deskripsi_kerusakan || '-' }}</td>
            <td 
              class="text-center status-pill parent-element"
              style="margin-top: 10px;"
              :class="{
                        'status-active': rusak.no_seri_alat.status === 'Ready', 
                        'status-rusak': rusak.no_seri_alat.status === 'Rusak', 
                        'status-error': rusak.no_seri_alat.status === 'Error'}"
            >{{ rusak.no_seri_alat ? rusak.no_seri_alat.status : '-' }}</td>
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
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <!--<a class="dropdown-item" @click="viewDetail(noseri.id)">
                    <i class="fas fa-eye text-info"></i> Riwayat
                  </a>-->
                  <a class="dropdown-item" @click="editData(rusak.id)">
                    <i class="fas fa-edit text-primary"></i> Edit
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
    kodeAlat: String
  },
  data() {
    return {
      staff: {
        nama_staff: '',
      },
      datarusak: [],
      showModalInput: false,
      showModalEdit: false,
      searchQuery: '',
      idEdit: null,
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
    }, 
    filteredData() {
      return this.datarusak.filter(rusak => {
        const searchQueryLower = this.searchQuery.toLowerCase();
        const noSeriAlat = rusak.no_seri_alat && rusak.no_seri_alat.no_seri_alat;
        const namaStaff = rusak.staff_analisa && rusak.staff_analisa.nama_staff;
        const Layout = rusak.layout && rusak.layout.nama_layout;
        const detailRusak = rusak.deskripsi_kerusakan;

        return(
          (noSeriAlat && noSeriAlat.toLowerCase().includes(searchQueryLower)) ||
          (namaStaff && namaStaff.toLowerCase().includes(searchQueryLower)) ||
          (Layout && Layout.toLowerCase().includes(searchQueryLower)) ||
          (detailRusak && detailRusak.toLowerCase().includes(searchQueryLower))
        );
      });
    },
  },
  methods: {
    async fetchAlatRusak() {
      try {
        const kodeAlat = this.kodeAlat;
        //console.log(this.kodeAlat);
        const response = await axios.get(`/api/alats/datarusak/${kodeAlat}`);
        this.datarusak = response.data;
        //console.log("Data alat:", this.datarusak); // Debug data        
      } catch (error) {
        console.error("Error fetching alat rusak detail:", error);
        //alert("Gagal memuat detail data rusak");
      }
    },
    debouncedFetchAlats: _.debounce(function () {
        this.fetchAlatRusak();
      }, 300),
    tambahDataRusak(){
      this.showModalInput = true;
    },
    editData(id) {
      this.showModalEdit = true;
      this.idEdit = id;
    },
    tutupModal() {
      this.showModalInput = false;
      this.showModalEdit = false;
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
    this.fetchAlatRusak();
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