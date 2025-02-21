<template>
  <div class="container-fluid">
    <div class="row align-items-center justify-content-end mr-3 mt-4 mb-2">         
          <div class="search-wrapper">
            <div class="input-group">
              <input 
                type="text" 
                placeholder="search..." 
                class="form-control"
                v-model="searchQueryMain"
                @input="debouncedFetchAlats"/> 
            </div>
          </div>    
        </div>
    <!-- Tabel Utama -->
    <div class="table-responsive p-3">
      <table class="table table-border no-border table-custom" style="overflow-x: auto;">
        <thead>
          <tr class="bg-table text-center">
            <th class="text-center" style="width: 10px; color: #000;">#</th>
            <th class="text-center" style="width: 10px; color: #000;">Kode</th>
            <th class="text-center" style="width: 10px; color: #000;">Nama</th>
            <th class="text-center" style="width: 10px; color: #000;">Jumlah</th>
            <th class="text-center" style="width: 10px; color: #000;">Tanggal Pengambilan</th>
            <th class="text-center" style="width: 10px; color: #000;">Aksi</th>
          </tr>
        </thead>
        <tbody v-if="data.length === 0">
          <tr>
            <td colspan="6" class="text-center">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="(item, index) in paginatedMainData" :key="item.id">
          <tr class="text-center">
            <td>{{ index + 1 }}</td>
            <td>{{ item.kode }}</td>
            <td>{{ item.nama }}</td>
            <td>{{ item.jumlah }}</td>
            <td>{{ item.tanggal_pengambilan }}</td>
            <td class="text-center">
              <button class="btn btn-info btn-sm" @click="toggleDetail(index)">
                {{ isDetailVisible(index) ? 'Sembunyikan Detail' : 'Detail' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination for Main Table -->
      <div class="d-flex justify-content-between align-items-center mt-3 mb-3" style="border-radius: 10px; background-color: #f3f4f6; height: 50px; color: #000;">
        <div class="ml-3">
          Rows per page:
          <span>{{ rowsPerPage }}</span>
        </div>
        <div class="mr-3">          
          <span>{{ mainPaginationInfo }}</span>
          <button @click="prevMainPage" :disabled="currentMainPage === 1" class="btn btn-sm btn-light">
            <i class="fas fa-angle-left"></i>
          </button>
          <span>  </span>
          <button @click="nextMainPage" :disabled="currentMainPage === totalMainPages" class="btn btn-sm btn-light">
            <i class="fas fa-angle-right"></i>
          </button>
        </div>
      </div>

    </div>

    <!-- Modal Tabel Detail (sub-tabel) -->
    <div v-for="(item, index) in data" :key="'detail-' + item.id" class="table-responsive p-3" v-if="isDetailVisible(index)">
      <span style="color: #000;"><b>{{ item.nama }}</b></span>
      <!-- Modal Tabel Detail Inner (Fitur lain seperti tombol dan search) -->
      <div class="container-fluid">
        <div class="row align-items-center justify-content-end mr-3 mb-2">
          <!-- Tombol hanya muncul jika ada item yang dipilih dan status belum diperbarui -->
          <button 
            v-if="selectedItems.length > 0 && !isStatusUpdated" 
            class="btn btn-primary m-3" 
            @click="updateStatusToMenungguDiambil">
            Di Ambil
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
                <th class="text-center" style="width: 10px; color: #000;">Tgl Peminjaman</th>
                <th class="text-center" style="width: 10px; color: #000;">Status</th>
                <th class="text-center" style="width: 10px; color: #000;">Aksi</th>
              </tr>
            </thead>
            <tbody v-if="filteredData.length == 0">
              <tr>
                <td colspan="6" class="text-center">Tidak Ada Data</td>
              </tr>
            </tbody>
            <tbody v-for="(peminjaman, index) in filteredData" :key="index">
              <tr class="text-center">
                <td>
                  <input 
                    type="checkbox" 
                    :value="peminjaman.id" 
                    v-model="selectedItems"
                  />
                </td>
                <td>{{ index + 1 }}</td>
                <td>{{ peminjaman.no_seri_alat ? peminjaman.no_seri_alat.no_seri_alat : '-' }}</td>
                <td>{{ peminjaman.tanggal_pinjam || '-' }}</td>
                <td>{{ peminjaman.status || '-' }}</td>
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
                      <i class="fas fa-ellipsis-v" style="color: #000;"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">                
                      <a class="dropdown-item" @click="setStatus(peminjaman, 'Diambil')">
                        <i class="fas fa-clock text-info"></i> Diambil
                      </a>
                      <a class="dropdown-item" @click="openRejectModal(peminjaman)">
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
  </div>
</template>

<script>
export default {
  props: {
    noPinjam: String,
  },
  data() {
    return {
      data: [
        { id: 1, kode: 'A001', nama: 'Clamp', jumlah: 1, tanggal_pengambilan: '2025-02-17' },
      ],
      dataPeminjaman: [],
      searchQuery: '',
      searchQueryMain: '',
      rowsPerPage: 10,
      currentPage: 1,
      currentMainPage: 1, // Current page for the main table
      mainRowsPerPage: 10, // Rows per page for the main table
      selectedItems: [], // track selected items
      selectedMainItems: [], // track selected items dalam tabel utama
      isStatusUpdated: false, // track if the status update button has been clicked
      rejectionReason: '',
      currentRejectItem: null,
      isRejectModalVisible: false,
    }
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
            item.no_seri_alat?.no_seri_alat?.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            item.tgl_pinjam.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
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
    },
    // Pagination info for main table
    totalMainPages() {
      return Math.ceil(this.data.length / this.mainRowsPerPage);
    },
    mainPaginationInfo() {
      const start = (this.currentMainPage - 1) * this.mainRowsPerPage + 1;
      const end = Math.min(this.currentMainPage * this.mainRowsPerPage, this.data.length);
      return `Showing ${start} to ${end} of ${this.data.length} entries`;
    },
    paginatedMainData() {
      const start = (this.currentMainPage - 1) * this.mainRowsPerPage;
      const end = start + this.mainRowsPerPage;
      return this.data.slice(start, end);
    },
  },
  methods: {
    async fetchPeminjaman() {
      try {
        const noPinjam = this.noPinjam;
        const response = await axios.get(`/api/peminjaman/alats/nopin/${noPinjam}`);
        this.dataPeminjaman = response.data;
      } catch (error) {
        console.error("Error fetching data peminjaman", error);
      }
    },
    debouncedFetchAlats: _.debounce(function () {
      this.fetchPeminjaman();
    }, 300),
    // Method for toggling visibility of detail modal for selected row
    isDetailVisible(index) {
      // Cek apakah hanya satu item yang dipilih
      return this.selectedMainItems[0] === index;
    },
    toggleDetail(index) {
      if (this.selectedMainItems[0] === index) {
        // Jika detail yang sama dipilih, maka tutup detailnya
        this.selectedMainItems = [];
      } else {
        // Jika detail lain dipilih, tampilkan detail yang baru
        this.selectedMainItems = [index];
      }
    },
    toggleSelectAll() {
      if (this.isAllSelected) {
        this.selectedItems = [];
      } else {
        this.selectedItems = this.filteredData.map(item => item.id);
      }
    },
    updateStatusToMenungguDiambil() {
      const selectedPeminjaman = this.dataPeminjaman.filter(item => this.selectedItems.includes(item.id));
      selectedPeminjaman.forEach(item => {
        item.status = 'Diambil';
      });

      this.isStatusUpdated = true;
    },
    setStatus(peminjaman, status) {
      peminjaman.status = status;
      if (status === 'ditolak') {
        this.openRejectModal(peminjaman);
      }
    },
    openRejectModal(peminjaman) {
      this.currentRejectItem = peminjaman;
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
    // Pagination for Main Table
    prevMainPage() {
      if (this.currentMainPage > 1) {
        this.currentMainPage--;
      }
    },
    nextMainPage() {
      if (this.currentMainPage < this.totalMainPages) {
        this.currentMainPage++;
      }
    },
  },
  mounted() {
    this.fetchPeminjaman();
  },
}
</script>
