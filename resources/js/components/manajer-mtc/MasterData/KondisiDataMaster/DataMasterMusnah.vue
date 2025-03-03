<template>
  <div class="container-fluid" style="margin-top: 30px;">

    <!--- Modal Input data -->
    <div id="app" class="modal-input" :class="{'is-visible': showModalInput}" @click.self="tutupModal">
      <div class="modal-content-input">
        <input-alat-musnah @tutup-modal="tutupModal"></input-alat-musnah>
      </div>
    </div>

    <div class="row align-items-center justify-content-end mr-3 mt-3 mb-4">
      <!-- Tombol Diterima -->
      <button v-if="selectedMusnahIds.length > 0" 
              class="btn btn-sm btn-outline-success mr-2" 
              @click="updateStatus('Diterima')">
        <i class="fa fa-check"></i> Diterima
      </button>
      <!-- Tombol Ditolak -->
      <button v-if="selectedMusnahIds.length > 0" 
              class="btn btn-sm btn-outline-danger mr-2" 
              @click="updateStatus('Ditolak')">
        <i class="fa fa-times"></i> Ditolak
      </button>      
      <!-- Tombol Print PDF (Hanya tampil jika ada item yang dipilih) -->
      <!-- <button v-if="selectedMusnahIds.length > 0" 
              class="btn btn-sm btn-outline-primary mr-2" 
              @click="printSelectedMusnah">
        <i class="fa fa-print"></i> Print BA
      </button> -->
      <!-- Tambah Data -->
      <button class="btn btn-sm btn-outline-primary mr-2 ml-1" @click="tambahDataMusnah">
        <i class="fa fa-plus-circle"></i> Tambah Data
      </button>
      <!-- Search -->
      <div class="search-wrapper">
        <div class="input-group">
          <input type="text" placeholder="search..." class="form-control"
            v-model="searchQuery"
            @input="debouncedFetchAlats"/>
        </div>
      </div>
    </div>

    <div class="table-responsive text-wrape">
      <table class="table table-border no-border table-custom">
        <thead>
          <tr class="bg-table">
            <th class="text-center">
              <input type="checkbox" 
                v-model="selectAll" 
                @change="toggleSelectAll"/>
            </th>
            <th class="text-center text-black-1 tr-center">#</th>
            <th class="text-center text-black-1">No. Seri Alat</th>
            <!--<th class="text-center text-black-1">Stok Musnah</th>-->
            <th class="text-center text-black-1">PIC Pemusnahan</th>
            <th class="text-center text-black-1">Tanggal Pemusnahan</th>
            <th class="text-center text-black-1">Deskripsi Pemusnahan</th>
            <th class="text-center text-black-1">Status</th>
            <th class="text-center text-black-1">Dokument Pendukung</th>
            <!-- <th class="text-center text-black-1">Aksi</th> -->
          </tr>
        </thead>
        <tbody v-if="filteredData.length===0">
          <tr>
            <td colspan="8" class="text-center">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="(musnah, index) in filteredData" :key="index">
          <tr class="text-center">
            <td>
              <input type="checkbox" 
                :value="musnah.id" 
                v-model="selectedMusnahIds"/>
            </td>
            <td class="text-center">              
              {{ index + 1 }}
            </td>
            <td class="text-center">{{ musnah.no_seri_alat ? musnah.no_seri_alat.no_seri_alat : '-' }}</td>
            <td class="text-center">{{ musnah.staff_pemusnahan ? musnah.staff_pemusnahan.nama_staff : '-' }}</td>
            <td class="text-center">{{ musnah.tanggal_musnah || '-' }}</td>
            <td class="text-center">{{ musnah.deskripsi_musnah || '-' }}</td>
            <td>
              <div class="btn-sts" :class="{
                'status-active' : musnah.status === 'Diterima',
                'status-hilang' : musnah.status === 'Proses',
                'status-rusak' : musnah.status === 'Ditolak',
              }">
                {{ musnah.status || '-' }}
              </div>
            </td>
            <td class="text-center">
              <div v-if="musnah.fileUrl">
                <img v-if="musnah.isImage" :src="musnah.fileUrl" width="100%" height="200" style="cursor: zoom-in;"/>
                <iframe v-else :src="musnah.fileUrl" width="100%" height="200" style="cursor: zoom-in;"></iframe>
              </div>
            </td>
            <!-- <td></td> -->
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
</template>
<script>
import axios from "axios";
import jsPDF from "jspdf";
import 'jspdf-autotable';

export default {
  props: {
    kodeAlat: String
  },
  data() {
    return {
      staff: {
        nama_staff: '',
      },
      datamusnah: [],            
      isImage: false,
      showModalInput: false,
      showModalEdit: false,
      searchQuery: '',
      idEdit: null,
      currentPage: 1,
      rowsPerPage: 10,
      selectedMusnahIds: [], // Menyimpan ID yang dipilih
      selectAll: false, // Mengontrol checkbox Select All
    };
  },
  computed: {
    paginatedAlats() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      return this.datamusnah.slice(start, start + this.rowsPerPage);
    },
    totalPages() {
      return Math.ceil(this.datamusnah.length / this.rowsPerPage);
    },
    paginationInfo() {
      if (!this.datamusnah.length) return '0-0 of 0';
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(start + this.rowsPerPage - 1, this.datamusnah.length);
      return `Showing ${start} to ${end} of ${this.datamusnah.length} entries`;
    },
    filteredData() {
      return this.datamusnah.filter(musnah =>{
        const searchQueryLower = this.searchQuery.toLowerCase();
        const noSeriAlat = musnah.no_seri_alat && musnah.no_seri_alat.no_seri_alat;
        const namaStaff = musnah.staff_pemusnahan && musnah.staff_pemusnahan.nama_staff;
        const Layout = musnah.layout && musnah.layout.nama_layout;
        const detailRusak = musnah.deskripsi_musnah;

        return(
          (noSeriAlat && noSeriAlat.toLowerCase().includes(searchQueryLower)) ||
          (namaStaff && namaStaff.toLowerCase().includes(searchQueryLower)) ||
          (Layout && Layout.toLowerCase().includes(searchQueryLower)) ||
          (detailRusak && detailRusak.toLowerCase().includes(searchQueryLower))
        );
      })
    }
  },
  methods: {
    async fetchAlatMusnah() {
      try {
        const kodeAlat = this.kodeAlat;
        //console.log(this.kodeAlat);
        const response = await axios.get(`/api/alats/datamusnah/${kodeAlat}`);
        this.datamusnah = response.data;
        console.log(this.datamusnah);
      } catch (error) {
        console.error("Error fetching detail alat musnah : ", error);
        //alert("Gagal memuat detail data alat musnah.");
      }
    }, 
    debouncedFetchAlats: _.debounce(function () {
        this.fetchAlatMusnah();
      }, 300),
    tambahDataMusnah() {
      this.showModalInput = true;
    },
    tutupModal() {
      this.showModalInput = false;
    },
    prevPage() {
      if (this.currentPage > 1) this.currentPage--;
    },
    nextPage() {
      if (this.currentPage < this.totalPages) this.currentPage++;
    },
    // Fungsi untuk memperbarui status
    updateStatus(status) {
      // Memperbarui status untuk setiap ID yang dipilih
      this.selectedMusnahIds.forEach(id => {
        const musnah = this.datamusnah.find(item => item.id === id);
        if (musnah) {
          musnah.status = status;
        }
      });
      // Reset selectedMusnahIds setelah memperbarui status
      this.selectedMusnahIds = [];
    },
    toggleSelectAll() {
      if (this.selectAll) {
        // Jika checkbox selectAll dicentang, pilih semua checkbox
        this.selectedMusnahIds = this.filteredData.map(musnah => musnah.id);
      } else {
        // Jika checkbox selectAll tidak dicentang, kosongkan semua pilihan
        this.selectedMusnahIds = [];
      }
    },
    printSelectedMusnah() {
      const selectedData = this.datamusnah.filter(musnah => 
        this.selectedMusnahIds.includes(musnah.id)
      );
      if (selectedData.length > 0) {
        const doc = new jsPDF();

      // Format tanggal
      const tanggalString = this.filteredData[0]?.tanggal_musnah || '-';
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

      // Title of the document
      const title = "Berita Acara Pemusnahan Barang";
      const titleWidth = doc.getStringUnitWidth(title) * doc.internal.getFontSize() / doc.internal.scaleFactor;
      const titleX = (doc.internal.pageSize.width - titleWidth) / 2;
      doc.text(title, titleX, 16);

      doc.setFont("helvetica", "normal");
      doc.setFontSize(12);
      doc.text('Berdasarkan SK No. : ', 14, 30);

      // Draw a long line next to 'SK No.'
      const skX = 60;  // X-coordinate where the line starts
      const skY = 30;  // Y-coordinate where the line should start
      const lineLength = 50; // Length of the line
      doc.line(skX, skY, skX + lineLength, skY); // Draw the line

      doc.text(`Sehubung dengan rusaknya barang maka pada : `, 14, 36);

      doc.text(`Hari`, 14, 42);

      doc.text(`: ${hari}`, 40, 42); // Menampilkan Hari

      doc.text(`Tanggal`, 14, 48);

      doc.text(`: ${tanggalFormatted}`, 40, 48); // Menampilkan tanggal dengan format Hari, Bulan, Tahun

      // Menulis teks dengan hanya PT. Sinko Prima Alloy yang bold
      const textBeforeBold = 'Bertempat di ';
      const boldText = 'PT. Sinko Prima Alloy';
      const textAfterBold = ' telah melaksanakan pemusnahan barang berupa.';

      // Tulis bagian normal terlebih dahulu
      doc.setFont("helvetica", "normal");
      doc.text(textBeforeBold, 14, 54);

      // Hitung posisi X untuk boldText (dimulai setelah textBeforeBold)
      const textBeforeBoldWidth = doc.getStringUnitWidth(textBeforeBold) * doc.internal.getFontSize() / doc.internal.scaleFactor;
      
      // Tulis PT. Sinko Prima Alloy dengan bold
      doc.setFont("helvetica", "bold");
      const boldTextX = 14 + textBeforeBoldWidth;  // Posisi setelah bagian normal
      doc.text(boldText, boldTextX, 54);

      // Hitung posisi X untuk textAfterBold (dimulai setelah boldText)
      const boldTextWidth = doc.getStringUnitWidth(boldText) * doc.internal.getFontSize() / doc.internal.scaleFactor;

      // Tulis sisanya setelah PT. Sinko Prima Alloy
      doc.setFont("helvetica", "normal");
      const textAfterBoldX = boldTextX + boldTextWidth;  // Posisi setelah bagian boldText
      doc.text(textAfterBold, textAfterBoldX, 54);

      // Menambahkan header tabel
      const headers = [
        "#",
        "Kode",
        "No Seri",
        "Nama",
        "Keterangan",
        "Status",
      ];
      const rows = [];

      // Mengisi data tabel
      this.filteredData.forEach((item, index) => {
        rows.push([
          index + 1,
          item.kode_alat,
          item?.no_seri_alat?.no_seri_alat,
          item?.alat?.nama_alat,
          item.deskripsi_musnah,
          item.status,
        ]);
      });

      // Menambahkan tabel ke PDF
      doc.autoTable({
        head: [headers],
        body: rows,
        startY: 60, // Menyesuaikan posisi tabel setelah judul, nama peminjam, dan divisi
      });

      const textY = doc.lastAutoTable.finalY + 10;
      doc.text("Barang tersebut telah diperiksa dan terdapat rusak/cacat sehingga tidak memungkinkan untuk ", 14, textY);
      doc.text("digunakan kembali.", 14, textY + 6);

      const signatureSectionY = textY + 16;
      doc.text("Dibuat Oleh,", 14, signatureSectionY);
      doc.text("Diperiksa Oleh,", 90, signatureSectionY);
      doc.text("Disetujui Oleh,", 150, signatureSectionY);

      const namaStaff = this.filteredData[0]?.staff_pemusnahan?.nama_staff || '-';

      const gapY = signatureSectionY + 30;
      doc.text(namaStaff, 17, gapY);
      doc.text("Manajer", 95, gapY);
      doc.text("Direktur", 155, gapY);

      doc.save("Berita-Acara-Pemusnahan-Barang.pdf");
      }
    },
  },
  mounted() {
    this.fetchAlatMusnah();
  }
}
</script>
<style>
  .no-border {
    border: none;
  }

  .no-border th,
  .no-border td {
    border-top: none !important;
    border-bottom: none !important;
  }

  .compact-table th,
  .compact-table td {
    padding: 0.1rem 0.3rem;
  }

  .compact-table tbody tr {
    margin-bottom: 0;
  }

  .compact-table th {
    padding-left: 0.2rem;
    padding-right: 0.2rem;
  }

  .compact-table td {
    padding-left: 0.2rem;
    padding-right: 0.2rem;
  }
  .status-pending {
  background-color: rgba(117, 134, 148, 0.1); /* Hijau dengan transparansi */
  color: rgba(117, 134, 148); 
}
</style>