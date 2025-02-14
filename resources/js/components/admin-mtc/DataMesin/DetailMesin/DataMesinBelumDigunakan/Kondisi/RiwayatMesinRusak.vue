<template>
  <div class="container-fluid">
    <div class="row align-items-center justify-content-end mr-3 mt-4 mb-2">          
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
            <thead>
              <tr class="bg-table text-center">
                <th class="text-center" style="width: 10px; color: #000;">#</th>
                <th class="text-center" style="width: 10px; color: #000;">Tgl</th>
                <th class="text-center" style="width: 10px; color: #000;">Jenis Riwayat</th>
                <th class="text-center" style="width: 10px; color: #000;">PIC</th>
                <th class="text-center" style="width: 10px; color: #000;">Detail</th>
              </tr>
            </thead>
            <tbody v-if="filteredData.length===0">
              <tr class="text-center">
                <td colspan="8">Tidak Ada Data</td>
              </tr>
            </tbody>
            <tbody v-for="(rynoseri, index) in filteredData" :key="index">
              <tr class="text-center">
                <td>{{ index + 1 }}</td>
                <td>{{ rynoseri.rusak ? rynoseri.rusak.tanggal_kerusakan : '-' }} <br> <small style="color: #444;"><i class="fas fa-clock"></i> {{ durasiData[index] !== '-' ? durasiData[index] + 'Hari' : '-' }}</small></td>
                <td>{{ rynoseri.rusak && rynoseri.rusak.noseri ? rynoseri.rusak.noseri.status : '-' }}</td>
                <td>{{ rynoseri.rusak && rynoseri.rusak.staff ? rynoseri.rusak.id_staff_kerusakan.nama_staff : '-' }}</td>
                <td>{{ rynoseri.rusak ? rynoseri.rusak.deskripsi_kerusakan : '-' }}</td>
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
</template>
<script>
import axios from 'axios';

export default {
  props: {
    noSeri: String,
  },
  data() {
    return {
      searchQuery: '',
      datanoseri: [],
      rowsPerPage: 10,
      currentPage: 1,
      tanggalAwal: '',
      tanggalAkhir: '',
      tujuanDivisiFilter: '',
      jenisFilter: '',
      kondisiFilter: '',
      tujuanDivisiOptions: [],
      jenisOptions: [],
      kondisiOptions: [],
      }
  },
  computed: {
    durasiData() {
      return this.datanoseri.map(noseri => {
        if (noseri.rusak && noseri.rusak.tanggal_kerusakan) {
          const tanggal = new Date(noseri.rusak && noseri.rusak.tanggal_kerusakan);
          const tanggalTerkini = new Date();
          const durasi = tanggalTerkini - tanggal;
          const hari = Math.floor(durasi / (1000 * 60 * 60 * 24));
          return hari;
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
      const end = this.currentPage * this.rowsPerPage;
      return this.datanoseri.slice(start, end);
    },
    filteredData() {
      if (this.searchQuery || this.tanggalAwal || this.tanggalAkhir || this.tujuanDivisiFilter || this.jenisFilter || this.kondisiFilter) {
        return this.paginatedData.filter(datanoseri => {
          const tanggalMatch = this.tanggalAwal && this.tanggalAkhir
            ? datanoseri.rusak && datanoseri.rusak.tanggal_rusak >= this.tanggalAwal && datanoseri.error && datanoseri.error.tanggal_error <= this.tanggalAkhir
            : true;
          const searchMatch = this.searchQuery
            ? (
              (datanoseri.no_seri && datanoseri.no_seri.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
              (datanoseri.tujuan && datanoseri.tujuan.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
              (datanoseri.nama_peminjam && datanoseri.nama_peminjam.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
              (datanoseri.staff && datanoseri.staff.nama_staff && datanoseri.staff.nama_staff.toLowerCase().includes(this.searchQuery.toLowerCase()))
            )
            : true;
          const tujuanDivisiMatch = this.tujuanDivisiFilter
            ? datanoseri.tujuan === this.tujuanDivisiFilter
            : true;
          const jenisMatch = this.jenisFilter
            ? datanoseri.jenis === this.jenisFilter
            : true;
          const kondisiMatch = this.kondisiFilter
            ? datanoseri.kondisi === this.kondisiFilter
            : true;
          return tanggalMatch && searchMatch && tujuanDivisiMatch && jenisMatch && kondisiMatch;
        });
      } else {
        return this.paginatedData;
      }
    }
  },
  methods: {
    async fetchNoSeriAlat() {
      try {
        const noSeri = this.noSeri;
        const response = await axios.get(`/api/no-seri/belum-digunakan/${noSeri}/riwayat`);
        this.datanoseri = response.data;
        this.tujuanDivisiOptions = [...new Set(this.datanoseri.map(datanoseri => datanoseri.tujuan))];
        this.jenisOptions = [...new Set(this.datanoseri.map(datanoseri => datanoseri.jenis))];
        this.kondisiOptions = [...new Set(this.datanoseri.map(datanoseri => datanoseri.kondisi))];
        //console.log(this.datanoseri);
      } catch (error) {
        console.error(error);
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
  },
  mounted() {
    this.fetchNoSeriAlat();
  }
}
</script>