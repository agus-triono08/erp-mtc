<template>
  <div class="container-fluid">

    <div id="app" class="modal-input" :class="{'is-visible': showModalEdit}" @click.self="tutupModal">
      <div class="modal-content-input">
        <edit-rincian-perawatan-alat @tutup-modal="tutupModal" :id="idEdit"></edit-rincian-perawatan-alat>
      </div>
    </div>

    <div class="row align-items-center justify-content-end mr-3 mt-4 mb-2">
      <button class="btn btn-sm btn-outline-primary mr-2">
        <i class="fas fa-print"></i>
      </button>
      <div class="search-wrapper">
        <div class="input-group">
          <input type="text" placeholder="search..." 
            class="form-control"
            v-model="searchQuery"
            @input="debouncedFetch"/>
        </div>
      </div>
    </div>

    <div class="table-responsive p-3">
      <table class="table table-border no-border table-custom" style="overflow-x: auto;">
        <thead>
          <tr class="bg-table text-center">
            <th class="text-center" style="width: 10px; color: #000;">#</th>
            <th class="text-center" style="width: 10px; color: #000;">No Seri Alat</th>
            <th class="text-center" style="width: 10px; color: #000;">Detail Perawatan</th>
            <th class="text-center" style="width: 10px; color: #000;">Kondisi</th>
            <th class="text-center" style="width: 10px; color: #000;">Status</th>
            <th class="text-center" style="width: 10px; color: #000;">Aksi</th>
          </tr>
        </thead>
        <tbody v-if="filteredData.length===0">
          <tr>
            <td colspan="5" class="text-center">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="(perawatan, index) in filteredData" :key="index">
          <tr class="text-center">
            <td>{{ index + 1 }}</td>
            <td>{{ perawatan.noseri ? perawatan.noseri.no_seri_alat : '-' }}</td>
            <td>{{ perawatan.detail_perawatan || '-' }}</td>
            <td>
              <div
                class="btn-sts"
                :class="{
                  'status-active': perawatan.noseri.status === 'Ready',
                  'status-error' : perawatan.noseri.status === 'Error',
                  'status-rusak' : perawatan.noseri.status === 'Rusak',                
                }"
              >
                {{ perawatan.noseri ? perawatan.noseri.status : '-' }}
              </div>
            </td>
            <td> 
              <div 
                class="btn-sts"              
                :class="{
                  'status-active' : perawatan.status == 'Sudah',
                  'status-rusak' : perawatan.status == 'Belum',
                }"
              >
                {{ perawatan.status || '-' }}
              </div>
            </td>
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
                  <a class="dropdown-item" @click="editData(perawatan.id)">
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
import axios from "axios";

export default {
  props: {
    noRawat: String,
  },
  data() {
    return {
      searchQuery: '',
      dataPerawatan: [],
      rowsPerPage: 10,
      idEdit: null,
      showModalEdit: false,
      currentPage: 1,
    }
  }, 
  computed: {
    totalPages() {
      return Math.ceil(this.dataPerawatan.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(this.currentPage * this.rowsPerPage, this.dataPerawatan.length);
      return `Showing ${start} - ${end} of ${this.dataPerawatan.length} entries`;
    },
    paginateddata() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = this.currentPage * this.rowsPerPage;
      return this.dataPerawatan.slice(start, end);
    },
    filteredData() {
      if (this.searchQuery) {
        return this.paginateddata.filter(perawatan => {
          return (
            perawatan.no_rawat.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            (perawatan.noseri && perawatan.noseri.no_seri_alat.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
            perawatan.detail_perawatan.toLowerCase().includes(this.searchQuery.toLowerCase())
          );
        });
      } else {
        return this.paginateddata;
      }
    }
  },
  methods: {
    async fetchPerawatan() {
      try {
        const noRawat = this.noRawat;
        //console.log(this.noRawat);
        const response = await axios.get(`/api/perawatan/alat/norawat/${noRawat}`);
        this.dataPerawatan = response.data;
        //console.log(this.DataPerawatanAlat);
      } catch (error) {
        console.error(error);
      }
    },
    debouncedFetch: _.debounce(function () {
      this.fetchPerawatan();
    }, 300),
    editData(id) {
      this.idEdit = id;
      this.showModalEdit = true;
    },
    tutupModal() {
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
    },
  },
  mounted() {
    this.fetchPerawatan();
  }
}
</script>