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
    <!-- Head -->
    <div class="row mb-2 align-items-center">
      <div class="col-sm-6">
        <h3 class="text-black-10 mt-5" style="font-family: Raleway; color: #000;"><b>Jadwal Perawatan Alat/Mesin</b></h3>        
      </div>
    </div>
    <ul id="pills-tab" role="tablist" class="nav nav-pills mb-3" style="margin-top: 1rem !important;">
      <li role="presentation" class="nav-item">
        <router-link id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true" class="nav-link" :class="{ active: $route.name === 'tabel-jadwal-perawatan' }" :to="{ name: 'tabel-jadwal-perawatan' }">Tabel</router-link>
      </li>
      <li role="presentation" class="nav-item">
        <router-link id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false" class="nav-link" :class="{ active: $route.name === 'kalender-perawatan-bulan' }" :to="{ name: 'kalender-perawatan-bulan' }">Kalender</router-link>
      </li>
    </ul>
    <div class="row mb-2 mt-2 align-items-center">
      <div class="col-sm-6">
        <h5 style="color: #000;"><b>{{ currentMonth }} {{ currentYear }}</b></h5>
      </div>      
    </div>
    <div class="row align-items-center justify-content-end mr-3">
      <button @click="showModalTambah" class="btn btn-sm btn-primary mr-2">Tambah</button>
      <button @click="exportToExcel" class="btn btn-sm btn-success mr-2">Export</button>
      <div class="search-wrapper">
        <div class="input-group">
          <input type="text" placeholder="Search..." class="form-control"
            v-model="search"
          />
        </div>
      </div>        
    </div>
    <!-- Perencanaan -->
    <div class="col-12 table-responsive p-3">
      <h5 style="color: #000;"><b>Perencanaan</b></h5>
      <table class="table table-custom has-text-centered is-bordered" style="white-space: nowrap" id="export_table">
        <thead class="bg-table">
          <tr style="color: #000;">
            <th rowspan="2">No Perawatan</th>
            <th rowspan="2">Nama Alat/Mesin</th>
            <th rowspan="2">No Seri</th>
            <th :colspan="last_date">Tanggal</th>
          </tr>
          <tr>
            <th v-for="i in Array.from(Array(last_date).keys())" :key="i" style="color: #000;" class="text-center">
              {{ i + 1 }}
            </th>
          </tr>
        </thead>
        <tbody v-if="filteredJadwalPerawatan.length > 0">
          <tr v-for="item in filteredJadwalPerawatan.slice((currentPage - 1) * perPage, currentPage * perPage)" :key="item.id">
            <td>{{ item.no_perawatan }}</td>
            <td>{{ item.no_seri.tools.nama }}</td>
            <td>{{ item.no_seri.no_seri }}</td>
            <!-- <td v-for="i in Array.from(Array(last_date).keys())" :key="i" :style="{ backgroundColor: weekend_date.indexOf(i + 1) !== -1 ? 'black' : isDate(item.tgl_perawatan, i + 1) ? 'yellow' : '' }">
            </td> -->
            <td v-for="i in Array.from(Array(last_date).keys())" :key="i" :style="{ backgroundColor: getBackgroundColor(item, i + 1) }"></td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td :colspan="last_date + 3">Tidak ada data</td>
          </tr>
        </tbody>
      </table>      
    </div>
    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3 mb-3" style="border-radius: 10px; background-color: #f3f4f6; height: 50px; color: #000;">
      <div class="ml-3">
        Rows per page:
        <span>{{ perPage }}</span>
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
    <!-- Pelaksanaan  -->
    <div class="row align-items-center justify-content-end mr-3">      
      <div class="search-wrapper">
        <div class="input-group">
          <input type="text" placeholder="Search..." class="form-control"
            v-model="search"
          />
        </div>
      </div>        
    </div>
    <div class="col-12 table-responsive p-3">
      <h5 style="color: #000;"><b>Pelaksanaan</b></h5>
      <table class="table table-custom has-text-centered is-bordered" style="white-space: nowrap" id="export_table">
        <thead class="bg-table">
          <tr style="color: #000;">
            <th rowspan="2">No Perawatan</th>
            <th rowspan="2">Nama Alat/Mesin</th>
            <th rowspan="2">No Seri</th>
            <th rowspan="2" class="text-center">Rentang Waktu</th>
            <th rowspan="2">Progres</th>
            <th rowspan="2">Selesai</th>
            <th rowspan="2">Aksi</th>
            <th :colspan="last_date">Tanggal</th>
          </tr>
          <tr>
            <th v-for="i in Array.from(Array(last_date).keys())" :key="i" style="color: #000;" class="text-center">
              {{ i + 1 }}
              <!-- Menampilkan nama PIC jika ada -->
              <!-- <div v-if="picPerTanggal[i + 1]" style="font-size: 12px; color: #000;">
                {{ picPerTanggal[i + 1] }}
              </div> -->
            </th>
          </tr>
        </thead>
        <tbody v-if="filteredJadwalPerawatan.length > 0">
          <tr v-for="(item, index) in filteredJadwalPerawatan.slice((currentPage - 1) * perPage, currentPage * perPage)" :key="item.id">
            <td>{{ item.no_perawatan }}</td>
            <td>{{ item.no_seri.tools.nama }}</td>
            <td>{{ item.no_seri.no_seri }}</td>
            <td class="text-center">{{ getTanggalStart(item) | formatDate }} - {{ getTanggalEnd(item) | formatDate }} <br>
              <small>{{ durasiDataPerawatan[index].status }}</small>
            </td>
            <td class="text-center">
              <i class="fas fa-check" v-if="item.status === 'Pelaksanaan'" style="color: green;"></i>
              <!-- <i class="fas fa-times" v-if="item.status === 'Belum Selesai'" style="color: red;"></i> -->
            </td>
            <td class="text-center">
              <i class="fas fa-check" v-if="item.status === 'Selesai'" style="color: green;"></i>
              <!-- <i class="fas fa-times" v-if="item.status === 'Belum Selesai'" style="color: red;"></i> -->
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
                  <a class="dropdown-item" @click="showModalEdit(item)" v-if="item.status !== 'Pelaksanaan' && item.status !== 'Selesai'">
                    <i class="bi bi-clock text-info"></i> Pelaksanaan
                  </a>
                  <a class="dropdown-item" @click="showModalSelesai(item)" v-if="item.status === 'Pelaksanaan' || item.status === 'Selesai'">
                    <i class="bi bi-check text-success"></i> Selesai
                  </a>
                </div>
              </div>
            </td>
            <td v-for="i in Array.from(Array(last_date).keys())" :key="i" :style="{ backgroundColor: getBackgroundColor(item, i + 1) }">
              <div v-if="picPerTanggal[item.id] && picPerTanggal[item.id][i + 1]" style="font-size: 12px; color: #000;">
                {{ picPerTanggal[item.id][i + 1] }}
              </div>
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td :colspan="last_date + 3">Tidak ada data</td>
          </tr>
        </tbody>
      </table>      
    </div>
    <!-- Pagination -->
    <div class="table-responsive d-flex justify-content-between align-items-center mt-3 mb-3" style="border-radius: 10px; background-color: #f3f4f6; height: 50px; color: #000;">
      <div class="ml-3">
        Rows per page:
        <span>{{ perPage }}</span>
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
    <!-- Modal untuk Tambah Data -->
    <div v-if="isModalTambahOpen" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content" style="max-height: 90vh; overflow-y: auto;">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Jadwal Perawatan</h5>
            <button type="button" class="close" @click="closeModalTambah">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="tools" style="color: #000;">
                  <b>Tanggal Perawatan</b>
                  <sup style="color: red;"> *</sup>
                </label>
                <!-- <input v-model="tglPerawatan" type="date" class="form-control" :max="dateFormatter()" :min="minDateFormatter()"> -->
                <input v-model="tglPerawatan" type="date" class="form-control" :min="minDateFormatter()" :max="dateFormatter()">
              </div>
              <div class="form-group">
                <label for="tools" style="color: #000;">
                  <b>Alat/Mesin</b>
                  <sup style="color: red;"> *</sup>
                </label>
                <select v-model="selectedToolId" @change="getNoSeri" class="form-control">
                  <option disabled value="">-- Pilih Tool --</option>
                  <option v-for="tool in tools" :key="tool.id" :value="tool.id">
                    {{ tool.nama }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label for="tools" style="color: #000;">
                  <b>No Seri</b>
                  <sup style="color: red;"> *</sup>
                </label>
                <select v-model="selectedNoSeriId" class="form-control">
                  <option disabled value="">-- Pilih No Seri --</option>
                  <option v-for="seri in noSeriList" :key="seri.id" :value="seri.id">
                    {{ seri.no_seri }}
                  </option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" @click="closeModalTambah">Batal</button>
            <button type="button" class="btn btn-primary" @click="submitPerawatan">Simpan</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal untuk Edit Data -->
    <div v-if="isModalEditOpen" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Jadwal Perawatan</h5>
            <button type="button" class="close" @click="closeModalEdit">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <!-- <div class="form-group">
                <label>Tanggal Pengerjaan</label>
                <input v-model="tanggal_perawatan" type="date" class="form-control">
              </div>
              <div class="form-group">
                <label>Waktu (Mulai - Selesai)</label>
                <div class="input-group">
                  <input type="time" class="form-control" id="waktu_mulai" v-model="waktu_mulai" required>
                  <span class="input-group-text">-</span>
                  <input type="time" class="form-control" id="waktu_selesai" v-model="waktu_selesai" required>
                </div>
              </div> -->
              <!-- <div class="form-group">
                <label>Tanggal Start</label>
                <input v-model="tanggal_start" type="date" class="form-control">
              </div> -->
              <div class="form-group">
                <label>PIC</label>
                <v-select
                  :options="picOptions"
                  v-model="jadwalPerawatan.pic"
                  multiple
                  label="text"
                  :reduce="(pic) => pic.value"
                />
              </div>
              <!-- <div class="form-group">
                <label>Keterangan Perawatan</label>
                <textarea v-model="detail" class="form-control"></textarea>
              </div>
              <div class="form-group">
                <label>Kondisi</label>
                <select class="form-control" id="kondisi" v-model="kondisi" required>
                  <option value="">Pilih Kondisi</option>
                  <option value="OK">OK</option>
                  <option value="Rusak">Rusak</option>
                  <option value="Error">Error</option>
                </select>
              </div> -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" @click="closeModalEdit">Batal</button>
            <button type="button" class="btn btn-primary" @click="simpanJadwalPerawatan" v-if="jadwalPerawatan.status !== 'Selesai'">Simpan</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal untuk Selesai -->
    <div v-if="isModalSelesaiOpen" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Selesai Jadwal Perawatan</h5>
            <button type="button" class="close" @click="closeModalSelesai">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>              
              <!-- <div class="form-group">
                <label>Tanggal End</label>
                <input v-model="tanggal_end" type="date" class="form-control">
              </div> -->
              <div class="form-group">
                <label>Keterangan Perawatan</label>
                <textarea v-model="keterangan_perawatan" class="form-control"></textarea>
              </div>
              <div class="form-group">
                <label>Kondisi</label>
                <select class="form-control" id="kondisi" v-model="kondisi" required>
                  <option value="">Pilih Kondisi</option>
                  <option value="OK">OK</option>
                  <option value="Rusak">Rusak</option>
                  <option value="Error">Error</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" @click="closeModalSelesai">Batal</button>
            <button type="button" class="btn btn-primary" @click="simpanSelesai">Simpan</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from 'vue-select';
import Swal from 'sweetalert2';
import { formatDate } from '@fullcalendar/core';
import axios from 'axios';

export default {
  components: {
    vSelect,
  },
  data() {
    return {
      form: [],
      getOnlyTanggal: [],
      tools: [],
      noSeriList: [],
      selectedToolId: '',
      selectedNoSeriId: '',
      tglPerawatan: '',
      picOptions: [
        { text: 'John Doe', value: 'John Doe' },
        { text: 'Jane Doe', value: 'Jane Doe' },
        { text: 'Bob Smith', value: 'Bob Smith' },
        { text: 'Alice Johnson', value: 'Alice Johnson' },
      ],
      jadwalPerawatan: [
        // {          
        //   id: 1,
        //   no_perawatan: 'R-01',
        //   nama_alat: 'Bor',
        //   no_seri: 'B-01',
        //   tanggal_perawatan: '6',
        //   tanggal_start: '',
        //   tanggal_end: '',
        //   waktu_mulai: '',
        //   waktu_selesai: '',
        //   pic: '',
        //   detail: '',
        //   kondisi: '',
        //   status: 'Belum Selesai'
        // },
        // {          
        //   id: 2,
        //   no_perawatan: 'R-02',
        //   nama_alat: 'Bor',
        //   no_seri: 'B-02',
        //   tanggal_perawatan: '20',
        //   tanggal_start: '',
        //   tanggal_end: '',
        //   waktu_mulai: '',
        //   waktu_selesai: '',
        //   pic: '',
        //   detail: '',
        //   kondisi: '',
        //   status: 'Belum Selesai'
        // },
        // {                    
        //   id: 3,
        //   no_perawatan: 'R-03',
        //   nama_alat: 'Bor',
        //   no_seri: 'B-03',
        //   tanggal_perawatan: '19',
        //   tanggal_start: '',
        //   tanggal_end: '',
        //   waktu_mulai: '',
        //   waktu_selesai: '',
        //   pic: '',
        //   detail: '',
        //   kondisi: '',
        //   status: 'Belum Selesai'
        // },
      ],
      status: '',
      currentPage: 1,
      pages: [],
      perPage: 10, // jumlah data per halaman
      search: '',
      tanggal: null,
      tanggal_start: '',
      tanggal_end: '',
      nama_alat: '',
      no_seri: '',
      isModalTambahOpen: false,
      isModalEditOpen: false,
      isModalSelesaiOpen: false,
      modalTitle: 'Tambah Jadwal Perawatan',
      isLoading: false,
      picPerTanggal: {},
    }
  },
  computed: {
    durasiData() {
      return this.jadwalPerawatan.map(item => {
        if (item.tanggal_perawatan) {
          const tanggalStart = new Date(item.tanggal_start);
          const tanggalEnd = new Date(item.tanggal_end);
          return {
            tanggalStart,
            tanggalEnd
          };
        }
      })
    },
    // durasiDataPerawatan() {
    //   return this.jadwalPerawatan.map(item => {
    //     if (item.tanggal_perawatan) {
    //       const tanggalStart = new Date(item.tanggal_start);
    //       const tanggalEnd = new Date(item.tanggal_end);
    //       const tanggalPerawatan = new Date(item.tanggal_perawatan);

    //       if (tanggalPerawatan < tanggalStart) {
    //         return {
    //           status: 'Maju',
    //         };
    //       }
    //     }
    //   })
    // },
    durasiDataPerawatan() {
      return this.jadwalPerawatan.map((item, index) => {
    if (item.tanggal_perawatan) {
      // const tanggalStart = item.tanggal_start ? item.tanggal_start : null;
      const tanggalStart = item.tanggal_start;
      const tanggalEnd = item.tanggal_end ? item.tanggal_end : null;
      const tanggalPerawatan = item.tanggal_perawatan;
      let tanggalNow = this.getOnlyTanggal[index] ? this.getOnlyTanggal[index] : null
      // console.log(this.getOnlyTanggal);
      if (!tanggalStart && !tanggalEnd) {
        return {
          status: '-',
        };
      }

      // Jika tanggal_start lebih besar dari tanggal_perawatan, berarti telat
      if (tanggalEnd && tanggalNow > tanggalPerawatan) {
        return {
          status: 'Telat',
        };
      }

      // Jika tanggal_end lebih kecil dari tanggal_perawatan, berarti maju
      if (tanggalEnd && tanggalNow < tanggalPerawatan) {
        return {
          status: 'Maju',
        };
      }

      // Jika tidak ada kondisi yang memenuhi, statusnya -
      return {
        status: 'Sesuai',
      };
    } else {
      return {
        status: '-',
        tanggalPerawatan: 'Tidak ada data',
      };
    }
  });
},

    // durasiDataPerawatan() {
    //   return this.jadwalPerawatan.map(item => {
    //     if (item.tanggal_perawatan) {
    //       const tanggalStart = item.tanggal_start ? new Date(item.tanggal_start).getTime() : null;
    //       const tanggalEnd = item.tanggal_end ? new Date(item.tanggal_end).getTime() : null;
    //       const tanggalPerawatan = new Date(item.tanggal_perawatan).getTime();

    //       if (tanggalStart && tanggalEnd) {
    //         if (tanggalPerawatan >= tanggalStart && tanggalPerawatan <= tanggalEnd) {
    //           return {
    //             status: 'Maju',
    //           };
    //         } else if (tanggalPerawatan > tanggalStart) {
    //           return {
    //             status: 'Telat',
    //           };
    //         } else {
    //           return {
    //             status: '-',
    //           };
    //         }
    //       } else {
    //         return {
    //           status: '-',
    //           tanggalPerawatan: 'Tidak ada data'
    //         };
    //       }
    //     } else {
    //       return {
    //         status: '-',
    //         tanggalPerawatan: 'Tidak ada data'
    //       };
    //     }
    //   });
    // },
    currentMonth() {
      const date = new Date();
      const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      return monthNames[date.getMonth()];
    },
    currentYear() {
      const date = new Date();
      return date.getFullYear();
    },
    last_date() {
      // Fixing last_date calculation to be more robust.
      const date = new Date(this.currentYear, this.getMonthNumber(this.currentMonth) + 1, 0);
      return date.getDate(); // Returns the last day of the current month
    },
    weekend_date() {
      const date = new Date(this.currentYear, this.getMonthNumber(this.currentMonth), 1); // Tanggal 1 bulan ini
      const weekendDates = [];
      
      for (let i = 0; i < this.last_date; i++) {
        date.setDate(i + 1); // Mengatur tanggal hari ini
        // Menyimpan hari Sabtu (6) dan Minggu (0)
        if (date.getDay() === 0 || date.getDay() === 6) {
          weekendDates.push(i + 1);
        }
      }
      return weekendDates;
    },
    filteredJadwalPerawatan() {
      const search = this.search.toLowerCase();
      return this.jadwalPerawatan.filter((item) => {
        return item.no_seri.no_seri.toLowerCase().includes(search) || item.no_seri.tools.nama.toLowerCase().includes(search) || item.no_perawatan.toLowerCase().includes(search);
      });
    },
    totalPages() {
      return Math.ceil(this.filteredJadwalPerawatan.length / this.perPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.perPage + 1;
      const end = this.currentPage * this.perPage;
      return `${start} - ${end} dari ${this.filteredJadwalPerawatan.length}`;
    },
  },
  methods: {
    async fetchDataFrom() {
      try {
        const response = await axios.get('/api/v1/perawatan')
        this.jadwalPerawatan = response.data;
        // console.log(this.jadwalPerawatan);
      } catch (error) {
        console.error(error);
      }
    },
    getTanggalStart(item) {
      if (item.tanggal_start) {
        return new Date(item.tanggal_start);
      }
      return null;
    },

    getTanggalEnd(item) {
      if (item.tanggal_end) {
        return new Date(item.tanggal_end);
      }
      return null;
    },
    // isDate(tanggal, hari) {
    //   // Cek apakah tanggal adalah string
    //   if (typeof tanggal === 'string') {
    //     // Jika tanggal adalah string, cek apakah tanggal mengandung hari
    //     if (tanggal.includes(hari)) {
    //       return true;
    //     }
    //   } else {
    //     // Jika tanggal bukan string, asumsikan tanggal adalah objek Date
    //     const date = new Date(tanggal);
    //     // Cek apakah tanggal sama dengan hari
    //     if (date.getDate() === hari) {
    //       return true;
    //     }
    //   }
    //   // Jika tidak ada kondisi yang terpenuhi, return false
    //   return false;
    // },
    // isDate(tanggal, hari, tanggal_start, tanggal_end) {
    //   // Cek apakah tanggal adalah string
    //   if (typeof tanggal === 'string') {
    //     // Jika tanggal adalah string, cek apakah tanggal mengandung hari
    //     if (tanggal.includes(hari)) {
    //       return true;
    //     }
    //   } else {
    //     // Jika tanggal bukan string, asumsikan tanggal adalah objek Date
    //     const date = new Date(tanggal);
    //     // Cek apakah tanggal sama dengan hari
    //     if (date.getDate() === hari) {
    //       return true;
    //     }
    // //   }
    isDate(tanggal, hari, tgl_mulai_perawatan, tgl_selesai_perawatan) {
      // Cek apakah tanggal adalah string
      if (typeof tanggal === 'string') {
        // Jika tanggal adalah string, cek apakah tanggal mengandung hari
        if (tanggal.includes(hari)) {
          return true;
        }
      } else {
        const isStartDate = tgl_mulai_perawatan && new Date(tgl_mulai_perawatan).getDate() === hari;
        const isEndDate = tgl_selesai_perawatan && new Date(tgl_selesai_perawatan).getDate() === hari;

        if (isStartDate || isEndDate) {
          return true;
        }

        const date = new Date(tanggal);
        return date.getDate() === hari;
      }
    },
    // isDate(tanggal, hari, tanggal_start, tanggal_end) {
    //   const isStartDate = tanggal_start && new Date(tanggal_start).getDate() === hari;
    //   const isEndDate = tanggal_end && new Date(tanggal_end).getDate() === hari;

    //   if (isStartDate || isEndDate) {
    //     return true;
    //   }

    //   const date = new Date(tanggal);
    //   return date.getDate() === hari;
    // },
    prevPage() {
      this.currentPage--;
    },
    nextPage() {
      this.currentPage++;
    },
    nowPage(paginate) {
      this.currentPage = paginate;
    },
    getMonthNumber(monthName) {
      const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      return monthNames.indexOf(monthName);
    },
    // getBackgroundColor(item, tanggal) {
    //   if (this.weekend_date.indexOf(tanggal) !== -1) {
    //     return 'black';
    //   } else if (this.isDate(item.tanggal_perawatan, tanggal, item.tanggal_start, item.tanggal_end)) {
    //     return 'yellow';
    //   } else {
    //     return '';
    //   }
    // },
    // getBackgroundColor(item, tanggal) {
    //   const day = parseInt(tanggal, 10); // Convert to integer for comparison
      
    //   // Highlight weekends
    //   if (this.weekend_date.includes(day)) {
    //     return 'black';
    //   }
      
    //   // Highlight date within the start-end range
    //   const startDay = item.tanggal_start ? new Date(item.tanggal_start).getDate() : null;
    //   const endDay = item.tanggal_end ? new Date(item.tanggal_end).getDate() : null;
      
    //   if (startDay && endDay && day >= startDay && day <= endDay) {
    //     return 'yellow';
    //   }
      
    //   // Default color
    //   return '';
    // },
    // getBackgroundColor(item, tanggal) {
    //   const day = parseInt(tanggal, 10); // Ubah ke angka untuk perbandingan

    //   // Highlight akhir pekan
    //   if (this.weekend_date.includes(day)) {
    //     return 'black';
    //   }

    //   // Highlight tanggal_start ke tanggal_perawatan
    //   const startDay = item.tanggal_start ? new Date(item.tanggal_start).getDate() : null;
    //   const perawatanDay = item.tanggal_perawatan ? new Date(item.tanggal_perawatan).getDate() : null;
    //   const endDay = item.tanggal_end ? new Date(item.tanggal_end).getDate() : null;

    //   // Dari tanggal_start ke tanggal_perawatan
    //   if (startDay && perawatanDay && day >= startDay && day <= perawatanDay) {
    //     return 'blue'; // Warna untuk tanggal_start ke tanggal_perawatan
    //   }

    //   // Dari tanggal_perawatan ke tanggal_end
    //   if (perawatanDay && endDay && day > perawatanDay && day <= endDay) {
    //     return 'yellow'; // Warna untuk tanggal_perawatan ke tanggal_end
    //   }

    //   // Warna default
    //   return '';
    // },
    getBackgroundColor(item, tanggal) {
      const day = parseInt(tanggal, 10);

      // Highlight jika hanya ada tgl_perawatan (tanpa mulai dan selesai)
      if (!item.tgl_mulai_perawatan && !item.tgl_selesai_perawatan && item.tgl_perawatan) {
        const perawatanDate = new Date(item.tgl_perawatan).getDate();
        if (perawatanDate === day && !this.weekend_date.includes(day)) {
          return 'yellow';
        }
      }

      // Highlight jika tgl_perawatan berada dalam rentang tgl_mulai dan tgl_selesai
      if (this.isDate(item.tgl_perawatan) && this.isDate(item.tgl_mulai_perawatan) && this.isDate(item.tgl_selesai_perawatan)) {
        const tanggalPerawatan = new Date(item.tgl_perawatan).getDate();
        const startDay = new Date(item.tgl_mulai_perawatan).getDate();
        const endDay = new Date(item.tgl_selesai_perawatan).getDate();

        if (tanggalPerawatan >= startDay && tanggalPerawatan <= endDay && tanggalPerawatan === day && !this.weekend_date.includes(day)) {
          return 'yellow';
        }
      }

      // Highlight tgl_mulai_perawatan secara spesifik
      if (item.tgl_mulai_perawatan && day === new Date(item.tgl_mulai_perawatan).getDate() && !this.weekend_date.includes(day)) {
        return 'yellow';
      }

      // Highlight akhir pekan
      if (this.weekend_date.includes(day)) {
        return 'black';
      }

      // Highlight jika tanggal berada di antara tgl_mulai_perawatan dan tgl_selesai_perawatan
      const startDay = item.tgl_mulai_perawatan ? new Date(item.tgl_mulai_perawatan).getDate() : null;
      const endDay = item.tgl_selesai_perawatan ? new Date(item.tgl_selesai_perawatan).getDate() : null;

      if (startDay && endDay && day >= startDay && day <= endDay && !this.weekend_date.includes(day)) {
        return 'yellow';
      }

      return '';
    },
    // getBackgroundColor(item, tanggal) {
    //   const day = parseInt(tanggal, 10); // Convert to integer for comparison
      
    //   // Highlight tanggal perawatan
    //   if ((item.tanggal_perawatan === String(day) || item.tanggal_start === String(day)) 
    //       && (!item.tanggal_start || new Date(item.tanggal_start).getDate() <= parseInt(item.tanggal_perawatan)) 
    //       && (!item.tanggal_end || new Date(item.tanggal_end).getDate() >= parseInt(item.tanggal_perawatan))) {
    //       return 'yellow';
    //   }
      
    //   // Highlight weekends
    //   if (this.weekend_date.includes(day)) {
    //     return 'black';
    //   }
      
    //   // Highlight date within the start-end range
    //   const startDay = item.tanggal_start ? new Date(item.tanggal_start).getDate() : null;
    //   const endDay = item.tanggal_end ? new Date(item.tanggal_end).getDate() : null;
    //   const perawatanDay = item.tanggal_perawatan ? parseInt(item.tanggal_perawatan) : null;
      
    //   if (startDay && endDay && day >= startDay && day <= endDay) {
    //     return 'yellow';
    //   } else if (startDay && day >= startDay && day < perawatanDay) {
    //     return 'yellow';
    //   } else if (startDay && endDay && day >= startDay && day <= endDay) {
    //     return 'yellow';
    //   } else if (perawatanDay && endDay && day > perawatanDay && day <= endDay) {
    //     return 'yellow';
    //   } else if (endDay <= perawatanDay && startDay && day >= startDay && day <= endDay) {
    //     return 'yellow';
    //   }  

      
    //   // Default color
    //   return '';
    // },
    minDateFormatter() {
      const date = new Date();
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0'); // tambah padding
      return `${year}-${month}-01`;
    },
    dateFormatter() {
      const date = new Date();
      const year = date.getFullYear();
      const month = date.getMonth();
      const lastDay = new Date(year, month + 1, 0).getDate();
      const paddedMonth = String(month + 1).padStart(2, '0'); // tambah padding
      const paddedDay = String(lastDay).padStart(2, '0');
      return `${year}-${paddedMonth}-${paddedDay}`;
    },
    showModalTambah() {
      this.isModalTambahOpen = true;
    },
    closeModalTambah() {
      this.isModalTambahOpen = false;
    },
    showModalEdit(item) {
      this.id = item.id;
      this.tanggal_perawatan = item.tanggal_perawatan;
      this.waktu_mulai = item.waktu_mulai;
      this.waktu_selesai = item.waktu_selesai;
      this.pic = item.pic;
      this.detail = item.detail;
      this.kondisi = item.kondisi;
      this.isModalEditOpen = true;
    },
    closeModalEdit() {
      this.isModalEditOpen = false;
    },
    showModalSelesai(item) {
      this.id = item.id;
      this.keterangan_perawatan = '';
      this.kondisi = '';
      this.isModalSelesaiOpen = true;
    },
    closeModalSelesai() {
      this.isModalSelesaiOpen = false;
    },
    async getTools() {
      try {
        const response = await axios.get('/api/v1/tools');
        this.tools = response.data;
      } catch (error) {
        console.error('Gagal mengambil data tools:', error);
      }
    },

    async getNoSeri() {
      try {
        if (this.selectedToolId) {
          const response = await axios.get(`/api/v1/tools/${this.selectedToolId}/no-seri`);
          this.noSeriList = response.data;
        } else {
          this.noSeriList = [];
        }
      } catch (error) {
        console.error('Gagal mengambil data no seri:', error);
      }
    },

    async submitPerawatan() {
      try {
        const payload = {
          tgl_perawatan: this.tglPerawatan,
          no_seri_id: this.selectedNoSeriId
        };

        const response = await axios.post('/api/v1/perawatan', payload);
        Swal.fire({
          title: 'Berhasil!',
          text: response.data.message,
          icon: 'success',
          confirmButtonText: 'OK'
        }).then(() => {
          window.location.reload();
        });
        // Reset form jika ingin
        this.tglPerawatan = '';
        this.selectedToolId = '';
        this.selectedNoSeriId = '';
        this.noSeriList = [];
      } catch (error) {
        if (error.response && error.response.data) {
          console.log(error.response.data); // ✅ lihat detail error dari Laravel
          Swal.fire({
            title: 'Gagal!',
            text: error.response.data.message || 'Gagal menyimpan perawatan.',
            icon: 'error',
            confirmButtonText: 'OK'
          });
        } else {
          console.error(error);
          Swal.fire({
            title: 'Gagal!',
            text: 'Terjadi kesalahan tidak terduga.',
            icon: 'error',
            confirmButtonText: 'OK'
          });
        }
      }
    },
    // tambahJadwalPerawatan() {
    //   const jadwalPerawatanBaru = {
    //     id: this.jadwalPerawatan.length + 1,
    //     tanggal_perawatan: [this.tanggal],
    //     nama_alat: this.nama_alat,
    //     no_seri: this.no_seri,
    //   };
    //   this.jadwalPerawatan.push(jadwalPerawatanBaru);
    //   this.closeModalTambah();  // Change hideModalTambah to closeModalTambah
    // },
    simpanSelesai() {
      const index = this.jadwalPerawatan.findIndex((item) => item.id === this.id);
      if (index !== -1) {
        this.jadwalPerawatan[index].detail = this.detail;
        this.jadwalPerawatan[index].tanggal_end = this.tanggal_end;
        const tanggalSekarang = new Date();
        this.getOnlyTanggal[index] = tanggalSekarang.getDate();
        // console.log(this.getOnlyTanggal[index]);
        const tanggalEnd = tanggalSekarang.toISOString().split('T')[0];
        const waktuSekarang = tanggalSekarang.toTimeString().split(' ')[0];
        const tanggalWaktuEnd = `${tanggalEnd} ${waktuSekarang}`;
        this.jadwalPerawatan[index].tanggal_end = tanggalWaktuEnd;
        this.jadwalPerawatan[index].kondisi = this.kondisi;
        this.jadwalPerawatan[index].status = 'Selesai';
      }
      this.closeModalSelesai();
      this.isModalSelesaiOpen = false;
    },
    // simpanSelesai() {
    //   const index = this.jadwalPerawatan.findIndex((item) => item.id === this.id);
    //   if (index !== -1) {
    //     this.jadwalPerawatan[index].detail = this.detail;
    //     // this.jadwalPerawatan[index].tanggal_end = this.tanggal_end;
    //     const tanggalSekarang = new Date();
    //     this.getOnlyTanggal[index] = tanggalSekarang.getDate();
    //     // console.log(this.getOnlyTanggal[index]);
    //     const tanggalEnd = this.tanggal_end.toISOString().split('T')[0];
    //     // const tanggalSekarang = new Date();
    //     // const tanggalEnd = this.tanggal_end.split('-')[2];
    //     // this.getOnlyTanggal[index] = this.tanggal_end.split('-')[2];
    //     // console.log(this.getOnlyTanggal)
    //     this.jadwalPerawatan[index].tanggal_end = tanggalEnd;
    //     this.jadwalPerawatan[index].kondisi = this.kondisi;
    //     this.jadwalPerawatan[index].status = 'Selesai';
    //   }
    //   this.closeModalSelesai();
    //   this.isModalSelesaiOpen = false;
    // },
    // simpanJadwalPerawatan() {
    //   const index = this.jadwalPerawatan.findIndex((item) => item.id === this.id);
    //   if (index !== -1) {
    //     this.jadwalPerawatan[index].tanggal_perawatan = this.tanggal_perawatan;
    //     this.jadwalPerawatan[index].tanggal_start = this.tanggal_start;
    //     const tanggalSekarang = new Date();
    //     const tanggalStart = tanggalSekarang.toISOString().split('T')[0];
    //     this.jadwalPerawatan[index].tanggal_start = tanggalStart;        
    //     this.jadwalPerawatan[index].waktu_mulai = this.waktu_mulai;
    //     this.jadwalPerawatan[index].waktu_selesai = this.waktu_selesai;
    //     this.jadwalPerawatan[index].pic = this.pic;
    //     this.jadwalPerawatan[index].detail = this.detail;
    //     this.jadwalPerawatan[index].kondisi = this.kondisi;
    //     this.jadwalPerawatan[index].status = 'Pelaksanaan';

    //     // Menyimpan PIC untuk setiap tanggal perawatan
    //     if (this.tanggal_perawatan) {
    //       const tanggal = parseInt(this.tanggal_perawatan, 10);  // Menyimpan PIC untuk tanggal tertentu
    //       this.picPerTanggal[tanggal] = this.jadwalPerawatan.pic;;  // Menyimpan nama PIC berdasarkan tanggal
    //       console.log(this.picPerTanggal);
    //     }
    //   }
    //   this.closeModalEdit();
    // },
    // simpanJadwalPerawatan() {
    //   const index = this.jadwalPerawatan.findIndex((item) => item.id === this.id);
    //   if (index !== -1) {
    //     // Update the jadwalPerawatan object
    //     this.jadwalPerawatan[index].tanggal_perawatan = this.tanggal_perawatan;
    //     this.jadwalPerawatan[index].tanggal_start = this.tanggal_start;

    //     const tanggalSekarang = new Date();
    //     const tanggalStart = tanggalSekarang.toISOString().split('T')[0];
    //     this.jadwalPerawatan[index].tanggal_start = tanggalStart;
    //     console.log(tanggalStart);
    //     const hari = tanggalStart.split('-')[2];
    //     console.log(hari);
    //     this.jadwalPerawatan[index].waktu_mulai = this.waktu_mulai;
    //     this.jadwalPerawatan[index].waktu_selesai = this.waktu_selesai;
    //     this.jadwalPerawatan[index].pic = this.pic;
    //     this.jadwalPerawatan[index].detail = this.detail;
    //     this.jadwalPerawatan[index].kondisi = this.kondisi;
    //     this.jadwalPerawatan[index].status = 'Pelaksanaan';

    //     // Menyimpan PIC untuk setiap tanggal perawatan
    //     // if (this.tanggal_perawatan) {
    //     //   const tanggal = parseInt(this.tanggal_perawatan, 10);  // Menyimpan PIC untuk tanggal tertentu
    //     //   this.picPerTanggal[tanggal] = this.jadwalPerawatan.pic; // Menyimpan nama PIC berdasarkan tanggal
    //     //   console.log(this.picPerTanggal);
    //     // }

    //     // Also store PIC for tanggal_start
    //     if (hari) {
    //       const startTanggal = parseInt(hari, 10);  // Menyimpan PIC untuk tanggal_start
    //       if (!this.picPerTanggal[this.id]) {
    //         this.picPerTanggal[this.id] = {}; // Pastikan ada objek untuk setiap id
    //       }
    //       this.picPerTanggal[this.id][startTanggal] = this.jadwalPerawatan.pic;  // Menyimpan nama PIC berdasarkan tanggal_start dan id
    //       console.log("PIC for tanggal_start: ", this.picPerTanggal);
    //     }
    //   }
    //   this.closeModalEdit();
    // },
    simpanJadwalPerawatan() {
      const index = this.jadwalPerawatan.findIndex((item) => item.id === this.id);
      
      if (index !== -1) {
        // Menampilkan SweetAlert dengan ikon peringatan dan tombol konfirmasi/pembatalan
        Swal.fire({
          title: 'Konfirmasi Pengiriman',
          text: 'Apakah kamu yakin ingin mengirim jadwal perawatan ini?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, kirim!',
          cancelButtonText: 'Tidak, batalkan!',
        }).then((result) => {
          if (result.isConfirmed) {
            // Proses jika pengguna memilih "Ya, kirim!"
            
            // Mendapatkan tanggal dan waktu saat ini
            const moment = require('moment-timezone');
            const tanggalSekarang = moment();
            const waktuGmt7 = tanggalSekarang.tz('Asia/Jakarta').format('HH:mm:ss');

            const tanggal = tanggalSekarang.format('YYYY-MM-DD');
            const tanggalWaktu = `${tanggal} ${waktuGmt7}`;
            
            // Update the jadwalPerawatan object dengan tanggal dan waktu
            this.jadwalPerawatan[index].tanggal_perawatan = this.tanggal_perawatan;
            this.jadwalPerawatan[index].tanggal_start = tanggalWaktu;
            this.jadwalPerawatan[index].waktu_mulai = this.waktu_mulai;
            this.jadwalPerawatan[index].waktu_selesai = this.waktu_selesai;
            this.jadwalPerawatan[index].pic = this.pic;
            this.jadwalPerawatan[index].detail = this.detail;
            this.jadwalPerawatan[index].kondisi = this.kondisi;
            this.jadwalPerawatan[index].status = 'Pelaksanaan';

            // Menyimpan PIC untuk setiap tanggal perawatan
            const hari = tanggal.split('-')[2];  // Mengambil hari dari tanggal_start
            if (hari) {
              const startTanggal = parseInt(hari, 10);  // Menyimpan PIC untuk tanggal_start
              if (!this.picPerTanggal[this.id]) {
                this.picPerTanggal[this.id] = {}; // Pastikan ada objek untuk setiap id
              }
              this.picPerTanggal[this.id][startTanggal] = this.jadwalPerawatan.pic;  // Menyimpan nama PIC berdasarkan tanggal_start dan id
            }

            // Menampilkan SweetAlert sukses setelah data disimpan
            Swal.fire({
              title: 'Berhasil!',
              text: 'Jadwal perawatan berhasil dikirim.',
              icon: 'success',
              confirmButtonText: 'OK'
            });
          } else {
            // Jika pengguna memilih "Tidak, batalkan!" maka proses dibatalkan dan tidak dilanjutkan
            Swal.fire({
              title: 'Dibatalkan',
              text: 'Pengiriman jadwal perawatan dibatalkan.',
              icon: 'info',
              confirmButtonText: 'OK'
            });
            return; // Membatalkan proses lebih lanjut
          }
        });
      } else {
        // Menampilkan SweetAlert jika tidak ditemukan
        Swal.fire({
          title: 'Gagal!',
          text: 'Jadwal perawatan tidak ditemukan.',
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }

      this.closeModalEdit();
    },
    // simpanJadwalPerawatan() {
    //   const index = this.jadwalPerawatan.findIndex((item) => item.id === this.id);
    //   if (index !== -1) {
    //     // Menampilkan SweetAlert dengan ikon peringatan dan tombol konfirmasi/pembatalan
    //     Swal.fire({
    //       title: 'Konfirmasi Pengiriman',
    //       text: 'Apakah kamu yakin ingin mengirim jadwal perawatan ini?',
    //       icon: 'warning',
    //       showCancelButton: true,
    //       confirmButtonText: 'Ya, kirim!',
    //       cancelButtonText: 'Tidak, batalkan!',
    //     }).then((result) => {
    //       if (result.isConfirmed) {
    //         // Proses jika pengguna memilih "Ya, kirim!"
    //         // Update the jadwalPerawatan object
    //         this.jadwalPerawatan[index].tanggal_perawatan = this.tanggal_perawatan;
    //         this.jadwalPerawatan[index].tanggal_start = this.tanggal_start;

    //         const tanggalSekarang = new Date();
    //         const tanggalStart = tanggalSekarang.toISOString().split('T')[0];
    //         const Tanggal = tanggalStart[0];
    //         const Waktu = tanggalStart[1].split('.')[0];
    //         const tanggalWaktu = `${Tanggal} ${Waktu}`;
    //         console.log(tanggalWaktu); 
    //         this.jadwalPerawatan[index].tanggal_start = tanggalStart;            
    //         const hari = tanggalStart.split('-')[2];            
    //         this.jadwalPerawatan[index].waktu_mulai = this.waktu_mulai;
    //         this.jadwalPerawatan[index].waktu_selesai = this.waktu_selesai;
    //         this.jadwalPerawatan[index].pic = this.pic;
    //         this.jadwalPerawatan[index].detail = this.detail;
    //         this.jadwalPerawatan[index].kondisi = this.kondisi;
    //         this.jadwalPerawatan[index].status = 'Pelaksanaan';

    //         // Menyimpan PIC untuk setiap tanggal perawatan
    //         // if (this.tanggal_perawatan) {
    //         //   const tanggal = parseInt(this.tanggal_perawatan, 10);  // Menyimpan PIC untuk tanggal tertentu
    //         //   this.picPerTanggal[tanggal] = this.jadwalPerawatan.pic; // Menyimpan nama PIC berdasarkan tanggal
    //         //   console.log(this.picPerTanggal);
    //         // }

    //         // Also store PIC for tanggal_start
    //         if (hari) {
    //           const startTanggal = parseInt(hari, 10);  // Menyimpan PIC untuk tanggal_start
    //           if (!this.picPerTanggal[this.id]) {
    //             this.picPerTanggal[this.id] = {}; // Pastikan ada objek untuk setiap id
    //           }
    //           this.picPerTanggal[this.id][startTanggal] = this.jadwalPerawatan.pic;  // Menyimpan nama PIC berdasarkan tanggal_start dan id
    //           // console.log("PIC for tanggal_start: ", this.picPerTanggal);
    //         }

    //         // Menampilkan SweetAlert sukses setelah data disimpan
    //         Swal.fire({
    //           title: 'Berhasil!',
    //           text: 'Jadwal perawatan berhasil dikirim.',
    //           icon: 'success',
    //           confirmButtonText: 'OK'
    //         });
    //       } else {
    //         // Jika pengguna memilih "Tidak, batalkan!" maka proses dibatalkan dan tidak dilanjutkan
    //         Swal.fire({
    //           title: 'Dibatalkan',
    //           text: 'Pengiriman jadwal perawatan dibatalkan.',
    //           icon: 'info',
    //           confirmButtonText: 'OK'
    //         });
    //         return; // Membatalkan proses lebih lanjut
    //       }
    //     });
    //   } else {
    //     // Menampilkan SweetAlert jika tidak ditemukan
    //     Swal.fire({
    //       title: 'Gagal!',
    //       text: 'Jadwal perawatan tidak ditemukan.',
    //       icon: 'error',
    //       confirmButtonText: 'OK'
    //     });
    //   }

    //   this.closeModalEdit();
    // },
    editJadwal(item) {
      this.id = item.id;
      this.tanggal_perawatan = item.tanggal_perawatan;
      this.waktu_mulai = item.waktu_mulai;
      this.waktu_selesai = item.waktu_selesai;
      this.pic = item.pic;
      this.detail = item.detail;
      this.kondisi = item.kondisi;
      this.modalTitle = 'Edit Jadwal Perawatan';
      this.showModalTambah();
    },
    exportToExcel() {
      this.convertToExcel("export_table", "Jadwal Perawatan");
    },
    convertToExcel(table, name) {
      let uri = "data:application/vnd.ms-excel;base64,",
        template =
          '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
        base64 = function (s) {
          return window.btoa(unescape(encodeURIComponent(s)));
        },
        format = function (s, c) {
          return s.replace(/{(\w+)}/g, function (m, p) {
            return c[p];
          });
        };

      if (!table.nodeType) table = document.getElementById(table);
      let ctx = {
        worksheet: name || "Worksheet",
        table: table.innerHTML,
      };
      window.location.href = uri + base64(format(template, ctx));
    },
  },
  watch: {
    nama_alat() {
      if (this.nama_alat === 'Bor') {
        this.no_seri = 'B-01';
      } else if (this.nama_alat === 'Gergaji') {
        this.no_seri = 'G-01';
      } else if (this.nama_alat === 'Pahat') {
        this.no_seri = 'P-01';
      } else {
        this.no_seri = '';
      }
    },
    search() {
      this.currentPage = 1;
    },
  },
  filters: {
    // Filter untuk format tanggal
    // formatDate(date) {
    //   if (date) {
    //     return `${date.toLocaleDateString('id-ID')}`;  // Format: dd-mm-yyyy        
    //   }
    //   return '-';
    // },
    formatDate(date, options = {}) {
      if (date) {
        // Menambahkan default options jika tidak ada
        const defaultOptions = {
          month: 'numeric',
          year: 'numeric',
          day: 'numeric',
          timeZoneName: 'short',
          timeZone: 'Asia/Jakarta',
          locale: 'id',
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit',
          hour12: false,
        };

        const finalOptions = { ...defaultOptions, ...options };
        
        // Menggunakan Intl.DateTimeFormat dengan opsi yang telah disesuaikan
        const formatter = new Intl.DateTimeFormat(finalOptions.locale, finalOptions);
        return formatter.format(new Date(date));
      }
      return '-';
    }
  },
  mounted() {
    this.fetchDataFrom();
    this.getTools();
    this.pages = Array.from({ length: Math.ceil(this.jadwalPerawatan.length / this.perPage) }, (_, i) => i + 1);
  }
}
</script>

<style scoped>
/* Tambahkan CSS untuk garis bawah */
.table td {
  border: 1px solid #fff;
}
#pills-tab .nav-link {
  color: #000;
}

#pills-tab .nav-link.active {
  background-color: #169ea8;
  color: #fff;
}
</style>