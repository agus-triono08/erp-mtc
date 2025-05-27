<template>
  <div class="container-fluid">
    <!-- Loader -->
    <!-- <div class="loader" v-if="isLoading">
      <div class="loading-overlay">
        <div class="loading-spinner">
            <span class="sr-only">Loading...</span>          
        </div>
      </div>
    </div> -->
    <h1 class="h3 mb-4 mt-4 text-gray-900"><b>Pengantian Alat/Mesin</b></h1>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <router-link class="nav-link" id="kategori-tab" data-toggle="tab" role="tab" aria-controls="kategori" aria-selected="false" :class="{ active: $route.name === 'data-baru-hilang' }" :to="{ name: 'data-baru-hilang' }">Baru</router-link>
        </li>
        <li class="nav-item" role="presentation">
          <router-link class="nav-link" id="kategori-tab" data-toggle="tab" role="tab" aria-controls="kategori" aria-selected="false" :class="{ active: $route.name === 'data-proses-hilang' }" :to="{ name: 'data-proses-hilang' }">Proses</router-link>
        </li>
        <li class="nav-item" role="presentation">
          <router-link class="nav-link" id="kategori-tab" data-toggle="tab" role="tab" aria-controls="kategori" aria-selected="true" :class="{ active: $route.name === 'data-selesai-hilang' }" :to="{ name: 'data-selesai-hilang' }">Selesai</router-link>
        </li>
      </ul>
    <div class="row align-items-center justify-content-end m-3">
      <div class="col-md-2">
        <label>Filter Tanggal Mulai Download</label>
        <input type="date" class="form-control" v-model="startDate">
      </div>
      <div class="col-md-2">
        <label>Filter Tanggal Selesai Download</label>
        <input type="date" class="form-control" v-model="endDate">
      </div>
      <div class="btn-group">
        <button class="btn btn-primary mr-2" @click="downloadKartuKehilangan()">Download Kartu Riwayat</button>            
      </div>
      <div class="search-wrapper">
        <div class="input-group">
          <input type="text" placeholder="Search..." class="form-control"
            v-model="searchQuery"
            @input="debouncedFetchNoSeri"
          />
        </div>
      </div>
    </div>
    <div class="table-responsive p-3">
      <table class="table table-border no-border table-custom" style="overflow-x: auto;">
        <thead>
          <tr class="bg-table text-center">
            <th class="text-black-1">#</th>
            <th @click="sortBy('no_kerusakan')" style="cursor: pointer; color: #000;">
              No Kehilangan
              <i class="fas" :class="{
                'fa-sort-up': sortKey === 'no_kerusakan' && sortDirection === 'asc',
                'fa-sort-down': sortKey === 'no_kerusakan' && sortDirection === 'desc'
              }"></i>
            </th>
            <th @click="sortBy('no_seri.tools.nama')" style="cursor: pointer; color: #000;">
              Nama Alat/Mesin
              <i class="fas" :class="{
                'fa-sort-up': sortKey === 'no_seri.tools.nama' && sortDirection === 'asc',
                'fa-sort-down': sortKey === 'no_seri.tools.nama' && sortDirection === 'desc'
              }"></i>
            </th>
            <th @click="sortBy('no_seri')" style="cursor: pointer; color: #000;">
              No Seri
              <i class="fas" :class="{
                'fa-sort-up': sortKey === 'no_seri' && sortDirection === 'asc',
                'fa-sort-down': sortKey === 'no_seri' && sortDirection === 'desc'
              }"></i>
            </th>
            <th @click="sortBy('tgl_kehilangan')" style="cursor: pointer; color: #000;">
              Tgl Hilang
              <i class="fas" :class="{
                'fa-sort-up': sortKey === 'tgl_kehilangan' && sortDirection === 'asc',
                'fa-sort-down': sortKey === 'tgl_kehilangan' && sortDirection === 'desc'
              }"></i>
            </th>
            <th @click="sortBy('status')" style="cursor: pointer; color: #000;">
              Status
              <i class="fas" :class="{
                'fa-sort-up': sortKey === 'status' && sortDirection === 'asc',
                'fa-sort-down': sortKey === 'status' && sortDirection === 'desc'
              }"></i>
            </th>
            <th class="text-black-1">Aksi</th>
          </tr>
        </thead>
        <tbody v-if="paginatedData.length === 0">
          <tr class="text-center">
            <td colspan="8">Data tidak ditemukan</td>
          </tr>
        </tbody>
        <tbody v-for="(item, index) in paginatedData" :key="item.id">
          <tr class="text-center">
            <td>{{ index + 1 }}</td>
            <td>{{ item.no_kehilangan || '-' }}</td>
            <td>{{ item.no_seri.tools.nama || '-' }}</td>
            <td>{{ item.no_seri.no_seri || '-' }}</td>
            <td>{{ item.tgl_kehilangan || '-' }}</td>
            <td>
              <div
                class="btn-sts"
                :class="{
                  'status-active': item.status === 'Selesai',
                  'status-error': item.status === 'Proses',
                  'status-rusak': item.status === 'Belum',
                  'status-musnah': item.status === 'Digunakan',
                  'status-hilang': item.status === 'Belum Diproses',
                }"
              >
                {{ item.status || '-' }}
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
                  <router-link :to="{ name: 'data-detail-selesai-hilang', params: { id: item.id } }" class="dropdown-item">
                    <i class="fas fa-eye text-info"></i> Detail
                  </router-link>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

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
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import vSelect from 'vue-select';
import jsPDF from "jspdf";
import 'jspdf-autotable';

export default {
  components: {
    vSelect,
  },
  data() {
    return {
      searchQuery: '',
      startDate: '',
      endDate: '',
      dataKehilangan: [],
      form: {
        jenis: '',
        nama_kategori: '',
        kode_kategori: '',
      },
      Jenis: [],
      sortKey: '',
      sortDirection: 'asc',
      currentIndex: null,
      modalTitle: '',
      modalAction: '',
      isModalOpen: false, // Flag untuk modal open/close
      rowsPerPage: 10, // Menentukan jumlah item per halaman
      currentPage: 1, // Halaman saat ini
      isStatusUpdated: false,
    }
  },
  mounted() {
    this.fetchData();
  },
  computed: {
    filteredData() {
      let result = this.dataKehilangan.filter(item => {
        return (
          item.no_seri.no_seri.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          item.no_seri.tools.nama.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          item.no_kerusakan.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      });

      if (this.sortKey) {
        result.sort((a, b) => {
          let aVal = this.getSortValue(a, this.sortKey);
          let bVal = this.getSortValue(b, this.sortKey);

          aVal = aVal ? aVal.toString().toLowerCase() : '';
          bVal = bVal ? bVal.toString().toLowerCase() : '';

          if (aVal < bVal) return this.sortDirection === 'asc' ? -1 : 1;
          if (aVal > bVal) return this.sortDirection === 'asc' ? 1 : -1;
          return 0;
        });
      }

      return result;
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
    durasidata() {
      return this.dataKehilangan.map(item => {
        if (item.tgl_selesai && item.tgl_perbaikan) {
          const tglSelesai = new Date(item.tgl_selesai);
          const tglSekarang = new Date(item.tgl_perbaikan);
          const tglSaatIni = new Date();
          const selisihHari = Math.abs(tglSelesai - tglSekarang);
          const hari = Math.floor(selisihHari / (1000 * 60 * 60 * 24 ));

          if (tglSaatIni > tglSelesai) {
            const selisihHariSaatIni = Math.abs(tglSaatIni - tglSelesai);
            const hariSaatIni = Math.floor(selisihHariSaatIni / (1000 * 60 * 60 * 24 ));
            return hariSaatIni + ' hari lewat';
          } else if (tglSelesai < tglSekarang) {
            return hari + ' hari lalu';
          } else {
            const excessDays = Math.ceil((tglSelesai-tglSekarang) / (1000 * 60 * 60 * 24));
            return excessDays + ' hari lagi';
          }
        } else {
          return 'Tidak ada tanggal';
        }
      })
    },
  },
  methods: {
    async fetchData() {
      const res = await fetch('/api/v1/kehilangan');
      const data = await res.json();
      this.dataKehilangan = data.byStatusSelesai;
      // console.log(this.dataKehilangan);
    },
    perbaikanData(item, status) {
      if (['Proses', 'Selesai'].includes(item.status)) {
        Swal.fire('Tidak Bisa Diubah', `Status No Perbaikan ${item.no_perbaikan} sudah ${item.status} dan tidak dapat diubah.`, 'warning');
        return;
      }

      axios.post('/api/v1/perbaikan/update-status', {
        id: item.id,
        status: status,
      })
      .then((response) => {
        item.status = status;
        this.isStatusUpdated = true;
        Swal.fire('Berhasil!', `Status No Perbaikan ${item.no_perbaikan} telah diubah menjadi ${status}.`, 'success');
      })
      .catch(error => {
        console.error(error);
        Swal.fire('Gagal', 'Gagal memperbarui status.', 'error');
      });
    },
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortKey = key;
        this.sortDirection = 'asc';
      }
    },
    getSortValue(item, key) {
      if (key === 'jenis') {
        return item.jenis?.nama_jenis;
      }
      return item[key];
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
    debouncedFetchNoSeri: _.debounce(function () {
        this.fetchData();
      }, 300),
    downloadKartuKehilangan() {
      // Filter data by date range if dates are provided
      let filteredData = this.dataKehilangan;
      
      if (this.startDate && this.endDate) {
        const start = new Date(this.startDate);
        const end = new Date(this.endDate);
        
        filteredData = this.dataKehilangan.filter(item => {
          if (!item.tgl_kehilangan) return false;
          const itemDate = new Date(item.tgl_kehilangan);
          return itemDate >= start && itemDate <= end;
        });
      }

      if (filteredData.length === 0) {
        Swal.fire('Error', 'Tidak ada data kerusakan dalam rentang tanggal yang dipilih', 'error');
        return;
      }

      const dateRangeText = this.startDate && this.endDate 
        ? `${this.startDate} sampai ${this.endDate}` 
        : 'Semua Data';
      
      const filename = `Kartu_Riwayat_Perbaikan_${dateRangeText}.pdf`;
      const pdf = new jsPDF();

      // Judul dengan informasi rentang tanggal
      pdf.setFont('helvetica', 'bold');
      pdf.setFontSize(16);
      const title = 'Kartu Riwayat Perbaikan';
      const titleWidth = pdf.getStringUnitWidth(title) * pdf.getFontSize() / pdf.internal.scaleFactor;
      pdf.text(title, (pdf.internal.pageSize.width - titleWidth) / 2, 10);
      
      // Tambahkan informasi rentang tanggal
      pdf.setFontSize(10);
      pdf.text(`Periode: ${dateRangeText}`, 14, 25);

      // Header Tabel
      const headers = [
        "#", 
        "No. Kehilangan", 
        "Nama Alat/Mesin", 
        "No Seri", 
        "Tanggal Kehilangan", 
        "Detail Kehilangan"
      ];

      // Format data untuk semua row
      const rows = filteredData.map((item, index) => {
        const tglKehilangan = item.tgl_kehilangan 
          ? new Date(item.tgl_kehilangan).toLocaleDateString('id-ID') 
          : '-';

        return [
          index + 1,
          item.no_kehilangan || '-',
          item.no_seri?.tools?.nama || '-',
          item.no_seri?.no_seri || '-',
          tglKehilangan,
          item.detail_kehilangan || '-'
        ];
      });

      // Buat tabel dengan autoTable
      pdf.autoTable({
        head: [headers],
        body: rows,
        startY: 30,
        styles: {
          fontSize: 8,
          cellPadding: 3
        },
        margin: { horizontal: 5 }
      });

      // Tanda tangan (di halaman terakhir)
      const lastPageNumber = pdf.getNumberOfPages();
      pdf.setPage(lastPageNumber);
      
      const finalY = pdf.lastAutoTable.finalY + 10;
      pdf.setFont('helvetica', 'normal');
      pdf.setFontSize(10);
      
      pdf.text("Dibuat oleh,", 14, finalY);
      pdf.text("Diperiksa oleh,", 90, finalY);
      pdf.text("Disetujui oleh,", 150, finalY);

      const gapY = finalY + 30;
      pdf.line(14, gapY, 74, gapY);
      pdf.line(90, gapY, 140, gapY);
      pdf.line(150, gapY, 200, gapY);

      pdf.save(filename);
    }
  }
}
</script>

<style scoped>
/* Pastikan backdrop tidak menutupi modal */
.modal-backdrop {
  z-index: 1040 !important; /* Atur backdrop di bawah modal */
}

.modal {
  z-index: 1050 !important; /* Modal di atas backdrop */
}
</style>