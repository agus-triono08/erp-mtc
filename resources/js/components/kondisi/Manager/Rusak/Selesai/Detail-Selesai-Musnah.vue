<template>
  <div class="container-fluid">
    <!-- Head -->
    <div class="row mb-2 align-items-center" >
      <div class="col-sm-6"><h3 style="font-family: Raleway;" class="text-black-10">Detail Pemusnahan Alat/Mesin</h3> 
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
          <h4 class="text-capitalize text-primary text-bold"><b>No Pemusnahan #{{ dataProses.no_pemusnahan }}</b></h4>
        </div>
        <div class="col-6">
          <dt style="color: #000;">Nama Produk</dt>
          <dd>{{ dataProses.no_seri && dataProses.no_seri.tools && dataProses.no_seri.tools.nama }}</dd>
          <dt class="text-black-10">Layout</dt>
          <dd v-if="dataProses.no_seri && dataProses.no_seri.layout">
            Ruang {{ dataProses.no_seri.layout.ruang }} / Rak {{ dataProses.no_seri.layout.rak }} / Lantai {{ dataProses.no_seri.layout.lantai }} / Koordinat: {{ dataProses.no_seri.layout.koordinat }}
          </dd>
        </div>
        <!-- <div class="col-3">
          <dt style="color: #000;">Target</dt>
          <dd>{{ dataProses.tgl_selesai }} <br>
            <small>
              <i :class="{'fas fa-clock': !durasidata[0].includes('hari lewat') && !durasidata[0].includes('hari lagi'), 'fas fa-exclamation-circle text-danger': durasidata[0].includes('hari lewat') || durasidata[0].includes('hari lagi')}"></i>
              <span :class="{'text-danger': durasidata[0].includes('hari lewat') || durasidata[0].includes('hari lagi')}">
                {{ durasidata[0] }}
              </span>
            </small>
          </dd>
        </div> -->
        <div class="col-6">
          <dt style="color: #000;">Status</dt>
          <dd>
            <div 
              class="badge"
              :class="{
                        'status-active': dataProses.status === 'Selesai',
                        'status-error': dataProses.status === 'Proses',
                        'status-musnah': dataProses.status === 'Belum',}">
              {{ dataProses.status }}
            </div>
          </dd>
        </div>
      </div>        
    </div>
    <!-- Aktivity -->
    <div class="card shadow mt-5 mb-3">
      <div class="m-2">
        <div class="col-12">
          <h4 class="text-capitalize text-primary text-bold"><b>Aktivitas Pemusnahan</b></h4>
        </div>        
        <div class="row align-items-center justify-content-end m-3">
          <!-- <button class="btn btn-primary mr-2" @click="showModal = true">Tambah</button>
          <div class="btn-group">
            <button class="btn btn-primary mr-2" @click="downloadBA(dataProses.length - 1)">Download BA</button>            
          </div>           -->
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
          <table class="table table-border no-border table-custom"  style="overflow-y: auto; min-width: 1300px;">
            <thead class="bg-table">
              <tr class="text-center" style="color: #000;">
                <th>#</th>
                <th>Dokumen Musnah</th>
                <th>Berita Acara</th>
                <th>Detail</th>
                <th>Tanggal Dimusnahkan</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody v-if="dataProses.musnah_activity && dataProses.musnah_activity.length === 0">
              <tr>
                <td colspan="4" class="text-center">Tidak Ada Data</td>
              </tr>
            </tbody>
            <tbody>
              <tr v-for="(item, index) in dataProses.musnah_activity" :key="item.id" class="text-center">
                <td>{{ index + 1 }}</td>
                <!-- === DOKUMEN PEMUSNAHAN === -->
                <td>
                  <ul>
                    <li v-for="(doc, idx) in JSON.parse(item.dokumen_pemusnahan)" :key="idx">
                      <!-- PDF -->
                      <embed
                        v-if="doc.endsWith('.pdf')"
                        :src="`/storage/${doc}`"
                        type="application/pdf"
                        width="300"
                        height="150"
                      />
                      <!-- Gambar -->
                      <img
                        v-else-if="['jpg', 'jpeg', 'png'].includes(doc.split('.').pop())"
                        :src="`/storage/${doc}`"
                        alt="Dokumen Pemusnahan"
                        class="img-thumbnail"
                        style="width: 100px; height: 100px"
                      />
                      <!-- Tipe lain -->
                      <!-- <a v-else :href="`/storage/${doc}`" target="_blank">Lihat Dokumen {{ idx + 1 }}</a> -->
                    </li>
                  </ul>
                </td>
                <!-- <td>
                  <div v-for="(image, index) in item.dokumen_pemusnahan" :key="index">
                    <iframe :src="image" frameborder="0"height="500" style="margin: 5px;"></iframe>
                  </div>
                </td> -->
                <!-- === BERITA ACARA === -->
                <td>
                  <div v-if="item.berita_acara">
                    <!-- Jika PDF -->
                    <embed
                      v-if="item.berita_acara.endsWith('.pdf')"
                      :src="`/storage/${item.berita_acara}`"
                      type="application/pdf"
                      width="300"
                      height="150"
                    />
                    <!-- Jika gambar -->
                    <img
                      v-else-if="['jpg', 'jpeg', 'png'].includes(item.berita_acara.split('.').pop())"
                      :src="`/storage/${item.berita_acara}`"
                      alt="Berita Acara"
                      class="img-thumbnail"
                      style="width: 100px; height: 100px"
                    />
                    <!-- Jika tipe lain -->
                    <!-- <a v-else :href="`/storage/${activity.berita_acara}`" target="_blank">Lihat Berita Acara</a> -->
                  </div>
                  <span v-else>Tidak ada berita acara</span>
                </td>
                <!-- <td>
                  <div style="text-align: center;">
                    <pdf :src="item.berita_acara" :page="1" :rotate="0" @num-pages="numPages = $event" @page-loaded="currentPage = $event" @link-clicked="currentPage = $event" style="width: max-content; height: max-content; margin: 0 auto;" />
                  </div>
                </td> -->
                <td>{{ item.detail_pemusnahan || '-'}}</td>
                <td>{{ item.changed_at || '-'}}</td>
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
                      <a class="dropdown-item" @click="downloadDokumenPemusnahan(item)">
                        <i class="fas fa-download text-primary"></i> Dokumen Pemusnahan
                      </a>
                      <a class="dropdown-item" @click="downloadBeritaAcara(item)">
                        <i class="fas fa-download text-primary"></i> Berita Acara
                      </a>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>     
          <!-- Pagination -->
          <div class="d-flex justify-content-between align-items-center mt-3 mb-3" style="min-width: 1300px; border-radius: 10px; background-color: #f3f4f6; height: 50px; color: #000;">
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
    <div class="modal fade show" id="modalAktivitas" tabindex="-1" role="dialog" aria-labelledby="modalAktivitasLabel" v-if="showModal" style="overflow-y: auto;">
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
                <textarea class="form-control" id="detail" v-model="aktivitas.detail" required></textarea>
              </div>
              <div class="form-group">
                <label for="ba">Upload Berita Acara (BA) - PDF Only</label>
                <input type="file" id="ba" @change="handleBAUpload" accept="application/pdf" class="form-control" required/>                              
              </div>
              <!-- Upload Bukti Pemusnahan - Multiple Files (Image/PDF) -->
              <div class="form-group">
                <label for="buktiPemusnahan">Upload Bukti Pemusnahan - Images/PDF</label>
                <input type="file" id="buktiPemusnahan" @change="handleBuktiPemusnahanUpload" multiple class="form-control" required/>
              </div>
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
import vSelect from 'vue-select';
import Swal from 'sweetalert2';
import pdf from 'vue-pdf';
import jsPDF from "jspdf";
import 'jspdf-autotable';


export default {
  components: {
    vSelect,
  },
  data() {
    return {
      showModal: false,
      showSelesaiModal: false,
      aktivitas: {
        detail: '',
        ba: null,
        buktiPemusnahan: [],
      },
      aktivitasList: [],
      dataProses: [],
      dataAktivitas: [],
      layouts: [],
      users: [],
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
      isAktivitasSelesai: false,
    }
  },
  computed: {
    detailItem() {
      return this.dataProses.find(item => item.no_perbaikan == this.$route.params.id);
    },
    isLastAktivitasCompleted() {
      const lastAktivitas = this.dataAktivitas[this.dataAktivitas.length - 1];
      return lastAktivitas && (lastAktivitas.kondisi === 'OK' || lastAktivitas.kondisi === 'Rusak');
    },
    isAllAktivitasCompleted() {
      return this.dataAktivitas.every(item => item.kondisi === 'OK' || item.kondisi === 'Rusak');
    },
    // Hanya tampilkan tombol "Tambah Aktivitas" jika aktivitas tidak selesai
    shouldShowTambahAktivitas() {
      return !this.isLastAktivitasCompleted && !this.isAllAktivitasCompleted;
    },
    durasidata() {
      return this.dataProses.map(item => {
        if (item.tgl_selesai && item.tgl_perbaikan) {
          const tglSelesai = new Date(item.tgl_selesai);
          const tglSekarang = new Date(item.tgl_perbaikan);
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
    filteredData() {
      let result = this.dataProses.filter(item => {
        return (
          item.detail_kerusakan.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          item.kondisi.toLowerCase().includes(this.searchQuery.toLowerCase()) 
        );
      });
      return result;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = start + this.rowsPerPage;
      return this.filteredData.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.dataProses.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(start + this.rowsPerPage - 1, this.dataProses.length);
      return `Showing ${start} to ${end} of ${this.dataProses.length} entries`;
    }
  },
  methods: {
    handleBAUpload(event) {
      this.aktivitas.ba = event.target.files[0];
    },
    // handleBAUpload(event) {
    //   const file = event.target.files[0];
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
    handleBuktiPemusnahanUpload(event) {
      this.aktivitas.buktiPemusnahan = Array.from(event.target.files).filter(file => {
        const allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
        const fileType = file.type.split('/').pop();
        console.log(fileType); // Tambahkan log untuk melihat tipe file
        return allowedTypes.includes(fileType);
      });
    },
    // handleBuktiPemusnahanUpload(event) {
    //   const files = event.target.files;
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
    //   // console.log('Bukti Pemusnahan berhasil diunggah:', this.aktivitas.buktiPemusnahan);
    // },
    async addAktivitas() {
      const id = this.$route.params.id;

      // Validasi wajib
      if (!this.aktivitas.detail || !this.aktivitas.ba || this.aktivitas.buktiPemusnahan.length === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Data tidak lengkap',
          text: 'Pastikan semua field wajib telah diisi.',
        });
        return;
      }

      const formData = new FormData();
      formData.append('id', id);
      formData.append('detail_pemusnahan', this.aktivitas.detail);
      formData.append('berita_acara', this.aktivitas.ba);

      this.aktivitas.buktiPemusnahan.forEach((file) => {
        formData.append('dokumen_pemusnahan[]', file);
      });

      try {
        const res = await fetch('/api/v1/pemusnahan/add-activity', {
          method: 'POST',
          headers: { Accept: 'application/json' },
          body: formData,
        });

        if (!res.ok) {
          const errData = await res.json();
          throw new Error(errData.message || 'Gagal menyimpan aktivitas');
        }

        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: 'Aktivitas berhasil disimpan!',
        });

        this.showModal = false;
        this.aktivitas = { detail: '', ba: null, buktiPemusnahan: [] };

      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: err.message || 'Terjadi kesalahan saat menyimpan data.',
        });
      }
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
    async fetchData() {
      const id = this.$route.params.id;
      const res = await fetch(`/api/v1/pemusnahan/${id}`);
      const data = await res.json();
      this.dataProses = data;
      // console.log(this.dataProses);
    },
    async fetchInitialData() {
      try {
        const [layoutsRes, usersRes] = await Promise.all([
          // axios.get('/api/v1/tools'),
          axios.get('/api/v1/layouts'),
          axios.get('/api/v1/users')
        ]);
        // this.tools = toolsRes.data;
        this.layouts = layoutsRes.data;
        this.users = usersRes.data.byPIC;
      } catch (err) {
        console.error('Gagal fetch data awal:', err);
      }
    },
    openAktivitasModal() {
      if (this.dataProses.rusak_activity && this.dataProses.rusak_activity.length > 0) {
        const lastAktivitas = this.dataProses.rusak_activity[this.dataProses.rusak_activity.length - 1];
        if (lastAktivitas.status === 'Menunggu Persetujuan Atasan') {
          Swal.fire({
            icon: 'error',
            title: 'Tidak dapat menambah Aktivitas',
            text: 'Aktivitas sebelumnya masih Menunggu Persetujuan Atasan.',
          });
          return;
        }
      }
      // Siapkan form modal
      this.aktivitas = {
        id: this.$route.params.id,
        waktu_mulai: '',
        waktu_selesai: '',
        detail_kerusakan: '',
        // kondisi: '',
        // layout: '',
        pic: [],
      };
      this.showModal = true;
    },
    downloadBA() {
      if (!this.dataProses) {
        console.error('No data available');
        return;
      }

      const filename = `BA_${this.dataProses.id}.pdf`;

      const pdf = new jsPDF();

      const tanggalString = this.dataProses.tgl_pemusnahan || '-';
      const tanggal = new Date(tanggalString);

      const hariArr = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
      const hari = hariArr[tanggal.getDay()];

      const bulanArr = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
      ];
      const bulan = bulanArr[tanggal.getMonth()];
      const tanggalFormatted = `${tanggal.getDate()} ${bulan} ${tanggal.getFullYear()}`;

      // Judul
      pdf.setFont('helvetica', 'bold');
      pdf.setFontSize(16);
      const title = 'Berita Acara Pemusnahan Barang';
      const titleWidth = pdf.getStringUnitWidth(title) * pdf.getFontSize() / pdf.internal.scaleFactor;
      const pageWidth = pdf.internal.pageSize.width;
      pdf.text(title, (pageWidth - titleWidth) / 2, 16);

      pdf.setFont("helvetica", "normal");
      pdf.setFontSize(12);
      pdf.text('Berdasarkan SK No. : ', 14, 30);
      pdf.line(60, 30, 110, 30); // garis kosong

      pdf.text(`Sehubung dengan rusaknya barang maka pada : `, 14, 36);
      pdf.text(`Hari`, 14, 42);
      pdf.text(`: ${hari}`, 52, 42);
      pdf.text(`Tanggal`, 14, 48);
      pdf.text(`: ${tanggalFormatted}`, 52, 48);

      pdf.text('Jenis Pemusnahan', 14, 54);
      pdf.text(':', 52, 54);
      pdf.line(55, 54, 110, 54); // garis kosong

      const textBeforeBold = 'Bertempat di ';
      const boldText = 'PT. Sinko Prima Alloy';
      const textAfterBold = ' telah melaksanakan pemusnahan barang berupa.';

      pdf.setFont("helvetica", "normal");
      pdf.text(textBeforeBold, 14, 60);
      const beforeWidth = pdf.getStringUnitWidth(textBeforeBold) * pdf.getFontSize() / pdf.internal.scaleFactor;
      pdf.setFont("helvetica", "bold");
      pdf.text(boldText, 14 + beforeWidth, 60);
      const boldWidth = pdf.getStringUnitWidth(boldText) * pdf.getFontSize() / pdf.internal.scaleFactor;
      pdf.setFont("helvetica", "normal");
      pdf.text(textAfterBold, 14 + beforeWidth + boldWidth, 60);

      // Tabel data
      const headers = ["#", "Nama Alat/Mesin", "No Seri"];
      const alat = this.dataProses.no_seri?.tools?.nama || '-';
      const noseri = this.dataProses.no_seri?.no_seri || '-';
      // const keterangan = this.dataProses.detail_pemusnahan || '-';

      const rows = [[1, alat, noseri]];

      pdf.autoTable({
        head: [headers],
        body: rows,
        startY: 68,
      });

      const textY = pdf.lastAutoTable.finalY + 10;
      pdf.text("Barang tersebut telah diperiksa dan terdapat rusak/cacat sehingga tidak memungkinkan untuk", 14, textY);
      pdf.text("digunakan kembali.", 14, textY + 6);

      const signatureSectionY = textY + 16;
      pdf.text("Dibuat Oleh,", 14, signatureSectionY);
      pdf.text("Diperiksa Oleh,", 90, signatureSectionY);
      pdf.text("Disetujui Oleh,", 150, signatureSectionY);

      const gapY = signatureSectionY + 30;
      const namaStaff = this.dataProses.users.nama || '-';
      pdf.text(namaStaff.toString(), 17, gapY);
      pdf.text("Manajer", 95, gapY);
      pdf.text("Direktur", 155, gapY);

      // Unduh PDF
      const pdfData = pdf.output('blob');
      const url = URL.createObjectURL(pdfData);
      const link = document.createElement('a');
      link.href = url;
      link.download = filename;
      link.click();
      link.remove();
    },
    downloadDokumenPemusnahan(item) {
      const dokumenPemusnahan = JSON.parse(item.dokumen_pemusnahan);
      dokumenPemusnahan.forEach((doc) => {
        const link = document.createElement('a');
        link.href = `/storage/${doc}`;
        link.download = doc;
        link.click();
        link.remove();
      });
    },
    downloadBeritaAcara(item) {
      if (item.berita_acara) {
        const link = document.createElement('a');
        link.href = `/storage/${item.berita_acara}`;
        link.download = item.berita_acara;
        link.click();
        link.remove();
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: 'Berita Acara tidak tersedia.',
        });
      }
    },
  },
  mounted() {
    this.fetchData();
    // this.fetchDataAktivitas();
    this.fetchInitialData();
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