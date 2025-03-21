<template>
  <div class="container-fluid">
    <!-- Modal Input Data -->
    <div id="app" class="modal-input" :class="{'is-visible': showModalInput}" @click.self="tutupModal">
      <div class="modal-content-input">
        <input-alat-belum-digunakan @tutup-modal="tutupModal"></input-alat-belum-digunakan>
      </div>
    </div>

    <!-- Modal Edit Data -->
    <div id="app" class="modal-input" :class="{'is-visible' :showModalEdit}" @click.self="tutupModal">
      <div class="modal-content-input">
        <edit-alat-belum-digunakan @tutup-modal="tutupModal" :id="idEdit"></edit-alat-belum-digunakan>
      </div>      
    </div>

    <div class="col-md-12">
      <button 
        class="btn btn-show mb-3 mr-1"
        :class="{active: showBelumDigunakan}"
        @click="toggleBelumDigunakan"
      >
        <span v-if="showBelumDigunakan">Belum Digunakan</span>
        <span v-else>Belum Digunakan</span>
      </button>
      <button 
        class="btn btn-show mb-3 ml-1 mr-1"
        :class="{active: showSudahDigunakan}"
        @click="toggleSudahDigunakan"
      >
        <span v-if="showSudahDigunakan">Sudah Digunakan</span>
        <span v-else>Sudah Digunakan</span>
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
              <th class="text-center text-black-1 tr-center">#</th>                    
              <th class="text-center text-black-1" style="cursor: pointer; position: relative; vertical-align: middle;">
                No. Seri Alat
                <span class="sort-icons">
                  <i @click="sortNoSeri('desc')" class="fas fa-sort-up"></i>
                  <i @click="sortNoSeri('asc')" class="fas fa-sort-down"></i>
                </span>
              </th>
              <th class="text-center text-black-1">Layout</th>
              <th class="text-center text-black-1">Tanggal Masuk</th>
              <th class="text-center text-black-1">Harga</th>
              <th class="text-center text-black-1">Kondisi</th>
              <th class="text-center text-black-1">Barcode</th>
              <th class="text-center text-black-1">Aksi</th>
            </tr>
          </thead>
          <tbody v-if="filteredData.length === 0">
            <tr>
              <td colspan="5" class="text-center">Tidak Ada Data</td>
            </tr>
          </tbody>
          <tbody v-for="(noseri, index) in filteredData" :key="noseri.id">
            <tr class="text-center">
              <td class="text-center">{{ (currentPage - 1) * rowsPerPage + index + 1 }}</td>
              <td class="text-center">{{ noseri.no_seri_alat || '-' }}</td>
              <td class="text-center">{{ noseri.layout ? noseri.layout.nama_lokasi : '-' }}</td>
              <td class="text-center">{{ noseri.tanggal_masuk }} <br>
                <small style="color: #444;"><i class="fas fa-clock"></i> {{ durasiData[index] !== '-' ? durasiData[index] + ' Hari' : '-' }}</small>
              </td>
              <td>{{ formatRupiah(noseri.harga) }} <br>
                <small v-if="noseri.kode_alat && noseri.kode_alat.startsWith('2-')">                  
                  <!-- {{ formatRupiah(getNilaiBuku(noseri)) }} -->
                  <span :style="{ color: getNilaiBuku(noseri) === 'Sudah Tidak Bernilai' ? 'red' : '' }">Depresiasi: {{ getNilaiBuku(noseri) }}</span>
                </small>
              </td>
              <td 
                class="text-center status-pill parent-element"
                style="margin-top: 20px;"
                :class="{
                          'status-active': noseri.status === 'OK', 
                          'status-rusak': noseri.status === 'Rusak', 
                          'status-error': noseri.status === 'Error',
                          'status-hilang': noseri.status === 'Hilang',
                          'status-dipinjam': noseri.status === 'Dipinjam'}"
              >{{ noseri.status || '-' }}</td>
              <td><vue-barcode :value="noseri.no_seri_alat" :options="{ width: 100, height: 50 }"></vue-barcode></td>
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
                    <a class="dropdown-item" @click="editData(noseri.id)">
                      <i class="fas fa-edit text-primary"></i> Edit
                    </a>
                    <a class="dropdown-item" @click="downloadBarcode(noseri.no_seri_alat)">
                      <i class="fas fa-download text-success"></i> Download Barcode
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
    <div v-if="showSudahDigunakan">
      <rincian-alat-sudah-digunakan :kode-alat="kodeAlat"></rincian-alat-sudah-digunakan>
    </div>
    <div v-if="showPeminjaman">
      <rincian-alat-peminjaman :kode-alat="kodeAlat"></rincian-alat-peminjaman>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import * as XLSX from 'xlsx';  // Impor XLSX dari library yang sudah diinstal
import VueBarcode from 'vue-barcode';
import jsPDF from 'jspdf';

export default {
  props: {
    kodeAlat: String,
    id: Number,
  },
  components: {
    VueBarcode
  },
  data() {
    return {
      datanoseri: [],
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
    };
  },
  computed: {
    availableKondisi() {
      return [...new Set(this.datanoseri.map(item => item.status))];
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
      return Math.ceil(this.datanoseri.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(this.currentPage * this.rowsPerPage, this.datanoseri.length);
      return `Showing ${start} to ${end} of ${this.datanoseri.length} entries`;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = start + this.rowsPerPage;
      return this.datanoseri.slice(start, end);
    },
    filteredData() {
      return this.datanoseri.filter(noseri => {
        const kondisiMatch = this.kondisiFilters.length ? this.kondisiFilters.includes(noseri.status) : true;
        const searchMatch = noseri.no_seri_alat.toLowerCase().includes(this.searchQuery.toLowerCase());

        return kondisiMatch && searchMatch;
      });
    }
  },
  methods: {
    async fetchAlatError() {
      try {
        const kodeAlat = this.kodeAlat; // Kode alat di URL
        const response = await axios.get(`/api/no-seri/belumdigunakan/${kodeAlat}`);
        this.datanoseri = response.data; // Menyimpan data alat
        //console.log(this.datanoseri)
      } catch (error) {
        console.error("Error fetching alat error detail:", error);
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
      return harga ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(harga) : 'Rp -';
    },
    depresiasiMesin(noseri) {
      if (noseri.kode_alat && noseri.kode_alat.startsWith('2-')) {
        const harga = noseri.harga;
        const depresiasi = harga / (8 * 12);
        return `Rp ${this.formatRupiah(depresiasi)}`;
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
      if (noseri.kode_alat && noseri.kode_alat.startsWith('2-')) {
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
      const data = this.filteredData.map((item, index) => ({
        No: index + 1, // Menambahkan nomor urut
        NoSeri: item.no_seri_alat,
        Layout: item?.layout?.nama_lokasi,
        TanggalMasuk: item.tanggal_masuk,
        Harga: `${this.formatRupiah(item.harga)}`,
        Depresiasi: this.getNilaiBuku(item),
        Kondisi: item.status,
      }));

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
      this.currentPage = 1; // Reset ke halaman pertama saat rowsPerPage berubah
    }
  },
  mounted() {
    this.fetchAlatError();
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
</style>