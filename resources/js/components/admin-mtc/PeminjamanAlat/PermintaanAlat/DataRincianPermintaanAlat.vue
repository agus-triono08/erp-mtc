<template>
  <div class="container-fluid">
    <div class="row align-items-center justify-content-end mr-3 mt-4 mb-2">
      <!-- Tombol hanya muncul jika ada item yang dipilih dan status belum diperbarui -->
      <button 
        v-if="selectedItems.length > 0 && !isStatusUpdated" 
        class="btn btn-primary mr-3" 
        @click="updateStatusToMenungguDiambil">
        Siap Di Ambil
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

    <div class="table-responsive p-3">
      <table class="table table-border no-border table-custom" style="overflow-x: auto;">
        <thead>
          <tr class="bg-table text-center">
            <th class="text-center" style="width: 10px; color: #000;">
              <input 
                type="checkbox" 
                @change="toggleSelectAll" 
                :checked="isAllSelected" 
                :indeterminate="isIndeterminate"
              />
            </th>
            <th class="text-center" style="width: 10px; color: #000;">#</th>
            <th class="text-center" style="width: 10px; color: #000;">No Seri Alat</th>
            <th class="text-center" style="width: 10px; color: #000;">Tgl Permintaan</th>
            <th class="text-center" style="width: 10px; color: #000;">Status</th>
            <th class="text-center" style="width: 10px; color: #000;">Aksi</th>
          </tr>
        </thead>
        <tbody v-if="filteredData.length==0">
          <tr>
            <td colspan="6" class="text-center">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="(permintaan, index) in filteredData" :key="index">
          <tr class="text-center">
            <td>
              <input 
                type="checkbox" 
                :value="permintaan.id" 
                v-model="selectedItems"
              />
            </td>
            <td>{{ index + 1 }}</td>
            <td>{{ permintaan.no_seri_alat ? permintaan.no_seri_alat.no_seri_alat : '-' }}</td>
            <td>{{ permintaan.tanggal_permintaan || '-' }}</td>
            <td>{{ permintaan.status || '-' }}</td>
            <td class="text-center">
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
                  <a class="dropdown-item" @click="setStatus(permintaan, 'menunggu diambil')">
                    <i class="fas fa-clock text-info"></i> Menunggu Diambil
                  </a>
                  <a class="dropdown-item" @click="openRejectModal(permintaan)">
                    <i class="fas fa-times text-danger"></i> Ditolak
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

    <!-- Modal Penolakan -->
    <div v-if="isRejectModalVisible" class="modal fade show" tabindex="-1" style="display: block;" id="rejectModal" aria-labelledby="rejectModalLabel" aria-hidden="true">
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
    </div>
  </div>
</template>

<script>
export default {
  props: {
    noPermintaan: String,
  },
  data() {
    return {
      dataPermintaan: [],
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
      selectedItems: [], // track selected items
      isStatusUpdated: false, // track if the status update button has been clicked
      rejectionReason: '',
      currentRejectItem: null,
      isRejectModalVisible: false,
    }
  },
  computed: {
    totalPages() {
      return Math.ceil(this.dataPermintaan.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(this.currentPage * this.rowsPerPage, this.dataPermintaan.length);
      return `Showing ${start} to ${end} of ${this.dataPermintaan.length} entries`;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = start + this.rowsPerPage;
      return this.dataPermintaan.slice(start, end);
    },
    filteredData() {
      if (this.searchQuery) {
        return this.paginatedData.filter(item => {
          return (
            item.no_seri_alat?.no_seri_alat?.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            item.tgl_permintaan.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            item.status.toLowerCase().includes(this.searchQuery.toLowerCase())
          );
        });
      } else {
        return this.paginatedData;
      }
    },
    isAllSelected() {
      return this.selectedItems.length === this.filteredData.length;
    },
    isIndeterminate() {
      return this.selectedItems.length > 0 && this.selectedItems.length < this.filteredData.length;
    }
  },
  methods: {
    async fetchPermintaan() {
      try {
        const noPermintaan = this.noPermintaan;
        const response = await axios.get(`/api/permintaan/rincian/noper/${noPermintaan}`);
        this.dataPermintaan = response.data;
      } catch (error) {
        console.error("Error fetching data peminjaman", error);
      }
    },
    debouncedFetchAlats: _.debounce(function () {
      this.fetchPermintaan();
    }, 300),
    toggleSelectAll() {
      if (this.isAllSelected) {
        this.selectedItems = [];
      } else {
        this.selectedItems = this.filteredData.map(item => item.id);
      }
    },
    updateStatusToMenungguDiambil() {
      const selectedPermintaan = this.dataPermintaan.filter(item => this.selectedItems.includes(item.id));
      selectedPermintaan.forEach(item => {
        item.status = 'Menunggu Diambil';
      });

      this.isStatusUpdated = true;
    },
    setStatus(permintaan, status) {
      permintaan.status = status;
      if (status === 'ditolak') {
        this.openRejectModal(permintaan);
      }
    },
    openRejectModal(permintaan) {
      this.currentRejectItem = permintaan;
      this.rejectionReason = ''; // Clear previous reason
      this.isRejectModalVisible = true; // Show modal
    },
    closeRejectModal() {
      this.isRejectModalVisible = false; // Hide modal
    },
    submitRejection() {
      if (!this.rejectionReason.trim()) {
        alert('Alasan penolakan tidak boleh kosong');
        return;
      }
      this.currentRejectItem.status = 'Ditolak';
      this.currentRejectItem.keteranganPenolakan = this.rejectionReason;
      this.closeRejectModal(); // Hide modal
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
    this.fetchPermintaan();
  },
}
</script>
