<template>
  <div class="container-fluid">
    <!-- Head -->
    <div class="row mb-2 align-items-center" >
      <div class="col-sm-6"><h3 style="font-family: Raleway;">Detail Pemusnahan Alat/Mesin</h3> 
        <h6 style="color: rgb(128, 128, 128);"></h6>
      </div> 
      <div class="col-sm-6 mt-3">
        <ol class="breadcrumb float-sm-right bg-table" style="border-radius: 10px;">
          <li class="breadcrumb-item">
            <a style="color: #169ea8; text-decoration: none;" href="javascript:history.back()">Pemusnahan Alat/Mesin</a>
          </li>
          <li class="breadcrumb-item active" style="color: red;">
            <span>Detail Pemusnahan Alat/Mesin</span>
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
          <dt style="color: #000;">PIC Pemusnahan</dt>
          <dd>{{ data[0].pic }}</dd>
        </div>
        <div class="col-3">
          <dt style="color: #000;">Nama Alat/Mesin</dt>
          <dd>{{ data[0].nama }}</dd>
          <dt style="color: #000;">Layout</dt>
          <dd>{{ data[0].layout }}</dd>
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
        <div class="col-3">
          <dt style="color: #000;">Tanggal Pemusnahan</dt>
          <dd>{{ data[0].tgl }}</dd>
          <dt style="color: #000;">Watu Pemusnahan</dt>
          <dd>{{ data[0].waktu_mulai }} - {{ data[0].waktu_selesai }}</dd>
        </div>
        <div class="col-3">
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
        </div>
      </div>        
    </div>
    <!-- Aktivity -->
    <div class="card shadow mt-5 mb-3">
      <div class="m-2">
        <!-- <div class="col-12">
          <h4 class="text-capitalize text-primary text-bold"><b>Aktivitas</b></h4>
        </div>         -->
        <div class="row align-items-center justify-content-end m-3">          
          <!-- <button class="btn btn-primary mr-2" @click="showModal = true">Tambah</button> -->
          <div class="btn-group">
            <button class="btn btn-primary mr-2" @click="downloadBA(aktivitasList.length - 1)">Download BA</button>
            <button class="btn btn-success mr-2" @click="uploadBA(aktivitasList.length - 1)">Upload BA</button>
            <button class="btn btn-info mr-2" @click="uploadBuktiPemusnahan(aktivitasList.length - 1)">Upload Bukti Pemusnahan</button>
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
        <div class="col-12 table-responsive p-3">
          <table class="table table-border no-border table-custom">
            <thead class="bg-table">
              <tr class="text-center" style="color: #000;">
                <th>#</th>
                <th>Dokumen Musnah</th>
                <th>Berita Acara</th>
                <th>Detail</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in aktivitasList" :key="index" class="text-center">
                <td>{{ index + 1 }}</td>
                <td>
                  <div v-if="item.buktiPemusnahan && item.buktiPemusnahan.length > 0">
                    <div v-for="(image, imgIndex) in item.buktiPemusnahan" :key="imgIndex">
                      <img v-if="isImage(image)" :src="image" alt="Bukti Pemusnahan" class="img-fluid" style="max-width: 100px; margin: 5px;" />
                      <div v-else-if="isPdf(image)" style="text-align: center;">
                        <pdf :src="image" :page="1" :rotate="0" @num-pages="numPages = $event" @page-loaded="currentPage = $event" @link-clicked="currentPage = $event" style="width: max-content; height: max-content; margin: 0 auto;"/>
                      </div>
                    </div>
                  </div>
                  <div v-else>
                    <span>No Bukti</span>
                  </div>
                </td>
                <td>
                  <div style="text-align: center;">
                    <pdf :src="item.ba" :page="1" :rotate="0" @num-pages="numPages = $event" @page-loaded="currentPage = $event" @link-clicked="currentPage = $event" style="width: max-content; height: max-content; margin: 0 auto;"/>
                  </div>
                </td>
                <td>{{ item.detail }}</td>
                <!-- <td>
                  <div 
                    class="btn-sts"
                    :class="{
                      'status-rusak': item.kondisi === 'Rusak',
                      'status-active': item.kondisi === 'OK',
                      'status-error': item.kondisi === 'Error',
                    }">
                    {{ item.kondisi }}
                    </div>
                </td> -->
                <!-- <td>
                  <div 
                    class="badge"
                    :class="{
                      'status-rusak': item.status === 'Ditolak',
                      'status-active': item.status === 'Disetujui',
                      'status-hilang': item.status === 'Menunggu Persetujuan',
                    }">
                    {{ item.status }}
                    </div>
                </td> -->
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
                      <!-- <a class="dropdown-item" @click="downloadBA(index)">
                        <i class="fas fa-download text-primary"></i> Download BA
                      </a>
                      <a class="dropdown-item" @click="uploadBA(index)">
                        <i class="fas fa-upload text-success"></i> Upload BA
                      </a>
                      <a class="dropdown-item" @click="uploadBuktiPemusnahan(index)">
                        <i class="fas fa-upload text-info"></i> Upload Bukti Pemusnahan
                      </a> -->
                      <a class="dropdown-item" @click="editAktivitas(index)">
                        <i class="fas fa-edit text-warning"></i> Edit
                      </a>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>            
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3 mb-3 ml-3 mr-3" style="border-radius: 10px; background-color: #f3f4f6; height: 50px; color: #000;">
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
            <h5 class="modal-title" id="modalAktivitasLabel">Tambah Aktivitas</h5>
            <button type="button" class="close" @click="showModal = false" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" v-model="aktivitas.tanggal">
              </div>
              <div class="form-group">
                <label for="detail">Detail</label>
                <textarea class="form-control" id="detail" v-model="aktivitas.detail"></textarea>
              </div>
              <div class="form-group">
                <label for="kondisi">Kondisi</label>
                <select class="form-control" id="kondisi" v-model="aktivitas.kondisi">
                  <option value="">Pilih Kondisi</option>
                  <option value="OK">OK</option>
                  <option value="Rusak">Rusak</option>
                  <option value="Error">Error</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showModal = false">Batal</button>
            <button type="button" class="btn btn-primary" @click="addAktivitas">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Upload BA -->
    <div class="modal fade show" id="modalUploadBA" tabindex="-1" role="dialog" aria-labelledby="modalUploadBALabel" v-if="showUploadBAModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalUploadBALabel">Upload Berita Acara</h5>
            <button type="button" class="close" @click="showUploadBAModal = false" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="handleUploadBA">
              <div class="form-group">
                <label for="baFile">Pilih File Berita Acara (PDF)</label>
                
                <!-- Area Drag and Drop -->
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

                <!-- Hidden Input File for Selecting File -->
                <input type="file" class="form-control" id="baFile" ref="fileInput" @change="onBAFileChange" accept="application/pdf" style="display: none;" required>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" @click="showUploadBAModal = false">Batal</button>
            <button type="button" class="btn btn-primary" @click="handleUploadBA">Upload</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal Upload Bukti Pemusnahan -->
    <div class="modal fade show" id="modalUploadBuktiPemusnahan" tabindex="-1" role="dialog" aria-labelledby="modalUploadBuktiPemusnahanLabel" v-if="showUploadBuktiPemusnahanModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalUploadBuktiPemusnahanLabel">Upload Bukti Pemusnahan</h5>
            <button type="button" class="close" @click="showUploadBuktiPemusnahanModal = false" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="handleUploadBuktiPemusnahan">
              <div class="form-group">
                <label for="buktiPemusnahanFile">Pilih File Bukti Pemusnahan (Image/PDF)</label>
                
                <!-- Area Drag and Drop -->
                <div 
                  class="drag-drop-area" 
                  @dragover.prevent="onDragOver" 
                  @dragleave="onDragLeave" 
                  @drop="onDrop" 
                  @click="triggerFileInput"
                  :class="{'dragging': isDragging}"
                  >
                  <p v-if="!files">Seret dan jatuhkan file di sini, atau klik untuk memilih</p>
                  <p v-else>{{ files.length }} file terpilih</p>
                </div>

                <!-- Hidden Input File for Selecting File -->
                <input type="file" class="form-control" id="buktiPemusnahanFile" ref="fileInput" @change="onBuktiPemusnahanFileChange" accept="image/jpeg, image/png, application/pdf" multiple style="display: none;" required>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" @click="showUploadBuktiPemusnahanModal = false">Batal</button>
            <button type="button" class="btn btn-primary" @click="handleUploadBuktiPemusnahan">Upload</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal Edit Aktivitas -->
    <div class="modal fade show" id="modalEditAktivitas" tabindex="-1" role="dialog" aria-labelledby="modalEditAktivitasLabel" v-if="showEditModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalEditAktivitasLabel">Edit Aktivitas</h5>
            <button type="button" class="close" @click="showEditModal = false" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" v-model="aktivitasList[selectedIndex].tanggal">
              </div>
              <div class="form-group">
                <label for="detail">Detail</label>
                <textarea class="form-control" id="detail" v-model="aktivitasList[selectedIndex].detail"></textarea>
              </div>
              <div class="form-group">
                <label for="kondisi">Kondisi</label>
                <select class="form-control" id="kondisi" v-model="aktivitasList[selectedIndex].kondisi">
                  <option value="">Pilih Kondisi</option>
                  <option value="OK">OK</option>
                  <option value="Rusak">Rusak</option>
                  <option value="Error">Error</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showEditModal = false">Batal</button>
            <button type="button" class="btn btn-primary" @click="saveEditAktivitas">Simpan</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import pdf from 'vue-pdf';
import jsPDF from "jspdf";
import 'jspdf-autotable';

export default {
  components: {
    pdf
  },
  data() {
    return {
      showModal: false,
      aktivitas: {
        tanggal: '',
        detail: '',
        kondisi: ''
      },
      aktivitasList: [
      {
        id: 1,
        tanggal: '2022-01-01',
        detail: 'Aktivitas 1',
        kondisi: 'OK',
        status: 'Menunggu Persetujuan',
        ba: '/file/Berita-Acara-Pemusnahan-Barang.pdf',
        buktiPemusnahan: ['/file/tang.jpeg',         
        '/file/Berita-Acara-Pemusnahan-Barang.pdf']
      }
      ],
      data: [
        { no_seri: '1122wscj121', waktu_mulai: '08:00', waktu_selesai: '12:00', nama: 'Clamp', layout: 'E7', tgl: '2025-02-01', kondisi: 'Musnah', detail: 'Sensor tidak berfungsi', pic: 'John Doe', tgl_selesai: '2025-02-05', status: 'Proses' },        
      ],
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
      showUploadBAModal: false, // Mengatur visibilitas modal upload BA
      selectedBAFile: null, // File yang dipilih untuk upload
      files: [],
      isDragging: false, // Status apakah file sedang di-drag
      showUploadBuktiPemusnahanModal: false,
      buktiPemusnahanFile: null,
      showEditModal: false,
      selectedIndex: null,
    }
  },
  computed: {
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
      this.aktivitasList.push({ ...this.aktivitas });
      this.aktivitas.tanggal = '';
      this.aktivitas.detail = '';
      this.aktivitas.kondisi = '';
      this.showModal = false;
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
     // Upload Berita Acara (BA)
    uploadBA(index) {
      this.openUploadBAModal(index);  // Open modal to upload BA for a specific activity
    },

    // Menampilkan modal untuk upload BA
    openUploadBAModal(index) {
      this.showUploadBAModal = true;
      this.selectedIndex = index; // Menyimpan index dari aktivitas yang akan di-upload BA
    },

    // Menangani perubahan file yang di-upload
    onBAFileChange(event) {
      const file = event.target.files[0];
      if (file && file.type === 'application/pdf') {
        this.selectedBAFile = file;
      } else {
        alert('Harap pilih file PDF!');
      }
    },

    // Menangani upload Berita Acara (BA)
    // handleUploadBA() {
    //   if (!this.selectedBAFile) {
    //     alert('Pilih file BA terlebih dahulu!');
    //     return;
    //   }

    //   const formData = new FormData();
    //   formData.append('ba', this.selectedBAFile);

    //   // Contoh pengiriman ke server untuk disimpan
    //   // Misalnya menggunakan axios:
    //   // axios.post('/upload-ba', formData).then(response => {
    //   //   console.log('File berhasil diupload:', response);
    //   // });

    //   // Menyimpan file ke array aktivitasList (sebagai contoh)
    //   this.aktivitasList[this.selectedIndex].ba = URL.createObjectURL(this.selectedBAFile);

    //   // Menutup modal setelah berhasil upload
    //   this.showUploadBAModal = false;
    //   this.selectedBAFile = null;
    //   alert('Berita Acara berhasil diupload!');
    // },

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
        this.showUploadBAModal = false; // Tambahkan kode ini untuk menutup modal
      } else {
        alert('Harap pilih file PDF terlebih dahulu!');
      }
    },
    uploadBuktiPemusnahan(index) {
      this.openUploadBuktiPemusnahanModal(index);
    },
    openUploadBuktiPemusnahanModal(index) {
      this.showUploadBuktiPemusnahanModal = true;
      this.selectedIndex = index;
    },
    onBuktiPemusnahanFileChange(event) {
      this.files = event.target.files;
    },
    handleUploadBuktiPemusnahan() {
      if (this.files.length > 0) {
        // Implementasikan logika untuk meng-upload file
        alert('File berhasil di-upload: ' + this.files.length + ' file');
        this.showUploadBuktiPemusnahanModal = false;
      } else {
        alert('Harap pilih file terlebih dahulu!');
      }
    },
    saveEditAktivitas() {
      // Simpan perubahan data
      this.showEditModal = false;
    },
    isImage(url) {
      return url.match(/\.(jpeg|jpg|gif|png)$/) !== null;
    },
    isPdf(url) {
      return url.match(/\.(pdf)$/) !== null;
    },
    downloadBA(index) {
      const ba = this.aktivitasList[index].ba;
      const filename = `BA_${index}.pdf`;

      // Buat elemen canvas untuk render PDF
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');

      // Membuat objek jsPDF
      const pdf = new jsPDF();

      const tanggalString = this.aktivitasList[index].tanggal || '-';
      const tanggal = new Date(tanggalString);

      // Menentukan hari dalam seminggu
      const hariArr = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
      const hari = hariArr[tanggal.getDay()];

      // Menentukan nama bulan
      const bulanArr = [
          "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"
      ];
      const bulan = bulanArr[tanggal.getMonth()];

      // Menyusun format tanggal
      const tanggalFormatted = `${tanggal.getDate()} ${bulan} ${tanggal.getFullYear()}`;

      // Set font menjadi bold dan ukuran 16
      pdf.setFont('helvetica', 'bold');
      pdf.setFontSize(16);

      // Menambahkan judul Berita Acara Pemusnahan Barang dengan teks di tengah
      const title = 'Berita Acara Pemusnahan Barang';
      const titleWidth = pdf.getStringUnitWidth(title) * pdf.getFontSize() / pdf.internal.scaleFactor;
      const pageWidth = pdf.internal.pageSize.width;
      const xPos = (pageWidth - titleWidth) / 2;
      pdf.text(title, xPos, 10);

      // Set font menjadi normal untuk teks berikutnya
      pdf.setFont('helvetica', 'normal');
      pdf.setFontSize(12);

      // Menambahkan informasi lainnya
      pdf.text(`Sehubung dengan rusaknya barang maka pada : `, 14, 20);
      pdf.text(`Hari`, 14, 26);
      pdf.text(`: ${hari}`, 40, 26);
      pdf.text(`Tanggal`, 14, 32);
      pdf.text(`: ${tanggalFormatted}`, 40, 32);
      // pdf.text('Tanggal: ' + this.aktivitasList[index].tanggal, 14, 26);
      // pdf.text('Detail: ' + this.aktivitasList[index].detail, 14, 32);

      const textBeforeBold = 'Bertempat di ';
      const boldText = 'PT. Sinko Prima Alloy';
      const textAfterBold = ' telah melaksanakan pemusnahan barang berupa.';

      // Tulis bagian normal terlebih dahulu
      pdf.setFont("helvetica", "normal");
      pdf.text(textBeforeBold, 14, 38);

      const textBeforeBoldWidth = pdf.getStringUnitWidth(textBeforeBold) * pdf.internal.getFontSize() / pdf.internal.scaleFactor;

      pdf.setFont("helvetica", "bold");
      const boldTextX = 14 + textBeforeBoldWidth;  // Posisi setelah bagian normal
      pdf.text(boldText, boldTextX, 38);

      const boldTextWidth = pdf.getStringUnitWidth(boldText) * pdf.internal.getFontSize() / pdf.internal.scaleFactor;

      pdf.setFont("helvetica", "normal");
      const textAfterBoldX = boldTextX + boldTextWidth;  // Posisi setelah bagian boldText
      pdf.text(textAfterBold, textAfterBoldX, 38);
      
      const headers = [
        "#",
        "Nama Alat/Mesin",
        "No Seri",
        "Keterangan",
      ];
      const rows = [];

      this.data.forEach((item, index) => {
        rows.push([
          index + 1,
          item.nama,
          item?.no_seri,
          item.detail,
        ]);
      });

      pdf.autoTable({
        head: [headers],
        body: rows,
        startY: 50, // Menyesuaikan posisi tabel setelah judul, nama peminjam, dan divisi
      });

      const textY = pdf.lastAutoTable.finalY + 10;
      pdf.text("Barang tersebut telah diperiksa dan terdapat rusak/cacat sehingga tidak memungkinkan untuk ", 14, textY);
      pdf.text("digunakan kembali.", 14, textY + 6);

      const signatureSectionY = textY + 16;
      pdf.text("Dibuat Oleh,", 14, signatureSectionY);
      pdf.text("Diperiksa Oleh,", 90, signatureSectionY);
      pdf.text("Disetujui Oleh,", 150, signatureSectionY);

      const namaStaff = this.data[0].pic || '-';

      const gapY = signatureSectionY + 30;
      pdf.text(namaStaff, 17, gapY);
      pdf.text("Manajer", 95, gapY);
      pdf.text("Direktur", 155, gapY);

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