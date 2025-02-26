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
            <th class="text-center text-black-1">No. Seri Alat</th>
            <!--<th class="text-center text-black-1">Stok Musnah</th>-->
            <th class="text-center text-black-1">PIC Pemusnahan</th>
            <th class="text-center text-black-1">Tanggal Pemusnahan</th>
            <th class="text-center text-black-1">Deskripsi Pemusnahan</th>
            <th class="text-center text-black-1">Status</th>
            <th class="text-center text-black-1">Dokument Pendukung</th>
            <!-- <th class="text-center text-black-1">Aksi</th> -->
          </tr>
        </thead>
        <tbody v-if="filteredData.length===0">
          <tr>
            <td colspan="6" class="text-center">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="(musnah, index) in filteredData" :key="index">
          <tr class="text-center">
            <td class="text-center">{{ index + 1 }}</td>
            <td class="text-center">{{ musnah.no_seri_alat ? musnah.no_seri_alat.no_seri_alat : '-' }}</td>
            <!--<td class="text-center">{{ musnah.stok_musnah || '-' }}</td>-->
            <td class="text-center">{{ musnah.staff_pemusnahan ? musnah.staff_pemusnahan.nama_staff : '-' }}</td>
            <td class="text-center">{{ musnah.tanggal_musnah || '-' }}</td>
            <td class="text-center">{{ musnah.deskripsi_musnah || '-' }}</td>
            <td>
              <div
                class="btn-sts"
                :class="{
                  'status-active' : musnah.status === 'Diterima',
                  'status-hilang' : musnah.status === 'Proses',
                  'status-rusak' : musnah.status === 'Ditolak',
                }"
              >
                {{ musnah.status || '-' }}
              </div>
            </td>
            <td class="text-center">
              <div v-if="musnah.fileUrl">
                <img v-if="musnah.isImage" :src="musnah.fileUrl" width="100%" height="200" style="cursor: zoom-in;"/>
                <iframe v-else :src="musnah.fileUrl" width="100%" height="200" style="cursor: zoom-in;"></iframe>
              </div>
            </td>
            <!-- <td>
              <button class="btn btn-sm btn-outline-primary mr-2">
                <i class="fas fa-print"></i>
              </button>
            </td> -->
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
    kodeAlat: String
  },
  data() {
    return {
      staff: {
        nama_staff: '',
      },
      datamusnah: [],            
      isImage: false,
      showModalInput: false,
      showModalEdit: false,
      searchQuery: '',
      idEdit: null,
      currentPage: 1,
      rowsPerPage: 10,
    };
  },
  computed: {
    paginatedAlats() {
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
    filteredData() {
      return this.datamusnah.filter(musnah =>{
        const searchQueryLower = this.searchQuery.toLowerCase();
        const noSeriAlat = musnah.no_seri_alat && musnah.no_seri_alat.no_seri_alat;
        const namaStaff = musnah.staff_pemusnahan && musnah.staff_pemusnahan.nama_staff;
        const Layout = musnah.layout && musnah.layout.nama_layout;
        const detailRusak = musnah.deskripsi_musnah;

        return(
          (noSeriAlat && noSeriAlat.toLowerCase().includes(searchQueryLower)) ||
          (namaStaff && namaStaff.toLowerCase().includes(searchQueryLower)) ||
          (Layout && Layout.toLowerCase().includes(searchQueryLower)) ||
          (detailRusak && detailRusak.toLowerCase().includes(searchQueryLower))
        );
      })
    }
  },
  methods: {
    async fetchAlatMusnah() {
      try {
        const kodeAlat = this.kodeAlat;
        //console.log(this.kodeAlat);
        const response = await axios.get(`/api/alats/datamusnah/${kodeAlat}`);
        this.datamusnah = response.data;
        //console.log(this.datamusnah);
      } catch (error) {
        console.error("Error fetching detail alat musnah : ", error);
        //alert("Gagal memuat detail data alat musnah.");
      }
    }, 
    debouncedFetchAlats: _.debounce(function () {
        this.fetchAlatMusnah();
      }, 300),
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
    this.fetchAlatMusnah();
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
  .status-pending {
  background-color: rgba(117, 134, 148, 0.1); /* Hijau dengan transparansi */
  color: rgba(117, 134, 148); 
}
</style>