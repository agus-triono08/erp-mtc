<template>
  <div class="container-fluid">
    <div class="row align-items-center justify-content-end mr-3 mt-4 mb-2">
          <!-- Tombol Print PDF -->
          <button class="btn btn-sm btn-outline-primary mr-2" @click="printPDF">
            <i class="fas fa-print"></i> Print PDF
          </button>
          <div class="search-wrapper">
            <div class="input-group">
              <input type="text" placeholder="search..." class="form-control"
              v-model="searchQuery"
              @input="debouncedFetchAlats"/>              
            </div>
          </div>
        </div>
        <div class="table-responsive p-3">
          <table class="table table-border no-border table-custom" style="overflow-x: auto;">
            <thead>
              <tr class="bg-table text-center">
                <th class="text-center" style="width: 10px; color: #000;"></th>
                <th class="text-center" style="width: 10px; color: #000;">#</th>
                <th class="text-center" style="width: 10px; color: #000;">Kode Alat</th>
                <th class="text-center" style="width: 10px; color: #000;">Jumlah</th>
                <th class="text-center" style="width: 10px; color: #000;">Tgl Peminjaman</th>
                <th class="text-center" style="width: 10px; color: #000;">Tgl Pengembalian</th>
              </tr>
            </thead>
            <tbody v-if="filteredData.length===0">
            <tr>
              <td colspan="6" class="text-center">Tidak Ada Data</td>
            </tr>
          </tbody>
          <tbody v-for="(peminjamanalat, index) in filteredData" :key="index">
          <tr class="text-center">
            <td><button class="btn btn-sm btn-outline-primary"
              :class="{active: Detail_no_seri}"
              @click="toggleDetail_no_seri"
              >
              <span v-if="Detail_no_seri">-</span>
              <span v-else>+</span>
            </button></td>
            <td>{{ index +1 }}</td>
            <td>{{ peminjamanalat.tools.kode || '-' }}</td>
            <td>{{ peminjamanalat.total || '-'}}</td>
            <td>{{ peminjamanalat.tgl_pinjam || '-'}}</td>
            <td>{{ peminjamanalat.tgl_kembali || '-'}}</td>
          </tr>
          <tr v-if="Detail_no_seri" style="background: rgb(244, 246, 249);">
            <td colspan="100%">
              <table class="table table-border no-border table-custom" style="overflow-x: auto; background-color: #fff;">
                <thead>
                  <tr class="bg-table text-center">
                    <th class="text-center" style="width: 10px; color: #000;">#</th>
                    <th class="text-center" style="width: 10px; color: #000;">No Seri Alat</th>
                    <th class="text-center" style="width: 10px; color: #000;">Tgl Pengembalian</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="text-center" v-for="(noSeri, index) in dataPeminjamanAlat" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>
                      {{ noSeri.no_seri && noSeri.no_seri.length ? noSeri.no_seri.map(n => n.no_seri).join(', ') : '-' }}
                    </td>
                    <td>{{ noSeri.tgl_kembali || '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        </tbody>
        </table>
          <!-- Pagination -->
          <div class="d-flex justify-content-between align-items-center mb-3 mt-3" style="border-radius: 10px; background-color: #f3f4f6; height: 50px; color: #000;">
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
  props:{
    noPinjam: String,
  },
  data() {
    return {
      noseri: {
        no_seri_alat: '',
      },
      dataPeminjamanAlat: [],    
      Detail_no_seri: false,
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
    }
  },
  computed: {
    totalPages() {
      return Math.ceil(this.dataPeminjamanAlat.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(this.currentPage * this.rowsPerPage, this.dataPeminjamanAlat.length);
      return `Showing ${start} to ${end} of ${this.dataPeminjamanAlat.length} entries`;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = start + this.rowsPerPage;
      return this.dataPeminjamanAlat.slice(start, end);
    },
    filteredData() {
      if (this.searchQuery) {
        return this.paginatedData.filter(peminjamanalat => {
          return (
            peminjamanalat.tools.kode.toLowerCase().includes(this.searchQuery.toLowerCase())
          );
        });
      } else {
        return this.paginatedData;
      }
    }
  },
  methods: {
    async fetchPeminjamanAlat() {
      try {
        const noPinjam =this.noPinjam;
        //console.log(this.noPinjam);
        const response = await axios.get(`/api/v1/peminjaman/getNoPeminjaman/${noPinjam}`);
        this.dataPeminjamanAlat = response.data;
        // console.log(this.dataPeminjamanAlat);
      } catch (error) {
        console.error("Error fetching data peminjaman", error);
      }
    },
    toggleDetail_no_seri(){
      this.Detail_no_seri = !this.Detail_no_seri;
    },
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
      }
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
      }
    },
    debouncedFetchAlats: _.debounce(function () {
      this.fetchPeminjamanAlat();
    }, 300),
    // Metode untuk mencetak PDF
    printPDF() {
      const doc = new jsPDF();

      // Menambahkan judul di tengah dan bold
      doc.setFont("helvetica", "bold");
      doc.setFontSize(16);
      const title = "Laporan Peminjaman Alat";

      // Menentukan posisi judul agar berada di tengah
      const titleWidth = doc.getStringUnitWidth(title) * doc.internal.getFontSize() / doc.internal.scaleFactor;
      const titleX = (doc.internal.pageSize.width - titleWidth) / 2;
      doc.text(title, titleX, 16); // Menambahkan judul

      // Menambahkan Nama Peminjam di luar tabel (tidak bold)
      doc.setFont("helvetica", "normal"); // Kembalikan ke font normal untuk nama peminjam
      doc.setFontSize(12);
      const namaPeminjam = this.filteredData[0]?.pengguna?.nama_pengguna || "Tidak Diketahui"; // Ambil nama peminjam pertama atau "Tidak Diketahui" jika kosong
      doc.text(`Nama Peminjam: ${namaPeminjam}`, 14, 30); // Menampilkan nama peminjam

      // Menambahkan Divisi di bawah Nama Peminjam
      const divisi = this.filteredData[0]?.pengguna?.divisi || "Tidak Diketahui"; // Ambil divisi peminjam pertama atau "Tidak Diketahui" jika kosong
      doc.text(`Divisi: ${divisi}`, 14, 36); // Menampilkan divisi

      const tanggalPinjam = this.filteredData[0]?.tgl_pinjam || "Tidak Diketahui";
      doc.text(`Tanggal Peminjaman: ${tanggalPinjam}`, 100, 30);

      // Menghitung Durasi (selisih antara Tanggal Peminjaman dan Tanggal Pengembalian)
      const tanggalPeminjaman = new Date(this.filteredData[0]?.tgl_pinjam);
      const tanggalPengembalian = new Date(this.filteredData[0]?.tgl_kembali);
      const durasiInMillis = tanggalPengembalian - tanggalPeminjaman;
      const durasiInDays = Math.floor(durasiInMillis / (1000 * 3600 * 24)); // Menghitung durasi dalam hari
      
      // Menambahkan Durasi di bawah Tanggal Peminjaman
      doc.text(`Durasi: ${durasiInDays} hari`, 100, 36);

      const tujuan = this.filteredData[0]?.detail_peminjaman || "Tidak Diketahui";
      doc.text(`Keperluan Peminjaman: ${tujuan}`, 14, 42);

      // Menambahkan header tabel
      const headers = [
        "#",
        "Nama",
        "Kode",
        "Jumlah",
        "No Seri"
      ];
      const rows = [];

      // Mengisi data tabel
      this.filteredData.forEach((item, index) => {
        rows.push([
          index + 1,
          item.tools.nama,
          item.tools.kode,          
          item.total,
          item.no_seri && item.no_seri.length ? item.no_seri.map(n => n.no_seri).join(', ') : '-'
        ]);
      });

      // Menambahkan tabel ke PDF
      doc.autoTable({
        head: [headers],
        body: rows,
        startY: 52, // Menyesuaikan posisi tabel setelah judul, nama peminjam, dan divisi
      });

      // Menambahkan tulisan "Peminjam" dan "Penyerahan"
      const signatureSectionY = doc.lastAutoTable.finalY + 20; // Menentukan posisi setelah tabel
      doc.text("Peminjam", 60, signatureSectionY); // Peminjam di kiri
      doc.text("Penyerahan", 140, signatureSectionY); // Penyerahan di kanan

      // Menambahkan garis panjang di bawah tulisan Peminjam dan Penyerahan
      doc.setLineWidth(0.5);
      doc.line(14, signatureSectionY + 4, 195, signatureSectionY + 4); // Garis untuk Peminjam

      // Menambahkan ruang kosong setelah garis
      const gapY = signatureSectionY + 50; // Memberi jarak untuk ruang kosong
      doc.text(namaPeminjam, 60, gapY); // Nama Peminjam di kiri
      doc.text("Administrator", 140, gapY); // Administrator di kanan

      // Menyimpan PDF
      doc.save("peminjaman-alat.pdf");
    },
  },
  mounted() {
    this.fetchPeminjamanAlat();
  }
}
</script>