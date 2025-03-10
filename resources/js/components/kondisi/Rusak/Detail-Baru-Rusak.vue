<template>
  <div class="container-fluid">
    <!-- Head -->
    <div class="row mb-2 align-items-center" >
      <div class="col-sm-6"><h3 class="text-black-10" style="font-family: Raleway;">Detail Kerusakan Alat/Mesin</h3> 
        <h6 style="color: rgb(128, 128, 128);"></h6>
      </div> 
      <div class="col-sm-6 mt-3">
        <ol class="breadcrumb float-sm-right bg-table" style="border-radius: 10px;">
          <li class="breadcrumb-item">
            <a style="color: #169ea8; text-decoration: none;" href="javascript:history.back()">Kerusakan Alat/Mesin</a>
          </li>
          <li class="breadcrumb-item active" style="color: red;">
            <span>Detail Kerusakan Alat/Mesin</span>
          </li>
        </ol>
      </div>
    </div>
    <!-- Detail -->
    <div class="card shadow">
      <div class="row m-1">
        <div class="col-12">
          <h4 class="text-capitalize text-primary text-bold"><b>No Seri #{{ $route.params.id }}</b></h4>
        </div>
        <div class="col-3">
          <dt style="color: #000;">PIC Kerusakan</dt>
          <dd>{{ data[0].pic }}</dd>
        </div>
        <div class="col-3">
          <dt style="color: #000;">Nama Alat/Mesin</dt>
          <dd>{{ data[0].nama }}</dd>
          <dt class="text-black-10">Layout</dt>
          <dd>{{ data[0].layout }}</dd>
        </div>
        <div class="col-3">
          <dt style="color: #000;">Tanggal Kerusakan</dt>
          <dd>{{ data[0].tgl }}</dd>          
        </div>
        <!-- <div class="col-3">
          <dt style="color: #000;">Target</dt>
          <dd>{{ data[0].tgl_selesai }} <br>
            <small>
              <i :class="{'fas fa-clock': !durasidata[0].includes('hari lewat') && !durasidata[0].includes('hari lagi'), 'fas fa-exclamation-circle text-danger': durasidata[0].includes('hari lewat') || durasidata[0].includes('hari lagi')}"></i>
              <span :class="{'text-danger': durasidata[0].includes('hari lewat') || durasidata[0].includes('hari lagi')}">
                {{ durasidata[0] }}
              </span>
            </small>
          </dd>
        </div> -->
        <!-- <div class="col-3">
          <dt style="color: #000;">Status</dt>
          <dd>
            <div 
              class="badge"
              :class="{
                        'status-active': data[0].status === 'Selesai',
                        'status-hilang': data[0].status === 'Proses',}">
              {{ data[0].status }}
            </div>
          </dd>
        </div> -->
      </div>        
    </div>
    <!-- Aktivity -->
    <div class="card shadow mt-5 mb-3">
      <div class="m-2">
        <div class="col-12">
          <h4 class="text-capitalize text-primary text-bold"><b>Aktivitas Pemusnahan</b></h4>
        </div>        
        <div class="row align-items-center justify-content-end m-3">          
          <!-- Tombol Tambah Aktivitas akan hilang jika aktivitas sudah selesai -->
          <button v-if="shouldShowTambahAktivitas" class="btn btn-primary mr-2" @click="showModal = true" :disabled="isAktivitasSelesai || isAllAktivitasCompleted">Tambah Aktivitas</button>      
          <!-- Tombol Selesai hanya muncul jika kondisi aktivitas terakhir adalah OK atau Rusak -->
          <button v-if="isLastAktivitasCompleted && !isAktivitasSelesai" class="btn btn-success mr-2" @click="selesaiAktivitas" :disabled="!isLastAktivitasCompleted">Selesai</button>
          <div class="search-wrapper">
            <div class="input-group">
              <input type="text" placeholder="Search..." class="form-control"
                v-model="searchQuery"
                @input="debouncedFetchNoSeri"
              />
            </div>
          </div>
        </div>
        <div class="col-12 table-responsive p-3">
          <table class="table table-border no-border table-custom">
            <thead class="bg-table">
              <tr class="text-center" style="color: #000;">
                <th>#</th>
                <th>Tanggal Pemusnahan</th>
                <th>Waktu Pemusnahan</th>
                <th>PIC Pemusnahan</th>
                <th>Kondisi</th>
                <th>Status</th>
                <th>Alasan Penolakan</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in aktivitasList" :key="index" class="text-center">
                <td>{{ index + 1 }}</td>
                <td>{{ item.tanggal }}</td>
                <td>{{ item.waktu_mulai }} - {{ item.waktu_selesai }}</td>
                <td>{{ item.pic }}</td>
                <td>
                  <div 
                    class="badge"
                    :class="{
                      'status-rusak': item.kondisi === 'Rusak',
                      'status-active': item.kondisi === 'OK',
                      'status-error': item.kondisi === 'Error',
                      'status-musnah': item.kondisi === 'Musnah',
                    }">
                    {{ item.kondisi }}
                    </div>
                </td>
                <td>
                  <div 
                    class="badge"
                    :class="{
                      'status-rusak': item.status === 'Ditolak',
                      'status-active': item.status === 'Diterima',
                      'status-hilang': item.status === 'Menunggu Persetujuan Atasan',
                    }">
                    {{ item.status }}
                    </div>
                </td>
                <td>{{ item.alasanPenolakan }}</td>
              </tr>
            </tbody>
          </table>
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
      </div>                  
    </div>    
    <!-- Modal -->
    <div class="modal fade show" id="modalAktivitas" tabindex="-1" role="dialog" aria-labelledby="modalAktivitasLabel" v-if="showModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalAktivitasLabel">Aktivitas Pemusnahan</h5>
            <button type="button" class="close" @click="showModal = false" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="tanggal" class="text-black-10"><b>Tanggal Pemusnahan <sup class="text-danger"> *</sup> </b></label>
                <input type="date" class="form-control" id="tanggal" v-model="aktivitas.tanggal" required>
              </div>
              <div class="form-group"> 
                <label for="waktu" class="text-black-10"><b>Waktu Pemusnahan (Mulai - Selesai) <sup class="text-danger"> *</sup> </b></label> 
                <div class="input-group"> <input type="time" class="form-control" id="waktu_mulai" v-model="aktivitas.waktu_mulai" required>
                  <span class="input-group-text">-</span> 
                  <input type="time" class="form-control" id="waktu_selesai" v-model="aktivitas.waktu_selesai" required> 
                </div> 
              </div>
              <div class="form-group">
                <label for="pic" class="text-black-10"><b>PIC Pemusnahan</b></label>
                <v-select
                  :options="picOptions"
                  v-model="aktivitas.pic"
                  multiple
                  label="text"
                  :reduce="(pic) => pic.value"
                />
              </div>
              <!-- <div class="form-group">
                <label for="detail" class="text-black-10"><b>Detail <sup class="text-danger"> *</sup></b></label>
                <textarea class="form-control" id="detail" v-model="aktivitas.detail" required></textarea>
              </div> -->
              <!-- <div class="form-group">
                <label for="kondisi" class="text-black-10"><b>Kondisi <sup class="text-danger"> *</sup></b></label>
                <select class="form-control" id="kondisi" v-model="aktivitas.kondisi" required>
                  <option value="" disabled>Pilih Kondisi</option>
                  <option value="Musnah">Musnah</option>
                  <option value="OK">OK</option>            
                  <option value="Error">Error</option>
                  <option value="Rusak">Rusak</option>
                </select>
              </div> -->
              <!-- <div class="form-group">
                <label for="status" class="text-black-10"><b>Status <sup class="text-danger"> *</sup></b></label>
                <select class="form-control" id="status" v-model="aktivitas.staus" required>
                  <option value="" disabled>Pilih Status</option>
                  <option value="Musnah">Musnah</option>
                  <option value="OK">OK</option>            
                  <option value="Error">Error</option>
                  <option value="Rusak">Rusak</option>
                </select>
              </div> -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" @click="showModal = false">Batal</button>
            <button type="button" class="btn btn-primary" @click="addAktivitas">Simpan</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal Selesai Aktivitas -->
    <!-- <div class="modal fade show" id="modalSelesaiAktivitas" tabindex="-1" role="dialog" aria-labelledby="modalSelesaiAktivitasLabel" v-if="showSelesaiModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalSelesaiAktivitasLabel">Selesai Aktivitas</h5>
            <button type="button" class="close" @click="showSelesaiModal = false" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Apakah Anda yakin ingin menyelesaikan aktivitas ini?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showSelesaiModal = false">Batal</button>
            <button type="button" class="btn btn-primary" @click="selesaiAktivitas">Selesai</button>
          </div>
        </div>
      </div>
    </div> -->
  </div>
</template>

<script>
import vSelect from 'vue-select';
import Swal from 'sweetalert2';

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
      showModal: false,
      showSelesaiModal: false,
      aktivitas: {
        tanggal: '',        
        waktu_mulai: '',
        waktu_selesai: '',
        pic: '',
        detail: '',
        kondisi: ''
      },
      aktivitasList: [
        { tanggal: '2025-02-01', waktu_mulai: '08:00', waktu_selesai: '12:00', pic: 'Jane Doe', detail: 'Contoh detail 1', kondisi: 'Rusak', status: 'Menunggu Persetujuan Atasan' },      
      ],
      data: [
        { no_seri: '1122wscj121', nama: 'Clamp', layout: 'E7', tgl: '2025-02-01', kondisi: 'Error', detail: 'Sensor tidak berfungsi', pic: 'John Doe', tgl_selesai: '2025-02-05', status: 'Proses' },        
      ],
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
      isAktivitasSelesai: false,
    }
  },
  computed: {
    isLastAktivitasCompleted() {
      const lastAktivitas = this.aktivitasList[this.aktivitasList.length - 1];
      return lastAktivitas && (lastAktivitas.kondisi === 'Musnah' && lastAktivitas.status === 'Diterima');
    },
    isAllAktivitasCompleted() {
      return this.aktivitasList.every(item => item.kondisi === 'Musnah' && item.status === 'Diterima');
    },
    // Hanya tampilkan tombol "Tambah Aktivitas" jika aktivitas tidak selesai
    shouldShowTambahAktivitas() {
      return !this.isLastAktivitasCompleted && !this.isAllAktivitasCompleted;
    },
    durasidata() {
      return this.data.map(item => {
        if (item.tgl_selesai && item.tgl) {
          const tglSelesai = new Date(item.tgl_selesai);
          const tglSekarang = new Date(item.tgl);
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
    totalPages() {
      return Math.ceil(this.aktivitasList.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(start + this.rowsPerPage - 1, this.aktivitasList.length);
      return `Menampilkan ${start} - ${end} dari ${this.aktivitasList.length} data`;
    }
  },
  methods: {
    addAktivitas() {      
      this.aktivitasList.push({ 
        tanggal: this.aktivitas.tanggal,        
        waktu_mulai: this.aktivitas.waktu_mulai,
        waktu_selesai: this.aktivitas.waktu_selesai,
        pic: this.aktivitas.pic,
        kondisi: 'Rusak',
        status: 'Menunggu Persetujuan Atasan' // tambahkan status menjadi "Proses"
      });
      this.aktivitas.tanggal = '';
      this.aktivitas.waktu_mulai = '';
      this.aktivitas.waktu_selesai = '';
      this.aktivitas.pic = '';
      this.aktivitas.kondisi = '';
      this.showModal = false;
    },
    selesaiAktivitas() {
      Swal.fire({
        title: 'Selesai Aktivitas?',
        text: 'Apakah Anda yakin ingin menyelesaikan aktivitas ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Selesai',
        cancelButtonText: 'Batal',
      }).then((result) => {
        if (result.isConfirmed) {
          // Lakukan aksi selesai aktivitas disini
          this.data[0].status = 'Proses';
          
          // Setelah aktivitas selesai, kita set isAktivitasSelesai menjadi true
          this.isAktivitasSelesai = true;
          this.showSelesaiModal = false;
          this.isLastAktivitasCompleted = false; // tambahkan ini untuk membuat tombol selesai hilang
          Swal.fire('Berhasil!', 'Aktivitas telah selesai.', 'success');
          this.$router.push('/kondisi-rusak');
        }
      });
    },
    updatePaginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      this.paginatedData = this.aktivitasList.slice(start, start + this.rowsPerPage);
    },
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
        this.updatePaginatedData();
      }
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        this.updatePaginatedData();
      }
    },
    debouncedFetchNoSeri() {
      // Implement debounce logic for search
      this.updatePaginatedData();
    },
  }
}
</script>

<style scoped>
.modal {
  display: block;
  z-index: 1000;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
}
.text-black-10 {
  color: #000;
}
</style>