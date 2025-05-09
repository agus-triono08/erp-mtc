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
            <td>{{ item.tools.kode || '-' }}</td>
            <td>{{ item.tools.nama || '-' }}</td>
            <td>{{ item.total }}</td>
            <td>{{ item.tgl_permintaan }}</td>
            <!-- <td class="text-center">
              <button class="btn btn-info btn-sm" @click="toggleDetail(index)">
                {{ isDetailVisible(index) ? 'Sembunyikan Detail' : 'Detail' }}
              </button>
            </td> -->
            <td>
              <button @click="toggleDetailModal(item.no_seri)" class="btn btn-primary">
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

    <!-- Modal Tabel Detail (sub-tabel) -->
    <div v-for="(item, index) in dataPeminjaman" :key="'detail-' + item.id" class="table-responsive p-3" v-if="isDetailVisible(index)">
      <span style="color: #000;"><b>{{ item.tools.nama }}</b></span>

      <!-- Modal Tabel Detail Inner (Fitur lain seperti tombol dan search) -->
      <div class="container-fluid">
        <div class="row align-items-center justify-content-end mr-3 mb-2">
          <!-- Tombol hanya muncul jika ada item yang dipilih dan status belum diperbarui -->
          <button 
            v-if="selectedItems.length > 0 && !isStatusUpdated" 
            class="btn btn-primary m-3" 
            @click="updateStatus('Menunggu Diambil')">
            Menunggu Diambil
          </button>

          <button 
            v-if="selectedItems.length > 0 && !isStatusUpdated" 
            class="btn btn-danger m-3" 
            @click="updateStatus('Ditolak')">
            Tolak Permintaan
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
                <th class="text-center" style="width: 10px; color: #000;">Kondisi</th>
                <th class="text-center" style="width: 10px; color: #000;">Status</th>
                <th class="text-center" style="width: 10px; color: #000;">Aksi</th>
              </tr>
            </thead>
            <tbody v-if="filteredData.length == 0">
              <tr>
                <td colspan="7" class="text-center">Tidak Ada Data</td>
              </tr>
            </tbody>
            <!-- <tbody v-for="(peminjaman, index) in filteredData" :key="index"> -->
              <tbody>
              <tr class="text-center">
                <td>
                  <input 
                    type="checkbox" 
                    :value="item.id" 
                    v-model="selectedItems"
                  />
                </td>
                <td>{{ index + 1 }}</td>
                <td>{{ item.tools.no_seri.no_seri || '-' }}</td>
                <td>{{ item.tgl_pinjam || '-' }}</td>
                <td>{{ item.tools.no_seri.kondisi || '-' }}</td>
                <td>{{ item.status }}</td>
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
                      <a class="dropdown-item" @click="setStatus(item, 'Menunggu Diambil')">
                        <i class="fas fa-clock text-info"></i> Menunggu Diambil
                      </a>
                      <a class="dropdown-item" @click="openRejectModal(item)">
                        <i class="fas fa-times text-danger"></i> Ditolak
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
              <!-- <button 
                v-if="selectedItems.length > 0 && !isStatusUpdated" 
                class="btn btn-primary m-3" 
                @click="updateStatus('Menunggu Diambil')">
                Menunggu Diambil
              </button>

              <button 
                v-if="selectedItems.length > 0 && !isStatusUpdated" 
                class="btn btn-danger m-3" 
                @click="updateStatus('Ditolak')">
                Tolak Permintaan
              </button> -->

              <!-- <div class="search-wrapper">
                <div class="input-group">
                  <input 
                    type="text" 
                    placeholder="search..." 
                    class="form-control"
                    v-model="searchQuery"
                    @input="debouncedFetchAlats"/> 
                </div>
              </div>     -->
            </div>

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
                  <th class="border p-2" style="color: #000;">No Seri</th>                  
                  <th class="border p-2" style="color: #000;">Kondisi</th>
                  <th class="border p-2" style="color: #000;">Tanggal Permintaan</th>
                  <th class="border p-2" style="color: #000;">Status</th>
                  <th class="border p-2" style="color: #000;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in selectedNoSeri" :key="index">
                  <td>
                    <input 
                      type="checkbox" 
                      :value="item.id" 
                      v-model="selectedItems"
                    />
                  </td>
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
                  <td class="border p-2">{{ item.tanggal_kondisi || '-'}}</td>                  
                  <td class="border p-2">
                    <div
                      class="btn-sts"
                        :class="{'status-active': item.kondisi_after === 'Selesai',
                                'status-error' : item.kondisi_after === 'Menunggu Diambil',
                                'status-rusak' : item.kondisi_after === 'Ditolak',
                                'status-musnah': item.kondisi_after === 'Digunakan',
                                'status-hilang': item.kondisi_after === 'Belum Diproses',
                      }"
                    >
                      {{ item.kondisi_after || '-'}}
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
                        <a class="dropdown-item" @click="setStatus(item, 'Menunggu Diambil')">
                          <i class="fas fa-clock text-info"></i> Menunggu Diambil
                        </a>
                        <a class="dropdown-item" @click="openRejectModal(item)">
                          <i class="fas fa-times text-danger"></i> Ditolak
                        </a>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- <div class="items-center px-4 py-3">
            <button @click="closeDetailModal" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600">
              Close
            </button>
          </div> -->
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import Swal from 'sweetalert2';
export default {
  props: {
    noPermintaan: String,
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
      showModal: false,
      selectedNoSeri: [],
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
    // isAllSelected() {
    //   return this.selectedItems.length === this.filteredData.length;
    // },
    isAllSelected() {
      return this.selectedItems.length === this.selectedNoSeri.length && this.selectedNoSeri.length > 0;
    },
    // isIndeterminate() {
    //   return this.selectedItems.length > 0 && this.selectedItems.length < this.filteredData.length;
    // },
    isIndeterminate() {
      return this.selectedItems.length > 0 && this.selectedItems.length < this.selectedNoSeri.length;
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
      return this.dataPeminjaman.slice(start, end);
    },
  },
  methods: {
    async fetchPeminjaman() {
      try {
        const noPermintaan = this.noPermintaan;
        // console.log(this.noPermintaan);
        const response = await axios.get(`/api/v1/permintaan/getNoPermintaan/${noPermintaan}`);
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
    // toggleSelectAll() {
    //   if (this.isAllSelected) {
    //     this.selectedItems = [];
    //   } else {
    //     this.selectedItems = this.filteredData.map(item => item.id);
    //   }
    // },
    toggleSelectAll(event) {
      if (event.target.checked) {
        this.selectedItems = this.selectedNoSeri.map(item => item.id);
      } else {
        this.selectedItems = [];
      }
    },
    // updateStatusToMenungguDiambil() {
    //   const selectedPeminjaman = this.dataPeminjaman.filter(item => this.selectedItems.includes(item.id));
    //   selectedPeminjaman.forEach(item => {
    //     item.status = 'Menunggu Diambil';
    //   });

    //   this.isStatusUpdated = true;
    // },
    // updateStatusToMenungguDiambil() {
    //   Swal.fire({
    //     title: 'Konfirmasi',
    //     text: 'Apakah Anda yakin ingin mengupdate status menjadi "Menunggu Diambil"?',
    //     icon: 'warning',
    //     showCancelButton: true,
    //     confirmButtonText: 'Ya, update!',
    //     cancelButtonText: 'Tidak, batalkan!',
    //   }).then((result) => {
    //     if (result.isConfirmed) {
    //       const selectedPeminjaman = this.dataPeminjaman.filter(item => this.selectedItems.includes(item.id));
    //       selectedPeminjaman.forEach(item => {
    //         item.status = 'Menunggu Diambil';
    //       });
    //       this.isStatusUpdated = true;
    //       Swal.fire('Berhasil!', 'Status telah diupdate.', 'success');
    //     }
    //   });
    // },
    updateStatusToMenungguDiambil() {
      if (this.selectedItems.length === 0) {
        Swal.fire('Peringatan', 'Tidak ada item yang dipilih.', 'warning');
        return;
      }

      const ditolakItems = this.selectedNoSeri.filter(item =>
        this.selectedItems.includes(item.id) &&
        (item.kondisi_after === 'Ditolak' || item.kondisi_after === 'Digunakan')
      );

      if (ditolakItems.length > 0) {
        Swal.fire(
          'Tidak Bisa',
          'Beberapa item yang dipilih memiliki status "Ditolak" atau "Digunakan" dan tidak dapat diubah.',
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
          axios.post('/api/v1/noseri/bulk-update-status-permintaan', {
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
    // setStatus(peminjaman, status) {
    //   peminjaman.status = status;
    //   if (status === 'ditolak') {
    //     this.openRejectModal(peminjaman);
    //   }
    // },
    // setStatus(peminjaman, status) {
    //   Swal.fire({
    //     title: 'Konfirmasi',
    //     text: `Apakah Anda yakin ingin mengupdate status menjadi "${status}"?`,
    //     icon: 'warning',
    //     showCancelButton: true,
    //     confirmButtonText: 'Ya, update!',
    //     cancelButtonText: 'Tidak, batalkan!',
    //   }).then((result) => {
    //     if (result.isConfirmed) {
    //       peminjaman.status = status;
    //       if (status === 'ditolak') {
    //         this.openRejectModal(peminjaman);
    //       }
    //     }
    //   });
    // },
    setStatus(item, status, reason = '') {
      // Cek jika status saat ini sudah "Ditolak" atau "Digunakan"
      if (['Ditolak', 'Digunakan'].includes(item.kondisi_after)) {
        Swal.fire('Tidak Bisa Diubah', `Status No Seri ${item.no_seri} sudah ${item.kondisi_after} dan tidak dapat diubah.`, 'warning');
        return;
      }

      // Cek jika ingin mengubah ke status "Ditolak" tanpa alasan
      if (status === 'Ditolak' && !reason) {
        Swal.fire('Alasan Diperlukan', 'Silakan isi alasan penolakan sebelum menolak No Seri.', 'warning');
        return;
      }

      axios.post('/api/v1/noseri/update-status-permintaan', {
        id: item.id,
        status: status,
        reason: reason
      })
      .then(response => {
        item.kondisi_after = status;
        if (reason) {
          this.$set(item, 'reject_reason', reason);
        }
        this.isStatusUpdated = true;
        Swal.fire('Berhasil!', `Status No Seri ${item.no_seri} telah diubah menjadi ${status}.`, 'success');
      })
      .catch(error => {
        console.error(error);
        Swal.fire('Gagal', 'Gagal memperbarui status No Seri.', 'error');
      });
    },
    // openRejectModal(peminjaman) {
    //   this.currentRejectItem = peminjaman;
    //   this.rejectionReason = ''; // Clear previous reason
    //   this.isRejectModalVisible = true; // Show modal
    // },
    openRejectModal(item) {
      if (['Menunggu Diambil', 'Digunakan'].includes(item.kondisi_after)) {
        Swal.fire({
          icon: 'warning',
          title: 'Tidak Bisa Ditolak',
          text: `No Seri ${item.no_seri} sedang dalam status ${item.kondisi_after} dan tidak dapat ditolak.`,
        });
        return; // hentikan proses
      }

      Swal.fire({
        title: 'Tolak No Seri',
        input: 'text',
        inputLabel: `Masukkan alasan penolakan untuk No Seri ${item.no_seri}:`,
        inputPlaceholder: 'Alasan penolakan...',
        inputAttributes: {
          'aria-label': 'Alasan penolakan'
        },
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Tolak',
        cancelButtonText: 'Batal',
        preConfirm: (reason) => {
          if (!reason) {
            Swal.showValidationMessage('Alasan harus diisi!');
          }
          return reason;
        }
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post('/api/v1/noseri/reject-permintaan', {
            id: item.id,
            status: 'Ditolak', // tambahkan status
            reason: result.value
          })
          .then(response => {
            this.setStatus(item, 'Ditolak', result.value);
            Swal.fire('Berhasil!', `No Seri ${item.no_seri} telah ditolak.`, 'success');
          })
          .catch(error => {
            console.error(error);
            Swal.fire('Gagal', 'Gagal menolak No Seri.', 'error');
          });
        }
      });
    },
    // closeRejectModal() {
    //   this.isRejectModalVisible = false; // Hide modal
    // },
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
    toggleDetailModal(noseriList) {
      if (this.showModal) {
        this.showModal = false;  // Menutup modal jika sudah terbuka
        this.selectedNoSeri = [];
      } else {
        this.selectedNoSeri = noseriList;  // Menyimpan data no_seri
        this.showModal = true;  // Membuka modal
      }
    },
    closeDetailModal() {
      this.showModal = false;
      this.selectedNoSeri = [];
    },
  },
  mounted() {
    this.fetchPeminjaman();
  },
}
</script>
