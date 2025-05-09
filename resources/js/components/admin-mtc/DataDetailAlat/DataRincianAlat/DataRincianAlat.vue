<template>
  <div class="container-fluid">
    <!-- Modal Input Data -->
    <div id="app" class="modal-input" :class="{'is-visible': showModalInput}" @click.self="tutupModal">
      <div class="modal-content-input">
        <input-alat-belum-digunakan :kode-alat="kodeAlat" @tutup-modal="tutupModal"></input-alat-belum-digunakan>
      </div>
    </div>

    <!-- Modal Edit Data -->
    <!-- <div id="app" class="modal-input" :class="{'is-visible' :showModalEdit}" @click.self="tutupModal">
      <div class="modal-content-input">
        <edit-alat-belum-digunakan @tutup-modal="tutupModal" :id="idEdit"></edit-alat-belum-digunakan>
      </div>      
    </div> -->

    <div class="col-md-12">
      <button 
        class="btn btn-show mb-3 mr-1"
        :class="{active: showBelumDigunakan}"
        @click="toggleBelumDigunakan"
      >
        <span v-if="showBelumDigunakan">Alat/Mesin Ready</span>
        <span v-else>Alat/Mesin Ready</span>
      </button>
      <button 
        class="btn btn-show mb-3 ml-1 mr-1"
        :class="{active: showSudahDigunakan}"
        @click="toggleSudahDigunakan"
      >
        <span v-if="showSudahDigunakan">Permintaan</span>
        <span v-else>Permintaan</span>
      </button>
      <button 
        class="btn btn-show mb-3 ml-1 mr-1"
        :class="{active: showPeminjaman}"
        @click="togglePeminjaman"
      >
        <span v-if="showPeminjaman">Peminjaman</span>
        <span v-else>Peminjaman</span>
      </button>
    </div>

    <div v-if="showBelumDigunakan">
      <div class="row align-items-center justify-content-end mr-3 mt-3 mb-4">
        <!-- Tombol Download Excel di samping kiri filter -->
        <button @click="exportSelected" class="btn btn-sm btn-outline-primary mr-1">
          <i class="fas fa-download"></i> Export Barcode
        </button>
        <!-- <button class="btn btn-sm btn-outline-primary mr-2 ml-1" @click="exportAllBarcode">
          <i class="fas fa-download text-success"></i> Download Semua Barcode
        </button> -->
        <div>
          <button @click="downloadExcel" class="btn btn-sm btn-outline-primary mr-1">
            <i class="fas fa-file-excel"></i> Export
          </button>
        </div>
        <!-- Filter Data -->
        <button 
          class="btn btn-sm btn-outline-primary mr-1 ml-1"
          type="button"
          id="filterDropdown"
          data-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false"
        >
          <i class="fas fa-filter"></i> Filter
        </button>
          <!-- Isi Filter -->
          <div 
            class="dropdown-menu p-3"
            aria-labelledby="filterDropdown"
            style="border-radius: 8px; width: 250px;"
            @click.stop
          >
            <!-- Kondisi -->
            <div>
              <label><strong>Kondisi</strong></label>
              <div v-for="kondisi in availableKondisi" :key="kondisi">
                <label><input type="checkbox" :value="kondisi" v-model="kondisiFilters"/> {{ kondisi }}</label>
              </div>
            </div>
          </div>
        <!-- Tambah Data -->
        <button class="btn btn-sm btn-outline-primary mr-2 ml-1" @click="tambahData">
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
        <table class="table table-border no-border table-custom text-center" style="overflow-x: auto;">
          <thead>
            <tr class="bg-table">
              <th class="text-center text-black-1 tr-center">
                <input type="checkbox" @change="updateSelectAll" @click="selectAll" v-model="selectAllCheckbox">
              </th>
              <th class="text-center text-black-1 tr-center">#</th>                    
              <th class="text-center text-black-1" style="cursor: pointer; position: relative; vertical-align: middle;">
                No. Seri Alat
                <span class="sort-icons">
                  <i @click="sortNoSeri('desc')" class="fas fa-sort-up"></i>
                  <i @click="sortNoSeri('asc')" class="fas fa-sort-down"></i>
                </span>
              </th>
              <th class="text-center text-black-1">No Seri Default</th>
              <th class="text-center text-black-1">Layout</th>
              <th class="text-center text-black-1">Tanggal Masuk</th>
              <th class="text-center text-black-1">Harga</th>
              <th class="text-center text-black-1">Kondisi</th>
              <th class="text-center text-black-1">Barcode</th>
              <th class="text-center text-black-1">Aksi</th>
            </tr>
          </thead>
          <tbody v-if="paginatedData.length === 0">
            <tr>
              <td colspan="9" class="text-center">Tidak Ada Data</td>
            </tr>
          </tbody>
          <tbody v-for="(noseri, index) in paginatedData" :key="noseri.id" :ref="'barcode' + index">
            <tr class="text-center">
              <td class="text-center">
                <input type="checkbox" @change="updateSelectedItems" v-model="selectedItems" :value="noseri.id">
              </td>
              <td class="text-center">{{ (currentPage - 1) * rowsPerPage + index + 1 }}</td>
              <td class="text-center">{{ noseri.no_seri || '-' }}</td>
              <td class="text-center">{{ noseri.no_seri_default || '-' }}</td>
              <!-- <td class="text-center">{{ noseri.layout ? noseri.layout.ruang : '-' }}</td> -->
              <td class="text-center">{{ formatLayout(noseri) }}</td>
              <td class="text-center">{{ noseri.tanggal_masuk }} <br>
                <small style="color: #444;"><i class="fas fa-clock"></i> {{ durasiData[index] !== '-' ? durasiData[index] + ' Hari' : '-' }}</small>
              </td>
              <td>{{ formatRupiah(noseri.harga) }} <br>
                <small v-if="noseri.tools.kode && noseri.tools.kode.startsWith('2-')" style="color: rgba(247, 0, 255)">                  
                  <!-- {{ formatRupiah(getNilaiBuku(noseri)) }} -->
                  <span :style="{ color: getNilaiBuku(noseri) === 'Sudah Tidak Bernilai' ? 'red' : '' }"><i class="bi bi-cash-coin"></i> {{ getNilaiBuku(noseri) }}</span>
                </small>
              </td>
              <td 
                class="text-center status-pill parent-element"
                style="margin-top: 20px;"
                :class="{
                          'status-active': noseri.kondisi === 'OK', 
                          'status-rusak': noseri.kondisi === 'Rusak', 
                          'status-error': noseri.kondisi === 'Error',
                          'status-hilang': noseri.kondisi === 'Hilang',
                          'status-musnah': noseri.kondisi === 'Musnah',
                          'status-dipinjam': noseri.kondisi === 'Dipinjam'}"
              >{{ noseri.kondisi || '-' }}</td>
              <td><vue-barcode :ref="'barcode' + index" :value="noseri.no_seri" :options="{ width: 100, height: 50 }"></vue-barcode></td>
              <!-- <vue-barcode :ref="'barcode' + index" :value="`${noseri.no_seri} - ${noseri.layout.ruang} | Rak ${noseri.layout.rak} | Lantai ${noseri.layout.lantai} | ${noseri.layout.koordinat}`" :options="{ width: 100, height: 50 }"></vue-barcode> -->
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
                    <a class="dropdown-item" @click="viewDetail(noseri.id)">
                      <i class="fas fa-eye text-info"></i> Riwayat
                    </a>
                    <a class="dropdown-item" @click="openModal('edit', noseri, index)">
                      <i class="bi bi-pencil-square text-primary"></i> Edit
                    </a>
                    <!-- <a class="dropdown-item" @click="editData(noseri.id)">
                      <i class="fas fa-edit text-primary"></i> Edit
                    </a> -->
                    <!-- <a class="dropdown-item" @click="downloadBarcode(noseri.no_seri_alat)">
                      <i class="fas fa-download text-success"></i> Download Barcode
                    </a>
                    <a class="dropdown-item" @click="exportPDF(noseri.no_seri_alat)">
                      <i class="fas fa-download text-success"></i> Download Barcode PDF
                    </a> -->
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
    <div v-if="showSudahDigunakan">
      <rincian-alat-sudah-digunakan :kode-alat="kodeAlat"></rincian-alat-sudah-digunakan>
    </div>
    <div v-if="showPeminjaman">
      <rincian-alat-peminjaman :kode-alat="kodeAlat"></rincian-alat-peminjaman>
    </div>

    <!-- Modal untuk Input dan Edit Data -->
    <div v-if="isModalOpen" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content" style="max-height: 90vh; overflow-y: auto;">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalTitle }}</h5>
            <button type="button" class="close" @click="closeModal">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="no-seri" style="color: #000;">
                <b>No Seri Alat/Mesin</b>
                <sup style="color: red;"> *</sup>
              </label>
              <input type="text" class="form-control" id="no-seri" v-model="form.no_seri" disabled>
            </div>
            <div class="form-group">
              <label for="layout" style="color: #000;">
                <b>Layout</b>
                <sup style="color: red;"> *</sup>
              </label>
              <select 
                id="layout"
                class="form-control"
                v-model="form.layout_id"
                required
              >
              <option value="" disabled selected> Pilih Layout </option>
              <option v-for="layout in Layout" :key="layout.id" :value="layout.id">{{ layout.ruang }}</option>
              </select>
            </div>
            <div class="row">
              <div class="form-group col-md-6">
                <label for="no_seri_default" style="color: #000;">
                  <b>No Seri Default</b>
                  <!-- <sup style="color: red;"> *</sup> -->
                </label>
                <input type="text" class="form-control" id="no_seri_default" v-model="form.no_seri_default">
              </div>
              <div class="form-group col-md-6">
                <label for="tanggal-masuk" style="color: #000;">
                  <b>Tanggal Masuk</b>
                  <!-- <sup style="color: red;"> *</sup> -->
                </label>
                <input type="date" class="form-control" id="tanggal-masuk" v-model="form.tanggal_masuk" disabled>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6">
                <label for="harga" style="color: #000;">
                  <b>Harga Satuan</b>
                  <sup style="color: red;"> *</sup>
                </label>
                <input type="number" class="form-control" id="harga" v-model="form.harga">
              </div>
              <div class="form-group col-md-6">
                <label for="harga" style="color: #000;">
                  <b>Kondisi</b>
                  <sup style="color: red;"> *</sup>
                </label>
                <select id="kondisi" class="form-control" v-model="form.kondisi" disabled>
                  <option value="" disabled>Pilih Kondisi Alat/Mesin</option>
                  <option value="OK">OK</option>
                  <option value="Error">Error</option>
                  <option value="Rusak">Rusak</option>
                  <option value="Musnah">Musnah</option>
                  <option value="Hilang">Hilang</option>
                </select>
                <!-- <input type="number" class="form-control" id="harga" v-model="datanoseri.harga"> -->
              </div>
            </div>                                    
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" @click="closeModal">Tutup</button>
            <button type="button" class="btn btn-primary" @click="saveData">{{ modalAction }}</button>
          </div>
        </div>
      </div>
    </div>    
  </div>
</template>

<script>
import axios from "axios";
import * as XLSX from 'xlsx';  // Impor XLSX dari library yang sudah diinstal
import VueBarcode from 'vue-barcode';
import jsPDF from 'jspdf';
import html2canvas from "html2canvas";
import Swal from 'sweetalert2';

export default {
  props: {
    kodeAlat: String,
    noSeriId: Number,
  },
  components: {
    VueBarcode
  },
  data() {
    return {
      datanoseri: [],
      form: {
        no_seri : '',
        layout_id : '',
        no_seri_default : '',
        tanggal_masuk : '',
        harga : '',
        kondisi : '',
      },
      currentIndex: null,
      modalTitle: '',
      modalAction: '',
      Layout: [],
      showModalInput: false,
      showModalEdit: false,
      idEdit: null,
      showBelumDigunakan: true,
      showSudahDigunakan: false,
      showPeminjaman: false,
      kondisiFilters: [],
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
      selectAllCheckbox: false,
      selectedItems: [],
      isModalOpen: false,
    };
  },
  computed: {
    availableKondisi() {
      return [...new Set(this.datanoseri.map(item => item && item.kondisi))]; // Add a check for undefined item objects
    },
    durasiData() {
      return this.datanoseri.map(noseri => {
        if (noseri.tanggal_masuk) {
          const tanggalMasuk = new Date(noseri.tanggal_masuk);
          const tanggalTerkini = new Date();
          const selisihWaktu = tanggalTerkini - tanggalMasuk;
          const selisihHari = Math.floor(selisihWaktu / (1000 * 60 * 60 * 24));
          return selisihHari;
        }
        return '-';
      });
    },
    totalPages() {
      return Math.ceil(this.filteredData.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(this.currentPage * this.rowsPerPage, this.filteredData.length);
      return `Showing ${start} to ${end} of ${this.filteredData.length} entries`;
    },
    filteredData() {
      return this.datanoseri.filter(noseri => {
        if (!noseri) return false;

        const kondisiMatch = this.kondisiFilters.length
          ? this.kondisiFilters.includes(noseri.kondisi)
          : true;

        const search = this.searchQuery.toLowerCase();
        const noSeriMatch = noseri.no_seri?.toLowerCase().includes(search);
        const ruangMatch = noseri.layout?.ruang?.toLowerCase().includes(search);

        return kondisiMatch && (noSeriMatch || ruangMatch);
      });
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      return this.filteredData.slice(start, start + this.rowsPerPage);
    }
  },
  methods: {
    // async fetchAlatError() {
    //   try {
    //     const kodeAlat = this.kodeAlat; // Kode alat di URL
    //     const response = await axios.get(`/api/no-seri/belumdigunakan/${kodeAlat}`);
    //     this.datanoseri = response.data; // Menyimpan data alat
    //     console.log(this.datanoseri)
    //   } catch (error) {
    //     console.error("Error fetching alat error detail:", error);
    //   }
    // },
    formatLayout(noseri) {
      if (!noseri.layout) return '-';
      return `${noseri.layout.ruang || '-'} | Rak ${noseri.layout.rak || '-'} | Lantai ${noseri.layout.lantai || '-'} | ${noseri.layout.koordinat || '-'}`;
    },
    async  fetchAlatError() {
      try {
        const kodeAlat = this.kodeAlat; // Kode alat di URL
        // console.log(this.kodeAlat);
        const response = await axios.get(`/api/v1/noseri/getNoSeri/${kodeAlat}`);
        this.datanoseri = response.data; // Menyimpan data alat
        // console.log(this.datanoseri);
        // console.log(kodeAlat);
      } catch (error) {
        console.error("Error fetching alat error detail:", error);
      }
    },
    // Mengambil Data Layout
    async fetchLayout() {
      try {
        const response = await axios.get('/api/v1/layouts');
        this.Layout = response.data;
      } catch (error) {
        console.error(error);
      }
    },
    sortNoSeri(order) {
      const sortedData = this.datanoseri.slice().sort((a, b) => {
        if (order === 'asc') {
          return a.no_seri_alat.localeCompare(b.no_seri_alat);
        } else {
          return b.no_seri_alat.localeCompare(a.no_seri_alat);
        }
      });
      this.datanoseri = sortedData;
    },
    debouncedFetchAlats: _.debounce(function () {
      this.fetchAlatError();
    }, 300),
    tambahData() {
      this.showModalInput = true;
    },
    editData(id) {
      this.idEdit = id;
      this.showModalEdit = true;
    },
    viewDetail(id) {
      this.$router.push(`/admin-mtc/data-alat/belum-digunakan/detail/${id}`);
    },
    tutupModal() {
      this.showModalInput = false;
      this.showModalEdit = false;
    },
    toggleBelumDigunakan() {
      this.showSudahDigunakan = false;
      this.showPeminjaman = false;
      this.showBelumDigunakan = !this.showBelumDigunakan;
    },
    toggleSudahDigunakan() {
      this.showBelumDigunakan = false;
      this.showPeminjaman = false;
      this.showSudahDigunakan = !this.showSudahDigunakan;
    },
    togglePeminjaman() {
      this.showBelumDigunakan = false;
      this.showSudahDigunakan = false;
      this.showPeminjaman = !this.showPeminjaman;
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
    formatRupiah(harga) {
      return harga ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(harga) : 'Rp. -';
    },
    depresiasiMesin(noseri) {
      if (noseri.kode_alat && noseri.kode_alat.startsWith('2-')) {
        const harga = noseri.harga;
        const depresiasi = harga / (8 * 12);
        return `Rp. ${this.formatRupiah(depresiasi)}`;
      } else {
        return '-';
      }
    },
    // getNilaiBuku(noseri) {
    //   if (noseri.kode_alat && noseri.kode_alat.startsWith('2-')) {
    //     const harga = noseri.harga;
    //     const tanggalMasuk = new Date(noseri.tanggal_masuk);
    //     const tanggalSekarang = new Date();
    //     const bulanMasuk = tanggalMasuk.getMonth();
    //     const bulanSekarang = tanggalSekarang.getMonth();
    //     const tahunMasuk = tanggalMasuk.getFullYear();
    //     const tahunSekarang = tanggalSekarang.getFullYear();
    //     const depresiasi = harga / (8 * 12);
    //     let nilaiBuku = harga;

    //     if (tahunSekarang > tahunMasuk) {
    //       nilaiBuku -= depresiasi * (bulanSekarang + (tahunSekarang - tahunMasuk - 1) * 12);
    //     } else if (bulanSekarang > bulanMasuk) {
    //       nilaiBuku -= depresiasi * (bulanSekarang - bulanMasuk);
    //     }

    //     return nilaiBuku;
    //   } else {
    //     return noseri.harga;
    //   }
    // },
    getNilaiBuku(noseri) {
      if (noseri.tools.kode && noseri.tools.kode.startsWith('2-')) {
        const harga = noseri.harga;
        const tanggalMasuk = new Date(noseri.tanggal_masuk);
        const tanggalSekarang = new Date();
        const bulanMasuk = tanggalMasuk.getMonth();
        const bulanSekarang = tanggalSekarang.getMonth();
        const tahunMasuk = tanggalMasuk.getFullYear();
        const tahunSekarang = tanggalSekarang.getFullYear();
        const depresiasi = harga / (8 * 12);
        let nilaiBuku = harga;

        const tahunBerlalu = tahunSekarang - tahunMasuk;
        const bulanBerlalu = (tahunSekarang - tahunMasuk) * 12 + bulanSekarang - bulanMasuk;

        nilaiBuku -= depresiasi * bulanBerlalu;

        if (nilaiBuku <= 0) {
          return 'Sudah Tidak Bernilai';
        } else {
          return `${this.formatRupiah(nilaiBuku)}`;
        }
      } else {
        return noseri.harga;
      }
    },
    openModal(action, item = null, index = null) {
      if (action === 'edit' && item) {
        this.modalTitle = 'Edit Data';
        this.modalAction = 'Update';
        this.form.no_seri = item.no_seri;
        this.form.layout_id = item.layout_id;
        this.form.no_seri_default = item.no_seri_default;
        this.form.tanggal_masuk = item.tanggal_masuk;
        this.form.harga = item.harga;
        this.form.kondisi = item.kondisi;
        this.currentIndex = index;
        this.currentId = item.id;
        // console.log('layout_id dari noseri:', item.layout_id);
      }
      this.isModalOpen = true;
    },
    closeModal() {
      this.isModalOpen = false; // Set modal status close
    },
    // saveData() {
    //   if (this.modalAction === 'Update') {
    //       console.log('Updating data with ID:', this.currentId);
    //       console.log('Data yang akan dikirim:', this.datanoseri); // Log data yang akan dikirim
    //       axios.put(`/api/v1/noseri/${this.currentId}`, this.datanoseri)
    //           .then(response => {
    //               console.log('Update successful:', response.data);
    //               const index = this.datanoseri.findIndex(item => item.id === this.currentId);
    //               if (index !== -1) {
    //                   this.datanoseri.splice(index, 1, response.data.data); // Update data di frontend
    //               }
    //               this.closeModal();
    //           })
    //           .catch(error => {
    //               console.error('Error updating data:', error);
    //               if (error.response) {
    //                   console.error('Response data:', error.response.data); // Log data respons
    //               }
    //           });
    //   }
    // },
    saveData() {
      if (this.currentIndex !== null) {
        axios.put(`/api/v1/noseri/${this.currentId}`, this.form)
        .then(response => {
          this.datanoseri[this.currentIndex] = response.data;
          this.closeModal();
          this.fetchAlatError();
          this.fetchLayout();
          this.currentPage = 1;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
        })
        .catch(error => {
          console.error('Error updating data:', error);
        })
      }
    },
    // saveData() {
    //   if (this.modalAction === 'Update') {
    //     console.log('Updating data with ID:', this.currentId);

    //     // Validasi simple
    //     if (!this.datanoseri.layout_id || !this.datanoseri.harga) {
    //       alert('Layout dan Harga tidak boleh kosong!');
    //       return;
    //     }

    //     // Convert harga jadi number
    //     this.datanoseri.harga = parseFloat(this.datanoseri.harga) || 0;

    //     axios.put(`/api/v1/noseri/${this.currentId}`, this.datanoseri)        
    //       .then(response => {
    //         console.log('Update successful:', response.data);

    //         // Update array yang menyimpan semua data (bukan datanoseri langsung)
    //         const index = this.datanoseri.findIndex(item => item.id === this.currentId);
    //           if (index !== -1) {
    //             this.datanoseri.splice(index, 1, response.data.data);
    //           }

    //         this.closeModal();            
    //       })
    //       .catch(error => {
    //         console.error('Error updating data:', error);
    //         if (error.response) {
    //           alert(error.response.data.message || 'Terjadi kesalahan saat menyimpan data.');
    //         }
    //       });
    //   }
    // },
    downloadBarcode(noSeri) {
      const link = document.createElement('a');
      link.href = `https://barcode.tec-it.com/barcode.ashx?data=${noSeri}&multiplebarcodes=true&translate-esc=on&download=true`;
      link.download = `barcode_${noSeri}.png`;
      document.body.appendChild(link); // Menambahkan elemen 'a' ke DOM
      setTimeout(() => {
        link.click();
        document.body.removeChild(link); // Menghapus elemen setelah klik
      }, 100);
    },
    exportPDF(noSeri) {
      const index = this.filteredData.findIndex(noseri => noseri.no_seri_alat === noSeri);
      if (index !== -1) {
        const barcode = this.$refs['barcode' + index][0]; // Mendapatkan elemen barcode
        html2canvas(barcode.$el).then(canvas => {
          const imgData = canvas.toDataURL('image/png');
          const doc = new jsPDF();
          doc.addImage(imgData, 'PNG', 10, 10, 26, 15);
          doc.save(`barcode_${noSeri}.pdf`);
        });
      }
    },
    exportAllBarcode() {
      const doc = new jsPDF();
      let y = 10; // Koordinat y untuk menambahkan barcode
      let x = 10; // Koordinat x untuk menambahkan barcode
      let baris = 0; // Jumlah baris
      let kolom = 0; // Jumlah kolom
      this.datanoseri.forEach((noseri, index) => {
        const barcode = this.$refs['barcode' + index][0]; // Mendapatkan elemen barcode
        html2canvas(barcode.$el).then(canvas => {
          const imgData = canvas.toDataURL('image/png');
          doc.addImage(imgData, 'PNG', x, y, 50, 20);
          x += 60; // Menambahkan 60 untuk menempatkan barcode berikutnya di samping
          kolom++;
          if (kolom === 2) {
            x = 10; // Reset posisi x
            y += 30; // Menambahkan 30 untuk menempatkan barcode berikutnya di bawah
            baris++;
            kolom = 0;
          }
          if (baris === 20) {
            doc.addPage(); // Tambahkan halaman baru
            y = 10; // Reset posisi y
            baris = 0;
          }
          if (index === this.datanoseri.length - 1) {
            doc.save('all_barcode.pdf');
          }
        });
      });
    },
    selectAll() {
      if (this.selectAllCheckbox) {
        this.selectedItems = this.filteredData.map(item => item.id);
      } else {
        this.selectedItems = [];
      }
    },
    updateSelectAll() {
      if (this.selectAllCheckbox) {
        this.selectedItems = this.filteredData.map(item => item.id);
      } else {
        this.selectedItems = [];
      }
    },
    updateSelectedItems() {
      if (this.selectedItems.length === this.filteredData.length) {
        this.selectAllCheckbox = true;
      } else {
        this.selectAllCheckbox = false;
      }
    },
    exportSelected() {
      if (this.selectedItems.length === 0) {
        Swal.fire({
          title: 'Tidak ada data yang dipilih',
          icon: 'error',
          confirmButtonText: 'OK'
        });
        return;
      }

      const doc = new jsPDF({
        orientation: "landscape",
        unit: "mm",
        format: [50, 20]
      });

      const selectedNoseri = this.filteredData.filter(item => this.selectedItems.includes(item.id));
      const promises = selectedNoseri.map((noseri, idx) => {
        const barcodeIndex = this.filteredData.indexOf(noseri);
        const barcodeRef = this.$refs[`barcode${barcodeIndex}`];

        if (barcodeRef && barcodeRef[0]) {
          return html2canvas(barcodeRef[0].$el).then(canvas => {
            const imgData = canvas.toDataURL('image/png');

            if (idx > 0) doc.addPage(); // tambah halaman baru selain di halaman pertama
            doc.setFontSize(4);
            doc.text("Elitech", 25, 1.5, null, null, "center");
            doc.addImage(imgData, 'PNG', 4, 2, 45, 18); // posisi dan ukuran gambar barcode
          });
        } else {
          console.error(`Barcode untuk item ${noseri.id} tidak ditemukan.`);
          return Promise.resolve(); // biar proses tidak berhenti
        }
      });

      Promise.all(promises).then(() => {
        doc.save('barcode_terpilih.pdf');
      }).catch(error => {
        console.error("Terjadi kesalahan saat memproses barcode:", error);
      });
    },
    // exportSelected() {
    //   if (this.selectedItems.length === 0) {
    //     alert("Tidak ada data yang dipilih");
    //     return;
    //   }

    //   const doc = new jsPDF();
    //   let y = 10, x = 10, baris = 0, kolom = 0;

    //   // Gunakan Promise untuk memastikan semua elemen diproses sebelum menyimpan file
    //   const promises = this.filteredData
    //     .filter(item => this.selectedItems.includes(item.id))
    //     .map(noseri => {
    //       const barcodeRef = this.$refs[`barcode${this.filteredData.indexOf(noseri)}`];
    //       if (barcodeRef && barcodeRef[0]) {
    //         return html2canvas(barcodeRef[0].$el).then(canvas => {
    //           const imgData = canvas.toDataURL('image/png');
    //           doc.addImage(imgData, 'PNG', x, y, 26, 15);
    //           x += 60;
    //           kolom++;
    //           if (kolom === 2) {
    //             x = 10; // Reset posisi x
    //             y += 30; // Tambahkan jarak vertikal
    //             baris++;
    //             kolom = 0;
    //           }
    //           if (baris === 20) {
    //             doc.addPage(); // Tambahkan halaman baru jika melebihi baris
    //             y = 10; // Reset posisi y
    //             baris = 0;
    //           }
    //         });
    //       } else {
    //         console.error(`Barcode untuk item ${noseri.id} tidak ditemukan.`);
    //       }
    //     });

    //   // Tunggu semua barcode selesai diproses, lalu simpan PDF
    //   Promise.all(promises).then(() => {
    //     doc.save('barcode_terpilih.pdf');
    //   }).catch(error => {
    //     console.error("Terjadi kesalahan saat memproses barcode:", error);
    //   });
    // },
    // exportSelected() {
    //   if (this.selectedItems.length === 0) {
    //     alert("Tidak ada data yang dipilih");
    //     return;
    //   }

    //   const doc = new jsPDF({
    //     unit: 'mm',
    //     format: [26, 15], // Ukuran kertas 26 mm x 15 mm
    //   });
    //   let y = 5; // Koordinat y untuk menambahkan barcode
    //   let x = 5; // Koordinat x untuk menambahkan barcode
    //   let baris = 0; // Jumlah baris
    //   let kolom = 0; // Jumlah kolom

    //   const barcodeWidth = 20; // Ukuran barcode 20 mm
    //   const barcodeHeight = 10; // Ukuran barcode 10 mm

    //   this.filteredData.filter(item => this.selectedItems.includes(item.id)).forEach((noseri, index) => {
    //     const barcode = this.$refs['barcode' + this.filteredData.indexOf(noseri)][0]; // Mendapatkan elemen barcode
    //     html2canvas(barcode.$el).then(canvas => {
    //       const imgData = canvas.toDataURL('image/png');
    //       doc.addImage(imgData, 'PNG', x, y, barcodeWidth, barcodeHeight);
    //       x += barcodeWidth + 2; // Menambahkan 2 mm untuk jarak antara barcode
    //       kolom++;
    //       if (kolom === 1) {
    //         x = 5; // Reset posisi x
    //         y += barcodeHeight + 2; // Menambahkan 2 mm untuk jarak antara barcode
    //         baris++;
    //         kolom = 0;
    //       }
    //       if (baris === 1) {
    //         doc.addPage(); // Tambahkan halaman baru
    //         y = 5; // Reset posisi y
    //         baris = 0;
    //       }
    //       if (index === this.selectedItems.length - 1) {
    //         doc.save('barcode_terpilih.pdf');
    //       }
    //     });
    //   });
    // },
    // downloadBarcode(noSeri) {
    //   const link = document.createElement('a');
    //   link.href = `https://barcode.tec-it.com/barcode.ashx?data=${noSeri}`;
    //   link.download = `barcode_${noSeri}.png`;
    //   link.click();
    // },
    // downloadBarcode(noSeri) {
    //   const doc = new jsPDF();
    //   const barcode = `https://barcode.tec-it.com/barcode.ashx?data=${noSeri}`;
    //   doc.addImage(barcode, 'PNG', 10, 10, 100, 50);
    //   doc.save(`barcode_${noSeri}.pdf`);
    // },
    downloadExcel() {
      if (this.filteredData.length === 0) {
        alert("Tidak ada data yang dapat di download");
        return;
      }

      // Menyiapkan data yang akan dikonversi, termasuk nomor urut
      const data = this.filteredData.map((item, index) => {
        const row = {
          No: index + 1,
          Nama: item.tools.nama,
          Kode: item.tools.kode,
          'No Seri': item.no_seri,
          Layout: this.formatLayout(item),
          'Tanggal Masuk': item.tanggal_masuk,
          Harga: `${this.formatRupiah(item.harga)}`,
          Kondisi: item.kondisi,
        };

        if (item.tools.jenis_id === 2) {
          row['Depresiasi Mesin'] = this.getNilaiBuku(item);
        }

        return row;
      });

      // Mengonversi data ke dalam format sheet Excel
      const ws = XLSX.utils.json_to_sheet(data);

      // Mengatur header agar menjadi bold
      const range = XLSX.utils.decode_range(ws['!ref']); // Mendapatkan range sheet
      for (let col = range.s.c; col <= range.e.c; col++) {
        const address = { r: range.s.r, c: col }; // Alamat cell header
        const cell = ws[XLSX.utils.encode_cell(address)];
        if (cell) {
          cell.s = { font: { bold: true } }; // Mengatur style font menjadi bold
        }
      }

      // Membuat workbook dari sheet yang sudah dibuat
      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, 'Data No Seri Belum Digunakan');

      // Menyimpan file Excel dengan nama yang ditentukan
      XLSX.writeFile(wb, 'data_no_seri_belum_digunakan.xlsx');
    }
  },
  watch: {
    rowsPerPage() {
      this.currentPage = 1;
    },
    searchQuery() {
      this.currentPage = 1;
    },
    kondisiFilters: {
      handler() {
        this.currentPage = 1;
      },
      deep: true
    }
  },
  mounted() {
    this.fetchAlatError();
    this.fetchLayout();
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

  .text-wrap {
    white-space: normal; /* Atau gunakan pre-wrap jika ingin mempertahankan spasi */
    word-wrap: break-word; /* Memungkinkan kata untuk terputus jika terlalu panjang */
    overflow-wrap: break-word; /* Memastikan kata panjang terputus */
  }

  /* Modal Input Styling */
  .modal-input {    
    display: none; /* Sembunyikan modal secara default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100vh; /* Ubah tinggi menjadi 100vh untuk membuat modal melebar */
    background-color: rgba(0, 0, 0, 0.5); /* Latar belakang transparan */
  }

  .modal-input.is-visible {
    display: flex; /* Tampilkan modal saat is-visible aktif */
    justify-content: center;
    align-items: center;
  }

  .modal-content-input {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    max-width: 80%; /* Ubah lebar menjadi 80% untuk membuat modal melebar */
    max-height: 90vh; /* Ubah tinggi menjadi 90vh untuk membuat modal melebar */
    overflow-y: auto; /* Tambahkan overflow-y untuk membuat modal dapat di-scroll */
    text-align: center;
    margin: 20px; /* Tambahkan margin untuk membuat modal tidak menempel di tepi */
  }

  .status-hilang {
    background-color: rgba(22, 22, 22, 0.1); /* Merah Tua dengan transparansi */
    color: rgba(22, 22, 22);
  }

  .status-dipinjam {
    background-color: rgba(235, 90, 60, 0.1);
    color: rgba(235, 90, 60);
  }

  .status-musnah {
    background-color: rgba(100, 13, 95, 0.1);
    color: rgba(100, 13, 95);
  }
</style>