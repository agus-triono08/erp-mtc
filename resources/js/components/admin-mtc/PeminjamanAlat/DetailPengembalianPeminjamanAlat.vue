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
            <th class="text-center" style="width: 10px; color: #000;">Tanggal Pengembalian</th>
            <th class="text-center" style="width: 10px; color: #000;">Aksi</th>
          </tr>
        </thead>
        <tbody v-if="paginatedData.length === 0">
          <tr>
            <td colspan="6" class="text-center">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="(item, index) in paginatedMainData" :key="item.id">
          <tr class="text-center">
            <td>{{ index + 1 }}</td>
            <td>{{ item.tools.kode || '-' }}</td>
            <td>{{ item.tools.nama || '-' }}</td>
            <td>{{ item.total }}</td>
            <td>{{ item.tgl_kembali }}</td>
            <td>
              <button @click="toggleDetailModal(item.no_seri, item.status_kondisi)" class="btn btn-primary">
                {{ showModal ? 'Sembunyikan Detail' : 'Detail' }}
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

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="my-modal">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <h3 class="text-lg leading-6 font-medium" style="color: #000;"><b>Detail No Seri</b></h3>
          <div class="mt-2">

            <div class="row align-items-center justify-content-end mr-3 mb-2">
              <!-- Tombol hanya muncul jika ada item yang dipilih dan status belum diperbarui -->
              <button 
                v-if="selectedItems.length > 0 && !isStatusUpdated" 
                class="btn btn-primary m-3" 
                @click="updateStatusToMenungguDiambil">
                Menunggu Diambil
              </button>
            </div>

            <table class="table table-border no-border table-custom" style="overflow-x: auto;">
              <thead>
                <tr class="bg-table text-center">
                  <!-- <th class="text-center" style="width: 10px; color: #000;">
                    <input 
                      type="checkbox" 
                      @change="toggleSelectAll" 
                      :checked="isAllSelected" 
                      :indeterminate="isIndeterminate"
                    />
                  </th> -->
                  <th class="border p-2" style="color: #000;">No Seri</th>                  
                  <th class="border p-2" style="color: #000;">Kondisi</th>
                  <th class="border p-2" style="color: #000;">Tanggal Pengecekan</th>
                  <th class="border p-2" style="color: #000;">Ket. Pengecekan</th>
                  <th class="border p-2" style="color: #000;">Status</th>
                  <th class="border p-2" style="color: #000;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in selectedNoSeri" :key="index">
                  <!-- <td>
                    <input 
                      type="checkbox" 
                      :value="item.id" 
                      v-model="selectedItems"
                    />
                  </td> -->
                  <td class="border p-2">{{ item.no_seri || '-' }}</td>
                  <td class="border p-2">
                    <div
                      class="btn-sts"
                        :class="{'status-active': item.kondisi === 'OK',
                                'status-error' : item.kondisi === 'Error',
                                'status-rusak' : item.kondisi === 'Rusak',
                                'status-hilang' : item.kondisi === 'Hilang',
                                'status-dipinjam' : item.kondisi === 'Musnah',
                      }"
                    >
                      {{ item.kondisi || '-'}}
                    </div>
                  </td>
                  <td class="border p-2">{{ item.tgl_pengecekan || '-'}}</td> 
                  <td class="border p-2">{{ item.deskripsi_cek || '-'}}</td>                                    
                  <td class="border p-2">
                    <div
                      class="btn-sts"
                      :class="{
                        'status-active': item.status_kondisi === 'Selesai',
                        'status-error': item.status_kondisi === 'Menunggu Diambil',
                        'status-rusak': item.status_kondisi === 'Ditolak',
                        'status-musnah': item.status_kondisi === 'Dipinjam',
                        'status-hilang': item.status_kondisi === 'Belum Diproses'
                      }"
                    >
                      {{ item.status_kondisi || '-' }}
                    </div>
                  </td>
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
                        <a class="dropdown-item" @click="openTestModal(item.id)">
                          <i class="fas fa-check-circle text-success"></i> Cek Kondisi
                        </a>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Cek Kondisi -->
    <div v-if="isTestModalVisible" class="modal fade show" tabindex="-1" role="dialog" aria-hidden="true" style="display: block;">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalCekKondisiLabel">Cek Kondisi No Seri</h5>
            <button type="button" class="close" @click="closeTestModal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- Tanggal Pengecekan -->
            <div class="mb-3">
              <label class="form-label" style="color: #000;"><b>Tanggal Pengecekan:</b></label>
              <input type="date" v-model="form.tgl_pengecekan" class="form-control" />
            </div>

            <!-- Hasil Pengecekan -->
            <div class="mb-3">
              <label class="form-label" style="color: #000;"><b>Hasil Pengecekan:</b></label>
              <div>
                <div class="form-check form-check-inline">
                  <input type="radio" v-model="form.kondisi" value="OK" id="ok" class="form-check-input" />
                  <label class="form-check-label" for="ok" style="color: #000;"><b>OK</b></label>
                </div>
                <div class="form-check form-check-inline">
                  <input type="radio" v-model="form.kondisi" value="Error" id="error" class="form-check-input" />
                  <label class="form-check-label" for="error" style="color: #000;"><b>Error</b></label>
                </div>                
                <div class="form-check form-check-inline">
                  <input type="radio" v-model="form.kondisi" value="Rusak" id="rusak" class="form-check-input" />
                  <label class="form-check-label" for="rusak" style="color: #000;"><b>Rusak</b></label>
                </div>
                <div class="form-check form-check-inline">
                  <input type="radio" v-model="form.kondisi" value="Hilang" id="hilang" class="form-check-input" />
                  <label class="form-check-label" for="hilang" style="color: #000;"><b>Hilang</b></label>
                </div>
              </div>
            </div>

            <!-- Keterangan -->
            <div class="mb-3">
              <label for="deskripsi_cek" class="form-label" style="color: #000;"><b>Keterangan:</b></label>
              <textarea 
                v-model="form.deskripsi_cek" 
                class="form-control" 
                rows="3" 
                placeholder="Masukkan keterangan di sini..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" @click="closeTestModal">Batal</button>
            <button type="button" class="btn btn-primary" @click="submitTestResult">Kirim</button>
          </div>
        </div>
      </div>
    </div>   

  </div>
</template>

<script>
import Swal from 'sweetalert2';
export default {
  props: {
    noPinjam: String,
  },
  data() {
    return {
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
      showModal: false,
      selectedNoSeri: [],
      isTestModalVisible: false,
      form: {
        id: null,
        tgl_pengecekan: '',
        kondisi: '',
        deskripsi_cek: '',
      },
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
            item.tools.no_seri.no_seri.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            item.tgl_pinjam.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            item.status.toLowerCase().includes(this.searchQuery.toLowerCase())
          );
        });
      } else {
        return this.paginatedData;
      }
    },
    isAllSelected() {
      return this.selectedItems.length === this.selectedNoSeri.length && this.selectedNoSeri.length > 0;
    },
    isIndeterminate() {
      return this.selectedItems.length > 0 && this.selectedItems.length < this.selectedNoSeri.length;
    },
    // Pagination info for main table
    totalMainPages() {
      return Math.ceil(this.dataPeminjaman.length / this.mainRowsPerPage);
    },
    mainPaginationInfo() {
      const start = (this.currentMainPage - 1) * this.mainRowsPerPage + 1;
      const end = Math.min(this.currentMainPage * this.mainRowsPerPage, this.dataPeminjaman.length);
      return `Showing ${start} to ${end} of ${this.dataPeminjaman.length} entries`;
    },
    paginatedMainData() {
      const start = (this.currentMainPage - 1) * this.mainRowsPerPage;
      const end = start + this.mainRowsPerPage;
      return this.dataPeminjaman.slice(start, end);
    },
  },
  methods: {
    async fetchPeminjaman() {
      try {
        const noPinjam = this.noPinjam;
        // console.log(this.noPinjam);
        const response = await axios.get(`/api/v1/peminjaman/getNoPeminjaman/${noPinjam}`);
        if (Array.isArray(response.data)) {
          this.dataPeminjaman = response.data;
        } else {
          this.dataPeminjaman = [response.data];
        }
        // console.log(this.dataPeminjaman);
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
    toggleSelectAll(event) {
      if (event.target.checked) {
        this.selectedItems = this.selectedNoSeri.map(item => item.id);
      } else {
        this.selectedItems = [];
      }
    },
    updateStatusToMenungguDiambil() {
      if (this.selectedItems.length === 0) {
        Swal.fire('Peringatan', 'Tidak ada item yang dipilih.', 'warning');
        return;
      }

      const ditolakItems = this.selectedNoSeri.filter(item =>
        this.selectedItems.includes(item.id) &&
        (item.kondisi_after === 'Ditolak' || item.kondisi_after === 'Dipinjam')
      );

      if (ditolakItems.length > 0) {
        Swal.fire(
          'Tidak Bisa',
          'Beberapa item yang dipilih memiliki status "Ditolak" atau "Dipinjam" dan tidak dapat diubah.',
          'error'
        );
        return;
      }

      Swal.fire({
        title: 'Yakin?',
        text: 'Semua item terpilih akan diubah menjadi "Menunggu Diambil".',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post('/api/v1/noseri/bulk-update-status', {
            ids: this.selectedItems,
            status: 'Menunggu Diambil'
          })
          .then(response => {
            this.selectedNoSeri.forEach(item => {
              if (this.selectedItems.includes(item.id)) {
                item.kondisi_after = 'Menunggu Diambil';
              }
            });
            this.isStatusUpdated = true;
            Swal.fire('Berhasil!', 'Status semua item telah diperbarui.', 'success');
          })
          .catch(error => {
            console.error(error);
            Swal.fire('Gagal', 'Gagal memperbarui status.', 'error');
          });
        }
      });
    },      
    closeDetailModal() {
      this.showModal = false;
      this.selectedItems = [];
      this.isStatusUpdated = false;
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
    toggleDetailModal(noseriList, statusKondisi) {
      if (this.showModal) {
        this.showModal = false;
        this.selectedNoSeri = [];
      } else {
        // Tambahkan status_kondisi ke setiap no_seri
        this.selectedNoSeri = noseriList.map(item => ({
          ...item,
          status_kondisi: statusKondisi
        }));
        this.showModal = true;
      }
    },
    closeDetailModal() {
      this.showModal = false;
      this.selectedNoSeri = [];
    },
    openTestModal(noseriId) {
      // Find the selected item based on the noseriId
      const selectedItem = this.selectedNoSeri.find(item => item.id === noseriId);
      // Check the kondisi_after property
      if (selectedItem && (selectedItem.kondisi_after === 'Selesai' || selectedItem.kondisi_after === 'Ditolak')) {
        Swal.fire('Tidak Bisa Diubah', `Status No Seri ${selectedItem.no_seri} sudah ${selectedItem.kondisi_after} dan tidak dapat diubah.`, 'warning');
        return; // Exit the method if the condition is met
      }
      this.form.id = noseriId;
      this.form.tgl_pengecekan = new Date().toISOString().substr(0, 10); // default today
      this.form.kondisi = '';
      this.form.deskripsi_cek = '';
      this.isTestModalVisible = true;
    },
    closeTestModal() {
      this.isTestModalVisible = false;
    },
    submitTestResult() {      
      axios.post('/api/v1/noseri/cek-kondisi', this.form)
        .then((response) => {
          Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin mengirim hasil pengecekan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, kirim!',
            cancelButtonText: 'Tidak, batalkan!',
          });
          this.closeTestModal();
          this.$emit('refreshData'); // opsional jika ingin refresh tabel parent
        })
        .catch((error) => {
          let msg = 'Tanggal pengecekan, kondisi, dan keterangan harus diisi.';
          if (error.response && error.response.data && error.response.data.message) {
            msg = error.response.data.message;
          }

          Swal.fire({
            icon: 'error',
            title: 'Data tidak lengkap',
            text: msg,
          });
        });
    },
  },
  mounted() {
    this.fetchPeminjaman();
  },
}
</script>
<style scoped>
.modal {
  z-index: 2000 !important;
}
.modal-backdrop {
  z-index: 1990 !important;
}
</style>
