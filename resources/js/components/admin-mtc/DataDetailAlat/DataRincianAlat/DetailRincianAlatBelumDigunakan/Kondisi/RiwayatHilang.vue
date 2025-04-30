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
                <th class="text-center" style="width: 10px; color: #000;">No Kehilangan</th>
                <th class="text-center" style="width: 10px; color: #000;">Tgl</th>
                <th class="text-center" style="width: 10px; color: #000;">Jenis Riwayat</th>
                <th class="text-center" style="width: 10px; color: #000;">Dipinjam Oleh</th>
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
                <td>{{ rynoseri.no_kehilangan }}</td>
                <td>{{ rynoseri.tgl_kehilangan || '-' }} <br> <small style="color: #444;"><i class="fas fa-clock"></i> {{ durasiData[index] !== '-' ? durasiData[index] + 'Hari' : '-' }}</small></td>
                <td>{{ rynoseri.kondisi || '-' }}</td>
                <td>{{ rynoseri.staff ? rynoseri.staff.nama_staff : '-' }}</td>
                <td>{{ rynoseri.detail_hilang || '-' }}</td>
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
import _ from 'lodash'; // pastikan lodash tersedia

export default {
  props: {
    noSeri: String,
  },
  data() {
    return {
      searchQuery: '',
      datanoseri: [], // harus array!
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
        if (noseri.tgl_kehilangan) {
          const tanggal = new Date(noseri.tgl_kehilangan);
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
      if (!Array.isArray(this.datanoseri)) return []; // perlindungan
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = this.currentPage * this.rowsPerPage;
      return this.datanoseri.slice(start, end);
    },
    filteredData() {
      if (
        this.searchQuery ||
        this.tanggalAwal ||
        this.tanggalAkhir ||
        this.tujuanDivisiFilter ||
        this.jenisFilter ||
        this.kondisiFilter
      ) {
        return this.paginatedData.filter(datanoseri => {
          const tanggalMatch = this.tanggalAwal && this.tanggalAkhir
            ? datanoseri.tgl_kehilangan >= this.tanggalAwal && datanoseri.tgl_kehilangan <= this.tanggalAkhir
            : true;

          const searchMatch = this.searchQuery
            ? (datanoseri.detail_hilang && datanoseri.detail_hilang.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
            (datanoseri.no_kehilangan && datanoseri.no_kehilangan.toLowerCase().includes(this.searchQuery.toLowerCase()))
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
        const response = await axios.get(`/api/v1/kehilangan/getHilang/${noSeri}`);
        // console.log('Respon dari backend:', response.data);

        // Penyesuaian jika backend mengirim data sebagai object { data: [...] }
        const result = response.data;
        if (Array.isArray(result)) {
          this.datanoseri = result;
        } else if (Array.isArray(result.data)) {
          this.datanoseri = result.data;
        } else {
          // console.warn('Response tidak sesuai format yang diharapkan.');
          this.datanoseri = [];
        }
      } catch (error) {
        console.error('Gagal fetch data:', error);
        this.datanoseri = [];
      }
    },
    debouncedFetchNoSeri: _.debounce(function () {
      this.fetchNoSeriAlat();
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
    this.fetchNoSeriAlat();
  }
}
</script>
