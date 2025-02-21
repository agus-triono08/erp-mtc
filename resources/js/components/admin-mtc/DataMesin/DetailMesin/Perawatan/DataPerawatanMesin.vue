<template>
  <div class="container-fluid">
    <!-- Modal Edit Data -->
    <div id="app" class="modal-input" :class="{'is-visible' :showModalEdit}" @click.self="tutupModal">
      <div class="modal-content-input">
        <edit-perawatan-alat @tutup-modal="tutupModal" :id="idEdit"></edit-perawatan-alat>
      </div>      
    </div>
    <!-- Data Peminjaman Alat -->
      <div class="row align-items-center justify-content-end mr-3 mt-3 mb-4">        
        <!-- Tambah Data -->
        <!-- <button class="btn btn-sm btn-outline-primary mr-2 ml-1" @click="tambahData">
          <i class="fa fa-plus-circle"></i> Tambah Data
        </button> -->
        <!-- Search -->
        <div class="search-wrapper">
          <div class="input-group">
            <input type="text" placeholder="search..." class="form-control"
              v-model="searchQuery"
              @input="debouncedFetchAlats"/>
            </div>
          </div>
      </div>
    <div class="table-responsive">
      <table class="table table-border no-border table-custom text-wrape" style="overflow-x: auto;">
        <thead>
          <tr class="bg-table">
            <th class="text-center text-black-1 tr-center">#</th>
            <th class="text-center text-black-1">No Seri Alat</th>
            <th class="text-center text-black-1">PIC</th>                       
            <th class="text-center text-black-1">Tgl Perawatan</th>                        
            <th class="text-center text-black-1">Status</th>
            <th class="text-center text-black-1">Action</th>
          </tr>
        </thead>
        <tbody v-if="filteredData.length===0">
          <tr>
            <td colspan="8" class="text-center text-black-1">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="(kodeGroup, code) in filteredGroupedData" :key="code">
          <tr>
            <td colspan="8" class="bg-teal text-white" style="font-size: 16px;"><strong>{{ code }}</strong></td>
          </tr>
          <tr v-for="(perawatan, index) in kodeGroup" :key="perawatan.id" class="text-center">
            <td class="text-center">{{ index + 1 }}</td>
            <td class="text-center">{{ perawatan.no_seri ? perawatan.no_seri.no_seri_alat : '-' }}</td>
            <td class="text-center">{{ perawatan.staff ? perawatan.staff.nama_staff : '-' }}</td>            
            <td class="text-center">{{ perawatan.tanggal_perawatan || '-' }}</td>            
            <td>
              <div
                class="btn-sts"
                :class="{
                  'status-active' :  perawatan.status == 'Sudah',
                  'status-rusak' : perawatan.status == 'Belum',
                }"
              >
                {{ perawatan.status }}
              </div>
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
                  <a class="dropdown-item" @click="viewDetail(perawatan.id)">
                    <i class="fas fa-eye text-info"></i> Detail
                  </a>
                  <a class="dropdown-item" @click="editData(perawatan.id)">
                    <i class="fas fa-edit text-primary"></i> Edit
                  </a>
                </div>
              </div>
            </td>
            <!--<td class="text-center">
              <button
                class="btn btn-show" 
                v-if="perawatan.status === 'Belum' && new Date(perawatan.tanggal_perawatan) <= new Date(currentDate)"
                @click="markAsDone(index)"
              >
                Ceklis
              </button>
            </td>-->
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
import _ from "lodash";

export default {
  props: {
    kodeAlat: String
  },
  data() {
    return {
      user: {
        nama_pengguna: '',
        divisi: '',
      },
      dataPerawatanAlat: [
        {
          id: 1,
          id_alat: 101,
          id_no_seri_alat: 201,
          id_staff: 301,
          detail_perawatan: 'Perawatan rutin bulan ini.',
          tanggal_perawatan: '2025-02-20',
          status: 'Belum',
          alat: {
            kode_alat: 'AL-001',
            nama_alat: 'Mesin Bor',
            kategori: 'Peralatan Tangan',
          },
          staff: {
            nama_staff: 'John Doe',
            divisi: 'Pemeliharaan',
          },
          no_seri_alat: {
            no_seri_alat: 'NS-001',
          },
        },
      ],
      //dataPeminjaman: [], // Menyimpan data error
      showModalInput: false, // Tambahkan variabel untuk mengontrol tampilan modal input
      showAlat: true,
      idEdit: null,
      showMesin: false,
      showModalInput : false,
      showModalEdit : false,
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
      currentDate: new Date().toISOString().split('T')[0], 
    };
  },
  computed: {
    totalPages() {
      return Math.ceil(this.filteredData.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(this.currentPage * this.rowsPerPage, this.filteredData.length);
      return `Showing ${start} to ${end} of ${this.filteredData.length} entries`;
    },
    filteredData() {
      return this.dataPerawatanAlat.filter((perawatan) => {
        return (
          perawatan.alat?.kode_alat?.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          perawatan.no_pinjam?.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          perawatan.staff?.nama_pengguna?.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      });
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = start + this.rowsPerPage;
      return this.filteredData.slice(start, end);
    },
    filteredGroupedData() {
      return this.paginatedData.reduce((groups, perawatan) => {
        const kodeAlat = perawatan.alat?.kode_alat || "UnCode";
        if (!groups[kodeAlat]) {
          groups[kodeAlat] = [];
        }
        groups[kodeAlat].push(perawatan);
        return groups;
      }, {});
    },
  },
  methods: {
    async fetchAlatPeminjaman() {
      try {
        if (this.dataPerawatanAlat.length > 0) {
          return;
        }
        const response = await axios.get(`/api/mesin/perawatan`, {
          params: {
            search: this.searchQuery
          }
        });
        this.dataPerawatanAlat = response.data.data.map((perawatanalat) => ({
          id: perawatanalat.id,
          id_alat: perawatanalat.id_alat,
          id_no_seri_alat: perawatanalat.id_no_seri_alat,
          id_staff: perawatanalat.id_staff,
          detail_perawatan: perawatanalat.detail_perawatan,
          tanggal_perawatan: perawatanalat.tanggal_perawatan,
          status: perawatanalat.status,
          alat: perawatanalat.alat,
          staff: perawatanalat.staff,
          no_seri: perawatanalat.no_seri_alat,
        })); // Menyimpan data alat
        //console.log(this.dataPerawatanAlat); // Debug data
      } catch (error) {
        console.error("Error fetching alat error detail:", error);
        //alert("Gagal memuat detail data alat error.");
      }
    },
  debouncedFetchAlats: _.debounce(function () {
    this.fetchAlatPeminjaman();
  }, 300),
    // Tandai alat sebagai sudah perawatan
    markAsDone(index) {
      const perawatan = this.dataPerawatanAlat[index];

      // Perbarui status menjadi sudah dan jadwal berikutnya
      perawatan.status = 'Sudah';
      perawatan.tanggal_perawatan = this.calculateNextDate(perawatan.tanggal_perawatan, 15); // Tambahkan 15 hari
    },
    // Hitung tanggal berikutnya berdasarkan interval (15 hari)
    calculateNextDate(currentDate, interval) {
      const date = new Date(currentDate);
      date.setDate(date.getDate() + interval);
      return date.toISOString().split('T')[0]; // Format yyyy-mm-dd
    },
    tambahData() {
      this.showModalInput = true;
    },
    editData(id) {
      this.idEdit = id;
      this.showModalEdit = true;      
    },
    tutupModal() {
      this.showModalInput = false;
      this.showModalEdit = false;
    },
    sortJumlah(order) {
      this.dataPeminjaman.sort((a, b) => {
        if (order === "asc") {
          return a.stok_dipinjam - b.stok_dipinjam;
        } else {
          return b.stok_dipinjam - a.stok_dipinjam;
        }
      });
    },
    sortTanggalPinjam(order) {
      this.dataPeminjaman.sort((a, b) => {
        if (order === "asc") {
          return new Date(a.tanggal_pinjam) - new Date(b.tanggal_pinjam);
        } else {
          return new Date(b.tanggal_pinjam) - new Date(a.tanggal_pinjam);
        }
      });
    },
    sortTanggalkembali(order) {
      this.dataPeminjaman.sort((a, b) => {
        if (order === "asc") {
          return new Date(a.tanggal_kembali) - new Date(b.tanggal_kembali);
        } else {
          return new Date(b.tanggal_kembali) - new Date(a.tanggal_kembali);
        }
      });
    },
    toggleAlat() {
      if (!this.showAlat) {
        this.showMesin = false;
        this.showAlat = true;
      }
    },
    toggleMesin() {
      if (!this.showMesin) {
        this.showAlat = false;
        this.showMesin = true;
      }
    },
    viewDetail(id) {
      this.$router.push(`/admin-mtc/data-perawatan/detail/${id}`);
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
    updateStatus() {
      this.dataPerawatanAlat.forEach(perawatan => {
        if (new Date(perawatan.tanggal_perawatan) <= new Date(this.currentDate)) {
          perawatan.status = 'Belum';
        }
      });
    }
  },
  mounted() {
    this.fetchAlatPeminjaman();
  },
  created() {
    this.fetchAlatPeminjaman();
    this.updateStatus();
    this.interval = setInterval(() => {
      this.updateStatus();
    }, 86400000);
  },
  beforeDestroy() {
    clearInterval(this.interval);
  },
}
</script>