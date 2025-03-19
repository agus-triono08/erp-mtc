<template>
  <div class="container-fluid">
    <!-- Loader -->
    <div class="loader" v-if="isLoading">
      <div class="loading-overlay">
        <div class="loading-spinner">
            <span class="sr-only">Loading...</span>          
        </div>
      </div>
    </div>
    <h1 class="h3 mb-4 mt-4 text-gray-900"><b>Riwayat</b></h1>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="kondisi-tab" data-toggle="tab" role="tab" aria-controls="kondisi" aria-selected="false" :class="{active: $route.name === 'data-riwayat-perkondisi'}" :to="{name: 'data-riwayat-perkondisi'}">Per Kondisi</router-link>
      </li>
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="peminjaman-tab" data-toggle="tab" role="tab" aria-controls="peminjaman" aria-selected="true" :class="{active: $route.name === 'data-riwayat-peminjaman'}" :to="{name: 'data-riwayat-peminjaman'}">Peminjaman</router-link>
      </li>
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="permintaan-tab" data-toggle="tab" role="tab" aria-controls="permintaan" aria-selected="false" :class="{active: $route.name === 'data-riwayat-permintaan'}" :to="{name: 'data-riwayat-permintaan'}">Permintaan</router-link>
      </li>
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="penggantian-tab" data-toggle="tab" role="tab" aria-controls="penggantian" aria-selected="false" :class="{active: $route.name === 'data-riwayat-penggantian'}" :to="{name: 'data-riwayat-penggantian'}">Penggantian Alat/Mesin Hilang</router-link>
      </li>
    </ul>
    <!-- <div class="col-md-12">
      <button 
        class="btn btn-show m-1"
        :class="{active: showAlat}"
        @click="toggleAlat"
      >
        <span v-if="showAlat">Alat</span>
        <span v-else>Alat</span>
      </button>
      <button 
        class="btn btn-show m-1"
        :class="{active: showMesin}"
        @click="toggleMesin"
      >
        <span v-if="showMesin">Mesin</span>
        <span v-else>Mesin</span>
      </button>
    </div> -->
    <div v-if="showAlat">
    <div class="row align-items-center justify-content-end mt-4 mb-2">
      <!-- Tombol Download Excel di samping kiri filter -->
      <div>
        <button @click="downloadExcel" class="btn btn-sm btn-primary-1 mr-2">
          <i class="fas fa-file-excel"></i> Export
        </button>
      </div>
      <!-- Filter -->
      <button
        class="btn btn-sm btn-primary-1"
        type="button"
        id="filterDropdown"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
      >
        <i class="fa fa-filter"></i> Filter
      </button>
      <div 
        class="dropdown-menu p-3"
        aria-labelledby="filterDropdown"
        style="border-radius: 8px; width: 250px;"
        @click.stop
      >
        <!-- <div>
          <label><b>Jenis</b></label>
          <div v-for="jenis in availableJenis" :key="jenis">
            <label><input type="checkbox" :value="jenis" v-model="jenisFilter"/>{{ jenis }}</label>
          </div>
        </div>
        <div>
          <label><b>Kondisi</b></label>
          <div v-for="kondisi in availablekondisi" :key="kondisi">
            <label><input type="checkbox" :value="kondisi" v-model="kondisiFilter"/>{{ kondisi }}</label>
          </div>
        </div> -->
        <div>
          <label><b>Tujuan Divisi</b></label>
          <div v-for="item in availabledivisi" :key="item">
            <label><input type="checkbox" :value="item" v-model="tujuanDivisiFilter"/>{{ item }}</label>
          </div>
        </div>
      </div>
      <!-- <div class="ml-2" style="border-radius: 5px;">
        <div class="input-group">
          <label class="mr-2" style="color: #000;"><b>Tujuan Divisi:</b></label>
          <select class="form-control" v-model="tujuanDivisiFilter" style="border-radius: 5px;">
            <option value="">Semua</option>
            <option v-for="(tujuan, index) in tujuanDivisiOptions" :key="index" :value="tujuan">{{ tujuan }}</option>
          </select>
          <label class="ml-2 mr-2" style="color: #000;"><b>Jenis:</b></label>
          <select class="form-control" v-model="jenisFilter" style="border-radius: 5px;">
            <option value="">Semua</option>
            <option v-for="(jenis, index) in jenisOptions" :key="index" :value="jenis">{{ jenis }}</option>
          </select>
          <label class="ml-2 mr-2" style="color: #000;"><b>Kondisi:</b></label>
          <select class="form-control" v-model="kondisiFilter" style="border-radius: 5px;">
            <option value="">Semua</option>
            <option v-for="(kondisi, index) in kondisiOptions" :key="index" :value="kondisi">{{ kondisi }}</option>
          </select>
        </div>
      </div>       -->
          <div class="search-wrapper col-3">
            <div class="input-group">
              <input type="text" placeholder="Search..." class="form-control"
                v-model="searchQuery"
                @input="debouncedFetchNoSeri"
              />            
            </div>
          </div>
        </div>
    <div class="row align-items-center justify-content-end mr-3 mt-4 mb-2">
      <div class="ml-2">
        <div class="input-group">
          <label class="mr-2" style="color: #000;"><b>Tanggal Awal:</b></label>
          <input type="date" class="form-control" v-model="tanggalAwal" style="border-radius: 5px;"/>
          <label class="ml-2 mr-2" style="color: #000;"><b>Tanggal Akhir:</b></label>
          <input type="date" class="form-control" v-model="tanggalAkhir" style="border-radius: 5px;">          
        </div>
      </div>
    </div>
        <div class="table-responsive p-3">
          <table class="table table-border no-border table-custom" style="overflow-x: auto;">
            <thead class="bg-table">
              <tr style="color: #000;" class="text-center">
                <th>#</th>
                <th @click="sortBy('tanggal')">
                  Tanggal
                  <i class="fas" :class="{'fa-sort-up': sortKey === 'tanggal' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'tanggal' && sortDirection === 'desc'}"></i>
                </th>
                <th @click="sortBy('PIC.nama_staff')">
                  Dipinjam Oleh
                  <i class="fas" :class="{'fa-sort-up': sortKey === 'PIC.nama_staff' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'PIC.nama_staff' && sortDirection === 'desc'}"></i>
                </th>
                <th @click="sortBy('tujuan')">
                  Tujuan Divisi
                  <i class="fas" :class="{'fa-sort-up': sortKey === 'tujuan' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'tujuan' && sortDirection === 'desc'}"></i>
                </th>
                <th @click="sortBy('no_pinjam')">
                  No Pinjam
                  <i class="fas" :class="{'fa-sort-up': sortKey === 'no_pinjam' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'no_pinjam' && sortDirection === 'desc'}"></i>
                </th>
                <th @click="sortBy('alat.nama_alat')">
                  Nama Alat/Mesin
                  <i class="fas" :class="{'fa-sort-up': sortKey === 'alat.nama_alat' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'alat.nama_alat' && sortDirection === 'desc'}"></i>
                </th>
                <th @click="sortBy('jumlah')">
                  Jumlah
                  <i class="fas" :class="{'fa-sort-up': sortKey === 'jumlah' && sortDirection === 'asc', 'fa-sort-down': sortKey === 'jumlah' && sortDirection === 'desc'}"></i>
                </th>
              </tr>
            </thead>
            <tbody v-if="filteredData.length===0">
              <tr class="text-center">
                <td colspan="8">Tidak Ada Data</td>
              </tr>
            </tbody>
            <tbody v-for="(riwayat, index) in filteredData" :key="index">
              <tr class="text-center">
                <td>{{ index + 1 }}</td>
                <td>{{ riwayat.tanggal || '-' }} <br> <small style="color: #444;"><i class="fas fa-clock"></i> {{ durasiData[index] !== '-' ? durasiData[index] + 'Hari' : '-' }}</small></td>                
                <td>{{ riwayat.PIC ? riwayat.PIC.nama_staff : '-' }}</td>     
                <!-- <td>{{ riwayat.pengguna ? riwayat.pengguna.divisi : '-' }}</td> -->
                <td>{{ riwayat.tujuan }}</td>
                <!-- <td>{{ riwayat.alat ? riwayat.alat.kode_alat : '-' }}</td>     -->
                <td>{{ riwayat.no_pinjam || '-' }}</td>            
                <!-- <td>
                  <div 
                    class="btn-sts"
                      :class="{'status-active': riwayat.noSeri.status === 'OK', 
                              'status-error': riwayat.noSeri.status === 'Error',
                              'status-rusak': riwayat.noSeri.status === 'Rusak',
                              'status-hilang': riwayat.noSeri.status === 'Hilang',
                              'status-dipinjam': riwayat.noSeri.status === 'Musnah',
                    }"
                  >{{ riwayat.noSeri ? riwayat.noSeri.status : '-' }}</div>
                  </td> -->
                  <td>{{ riwayat.alat ? riwayat.alat.nama_alat : '-' }}</td>
                  <td>{{ riwayat.jumlah }}</td>
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
    <div v-if="showMesin">

    </div>
  </div>
</template>
<script>
import axios from 'axios';
import * as XLSX from 'xlsx';  
export default {
  props: {
    noSeri: String,
  },
  data() {
    return {
      searchQuery: '',
      datariwayat: [
        {
        id: 1,
        id_alat: 101,
        id_pengguna: 1,
        no_seri: "ABC123456",
        no_pinjam: "P001",
        layout: "Layout 1",
        pic: 1,
        jumlah: 5,
        tanggal: "2025-02-20",
        PIC: { nama_staff: "John Doe" },
        noSeri: { status: "OK" },
        alat: { kode_alat: "ALAT001", nama_alat: "Alat 1" },
        pengguna: { divisi: "Engineering" },
        tujuan: "Engineering",
        jenis: "Tool",
        kondisi: "OK",
      },
      {
        id: 2,
        id_alat: 102,
        id_pengguna: 2,
        no_seri: "DEF789101",
        no_pinjam: "P002",
        layout: "Layout 2",
        pic: 2,
        jumlah: 10,
        tanggal: "2025-02-15",
        PIC: { nama_staff: "Jane Smith" },
        noSeri: { status: "Rusak" },
        alat: { kode_alat: "ALAT002", nama_alat: "Alat 2" },
        pengguna: { divisi: "Maintenance" },
        tujuan: "Maintenance",
        jenis: "Machine",
        kondisi: "Rusak",
      },
      {
        id: 3,
        id_alat: 103,
        id_pengguna: 3,
        no_seri: "GHI112233",
        no_pinjam: "P003",
        layout: "Layout 3",
        pic: 3,
        jumlah: 3,
        tanggal: "2025-02-18",
        PIC: { nama_staff: "Alice Johnson" },
        noSeri: { status: "Error" },
        alat: { kode_alat: "ALAT003", nama_alat: "Alat 3" },
        pengguna: { divisi: "IT" },
        tujuan: "IT Support",
        jenis: "Machine",
        kondisi: "Error",
      },
      {
        id: 4,
        id_alat: 104,
        id_pengguna: 4,
        no_seri: "JKL345678",
        no_pinjam: "P004",
        layout: "Layout 4",
        pic: 4,
        jumlah: 7,
        tanggal: "2025-02-22",
        PIC: { nama_staff: "Bob Lee" },
        noSeri: { status: "Musnah" },
        alat: { kode_alat: "ALAT004", nama_alat: "Alat 4" },
        pengguna: { divisi: "Logistics" },
        tujuan: "Logistics",
        jenis: "Tool",
        kondisi: "Musnah",
      },
      {
        id: 5,
        id_alat: 105,
        id_pengguna: 5,
        no_seri: "MNO567890",
        no_pinjam: "P005",
        layout: "Layout 5",
        pic: 5,
        jumlah: 2,
        tanggal: "2025-02-10",
        PIC: { nama_staff: "Charlie Brown" },
        noSeri: { status: "Hilang" },
        alat: { kode_alat: "ALAT005", nama_alat: "Alat 5" },
        pengguna: { divisi: "HR" },
        tujuan: "HR Department",
        jenis: "Machine",
        kondisi: "Hilang",
      },
      ],
      rowsPerPage: 10,
      currentPage: 1,
      tanggalAwal: '',
      tanggalAkhir: '',
      tujuanDivisiFilter: [],
      // jenisFilter: '',
      kondisiFilter: [],
      divisiFilter: [],
      tujuanDivisiOptions: [],
      jenisOptions: [],
      kondisiOptions: [],
      showAlat: true,
      showMesin: false,
      jenisFilter: [],
      sortKey: '',
      sortDirection: 'asc',
      isLoading: false,
      }
  },
  computed: {
    availableJenis() {
      return [...new Set(this.datariwayat.map(item=>item.jenis))];
    },
    availablekondisi() {
      return [...new Set(this.datariwayat.map(item=>item?.noSeri?.status))];
    },
    availabledivisi() {
      return [...new Set(this.datariwayat.map(item=>item.tujuan))];
    },
    durasiData() {
      return this.datariwayat.map(noseri => {
        if (noseri.tanggal) {
          const tanggal = new Date(noseri.tanggal);
          const tanggalTerkini = new Date();
          const durasi = tanggalTerkini - tanggal;
          const hari = Math.floor(durasi / (1000 * 60 * 60 * 24));
          return hari;
        }
        return '-';
      });
    },
    totalPages() {
      return Math.ceil(this.datariwayat.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(this.currentPage * this.rowsPerPage, this.datariwayat.length);
      return `Showing ${start} to ${end} of ${this.datariwayat.length} entries`;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = this.currentPage * this.rowsPerPage;
      return this.datariwayat.slice(start, end);
    },
    filteredData() {
      if (this.searchQuery || this.tanggalAwal || this.tanggalAkhir || this.tujuanDivisiFilter || this.jenisFilter || this.kondisiFilter) {
        return this.paginatedData.filter(datariwayat => {
          const tanggalMatch = this.tanggalAwal && this.tanggalAkhir
            ? datariwayat.tanggal >= this.tanggalAwal && datariwayat.tanggal <= this.tanggalAkhir
            : true;
          const searchMatch = this.searchQuery
            ? (
              (datariwayat.no_seri && datariwayat.no_seri.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
              (datariwayat.tujuan && datariwayat.tujuan.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
              (datariwayat.nama_peminjam && datariwayat.nama_peminjam.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
              (datariwayat.staff && datariwayat.staff.nama_staff && datariwayat.staff.nama_staff.toLowerCase().includes(this.searchQuery.toLowerCase()))
            )
            : true;
          const tujuanDivisiMatch = this.tujuanDivisiFilter.length
            ? this.tujuanDivisiFilter.includes(datariwayat.tujuan)
            : true;
          const jenisMatch = this.jenisFilter.length
            ? this.jenisFilter.includes(datariwayat.jenis)
            : true;
          const kondisiMatch = this.kondisiFilter.length
            ? this.kondisiFilter.includes(datariwayat?.noSeri?.status)
            : true;
          return tanggalMatch && searchMatch && tujuanDivisiMatch && jenisMatch && kondisiMatch;
        }).sort((a, b) => {
          if (this.sortKey === 'alat.nama_alat') {
            const namaAlatA = a.alat ? a.alat.nama_alat : '';
            const namaAlatB = b.alat ? b.alat.nama_alat : '';
            return this.sortDirection === 'asc' ? namaAlatA.localeCompare(namaAlatB) : namaAlatB.localeCompare(namaAlatA);
          } else if (this.sortKey === 'PIC.nama_staff') {
            const picA = a.PIC ? a.PIC.nama_staff : '';
            const picB = b.PIC ? b.PIC.nama_staff : '';
            return this.sortDirection === 'asc' ? picA.localeCompare(picB) : picB.localeCompare(picA);
          } else {
            if (this.sortDirection === 'asc') {
              return a[this.sortKey] > b[this.sortKey] ? 1 : -1;
            } else {
              return a[this.sortKey] < b[this.sortKey] ? 1 : -1;
            }
          }
        });
      } else {
        return this.datariwayat.sort((a, b) => {
          if (this.sortKey === 'alat.nama_alat') {
            const namaAlatA = a.alat ? a.alat.nama_alat : '';
            const namaAlatB = b.alat ? b.alat.nama_alat : '';
            return this.sortDirection === 'asc' ? namaAlatA.localeCompare(namaAlatB) : namaAlatB.localeCompare(namaAlatA);
          } else if (this.sortKey === 'PIC.nama_staff') {
            const picA = a.PIC ? a.PIC.nama_staff : '';
            const picB = b.PIC ? b.PIC.nama_staff : '';
            return this.sortDirection === 'asc' ? picA.localeCompare(picB) : picB.localeCompare(picA);
          } else {
            if (this.sortDirection === 'asc') {
              return a[this.sortKey] > b[this.sortKey] ? 1 : -1;
            } else {
              return a[this.sortKey] < b[this.sortKey] ? 1 : -1;
            }
          }
        });
      }
    }
  },
  methods: {
    async fetchNoSeriAlat() {
      this.isLoading = true;
      try {
        const response = await axios.get(`/api/riwayat/alats`);
        this.datariwayat = response.data.data.map((riwayat)=> ({
          id: riwayat.id,
          id_alat: riwayat.id_alat,
          id_pengguna: riwayat.id_pengguna,
          no_seri: riwayat.id_no_seri_alat,
          layout: riwayat.id_layout,
          pic: riwayat.id_staff,
          jumlah: riwayat.jumlah,
          tanggal: riwayat.tanggal,
          PIC: riwayat.staff,
          noSeri: riwayat.no_seri_alat,
          alat: riwayat.alat,
          pengguna: riwayat.pengguna,
        }));
        this.tujuanDivisiOptions = [...new Set(this.datariwayat.map(datariwayat => datariwayat.tujuan))];
        this.jenisOptions = [...new Set(this.datariwayat.map(datariwayat => datariwayat.jenis))];
        this.kondisiOptions = [...new Set(this.datariwayat.map(datariwayat => datariwayat.kondisi))];
        console.log(this.datariwayat);
      } catch (error) {
        console.error(error);
      } finally {
        this.isLoading = false; // Hilangkan loader
      }
    },
    debouncedFetchNoSeri: _.debounce(function () {
      this.fetchNoSeriAlat();
    }, 300),
    prevPage () {
      if (this.currentPage > 1) {
        this.currentPage--;
      }
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
      }
    },
    toggleAlat() {
      this.showMesin = false;
      this.showAlat = !this.showAlat;
    },
    toggleMesin() {
      this.showAlat = false;
      this.showMesin = !this.showMesin;
    },
    sortTanggal(order) {
      this.datariwayat.sort((a, b) => {
        const dateA = new Date(a.tanggal); // Mengonversi tanggal menjadi objek Date
        const dateB = new Date(b.tanggal); // Mengonversi tanggal menjadi objek Date

        if (order === 'asc') {
          return dateA - dateB; // Urutkan berdasarkan tanggal, ascending
        } else {
          return dateB - dateA; // Urutkan berdasarkan tanggal, descending
        }
      });
    },
    sortBy(key) {
      this.sortKey = key
      this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc'
    },
    // Metode untuk mendownload data ke Excel
    downloadExcel() {
      // Memastikan data yang akan diekspor sudah ada
      if (this.filteredData.length === 0) {
        alert("Tidak ada data untuk di-download");
        return;
      }

      // Menyiapkan data yang akan dikonversi
      const data = this.filteredData.map(item => ({
        Tanggal: item.tanggal,
        'Dipinjam Oleh': item?.PIC?.nama_staff,
        'Tujuan Divisi': item.tujuan,
        'No Pinjam': item.no_pinjam,
        'Nama Alat/Mesin': item?.alat?.nama_alat,        
        Jumlah: item.jumlah,
      }));

      // Mengonversi data ke dalam format sheet Excel
      const ws = XLSX.utils.json_to_sheet(data);
      
      // Membuat workbook dari sheet yang sudah dibuat
      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, 'Riwayat Peminjaman');

      // Menyimpan file Excel dengan nama yang ditentukan
      XLSX.writeFile(wb, 'riwayat-peminjaman.xlsx');
    },
  },
  mounted() {
    this.fetchNoSeriAlat();
  }
}
</script>
<style>
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.5);
  z-index: 1000;
  display: flex;
  justify-content: center;
  align-items: center;
}

.loading-spinner {
  display: inline-block;
  width: 100px;
  height: 100px;
  border: 10px solid rgba(220, 53, 69, 0.1);
  border-top-color: #dc3545;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>