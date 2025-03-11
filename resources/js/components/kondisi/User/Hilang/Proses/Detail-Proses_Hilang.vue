<template>
  <div class="container-fluid">
    <!-- Head -->
    <div class="row mb-2 align-items-center" >
      <div class="col-sm-6"><h3 class="text-black-10" style="font-family: Raleway;">Detail Penggantian Alat/Mesin</h3> 
        <h6 style="color: rgb(128, 128, 128);"></h6>
      </div> 
      <div class="col-sm-6 mt-3">
        <ol class="breadcrumb float-sm-right bg-table" style="border-radius: 10px;">
          <li class="breadcrumb-item">
            <a style="color: #169ea8; text-decoration: none;" href="/user/data-hilang">Penggantian Alat/Mesin</a>
          </li>
          <li class="breadcrumb-item active" style="color: red;">
            <span>Detail Penggantian Alat/Mesin</span>
          </li>
        </ol>
      </div>
    </div>
    <!-- Detail -->
    <div class="card shadow">
      <div class="row m-1">
        <div class="col-12">
          <h4 class="text-capitalize text-primary text-bold"><b>No Penggantian #{{ $route.params.id }}</b></h4>
        </div>
        <div class="col-3">
          <dt style="color: #000;">Nama Peminjam</dt>
          <dd>{{ data[0].pic }}</dd>
          <dt style="color: #000;">Divisi</dt>
          <dd>{{ data[0].divisi }}</dd>
        </div>
        <div class="col-3">
          <dt style="color: #000;">Nama Alat/Mesin</dt>
          <dd>{{ data[0].nama }}</dd>
        </div>
        <div class="col-3">
          <dt style="color: #000;">Detail Alat/Mesin Hilang</dt>
          <dd>{{ data[0].detail }}</dd>
        </div>
        <div class="col-3">
          <dt style="color: #000;">Tanggal Hilang Alat/Mesin</dt>
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
          <h4 class="text-capitalize text-primary text-bold"><b>Aktivitas Penggantian Alat/Mesin</b></h4>
        </div>        
        <div class="row align-items-center justify-content-end m-3">
          <!-- <button class="btn btn-primary mr-2" @click="downloadBukti(aktivitasList.length - 1)"><i class="fas fa-download"></i> Download Bukti Pertanggung Jawaban</button> -->
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
                <th>Tanggal Penggantian Alat/Mesin</th>
                <th>No Seri Lama</th>
                <th>No Seri Baru</th>
                <th>Harga</th>
                <th>Status</th>
                <!-- <th>Aksi</th> -->
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in aktivitasList" :key="index" class="text-center">
                <td>{{ index + 1 }}</td>
                <td>{{ item.tanggal }}</td>
                <td>{{ item.no_seri_lama }}</td>
                <td>{{ item.no_seri_baru }}</td>
                <td>{{ item.harga }}</td>
                <td>{{ item.status }}</td>
                <!-- <td>
                  Dropdown yang berfungsi dengan benar
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
                      Tombol Serahkan Alat/Mesin
                      <a v-if="!item.isSerah" class="dropdown-item" @click="serahkanAlat(item)">
                        <i class="fas fa-share text-primary"></i> Serahkan Alat/Mesin
                      </a>
                      Tombol Diterima hanya muncul setelah Serahkan Alat/Mesin ditekan
                      <a v-if="item.isSerah" class="dropdown-item" @click="terimaAktivitas(item)">
                        <i class="fas fa-check text-success"></i> Diterima
                      </a>
                    </div>
                  </div>
                </td> -->
              </tr>
            </tbody>
          </table>           
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center m-3" style="border-radius: 10px; background-color: #f3f4f6; height: 50px; color: #000;">
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
    <!-- Modal -->
    <div class="modal fade show" id="modalAktivitas" tabindex="-1" role="dialog" aria-labelledby="modalAktivitasLabel" v-if="showModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalAktivitasLabel">Aktivitas Penggantian Alat/Mesin</h5>
            <button type="button" class="close" @click="showModal = false" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="tanggal" class="text-black-10"><b>Tanggal Penggantian Alat/Mesin <sup class="text-danger"> *</sup> </b></label>
                <input type="date" class="form-control" id="tanggal" v-model="aktivitas.tanggal" required>
              </div>
              <div class="form-group">
                <label for="noSeriLama" class="text-black-10"><b>No Seri Lama <sup class="text-danger"> *</sup> </b></label>
                <input type="text" class="form-control" id="noSeriLama" v-model="aktivitas.no_seri_lama" required>
              </div>
              <div class="form-group">
                <label for="noSeriBaru" class="text-black-10"><b>No Seri Baru <sup class="text-danger"> *</sup> </b></label>
                <input type="text" class="form-control" id="noSeriBaru" v-model="aktivitas.no_seri_baru" required>
              </div>
              <div class="form-group">
                <label for="harga" class="text-black-10"><b>Harga <sup class="text-danger"> *</sup> </b></label>
                <input type="text" class="form-control" id="harga" v-model="aktivitas.harga" required>
              </div>
              <!-- <form @submit.prevent="handleUploadBA">
              <div class="form-group">
                <label for="baFile">Pilih File Bukti Pertanggung Jawaban (PDF)</label>
                
                Area Drag and Drop
                <div 
                  class="drag-drop-area" 
                  @dragover.prevent="onDragOver" 
                  @dragleave="onDragLeave" 
                  @drop="onDrop" 
                  @click="triggerFileInput"
                  :class="{'dragging': isDragging}"
                  >
                  <p v-if="!file">Seret dan jatuhkan file PDF di sini, atau klik untuk memilih</p>
                  <p v-else>{{ file.name }}</p>
                </div>

                Hidden Input File for Selecting File
                <input type="file" class="form-control" id="baFile" ref="fileInput" @change="onBAFileChange" accept="application/pdf" style="display: none;" required>
              </div>
            </form> -->
              <!-- <div class="form-group"> 
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
              </div> -->
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
    <!-- Tambahkan modal untuk input alasan penolakan -->
    <div class="modal fade show" id="modalTolakAktivitas" tabindex="-1" role="dialog" aria-labelledby="modalTolakAktivitasLabel" v-if="showTolakModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTolakAktivitasLabel">Alasan Penolakan</h5>
            <button type="button" class="close" @click="showTolakModal = false" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="alasan" class="text-black-10"><b>Alasan Penolakan <sup class="text-danger"> *</sup> </b></label>
                <textarea class="form-control" id="alasan" v-model="alasanPenolakan" required></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showTolakModal = false">Batal</button>
            <button type="button" class="btn btn-primary" @click="simpanTolakAktivitas">Simpan</button>
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
import pdf from 'vue-pdf';
import jsPDF from "jspdf";
import 'jspdf-autotable';

export default {
  components: {
    vSelect,
    pdf,
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
        kondisi: '',
        alasanPenolakan: '',
        harga: '',
        no_seri_lama: '',
        no_seri_baru: '',
      },
      aktivitasList: [
      { 
        tanggal: '2025-02-01', 
        no_seri_lama: '1122wscj121', 
        no_seri_baru: '1122wscj122', 
        harga: 'Rp 6.000', 
        BuktiPertanggungJawaban: '/file/Berita-Acara-Pemusnahan-Barang.pdf', 
        status: 'Menunggu Konfirmasi',
        isSerah: false        
      },  
      ],
      data: [
        { no_seri: '1122wscj121', nama: 'Clamp', layout: 'E7', tgl: '2025-02-01', kondisi: 'Error', detail: 'Lupa di taroh dimana', pic: 'John Doe', divisi: 'Maintenance', tgl_selesai: '2025-02-05', status: 'Proses' },        
      ],
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
      isAktivitasSelesai: false,
      showTolakModal: false,
      tolakIndex: null,
      alasanPenolakan: '',
      file:[],
      isDragging: false,
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
    shouldShowDiterima() {
      return this.aktivitasList.length > 0 && this.aktivitasList[this.aktivitasList.length - 1].status === 'Menunggu Persetujuan Atasan';
    },
    shouldShowDitolak() {
      return this.aktivitasList.length > 0 && this.aktivitasList[this.aktivitasList.length - 1].status === 'Menunggu Persetujuan Atasan';
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
        // BuktiPertanggungJawaban: URL.createObjectURL(this.file),
        no_seri_lama: this.aktivitas.no_seri_lama,
        no_seri_baru: this.aktivitas.no_seri_baru,
        harga: this.aktivitas.harga,      
        // waktu_mulai: this.aktivitas.waktu_mulai,
        // waktu_selesai: this.aktivitas.waktu_selesai,
        // pic: this.aktivitas.pic,
        // kondisi: 'Rusak',
        // status: 'Menunggu Persetujuan Atasan' // tambahkan status menjadi "Proses"
      });
      this.aktivitas.tanggal = '';
      // this.aktivitas.waktu_mulai = '';
      // this.aktivitas.waktu_selesai = '';
      // this.aktivitas.pic = '';
      // this.aktivitas.kondisi = '';
      this.showModal = false;
    },
     // Fungsi untuk menangani event dragover
     onDragOver(event) {
      event.preventDefault(); // Mencegah perilaku default
      this.isDragging = true; // Menandakan bahwa file sedang di-drag
    },

    // Fungsi untuk menangani event dragleave
    onDragLeave(event) {
      this.isDragging = false; // Menandakan bahwa file tidak lagi di-drag
    },

    // Fungsi untuk menangani event drop
    onDrop(event) {
      this.isDragging = false; // Menandakan bahwa file sudah dijatuhkan
      const droppedFile = event.dataTransfer.files[0]; // Mengambil file yang dijatuhkan
      if (droppedFile && droppedFile.type === 'application/pdf') {
        this.file = droppedFile; // Menyimpan file jika valid
      } else {
        alert('Hanya file PDF yang diperbolehkan!');
      }
    },

    // Fungsi untuk memicu input file ketika area drag-and-drop diklik
    triggerFileInput() {
      this.$refs.fileInput.click(); // Men-trigger click pada input file
    },

    // Fungsi untuk menangani perubahan file melalui file explorer
    onBAFileChange(event) {
      const selectedFile = event.target.files[0];
      if (selectedFile && selectedFile.type === 'application/pdf') {
        this.file = selectedFile; // Menyimpan file yang dipilih
      } else {
        alert('Hanya file PDF yang diperbolehkan!');
      }
    },

    // Fungsi untuk meng-handle upload
    handleUploadBA() {
      if (this.file) {
        // Implementasikan logika untuk meng-upload file
        alert('File berhasil di-upload: ' + this.file.name);
        this.showSelesaiModal = false; // Tambahkan kode ini untuk menutup modal
      } else {
        alert('Harap pilih file PDF terlebih dahulu!');
      }
    },
    downloadBuktiPertanggungJawaban(item) {
      const link = document.createElement('a');
      link.href = item.BuktiPertanggungJawaban;
      link.download = 'Bukti Pertanggung Jawaban.pdf';
      link.click();
    },
    selesaiAktivitas() {
      Swal.fire({
        title: 'Selesai Aktivitas?',
        text: 'Apakah Anda yakin ingin melanjutkan aktivitas ini ke tahapan proses?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
      }).then((result) => {
        if (result.isConfirmed) {
          // Lakukan aksi selesai aktivitas disini
          this.data[0].status = 'Diterima';
          // Update the underlying data instead of assigning to isLastAktivitasCompleted
          this.aktivitasList[this.aktivitasList.length - 1].kondisi = 'Musnah';
          this.aktivitasList[this.aktivitasList.length - 1].status = 'Diterima';
          Swal.fire('Berhasil!', 'Aktivitas telah selesai.', 'success');
          this.$router.push('/admin-mtc/data-hilang');
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
    terimaAktivitas(index) {
      this.aktivitasList[index].status = 'Diterima';
      this.aktivitasList[index].kondisi = 'Musnah';
    },
    tolakAktivitas(index) {
      this.showTolakModal = true;
      this.tolakIndex = index;
    },
    simpanTolakAktivitas() {
      this.aktivitasList[this.tolakIndex].status = 'Ditolak';
      this.aktivitasList[this.tolakIndex].kondisi = 'Rusak';
      this.aktivitasList[this.tolakIndex].alasanPenolakan = this.alasanPenolakan;
      this.showTolakModal = false;
      this.alasanPenolakan = '';
    },
    serahkanAlat(item) {
      // Update the status to 'Serah' (or any state you choose)
      item.status = 'Serah';
      // Here you can implement any logic for when the item is 'Serah'
      item.isSerah = true;
    },
    terimaAktivitas(item) {
      // Update the status to 'Diterima'
      item.status = 'Diterima';

      // Redirect to the /admin-mtc/data-hilang page
      this.$router.push('/admin-mtc/data-hilang');
    },
    downloadBukti(index) {
      const ba = this.aktivitasList[index].ba;
      const filename = 'Bukti-Pertanggung-Jawaban.pdf';

      // Buat elemen canvas untuk render PDF
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');

      // Membuat objek jsPDF
      const pdf = new jsPDF();

      // Set font menjadi bold dan ukuran 16
      pdf.setFont('helvetica', 'bold');
      pdf.setFontSize(16);

      // Menambahkan judul Berita Acara Pemusnahan Barang dengan teks di tengah
      const title = 'SURAT PERNYATAAN PERTANGGUNG JAWABAN GANTI RUGI';
      const titleWidth = pdf.getStringUnitWidth(title) * pdf.getFontSize() / pdf.internal.scaleFactor;
      const pageWidth = pdf.internal.pageSize.width;
      const xPos = (pageWidth - titleWidth) / 2;
      pdf.text(title, xPos, 16);

      // Set font menjadi normal untuk teks berikutnya
      pdf.setFont('helvetica', 'normal');
      pdf.setFontSize(12);

      // Menambahkan informasi lainnya
      pdf.text(`Yang bertanda tangan di bawah ini : `, 25, 30);
      pdf.text(`Nama`, 14, 36);
      pdf.text(`Jabatan`, 14, 42);
      pdf.text(`Divisi`, 14, 48);
      pdf.text(`:`, 40, 36);
      pdf.text(`:`, 40, 42);      
      pdf.text(`:`, 40, 48);

      // Menambahkan garis panjang
      pdf.line(43, 38, 100, 38); // garis panjang untuk nama
      pdf.line(43, 44, 100, 44); // garis panjang untuk jabatan
      pdf.line(43, 50, 100, 50); // garis panjang untuk divisi

      pdf.text(`Dengan ini menyatakan bahwa saya bertanggung jawab atas penghilangan alat/mesin yang`, 25, 56);
      pdf.text(`terjadi pada tanggal `, 14, 62);
      pdf.line(53, 63, 100, 63); // garis panjang untuk tanggal
      pdf.text(`di area PT SINKO PRIMA ALLOY. `, 102, 62);

      pdf.text(`Adapun alat/mesin yang hilang antara lain:`, 18, 68);

      const headers = [
        "#",
        "Nama Alat/Mesin",
        "No Seri",
      ];
      const rows = [];

      this.data.forEach((item, index) => {
        rows.push([
          index + 1,
          item.nama,
          item?.no_seri,
        ]);
      });

      pdf.autoTable({
        head: [headers],
        body: rows,
        startY: 75, // Menyesuaikan posisi tabel setelah judul, nama peminjam, dan divisi
      });

      const textY = pdf.lastAutoTable.finalY + 10;
      pdf.text("Saya menyatakan bahwa saya bertanggung jawab atas ganti rugi atas penghilangan ", 25, textY);
      pdf.text("alat/mesin tersebut dan saya berkomitmen untuk menganti alat/mesin tersebut dengan alat/mesin ", 14, textY + 6);
      pdf.text("yang sama atau yang lebih baik kepada PT SINKO PRIMA ALLOY.", 14, textY + 12);

      pdf.text("Saya menyatakan bahwa pernyataan ini dibuat dengan sadar dan tanpa paksaan dari pihak", 25, textY + 18);
      pdf.text("manapun.", 14, textY + 24);

      pdf.text("Demikian pernyataan ini saya buat dengan sebenar-benarnya.", 25, textY + 30);

      const signatureSectionY = textY + 38;
      pdf.text("Dibuat Oleh,", 155, signatureSectionY);

      const gapY = signatureSectionY + 30;
      pdf.line(150, gapY, 190, gapY);

      // Simpan PDF ke dalam file
      const pdfData = pdf.output('blob');
      const url = URL.createObjectURL(pdfData);

      // Buat elemen link untuk download file
      const link = document.createElement('a');
      link.href = url;
      link.download = filename;
      link.click();

      // Hapus elemen link setelah download selesai
      link.remove();
    }
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
.drag-drop-area {
  border: 2px dashed #ccc;
  padding: 20px;
  text-align: center;
  cursor: pointer;
  background-color: #fff;
}

.dragging {
  border-color: #169ea8;
  background-color: #e9f7ff;
}

.drag-drop-area p {
  margin: 0;
  font-size: 16px;
}
</style>