<template>
  <div class="container-fluid">
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
        <router-link id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false" class="nav-link" :class="{ active: $route.name === 'kalender-perawatan' }" :to="{ name: 'kalender-perawatan' }">Kalender</router-link>
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
            <th rowspan="2">Nama Alat/Mesin</th>
            <th rowspan="2">No Seri</th>
            <th :colspan="last_date">Tanggal</th>
          </tr>
          <tr>
            <th v-for="i in Array.from(Array(last_date).keys())" :key="i" style="color: #000;">
              {{ i + 1 }}
            </th>
          </tr>
        </thead>
        <tbody v-if="filteredJadwalPerawatan.length > 0">
          <tr v-for="item in filteredJadwalPerawatan.slice((currentPage - 1) * perPage, currentPage * perPage)" :key="item.id">
            <td>{{ item.nama_alat }}</td>
            <td>{{ item.no_seri }}</td>
            <td v-for="i in Array.from(Array(last_date).keys())" :key="i" :style="{ backgroundColor: weekend_date.indexOf(i + 1) !== -1 ? 'black' : isDate(item.tanggal_perawatan, i + 1) ? 'yellow' : '' }">
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td :colspan="last_date + 3">Tidak ada data</td>
          </tr>
        </tbody>
      </table>
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
            <th rowspan="2">Nama Alat/Mesin</th>
            <th rowspan="2">No Seri</th>
            <th rowspan="2">Progres</th>
            <th rowspan="2">Selesai</th>
            <th rowspan="2">Aksi</th>
            <th :colspan="last_date">Tanggal</th>
          </tr>
          <tr>
            <th v-for="i in Array.from(Array(last_date).keys())" :key="i" style="color: #000;">
              {{ i + 1 }}
            </th>
          </tr>
        </thead>
        <tbody v-if="filteredJadwalPerawatan.length > 0">
          <tr v-for="item in filteredJadwalPerawatan.slice((currentPage - 1) * perPage, currentPage * perPage)" :key="item.id">
            <td>{{ item.nama_alat }}</td>
            <td>{{ item.no_seri }}</td>
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
            <td v-for="i in Array.from(Array(last_date).keys())" :key="i" :style="{ backgroundColor: weekend_date.indexOf(i + 1) !== -1 ? 'black' : isDate(item.tanggal_perawatan, i + 1) ? 'yellow' : '' }">
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
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Jadwal Perawatan</h5>
            <button type="button" class="close" @click="closeModalTambah">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label>Tanggal</label>
                <input v-model="tanggal" type="date" class="form-control">
              </div>
              <div class="form-group">
                <label>Nama Alat/Mesin</label>
                <select v-model="nama_alat" class="form-control">
                  <option value="">Pilih Nama Alat/Mesin</option>
                  <option value="Bor">Bor</option>
                  <option value="Gergaji">Gergaji</option>
                  <option value="Pahat">Pahat</option>
                </select>
              </div>
              <div class="form-group">
                <label>No Seri</label>
                <input v-model="no_seri" type="text" class="form-control" :disabled="nama_alat === ''">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModalTambah">Batal</button>
            <button type="button" class="btn btn-primary" @click="tambahJadwalPerawatan">Simpan</button>
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
              </div>
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

export default {
  components: {
    vSelect,
  },
  data() {
    return {
      picOptions: [
        { text: 'John Doe', value: 'John Doe' },
        { text: 'Jane Doe', value: 'Jane Doe' },
        { text: 'Bob Smith', value: 'Bob Smith' },
        { text: 'Alice Johnson', value: 'Alice Johnson' },
      ],
      jadwalPerawatan: [
        {          
          id: 1,
          no_perawatan: 'R-01',
          nama_alat: 'Bor',
          no_seri: 'B-01',
          tanggal_perawatan: '6',
          waktu_mulai: '',
          waktu_selesai: '',
          pic: '',
          detail: '',
          kondisi: '',
          status: 'Belum Selesai'
        },
        {          
          id: 2,
          no_perawatan: 'R-02',
          nama_alat: 'Bor',
          no_seri: 'B-02',
          tanggal_perawatan: '20',
          waktu_mulai: '',
          waktu_selesai: '',
          pic: '',
          detail: '',
          kondisi: '',
          status: 'Belum Selesai'
        },
        {                    
          id: 3,
          no_perawatan: 'R-03',
          nama_alat: 'Bor',
          no_seri: 'B-03',
          tanggal_perawatan: '19',
          waktu_mulai: '',
          waktu_selesai: '',
          pic: '',
          detail: '',
          kondisi: '',
          status: 'Belum Selesai'
        },
      ],
      status: '',
      currentPage: 1,
      pages: [],
      perPage: 10, // jumlah data per halaman
      search: '',
      tanggal: '',
      nama_alat: '',
      no_seri: '',
      isModalTambahOpen: false,
      isModalEditOpen: false,
      isModalSelesaiOpen: false,
      modalTitle: 'Tambah Jadwal Perawatan',
    }
  },
  computed: {
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
        return item.nama_alat.toLowerCase().includes(search) || item.no_seri.toLowerCase().includes(search);
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
    isDate(tanggal, hari) {
      // Cek apakah tanggal adalah string
      if (typeof tanggal === 'string') {
        // Jika tanggal adalah string, cek apakah tanggal mengandung hari
        if (tanggal.includes(hari)) {
          return true;
        }
      } else {
        // Jika tanggal bukan string, asumsikan tanggal adalah objek Date
        const date = new Date(tanggal);
        // Cek apakah tanggal sama dengan hari
        if (date.getDate() === hari) {
          return true;
        }
      }
      // Jika tidak ada kondisi yang terpenuhi, return false
      return false;
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
    tambahJadwalPerawatan() {
      const jadwalPerawatanBaru = {
        id: this.jadwalPerawatan.length + 1,
        tanggal_perawatan: [this.tanggal],
        nama_alat: this.nama_alat,
        no_seri: this.no_seri,
      };
      this.jadwalPerawatan.push(jadwalPerawatanBaru);
      this.closeModalTambah();  // Change hideModalTambah to closeModalTambah
    },
    simpanSelesai() {
      const index = this.jadwalPerawatan.findIndex((item) => item.id === this.id);
      if (index !== -1) {
        this.jadwalPerawatan[index].keterangan_perawatan = this.keterangan_perawatan;
        this.jadwalPerawatan[index].kondisi = this.kondisi;
        this.jadwalPerawatan[index].status = 'Selesai';
      }
      this.closeModalSelesai();
      this.isModalSelesaiOpen = false;
    },
    simpanJadwalPerawatan() {
      const index = this.jadwalPerawatan.findIndex((item) => item.id === this.id);
      if (index !== -1) {
        this.jadwalPerawatan[index].tanggal_perawatan = this.tanggal_perawatan;
        this.jadwalPerawatan[index].waktu_mulai = this.waktu_mulai;
        this.jadwalPerawatan[index].waktu_selesai = this.waktu_selesai;
        this.jadwalPerawatan[index].pic = this.pic;
        this.jadwalPerawatan[index].detail = this.detail;
        this.jadwalPerawatan[index].kondisi = this.kondisi;
        this.jadwalPerawatan[index].status = 'Pelaksanaan';
        // Update tanggal pengerjaan pada tabel
        const tanggalPerawatan = this.tanggal_perawatan.split('-');
        const tanggal = tanggalPerawatan[2];
        this.jadwalPerawatan[index].tanggal_perawatan = [parseInt(tanggal)];
      }
      this.closeModalEdit();
    },
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
  mounted() {
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