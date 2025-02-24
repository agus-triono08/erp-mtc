<template>
  <div class="container-fluid" style="margin-top: 30px;">
    <!-- Modal Input Data -->
    <div id="app" class="modal-input" :class="{'is-visible': showModalInput}" @click.self="tutupModal">
      <div class="modal-content-input">
        <input-alat-hilang @tutup-modal="tutupModal"></input-alat-hilang>
      </div>
    </div>

    <!-- Modal Edit Data -->
    <div id="app" class="modal-input" :class="{'is-visible' :showModalEdit}">
      <div class="modal-content-input">
        <edit-alat-hilang @tutup-modal="tutupModal" :id="idEdit"></edit-alat-hilang>
      </div>      
    </div>

    <div class="row align-items-center justify-content-end mr-3 mt-3 mb-4">            
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
            <th colspan="6" class="text-center text-black-1" >Mesin Hilang</th>
            <th colspan="3" class="text-center text-black-1" >Penganti Mesin</th>
          </tr>
          <tr class="bg-table">
            <th class="text-center text-black-1 tr-center">#</th>
            <th class="text-center text-black-1 tr-center">No. Seri Alat</th>
            <!--<th class="text-center text-black-1">Stok Hilang</th>-->
            <th class="text-center text-black-1">Peminjam yang Menghilangkan</th>
            <th class="text-center text-black-1">Divisi yang Menghilangkan</th>
            <th class="text-center text-black-1">Tanggal Alat Hilang</th>
            <th class="text-center text-black-1">Detail Kehilangan Alat</th>
            <th class="text-center text-black-1">Tanggal Pengantian Alat yang Hilang</th>            
            <th class="text-center text-black-1">Detail Pengantian Alat</th>
            <th class="text-center text-black-1">Aksi</th>
          </tr>
        </thead>
        <tbody v-if="filteredData.length===0">
          <tr>
            <td colspan="10" class="text-center">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="(hilang, index) in filteredData" :key="index">
          <tr>
            <td class="text-center">{{ index + 1 }}</td>
            <td class="text-center">{{ hilang.no_seri_alat ? hilang.no_seri_alat.no_seri_alat : '-' }}</td>
            <!--<td class="text-center">{{ hilang.stok_hilang || '-' }}</td>-->
            <td class="text-center">{{ hilang.user_penghilang ? hilang.user_penghilang.nama_pengguna : '-' }}</td>
            <td class="text-center">{{ hilang.divisi_penghilang ? hilang.divisi_penghilang.divisi : '-' }}</td>
            <td class="text-center">{{ hilang.tanggal_hilang || '-' }}</td>
            <td class="text-center">{{ hilang.detail_hilang || '-' }}</td>
            <td class="text-center">{{ hilang.tanggal_ganti || '-' }}</td>            
            <td class="text-center">{{ hilang.detail_ganti || '-' }}</td>
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
                  <a class="dropdown-item" @click="editData(hilang.id)">
                    <i class="fas fa-edit text-primary"></i> Edit
                  </a>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>      
    </div>
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
</template>
<script>
import axios from 'axios';
  
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
      datahilang: [
        {
          id: 3,
          no_seri_alat: { no_seri_alat: '112233445' },
          user_penghilang: { nama_pengguna: 'Michael Johnson' },
          divisi_penghilang: { divisi: 'HR' },
          tanggal_hilang: '2025-02-10',
          detail_hilang: 'Lost while traveling.',
          tanggal_ganti: '2025-02-12',
          detail_ganti: 'Replaced with slightly older model.',
        },
      ],
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
      return Math.ceil(this.datahilang.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(this.currentPage * this.rowsPerPage, this.datahilang.length);
      return `Showing ${start} to ${end} of ${this.datahilang.length} entries`;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = start + this.rowsPerPage;
      return this.datahilang.slice(start, end);
    },
    filteredData() {
      return this.datahilang.filter(musnah => {
        const searchQueryLower = this.searchQuery.toLowerCase();
        const noSeriAlat = musnah.no_seri_alat && musnah.no_seri_alat.no_seri_alat;
        const namaPengguna = musnah.user_penghilang && musnah.user_penghilang.nama_pengguna;
        const divisi = musnah.divisi_penghilang && musnah.divisi_penghilang.divisi;
        const Layout = musnah.layout && musnah.layout.nama_layout;
        const detailHilang = musnah.detail_hilang;

        return(
          (noSeriAlat && noSeriAlat.toLowerCase().includes(searchQueryLower)) ||
          (namaPengguna && namaPengguna.toLowerCase().includes(searchQueryLower)) ||
          (divisi && divisi.toLowerCase().includes(searchQueryLower)) ||
          (Layout && Layout.toLowerCase().includes(searchQueryLower)) ||
          (detailHilang && detailHilang.toLowerCase().includes(searchQueryLower))
        );
      });
    },
  },
  methods: {
    async fetchAlatHilang() {
      try{
        const kodeAlat = this.kodeAlat;
        //console.log(this.kodeAlat);
        const response = await axios.get(`/api/alats/datahilang/${kodeAlat}`);
        this.datahilang = response.data;
      } catch(error) {
        console.error("Error fetching alat hilang detail:", error);
        //alert("Gagal memuat detail data alat hilang.");
      }
    },
    debouncedFetchAlats: _.debounce(function () {
        this.fetchAlatHilang();
      }, 300),
    tambahDataHilang() {
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
    this.fetchAlatHilang();
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