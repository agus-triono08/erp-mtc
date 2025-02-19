<template>
  <div class="container-fluid">
    <div class="row align-items-center justify-content-end mr-3 mt-4 mb-2">      
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

    <div class="table-responsive p-3">
      <table class="table table-border no-border table-custom" style="overflow-x: auto;">
        <thead>
          <tr class="bg-table text-center">          
            <th class="text-center" style="width: 10px; color: #000;">#</th>
            <th class="text-center" style="width: 10px; color: #000;">No Pengajuan</th>
            <th class="text-center" style="width: 10px; color: #000;">Tgl Peminjaman</th>
            <th class="text-center" style="width: 10px; color: #000;">Status Sebelumnya</th>
            <th class="text-center" style="width: 10px; color: #000;">Alasan Ditolak</th>            
          </tr>
        </thead>
        <tbody v-if="filteredData.length==0">
          <tr>
            <td colspan="6" class="text-center">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="(peminjaman, index) in filteredData" :key="index">
          <tr class="text-center">
            <td>{{ index + 1 }}</td>
            <td>{{ peminjaman.no_pengajuan || '-' }}</td>
            <td>{{ peminjaman.tanggal_peminjaman || '-' }}</td>
            <td>{{ peminjaman.status || '-' }}</td>
            <td>{{ peminjaman.deskriksi }}</td>
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

    <!-- Modal Penolakan -->
    <!-- <div v-if="isRejectModalVisible" class="modal fade show" tabindex="-1" style="display: block;" id="rejectModal" aria-labelledby="rejectModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="rejectModalLabel">Masukkan Alasan Penolakan</h5>
            <button type="button" class="close" @click="closeRejectModal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <textarea 
              v-model="rejectionReason" 
              class="form-control" 
              rows="3" 
              placeholder="Masukkan alasan penolakan di sini..."></textarea>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeRejectModal">Batal</button>
            <button type="button" class="btn btn-danger" @click="submitRejection">Kirim</button>
          </div>
        </div>
      </div>
    </div> -->
  </div>
</template>

<script>
export default {
  props: {
    noPinjam: String,
  },
  data() {
  return {
    dataPeminjaman: [
    {
        no_pengajuan: "A001",
        tanggal_peminjaman: "2025-02-01",
        status: "Ditolak",
        deskriksi: "Alat dalam perbaikan"
      },
    ],
    searchQuery: '',
    rowsPerPage: 10,
    currentPage: 1,
  };
},
computed: {
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
      return this.paginatedData.filter(item => {
        return (
          item.no_pengajuan.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          item.tanggal_peminjaman.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          item.status.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      });
    } else {
      return this.paginatedData;
    }
  },
},
methods: {
  async fetchPeminjaman() {
    try {
      const noPinjam = this.noPinjam;
      const response = await axios.get(`/api/Peminjaman/rincian/${noPinjam}`);
      this.dataPeminjaman = response.data;
    } catch (error) {
      console.error("Error fetching data peminjaman", error);
    }
  },
  debouncedFetchAlats: _.debounce(function () {
    this.fetchPeminjaman();
  }, 300),
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
  this.fetchPeminjaman();
},
}
</script>
