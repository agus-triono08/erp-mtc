<template>
  <div class="container-fluid" style="margin-top: 30px;">
    <div class="row align-items-center justify-content-end mr-3 mt-3 mb-4">
      <button class="btn btn-sm btn-outline-primary mr-2 ml-1" @click="tambahData">
        <i class="fa fa-plus-circle"></i> Tambah Data
      </button>
      <div class="search-wrapper">
        <div class="input-group">
          <input 
            type="text" 
            placeholder="search..." 
            class="form-control"
            v-model="searchQuery"
            @input="debouncedFetchAlats"/>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-border no-border table-custom text-wrape" style="overflow-x: auto;">
        <thead>
          <tr class="bg-table">
            <th class="text-center text-black-1 tr-center">#</th>
            <th class="text-center text-black-1">No Seri Alat</th>
            <th class="text-center text-black-1">PIC</th>                       
            <th class="text-center text-black-1">Tgl Perawatan</th>                        
            <th class="text-center text-black-1">Status</th>
            <th class="text-center text-black-1">Action</th>
          </tr>
        </thead>
        <tbody v-if="filteredData.length===0">
          <tr>
            <td colspan="8" class="text-center text-black-1">Tidak Ada Data</td>
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
      dataPerawatan: [],
      showModalInput: false,
      showModalEdit: false,
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
    }
  },
  computed: {
    filteredData() {
      return this.dataPerawatan.filter(item => 
      item.no_seri_alat.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
      item.pic.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
      item.tgl_perawatan.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
      item.status.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
    totalPages() {
      return Math.ceil(this.filteredData.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(this.currentPage * this.rowsPerPage, this.filteredData.length);
      return `Showing ${start} to ${end} of ${this.filteredData.length} entries`;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = start + this.rowsPerPage;
      return this.filteredData.slice(start, end);
    },
    filteredGroupedData() {
      return this.paginatedData.reduce((groups, perawatan) => {
        const kodeMesin = perawatan.mesin?.kode_mesin || "UnCode";
        if(!groups[kodeMesin]) {
          groups[kodeMesin] = [];
        }
        groups[kodeMesin].push(perawatan);
        return groups;
      })
    }
  },
  methods: {
    async fetchPerawatan(){
      try {
        if (this.dataPerawatan.length > 0) {
          return;
        }
        const response = await this.$axios.get('api/perawatan', {
          params: {
            search: this.searchQuery,
          }
        });
        this.dataPerawatan = response.data.data;
        console.log(this.dataPerawatan)
      } catch (error) {}
    },
    debouncedFetchAlats: _.debounce(function () {
      this.fetchPerawatan();
    }, 300),
    tambahData() {
      this.showModalInput = true;
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