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
          <button class="btn btn-primary mr-2" @click="showModal = true">Tambah</button>
          <div class="btn-group">
            <button class="btn btn-primary mr-2" @click="downloadBA(aktivitasList.length - 1)">Download BA</button>            
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
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in aktivitasList" :key="index" class="text-center">
                <td>{{ index + 1 }}</td>
                <td>
                  <!-- <div v-if="item.buktiPemusnahan && item.buktiPemusnahan.length > 0">
                    <div v-for="(image, imgIndex) in item.buktiPemusnahan" :key="imgIndex">
                      <img v-if="isImage(image)" :src="image" alt="Bukti Pemusnahan" class="img-fluid" style="max-width: 100px; margin: 5px;" />
                      <div v-else-if="isPdf(image)" style="text-align: center;">
                        <pdf :src="image" :page="1" :rotate="0" @num-pages="numPages = $event" @page-loaded="currentPage = $event" @link-clicked="currentPage = $event" style="width: max-content; height: max-content; margin: 0 auto;"/>
                        <iframe :src="image" frameborder="0"></iframe>
                      </div>
                    </div>
                  </div>
                  <div v-else>
                    <span>No Bukti</span>
                  </div> -->
                  <!-- <div v-for="(image, index) in item.buktiPemusnahan" :key="index">
                    <pdf v-if="isPdf(image)" :src="image" :page="1" :rotate="0" @num-pages="numPages = $event" @page-loaded="currentPage = $event" @link-clicked="currentPage = $event" style="width: max-content; height: max-content; margin: 0 auto;" />
                    <img v-if="isImage(image)" :src="image" alt="Gambar" width="100" height="100" style="margin: 5px;">
                  </div> -->
                  <div v-for="(image, index) in item.buktiPemusnahan" :key="index">
                    <!-- <img :src="image" alt="Gambar" width="100" height="100" style="margin: 5px;"> -->
                    <iframe :src="image" frameborder="0"height="500" style="margin: 5px;"></iframe>
                    <!-- <pdf :src="image" :page="1" :rotate="0" @num-pages="numPages = $event" @page-loaded="currentPage = $event" @link-clicked="currentPage = $event" style="width: max-content; height: max-content; margin: 0 auto;" /> -->
                  </div>                  
                </td>
                <td>
                  <div style="text-align: center;">
                    <pdf :src="item.ba" :page="1" :rotate="0" @num-pages="numPages = $event" @page-loaded="currentPage = $event" @link-clicked="currentPage = $event" style="width: max-content; height: max-content; margin: 0 auto;" />
                    <!-- <a v-else :href="item.ba" target="_blank" download>{{ item.ba }}</a> -->
                    <!-- <iframe :src="item.ba" frameborder="0"></iframe> -->
                  </div>
                </td>
                <td>{{ item.detail }}</td>
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
            <form @submit.prevent="addAktivitas">
              <div class="form-group">
                <label for="detail">Detail</label>
                <textarea class="form-control" id="detail" v-model="aktivitas.detail"></textarea>
              </div>

              <!-- Upload Berita Acara (BA) - Drag and Drop + PDF Only -->
              <!-- <div class="form-group">
                <label for="ba">Upload Berita Acara (BA) - PDF Only</label>
                <div
                  class="drop-zone"
                  @dragover.prevent="handleDragOver"
                  @dragleave="handleDragLeave"
                  @drop.prevent="handleDrop"
                  @click="triggerBAInput"
                  :class="{ 'drag-over': isDragging }"
                >
                <p v-if="!baPreview">
                  <i class="fas fa-file-pdf"></i><br>
                  Drag and drop your PDF file here <br>or <br><span class="browse-link">Browse</span>
                </p>
                <p v-else>
                  <pdf :src="baPreview" :page="1" :rotate="0" />
                </p>
                </div>
                <input type="file" id="ba" ref="baInput" @change="handleBAUpload" accept="application/pdf" style="display: none;" />              
              </div> -->

              <div class="form-group">
                <label for="ba">Upload Berita Acara (BA) - PDF Only</label>
                <input type="file" id="ba" @change="handleBAUpload" accept="application/pdf" class="form-control"/>                              
              </div>

              <!-- Upload Bukti Pemusnahan - Multiple Files (Image/PDF) -->
              <div class="form-group">
                <label for="buktiPemusnahan">Upload Bukti Pemusnahan - Images/PDF</label>
                <input type="file" id="buktiPemusnahan" @change="handleBuktiPemusnahanUpload" multiple class="form-control" />
              </div>

              <!-- Upload Bukti Pemusnahan - Multiple Files (Image/PDF) -->
              <!-- <div class="form-group">
                <label for="buktiPemusnahan">Upload Bukti Pemusnahan - Images/PDF</label>
                <div
                  class="drop-zone"
                  @dragover.prevent="handleDragOverBuktiPemusnahan"
                  @dragleave="handleDragLeaveBuktiPemusnahan"
                  @drop.prevent="handleDropBuktiPemusnahan"
                  @click="triggerBuktiPemusnahanInput"
                  :class="{ 'drag-over': isDraggingBuktiPemusnahan }"
                >
                  <p v-if="!buktiPemusnahanPreview">
                    <i class="fas fa-file-pdf"></i><br>
                    Drag and drop your PDF file here <br>or <br><span class="browse-link">Browse</span>
                  </p>
                  <p v-else>
                    <img v-if="isImage(buktiPemusnahanPreview)" :src="buktiPemusnahanPreview" alt="Gambar" width="100" height="100" style="margin: 5px;">
                    <pdf v-if="isPdf(buktiPemusnahanPreview)" :src="buktiPemusnahanPreview" :page="1" :rotate="0" />
                  </p>
                </div>
                <input type="file" id="buktiPemusnahan" @change="handleBuktiPemusnahanUpload" multiple class="form-control" style="display: none;" />
              </div> -->

              <div class="modal-footer">
                <button type="button" class="btn btn-danger" @click="showModal = false">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
              </div>
            </form>
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
        detail: '',
        ba: null, // BA file (PDF)
        buktiPemusnahan: [],
      },
      aktivitasList: [
      // {
      //   id: 1,
      //   tanggal: '2022-01-01',
      //   detail: 'Aktivitas 1',
      //   kondisi: 'OK',
      //   status: 'Menunggu Persetujuan',
      //   ba: '/file/Berita-Acara-Pemusnahan-Barang.pdf',
      //   buktiPemusnahan: ['/file/tang.jpeg',         
      //   '/file/Berita-Acara-Pemusnahan-Barang.pdf']
      // }
      ],
      data: [
        { no_seri: '1122wscj121', waktu_mulai: '08:00', waktu_selesai: '12:00', nama: 'Clamp', layout: 'E7', tgl: '2025-02-01', kondisi: 'Musnah', detail: 'Sensor tidak berfungsi', pic: 'John Doe', tgl_selesai: '2025-02-05', status: 'Proses' },        
      ],
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
      isDragging: false,
      baPreview: null,
      isDraggingBuktiPemusnahan: false,
      buktiPemusnahanPreview: null,
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
    // handleBAUpload(event) {
    //   const file = event.target.files[0];
    //   if (file && file.type === 'application/pdf') {
    //     // Mengonversi file PDF menjadi URL objek yang dapat diakses oleh komponen
    //     this.aktivitas.ba = URL.createObjectURL(file);
    //     console.log('Berita Acara berhasil diunggah:', this.aktivitas.ba);
    //   } else {
    //     alert("Hanya file PDF yang diperbolehkan untuk Berita Acara!");
    //     this.aktivitas.ba = null;
    //   }
    // },

    // handleDragOver() {
    //   this.isDragging = true;
    // },
    // handleDragLeave() {
    //   this.isDragging = false;
    // },
    // handleDrop(event) {
    //   this.isDragging = false;
    //   const file = event.dataTransfer.files[0];
    //   if (file && file.type === "application/pdf") {
    //     // Mengonversi file PDF menjadi URL objek yang dapat diakses oleh komponen
    //     this.aktivitas.ba = URL.createObjectURL(file);
    //     // console.log("Berita Acara berhasil diunggah:", this.aktivitas.ba);
    //     // alert(`File "${file.name}" uploaded successfully!`);
    //   } else {
    //     alert("Hanya file PDF yang diperbolehkan untuk Berita Acara!");
    //     this.aktivitas.ba = null;
    //   }
    // },
    // triggerBAInput() {
    //   this.$refs.baInput.click();
    // },
    handleBAUpload(event) {
      const file = event.target.files[0];
      if (file && file.type === "application/pdf") {
        // Mengonversi file PDF menjadi URL objek yang dapat diakses oleh komponen
        this.aktivitas.ba = URL.createObjectURL(file);
        // console.log("Berita Acara berhasil diunggah:", this.aktivitas.ba);
        // alert(`File "${file.name}" uploaded successfully!`);
      } else {
        alert("Hanya file PDF yang diperbolehkan untuk Berita Acara!");
        this.aktivitas.ba = null;
      }
    },

    // handleDragOverBuktiPemusnahan() {
    //   this.isDraggingBuktiPemusnahan = true;
    // },
    // handleDragLeaveBuktiPemusnahan() {
    //   this.isDraggingBuktiPemusnahan = false;
    // },
    // handleDropBuktiPemusnahan(event) {
    //   this.isDraggingBuktiPemusnahan = false;
    //   const files = event.dataTransfer.files;
    //   const validFiles = [];

    //   for (let i = 0; i < files.length; i++) {
    //     const file = files[i];
    //     if (file.type === 'application/pdf' || file.type.startsWith('image/')) {
    //       // Mengonversi file menjadi URL objek
    //       validFiles.push(URL.createObjectURL(file));
    //     } else {
    //       alert("Hanya file PDF atau gambar yang diperbolehkan untuk Bukti Pemusnahan!");
    //     }
    //   }

    //   // Memperbarui buktiPemusnahan dengan file yang valid (URL objek)
    //   this.aktivitas.buktiPemusnahan = validFiles;
    //   this.buktiPemusnahanPreview = validFiles[0];
    // },
    // triggerBuktiPemusnahanInput() {
    //   this.$refs.buktiPemusnahanInput.click();
    // },

    // Method untuk menangani upload Bukti Pemusnahan (Multiple files, gambar/PDF)
    handleBuktiPemusnahanUpload(event) {
      const files = event.target.files;
      const validFiles = [];

      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (file.type === 'application/pdf' || file.type.startsWith('image/')) {
          // Mengonversi file menjadi URL objek
          validFiles.push(URL.createObjectURL(file));
        } else {
          alert("Hanya file PDF atau gambar yang diperbolehkan untuk Bukti Pemusnahan!");
        }
      }

      // Memperbarui buktiPemusnahan dengan file yang valid (URL objek)
      this.aktivitas.buktiPemusnahan = validFiles;
      // console.log('Bukti Pemusnahan berhasil diunggah:', this.aktivitas.buktiPemusnahan);
    },

    // Method untuk menangani upload Bukti Pemusnahan (Multiple files, hanya PDF)
    // handleBuktiPemusnahanUpload(event) {
    //   const file = event.target.files[0];
    //   if (file && file.type === 'application/pdf') {
    //     // Mengonversi file PDF menjadi URL objek yang dapat diakses oleh komponen
    //     this.aktivitas.buktiPemusnahan = URL.createObjectURL(file);
    //     console.log('Berita Acara berhasil diunggah:', this.aktivitas.buktiPemusnahan);
    //   } else {
    //     alert("Hanya file PDF yang diperbolehkan untuk Berita Acara!");
    //     this.aktivitas.buktiPemusnahan = null;
    //   }
    // },
    addAktivitas() {
      if (!this.aktivitas.ba || this.aktivitas.buktiPemusnahan.length === 0) {
        alert("Harap unggah Berita Acara (BA) dan Bukti Pemusnahan!");
        return;
      }

      // Create a new activity object with the uploaded files
      this.aktivitasList.push({ ...this.aktivitas });

      // Reset form fields
      this.aktivitas = {
        detail: '',
        ba: null,
        buktiPemusnahan: [],
      };

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
    isImage(url) {
      // Memastikan bahwa url adalah string sebelum menggunakan match
      return typeof url === 'string' && url.match(/\.(jpeg|jpg|gif|png)$/) !== null;
    },
    isPdf(url) {
      // Memastikan bahwa url adalah string sebelum menggunakan match
      return typeof url === 'string' && url.match(/\.(pdf)$/) !== null;
    },
    fileUrl(url) {
      return URL.createObjectURL(url);
    },
    // isPdf(url) {
    //   return url.endsWith('.pdf');
    // },
    // fileUrl(url) {
    //   return url;
    // },
    downloadBA(index) {
      // const ba = this.data[index].ba;
      if (this.data.length === 0) {
        console.error('No data available');
        return;
      }
      const filename = `BA_${index}.pdf`;

      // Buat elemen canvas untuk render PDF
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');

      // Membuat objek jsPDF
      const pdf = new jsPDF();      

      const tanggalString = this.data[0].tgl || '-';
      console.log(tanggalString);
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
      pdf.text(title, xPos, 16);

      pdf.setFont("helvetica", "normal");
      pdf.setFontSize(12);
      pdf.text('Berdasarkan SK No. : ', 14, 30);

      // Draw a long line next to 'SK No.'
      const skX = 60;  // X-coordinate where the line starts
      const skY = 30;  // Y-coordinate where the line should start
      const lineLength = 50; // Length of the line
      pdf.line(skX, skY, skX + lineLength, skY); // Draw the line

      // Menambahkan informasi lainnya
      pdf.text(`Sehubung dengan rusaknya barang maka pada : `, 14, 36);
      pdf.text(`Hari`, 14, 42);
      pdf.text(`: ${hari}`, 52, 42);
      pdf.text(`Tanggal`, 14, 48);
      pdf.text(`: ${tanggalFormatted}`, 52, 48);
      // pdf.text('Tanggal: ' + this.aktivitasList[index].tanggal, 14, 26);
      // pdf.text('Detail: ' + this.aktivitasList[index].detail, 14, 32);

      pdf.setFont("helvetica", "normal");
      pdf.setFontSize(12);
      pdf.text('Jenis Pemusnahan', 14, 54);
      pdf.text(':', 52, 54);
      // Draw a long line next to 'SK No.'
      const jpX = 55;  // X-coordinate where the line starts
      const jpY = 54;  // Y-coordinate where the line should start
      const lineLengthjp = 55; // Length of the line
      pdf.line(jpX, jpY, jpX + lineLengthjp, jpY); // Draw the line

      const textBeforeBold = 'Bertempat di ';
      const boldText = 'PT. Sinko Prima Alloy';
      const textAfterBold = ' telah melaksanakan pemusnahan barang berupa.';

      // Tulis bagian normal terlebih dahulu
      pdf.setFont("helvetica", "normal");
      pdf.text(textBeforeBold, 14, 60);

      const textBeforeBoldWidth = pdf.getStringUnitWidth(textBeforeBold) * pdf.internal.getFontSize() / pdf.internal.scaleFactor;

      pdf.setFont("helvetica", "bold");
      const boldTextX = 14 + textBeforeBoldWidth;  // Posisi setelah bagian normal
      pdf.text(boldText, boldTextX, 60);

      const boldTextWidth = pdf.getStringUnitWidth(boldText) * pdf.internal.getFontSize() / pdf.internal.scaleFactor;

      pdf.setFont("helvetica", "normal");
      const textAfterBoldX = boldTextX + boldTextWidth;  // Posisi setelah bagian boldText
      pdf.text(textAfterBold, textAfterBoldX, 60);
      
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
        startY: 68, // Menyesuaikan posisi tabel setelah judul, nama peminjam, dan divisi
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

.drop-zone {
  width: 100%;
  height: 150px;
  border: 2px dashed #169ea8;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #000;
  font-size: 18px;
  cursor: pointer;
  transition: background-color 0.3s, border-color 0.3s;
}
.drop-zone.drag-over {
  border-color: #007bff;
  background-color: #f0f8ff;
  color: #007bff;
}
.preview-mini {
  width: 100px;
  height: 100px;
  border: 1px solid #ddd;
  margin-top: 10px;
}
</style>