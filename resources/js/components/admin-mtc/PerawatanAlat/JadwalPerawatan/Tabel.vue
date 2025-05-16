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
          <tr style="color: #000;" class="text-center">
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
          <tr v-for="item in filteredJadwalPerawatan.slice((currentPage - 1) * perPage, currentPage * perPage)" :key="item.id" class="text-center">
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
          <tr style="color: #000;" class="text-center">
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
          <tr v-for="(item, index) in filteredJadwalPerawatan.slice((currentPage - 1) * perPage, currentPage * perPage)" :key="item.id" class="text-center">
            <td>{{ item.no_perawatan }}</td>
            <td>{{ item.no_seri.tools.nama }}</td>
            <td>{{ item.no_seri.no_seri }}</td>
            <td class="text-center">{{ item.tgl_mulai_perawatan || '-' }} s/d {{ item.tgl_selesai_perawatan || '-' }}<br>
              <small>{{ item.waktu_mulai || '-' }} s/d {{ item.waktu_selesai || '-' }}</small>
              <br><small :style="{ color: durasiDataPerawatan[index].color }">
                    {{ durasiDataPerawatan[index].status }}
                  </small>
            </td>
            <td class="text-center">
              <i class="fas fa-check" v-if="item.status === 'Dalam Proses Perawatan'" style="color: green;"></i>
              <!-- <i class="fas fa-times" v-if="item.status === 'Belum Selesai'" style="color: red;"></i> -->
            </td>
            <td class="text-center">
              <i class="fas fa-check" v-if="item.status === 'Selesai Perawatan'" style="color: green;"></i>
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
                  <a class="dropdown-item" @click="showModalEdit(item)" v-if="item.status !== 'Dalam Proses Perawatan' && item.status !== 'Selesai Perawatan'">
                    <i class="bi bi-clock text-info"></i> Pelaksanaan
                  </a>
                  <a class="dropdown-item" @click="showModalSelesai(item)" v-if="item.status === 'Dalam Proses Perawatan' || item.status === 'Selesai Perawatan'">
                    <i class="bi bi-check text-success"></i> Selesai
                  </a>
                </div>
              </div>
            </td>
            <td v-for="i in Array.from(Array(last_date).keys())" :key="i" :style="{ backgroundColor: getBackgroundColor(item, i + 1) }">
              <div v-if="picPerTanggal[item.id] && picPerTanggal[item.id][i + 1]" style="font-size: 12px; color: #000;">
                <!-- {{ picPerTanggal[item.id][i + 1] }} -->
                  {{ item.pic }}
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
                <label for="tgl_perawatan" style="color: #000;">
                  <b>Tanggal Perawatan</b>
                  <sup style="color: red;"> *</sup>
                </label>
                <input v-model="tglPerawatan" type="date" class="form-control" :max="dateFormatter()" :min="minDateFormatter()">
              </div>
              <div class="form-group">
                <label for="waktu_perawatan" style="color: #000;">
                  <b>Waktu Perawatan</b>
                  <sup style="color: red;"> *</sup>
                </label>
                <input v-model="waktuPerawatan" type="time" class="form-control">
              </div>
              <div class="form-group">
                <label for="nama" style="color: #000;">
                  <b>Alat/Mesin</b>
                  <sup style="color: red;"> *</sup>
                </label>
                <select v-model="selectedToolId" @change="getNoSeri" class="form-control">
                  <option disabled value="">-- Pilih Tool --</option>
                  <option v-for="tool in tools" :key="tool.id" :value="tool.id">
                    {{ tool.nama }} - {{ getNamaTipe(tool) }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label for="no_seri" style="color: #000;">
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
              <div class="form-group">
                <label><b>PIC</b><sup style="color: red;"></sup></label>
                <v-select
                  :options="users"
                  v-model="aktivitas.pic"
                  multiple
                  placeholder="Pilih PIC"
                  :searchable="true"
                  label="nama"
                  :reduce="user => user.id"
                />
              </div>     
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
                <label><b>Keterangan Perawatan</b><sup style="color: red;"> *</sup></label>
                <div class="textarea-wrapper">
                  <textarea 
                  id="detail_perawatan"
                  v-model="aktivitasSelesai.detail_perawatan"
                  class="form-control"
                  rows="3"
                  placeholder="Masukkan Keterangan Perawatan (Maksimal 200 karakter)"
                  maxlength="200">
                  </textarea>
                  <small class="text-muted char-counter">
                    {{ aktivitasSelesai.detail_perawatan.length }} / 200
                  </small>
                </div>
                <!-- <textarea v-model="aktivitasSelesai.detail_perawatan" class="form-control"></textarea> -->
              </div>
              <div class="form-group">
                <label><b>Kondisi</b><sup style="color: red;"> *</sup></label>
                <select class="form-control" id="kondisi" v-model="aktivitasSelesai.kondisi" required>
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
      aktivitas: {
        id: null,
        pic: [],
      },
      aktivitasSelesai: {
        id: null,
        detail_perawatan: '',
        kondisi: '',
      },
      users: [],
      form: [],
      getOnlyTanggal: [],
      tools: [],
      noSeriList: [],
      selectedToolId: '',
      selectedNoSeriId: '',
      tglPerawatan: '',
      waktuPerawatan: '',
      jadwalPerawatan: [],
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
    durasiDataPerawatan() {
      return this.jadwalPerawatan.map((item) => {
        if (!item.tgl_perawatan) {
          return {
            status: '-',
            color: 'black' // warna default
          };
        }

        const tglPerawatan = new Date(item.tgl_perawatan);
        const tglMulai = item.tgl_mulai_perawatan ? new Date(item.tgl_mulai_perawatan) : null;
        const tglSelesai = item.tgl_selesai_perawatan ? new Date(item.tgl_selesai_perawatan) : null;

        // Jika belum ada pelaksanaan sama sekali
        if (!tglMulai && !tglSelesai) {
          return {
            status: '-',
            color: 'black'
          };
        }

        // Jika sudah mulai tapi belum selesai
        if (tglMulai && !tglSelesai) {
          if (tglMulai < tglPerawatan) {
            return {
              status: 'Maju dari Jadwal Perawatan',
              color: 'blue'
            };
          } else if (tglMulai > tglPerawatan) {
            return {
              status: 'Telat dari Jadwal Perawatan',
              color: 'red'
            };
          } else {
            return {
              status: 'Sesuai Jadwal Perawatan',
              color: 'green'
            };
          }
        }

        // Jika sudah selesai
        if (tglMulai && tglSelesai) {
          // Cek waktu mulai
          if (tglMulai < tglPerawatan && tglSelesai < tglPerawatan) {
            return {
              status: 'Lebih Cepat dari Jadwal Perawatan',
              color: 'blue'
            };
          } else if (tglMulai > tglPerawatan && tglSelesai > tglPerawatan) {
            return {
              status: 'Lewat dari Jadwal Perawatan',
              color: 'red'
            };
          } else if (tglMulai.getTime() === tglPerawatan.getTime() && 
                    tglSelesai.getTime() === tglPerawatan.getTime()) {
            return {
              status: 'Sesuai Jadwal Perawatan',
              color: 'green'
            };
          } else {
            // Kasus campuran (mulai lebih awal/sesuai, selesai lebih lambat)
            if (tglSelesai > tglPerawatan) {
              return {
                status: 'Lewat dari Jadwal Perawatan',
                color: 'red'
              };
            } else {
              return {
                status: 'Lebih Cepat dari Jadwal Perawatan',
                color: 'blue'
              };
            }
          }
        }

        // Default fallback
        return {
          status: '-',
          color: 'black'
        };
      });
    },
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
      if (item.tgl_mulai_perawatan) {
        return new Date(item.tgl_mulai_perawatan);
      }
      return null;
    },

    getTanggalEnd(item) {
      if (item.tanggal_end) {
        return new Date(item.tanggal_end);
      }
      return null;
    },    
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
      // this.id = item.id;
      // this.tanggal_perawatan = item.tanggal_perawatan;
      // this.waktu_mulai = item.waktu_mulai;
      // this.waktu_selesai = item.waktu_selesai;
      // this.pic = item.pic;
      // this.detail = item.detail;
      // this.kondisi = item.kondisi;
      this.aktivitas = {
        id: item.id,
        pic: [],
      };
      this.isModalEditOpen = true;
    },
    closeModalEdit() {
      this.isModalEditOpen = false;
    },
    showModalSelesai(item) {
      // this.id = item.id;
      // this.keterangan_perawatan = '';
      // this.kondisi = '';
      this.aktivitasSelesai = {
        id: item.id,
        detail_perawatan: '',
        kondisi: '',
      };
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
    getNamaTipe(item) {
      const parts = item.kode?.split('-') || [];
      const kodeTipe = parts[3]; // ambil bagian tipe dari kode, misal "T0" dari "1-S3-G0-T0-001"
      const tipe = item.jenis?.kategori
        ?.flatMap(k => k.merek || [])
        .flatMap(m => m.tipe || [])
        .find(t => t.kode_tipe === kodeTipe);
      return tipe ? tipe.nama_tipe : '-';
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
          no_seri_id: this.selectedNoSeriId,
          waktu_perawatan: this.waktuPerawatan,
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
        this.waktuPerawatan = '';
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
    async simpanSelesai() {
      if (!this.aktivitasSelesai.detail_perawatan | this.aktivitasSelesai.kondisi.length === 0) 
      {
        Swal.fire({
          icon: 'error',
          title: 'Data tidak lengkap',
          text: 'Detail Perawatan dan Kondisi tidak boleh kosong.',
        });
        return;
      }
      try {
        const payload = {
          id: this.aktivitasSelesai.id,
          detail_perawatan: this.aktivitasSelesai.detail_perawatan,
          kondisi: this.aktivitasSelesai.kondisi,
        };
        const confirm = await Swal.fire({
          title: 'Konfirmasi',
          text: 'Apakah Anda yakin ingin mengedit jadwal perawatan?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, kirim!',
          cancelButtonText: 'Tidak, batalkan!'
        });
        if (confirm.isConfirmed) {
          await axios.post('/api/v1/perawatan/status-selesai', payload);
          Swal.fire('Terkirim!', 'Data aktivitas berhasil disimpan.', 'success');
          this.closeModalSelesai();
          this.isModalSelesaiOpen = false;
        }
      } catch (error) {
        let msg = 'Terjadi kesalahan saat mengirim data.';
        if (error.response && error.response.data && error.response.data.message) {
          msg = error.response.data.message;
        }
        Swal.fire({
          icon: 'error',
          title: 'Gagal mengirim',
          text: msg,
        });
      }
    },
    // simpanSelesai() {
    //   const index = this.jadwalPerawatan.findIndex((item) => item.id === this.id);
    //   if (index !== -1) {
    //     this.jadwalPerawatan[index].detail = this.detail;
    //     this.jadwalPerawatan[index].tanggal_end = this.tanggal_end;
    //     const tanggalSekarang = new Date();
    //     this.getOnlyTanggal[index] = tanggalSekarang.getDate();
    //     // console.log(this.getOnlyTanggal[index]);
    //     const tanggalEnd = tanggalSekarang.toISOString().split('T')[0];
    //     const waktuSekarang = tanggalSekarang.toTimeString().split(' ')[0];
    //     const tanggalWaktuEnd = `${tanggalEnd} ${waktuSekarang}`;
    //     this.jadwalPerawatan[index].tanggal_end = tanggalWaktuEnd;
    //     this.jadwalPerawatan[index].kondisi = this.kondisi;
    //     this.jadwalPerawatan[index].status = 'Selesai';
    //   }
    //   this.closeModalSelesai();
    //   this.isModalSelesaiOpen = false;
    // },
    async simpanJadwalPerawatan() {
      if(this.aktivitas.pic.length === 0)
      {
        Swal.fire({
          icon: 'error',
          title: 'Data tidak lengkap',
          text: 'PIC tidak boleh kosong',
        });
        return;
      }
      try {
        const payload = {
          id: this.aktivitas.id,
          pic: this.aktivitas.pic,
        };
        const confirm = await Swal.fire({
          title: 'Konfirmasi',
          text: 'Apakah Anda yakin ingin mengedit jadwal perawatan?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, kirim!',
          cancelButtonText: 'Tidak, batalkan!',
        });
        if (confirm.isConfirmed) {
          await axios.post('/api/v1/perawatan/status-pelaksanaan', payload);
          Swal.fire('Terkirim!', 'Data aktivitas berhasil disimpan.', 'success');
          this.closeModalEdit();
          this.isModalEditOpen = false;
        }
      } catch (error) {
        let msg = 'Terjadi kesalahan saat mengirim data.';
        if (error.response && error.response.data && error.response.data.message) {
          msg = error.response.data.message;
        }
        Swal.fire({
          icon: 'error',
          title: 'Gagal mengirim',
          text: msg,
        });
      }
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
            
    //         // Mendapatkan tanggal dan waktu saat ini
    //         const moment = require('moment-timezone');
    //         const tanggalSekarang = moment();
    //         const waktuGmt7 = tanggalSekarang.tz('Asia/Jakarta').format('HH:mm:ss');

    //         const tanggal = tanggalSekarang.format('YYYY-MM-DD');
    //         const tanggalWaktu = `${tanggal} ${waktuGmt7}`;
            
    //         // Update the jadwalPerawatan object dengan tanggal dan waktu
    //         this.jadwalPerawatan[index].tanggal_perawatan = this.tanggal_perawatan;
    //         this.jadwalPerawatan[index].tanggal_start = tanggalWaktu;
    //         this.jadwalPerawatan[index].waktu_mulai = this.waktu_mulai;
    //         this.jadwalPerawatan[index].waktu_selesai = this.waktu_selesai;
    //         this.jadwalPerawatan[index].pic = this.pic;
    //         this.jadwalPerawatan[index].detail = this.detail;
    //         this.jadwalPerawatan[index].kondisi = this.kondisi;
    //         this.jadwalPerawatan[index].status = 'Pelaksanaan';

    //         // Menyimpan PIC untuk setiap tanggal perawatan
    //         const hari = tanggal.split('-')[2];  // Mengambil hari dari tanggal_start
    //         if (hari) {
    //           const startTanggal = parseInt(hari, 10);  // Menyimpan PIC untuk tanggal_start
    //           if (!this.picPerTanggal[this.id]) {
    //             this.picPerTanggal[this.id] = {}; // Pastikan ada objek untuk setiap id
    //           }
    //           this.picPerTanggal[this.id][startTanggal] = this.jadwalPerawatan.pic;  // Menyimpan nama PIC berdasarkan tanggal_start dan id
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
    async fetchInitialData() {
      try {
        const [usersRes] = await Promise.all([
          // axios.get('/api/v1/tools'),
          // axios.get('/api/v1/layouts'),
          axios.get('/api/v1/users')
        ]);
        // this.tools = toolsRes.data;
        // this.layouts = layoutsRes.data;
        this.users = usersRes.data.byPIC;
      } catch (err) {
        console.error('Gagal fetch data awal:', err);
      }
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
    this.fetchInitialData();
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