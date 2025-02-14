<template>
  <div class="row align-items-center justify-content-end mr-3 mt-3 mb-4">
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

    <div class="table-responsive">
      <table class="table table-border no-border table-custom text-wrape" style="overflow-x: auto;">
        <thead>
          <tr class="text-center">
            <th class="text-black-1 ">No Permintaan</th>
            <th class="text-black-1">Tgl Permintaan</th>
            <th class="text-black-1">Nama</th>
            <th class="text-black-1">Divisi</th>
            <th class="text-black-1">Tgl Kembali</th>
            <th class="text-black-1">Durasi</th>
            <th class="text-black-1">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr class="text-center">
            <td>PR-001</td>
            <td>2025-02-12</td>
            <td>Gus Tri</td>
            <td>IT</td>
            <td>2025-02-17</td>
            <td>6 Hari</td>
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
                  <i class="fa fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <a class="dropdown-item" @click="viewDetail">
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
</template>
<script>
export default {
  data() {
    return {
      searchQuery: '',
      dataPermintaanAlat: [],
      rowsPerPage: 10,
      currentPage: 1,
    }
  },
  computed: {
    totalPages() {
      return Math.ceil(this.dataPermintaanAlat.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(this.currentPage * this.rowsPerPage, this.dataPermintaanAlat.length);
      return `Showing ${start} to ${end} of ${this.dataPermintaanAlat.length} entries`;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = this.currentPage * this.rowsPerPage;
      return this.dataPermintaanAlat.slice(start, end);
    }
  },
  methods: {
    viewDetail() {
      this.$router.push(`/admin-mtc/data-alat/detail-permintaan/`);
    },
  },
  prevPage() {
    if (this.currentPage > 1) {
      this.currentPage --;
    }
  },
  nextPage() {
    if (this.currentPage < this.totalPages) {
      this.currentPage ++;
    }
  }
}
</script>