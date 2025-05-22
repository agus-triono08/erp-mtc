<template>
  <div class="container-fluid">
    <!-- Head -->
    <div class="row mb-2 align-items-center" >
      <div class="col-sm-6"><h3 style="font-family: Raleway;" class="text-black-10">Detail Pengantian Alat/Mesin</h3> 
        <h6 style="color: rgb(128, 128, 128);"></h6>
      </div> 
      <div class="col-sm-6 mt-3">
        <ol class="breadcrumb float-sm-right bg-table" style="border-radius: 10px;">
          <li class="breadcrumb-item">
            <a style="color: #169ea8; text-decoration: none;" href="javascript:history.back()">Pengantian Alat/Mesin</a>
          </li>
          <li class="breadcrumb-item active" style="color: red;">
            <span>Detail Pengantian Alat/Mesin</span>
          </li>
        </ol>
      </div>
    </div>
    <!-- Detail -->
    <div class="card shadow">
      <div class="row m-1">
        <div class="col-12">
          <h4 class="text-capitalize text-primary text-bold"><b>No Kehilangan #{{ dataProses.no_kehilangan }}</b></h4>
        </div>
        <div class="col-3">
          <dt style="color: #000;">Nama Peminjam</dt>
          <dd>{{ dataProses.users && dataProses.users.nama }}</dd>
          <dt class="text-black-10">Divisi</dt>
          <dd>{{ dataProses.users && dataProses.users.divisi && dataProses.users.divisi.divisi }}</dd>          
        </div>
        <div class="col-3">
          <dt style="color: #000;">Nama Produk</dt>
          <dd>{{ dataProses.no_seri && dataProses.no_seri.tools && dataProses.no_seri.tools.nama }}</dd>
          <dt class="text-black-10">Layout</dt>
          <dd v-if="dataProses.no_seri && dataProses.no_seri.layout">
            Ruang {{ dataProses.no_seri.layout.ruang }} / Rak {{ dataProses.no_seri.layout.rak }} / Lantai {{ dataProses.no_seri.layout.lantai }} / Koordinat: {{ dataProses.no_seri.layout.koordinat }}
          </dd>
        </div>
        <div class="col-3">
          <dt style="color: #000;">Detail Hilang</dt>
          <dd>{{ dataProses.detail_hilang }}</dd>
          <dt class="text-black-10">Tanggal Kehilangan</dt>
          <dd>{{ dataProses.tgl_kehilangan }}</dd>          
        </div>
        <div class="col-3">
          <dt style="color: #000;">Status</dt>
          <dd>
            <div 
              class="badge"
              :class="{
                        'status-active': dataProses.status === 'Selesai',
                        'status-musnah': dataProses.status === 'Belum',
                        'status-error': dataProses.status === 'Proses'}">
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
          <h4 class="text-capitalize text-primary text-bold"><b>Aktivitas Kehilangan</b></h4>
        </div>        
        <div class="row align-items-center justify-content-end m-3">
          <!-- <button class="btn btn-primary mr-2" @click="showModal = true">Tambah</button> -->
          <!-- <button class="btn btn-primary mr-2" @click="downloadPJ(dataProses.length - 1)">Download BA</button>             -->
          <!-- <button class="btn btn-primary mr-3" @click="openAktivitasModal">Tambah Aktivitas</button>           -->          
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
                <th>Tanggal Penggantian Alat/Mesin</th>
                <th>No Seri Lama</th>
                <th>No Seri Baru</th>
                <th>Harga</th>
                <th>Status</th>

                <!-- <th>Aksi</th> -->
              </tr>
            </thead>
            <tbody v-if="dataProses.hilang_activity_proses && dataProses.hilang_activity_proses.length === 0">
              <tr>
                <td colspan="7" class="text-center">Tidak Ada Data</td>
              </tr>
            </tbody>
            <tbody>
              <tr v-for="(item, index) in dataProses.hilang_activity_proses" :key="item.id" class="text-center">
                <td>{{ index + 1 }}</td>
                <td>{{ item.tgl_penggantian || '-'}}</td>
                <td>{{ item.no_seri_old || '-'}}</td>
                <td>{{ item.no_seri_new || '-'}}</td>
                <td>{{ item.harga || '-'}}</td>
                <td>
                  <div 
                    class="btn-sts"
                    :class="{
                      'status-rusak': item.status === 'Menunggu Konfirmasi',
                      'status-active': item.status === 'Diterima',
                      'status-error': item.status === 'Serahkan Alat/Mesin',
                    }">
                    {{ item.status }}
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
                <label for="tgl_penggantian">Tanggal Pengantian</label>
                <input type="date" class="form-control" id="tgl_penggantian" v-model="aktivitas.tgl_penggantian" required></input>
              </div>
              <div class="form-group">
                <label for="harga">Harga</label>
                <input type="numeric" class="form-control" id="harga" v-model="aktivitas.harga" required></input>
              </div>
              <!-- <div class="form-group">
                <label for="ba">Upload Bukti Pertanggung Jawaban - PDF Only</label>
                <input type="file" id="buktiPertanggungJawaban" @change="handlePJUpload" accept="application/pdf" class="form-control" required/>                              
              </div> -->
              <!-- Upload Bukti Pemusnahan - Multiple Files (Image/PDF) -->
              <!-- <div class="form-group">
                <label for="buktiPemusnahan">Upload Bukti Pemusnahan - Images/PDF</label>
                <input type="file" id="buktiPemusnahan" @change="handleBuktiPemusnahanUpload" multiple class="form-control" required/>
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
import vSelect from 'vue-select';
import Swal from 'sweetalert2';
import jsPDF from "jspdf";
import 'jspdf-autotable';

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
        tgl_penggantian: null,
        harga: null,
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
    shouldShowDiterima() {
      return this.dataProses.rusak_activity.length > 0 && this.dataProses.rusak_activity[this.dataProses.rusak_activity.length - 1].status === 'Menunggu Persetujuan Atasan';
    },
    shouldShowDitolak() {
      return this.dataProses.rusak_activity.length > 0 && this.dataProses.rusak_activity[this.dataProses.rusak_activity.length - 1].status === 'Menunggu Persetujuan Atasan';
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
    handlePJUpload(event) {
      this.aktivitas.buktiPertanggungJawaban = event.target.files[0];
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
      const res = await fetch(`/api/v1/kehilangan/${id}`);
      const data = await res.json();
      this.dataProses = data;
      // console.log(this.dataProses);
    },
    async addAktivitas() {
      const id = this.$route.params.id;

      // Validasi wajib
      if (!this.aktivitas.tgl_penggantian || !this.aktivitas.harga) {
        Swal.fire({
          icon: 'warning',
          title: 'Data tidak lengkap',
          text: 'Pastikan semua field wajib telah diisi.',
        });
        return;
      }

      const formData = new FormData();
      formData.append('id', id);
      formData.append('tgl_penggantian', this.aktivitas.tgl_penggantian);
      formData.append('harga', this.aktivitas.harga);

      try {
        const res = await fetch('/api/v1/kehilangan/add-activity-proses', {
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
        this.aktivitas = { buktiPertanggungJawaban: null };

        await this.fetchData();

      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: err.message || 'Terjadi kesalahan saat menyimpan data.',
        });
      }
    },
    downloadPJ() {
      if (!this.dataProses) {
        console.error('No data available');
        return;
      }

      const filename = `PJ_${this.dataProses.id}.pdf`;

      // Buat elemen canvas untuk render PDF
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');

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

      // Tabel data
      const headers = ["#", "Nama Alat/Mesin", "No Seri"];
      const alat = this.dataProses.no_seri?.tools?.nama || '-';
      const noseri = this.dataProses.no_seri?.no_seri || '-';

      const rows = [[1, alat, noseri]];

      pdf.autoTable({
        head: [headers],
        body: rows,
        startY: 75,
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
    },
  },
  mounted() {
    this.fetchData();
    // this.fetchDataAktivitas();
    // this.fetchInitialData();
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