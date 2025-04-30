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

                <th @click="sortBy('no_peminjaman')" style="cursor: pointer; color: #000;">
                  No Permintaan
                  <i v-if="sortKey === 'no_peminjaman'" :class="sortOrder === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                </th>

                <th @click="sortBy('total')" style="cursor: pointer; color: #000;">
                  Total 
                  <i v-if="sortKey === 'total'" :class="sortOrder === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                </th>

                <!-- <th @click="sortBy('no_seri.no_seri')" style="cursor: pointer; color: #000;">
                  No Seri
                  <i v-if="sortKey === 'no_seri.no_seri'" :class="sortOrder === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                </th> -->

                <th @click="sortBy('tgl_pinjam')" style="cursor: pointer; color: #000;">
                  Tgl Permintaan
                  <i v-if="sortKey === 'tgl_pinjam'" :class="sortOrder === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                </th>

                <th @click="sortBy('staff.nama_staff')" style="cursor: pointer; color: #000;">
                  Diminta Oleh
                  <i v-if="sortKey === 'staff.nama_staff'" :class="sortOrder === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                </th>

                <th @click="sortBy('divisi')" style="cursor: pointer; color: #000;">
                  Divisi
                  <i v-if="sortKey === 'divisi'" :class="sortOrder === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                </th>

                <!-- <th @click="sortBy('tgl_kembali')" style="cursor: pointer; color: #000;">
                  Tgl Kembali
                  <i v-if="sortKey === 'tgl_kembali'" :class="sortOrder === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                </th> -->

                <!-- <th style="color: #000;">Durasi</th> -->

                <!-- <th @click="sortBy('no_seri.kondisi')" style="cursor: pointer; color: #000;">
                  Kondisi
                  <i v-if="sortKey === 'no_seri.kondisi'" :class="sortOrder === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                </th> -->

                <th @click="sortBy('status_kondisi')" style="cursor: pointer; color: #000;">
                  Status
                  <i v-if="sortKey === 'status_kondisi'" :class="sortOrder === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                </th>
                <th style="color: #000;">Aksi</th>
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
                <td>{{ rynoseri.no_permintaan || '-' }}</td>
                <td>{{ rynoseri.total || '-' }}</td>
                <!-- <td>
                  <div v-for="(noseri, i) in rynoseri.tools.no_seri" :key="i">
                    {{ noseri.no_seri }}
                  </div>
                </td> -->
                <td>{{ rynoseri.tgl_permintaan || '-' }}</td>
                <td>{{ rynoseri.kondisi || '-' }}</td>
                <td>{{ rynoseri.staff ? rynoseri.staff.nama_staff : '-' }}</td>
                <!-- <td>{{ rynoseri.tgl_kembali }}</td> -->
                <!-- <td>
                  {{ durasiData[index] !== '-' ? durasiData[index] + ' Hari' : '-' }} <br>
                  <small>
                    <i :class="{'fas fa-clock': !durasiDataKembali[index].includes('Hari Lebih'), 'fas fa-exclamation-circle text-danger': durasiDataKembali[index].includes('Hari Lebih')}"></i>
                    <span :class="{'text-danger': durasiDataKembali[index].includes('Hari Lebih')}">
                      {{ durasiDataKembali[index] }}
                    </span>
                  </small>
                </td> -->
                <!-- <td>
                  <div v-for="(noseri, i) in rynoseri.tools.no_seri" :key="i">
                    <div
                      class="status-pill parent-element"
                      :class="{
                        'status-active': noseri.kondisi === 'OK',
                        'status-error': noseri.kondisi === 'Error',
                        'status-rusak': noseri.kondisi === 'Rusak',
                        'status-musnah': noseri.kondisi === 'Musnah',
                        'status-hilang': noseri.kondisi === 'Hilang',
                      }"
                    >
                      {{ noseri.kondisi }}
                    </div>
                  </div>
                </td> -->
                <td>
                  <div
                    class="status-pill parent-element"
                    :class="{
                      'status-active': rynoseri.status_kondisi === 'Selesai',
                      'status-error': rynoseri.status_kondisi === 'Menunggu Diambil',
                      'status-rusak': rynoseri.status_kondisi === 'Ditolak',
                      'status-musnah': rynoseri.status_kondisi === 'Digunakan',
                      'status-hilang': rynoseri.status_kondisi === 'Belum Diproses',
                    }"
                  >
                    {{ rynoseri.status_kondisi || '-' }}
                  </div>
                </td>
                <td>
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
                    <a class="dropdown-item" @click="openDetailModal(rynoseri.no_seri)">
                      <i class="fas fa-eye text-info"></i> Detail
                    </a>
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

        <!-- Modal Detail No Seri -->
        <div v-if="isDetailModalOpen" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content" style="max-height: 90vh; overflow-y: auto;">
              <div class="modal-header">
                <h5 class="modal-title"><b>Detail No Seri</b></h5>
                <button type="button" class="close" @click="closeDetailModal">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <table class="table table-bordered">
                  <thead class="thead-light">
                    <tr>
                      <th>No Seri</th>
                      <th>Kondisi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in selectedNoSeri" :key="index">
                      <td>{{ item.no_seri }}</td>
                      <td>{{ item.kondisi }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" @click="closeDetailModal">Tutup</button>
              </div>
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
    kodeAlat: String,
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
      sortKey: '',
      sortOrder: 'asc',
      isDetailModalOpen: false,
      selectedNoSeri: [],
    }
  },
  computed: {
    durasiData() {
      return this.datanoseri.map(noseri => {
        if (noseri.tgl_permintaan) {
          const tanggal = new Date(noseri.tgl_permintaan);
          const tanggalTerkini = new Date();
          const durasi = tanggalTerkini - tanggal;
          const hari = Math.floor(durasi / (1000 * 60 * 60 * 24));
          return hari;
        }
        return '-';
      });
    },
    // durasiData() {
    //   return this.datanoseri.map(peminjamanalat => {
    //     if (peminjamanalat.tgl_kembali) {
    //       const tanggalPinjam = new Date(peminjamanalat.tgl_pinjam);
    //       const tanggalKembali = new Date(peminjamanalat.tgl_kembali);
    //       const selisihHari = Math.abs(tanggalKembali - tanggalPinjam);
    //       const hari = Math.ceil(selisihHari / (1000 * 60 * 60 * 24));
    //       return hari;
    //     }
    //   });
    // },
    // durasiDataKembali() {
    //   return this.datanoseri.map(peminjamanalat => {
    //     if (peminjamanalat.tgl_kembali) {
    //       const tanggalTerkini = new Date();
    //       const tanggalKembali = new Date(peminjamanalat.tgl_kembali);
    //       const selisihHari = Math.abs(tanggalKembali - tanggalTerkini);
    //       const hari = Math.ceil(selisihHari / (1000 * 60 * 60 * 24));

    //       // Jika tanggal terkininya kurang dari tanggal kembali
    //       if (tanggalTerkini < tanggalKembali) {
    //         return hari + ' Hari Lagi';
    //       } else {
    //         // Jika tanggal terkininya lebih dari tanggal kembali
    //         const excessDays = Math.ceil((tanggalTerkini - tanggalKembali) / (1000 * 60 * 60 * 24));
    //         return excessDays + ' Hari Lebih';
    //       }
    //     }
    //   });
    // },
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
            ? datanoseri.tgl_permintaan >= this.tanggalAwal && datanoseri.tgl_permintaan <= this.tanggalAkhir
            : true;

          const searchMatch = this.searchQuery
            ? (datanoseri.tools.no_seri.no_seri && datanoseri.tools.no_seri.no_seri.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
            (datanoseri.no_peminjaman && datanoseri.no_peminjaman.toLowerCase().includes(this.searchQuery.toLowerCase()))
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
        const kodeAlat = this.kodeAlat;
        // console.log(this.kodeAlat);
        const response = await axios.get(`/api/v1/permintaan/getPermintaan/${kodeAlat}`);
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
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortKey = key;
        this.sortOrder = 'asc';
      }

      this.datanoseri.sort((a, b) => {
        let aVal = _.get(a, key);
        let bVal = _.get(b, key);

        // Normalize undefined/null
        aVal = aVal === undefined || aVal === null ? '' : aVal;
        bVal = bVal === undefined || bVal === null ? '' : bVal;

        if (typeof aVal === 'string') aVal = aVal.toLowerCase();
        if (typeof bVal === 'string') bVal = bVal.toLowerCase();

        if (aVal < bVal) return this.sortOrder === 'asc' ? -1 : 1;
        if (aVal > bVal) return this.sortOrder === 'asc' ? 1 : -1;
        return 0;
      });
    },
    openDetailModal(noseriList) {
      this.selectedNoSeri = noseriList;
      this.isDetailModalOpen = true;
    },
    closeDetailModal() {
      this.isDetailModalOpen = false;
      this.selectedNoSeri = [];
    },
  },
  mounted() {
    this.fetchNoSeriAlat();
  }
}
</script>
<style>
  .btn-sts {
    border: 1px solid transparent;
    transition: background-color 0.3s ease, color 0.3s ease, border 0.3s ease;
    height: 25px;
    width: auto;
    border-radius: 10px;
    text-align: center;
    justify-content: center;
    align-items: center;
  }
</style>