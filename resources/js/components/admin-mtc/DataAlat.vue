<template>
  <div class="container-fluid mt-3 mr-3 mb-3">
    <!-- Modal Konfirmasi -->
    <div class="modal" :class="{ 'is-visible': showModal }">
        <div class="modal-content">
          <h2>Konfirmasi Penghapusan</h2>
          <p>Apakah Anda yakin ingin menghapusnya?</p>
          <button id="Buttonconfirm" @click="confirmDelete">Ya, Hapus</button>
          <button id="Buttoncancel" @click="cancelDelete">Tidak, Kembali</button>
        </div>
    </div>

    <!-- Modal Tambah Data -->
    <!--<diV id="app" class="modal-input" :class="{'is-visible': showModalInput}">
        <div class="modal-content-input">
          <input-alat @tutup-modal="tutupModal"></input-alat>
        </div>
      </diV>-->

    <h1 class="h3 mb-4 text-gray-900"><b>Master Data</b></h1>

    <!-- <div class="col-md-12">
      <button 
        class="btn btn-show m-1"
        :class="{active: showAlat}"
        @click="toggleAlat"
      >
        <span v-if="showAlat">Alat</span>
        <span v-else>Alat</span>
      </button>
      <button 
        class="btn btn-show m-1"
        :class="{active: showMesin}"
        @click="toggleMesin"
      >
        <span v-if="showMesin">Mesin</span>
        <span v-else>Mesin</span>
      </button>
    </div> -->

    <!-- Data Alat -->
    <div v-if="showAlat">
    <!-- Filter and Search -->
    <div class="d-flex justify-content-between mb-3">
      <!-- Filter Button -->
      <div>
        
      </div>      

      <!-- Search and Add -->
      <div>
        <form class="d-flex align-items-center">
          <div>
            <button @click="downloadExcel" class="btn btn-sm btn-primary-1 mr-3">
              <i class="fas fa-file-excel"></i> Export
            </button>
          </div>
          <button
            class="btn btn-sm btn-primary-1"
            type="button"
            id="filterDropdown"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <i class="fas fa-filter"></i> Filter
          </button>
          <div
            class="dropdown-menu p-3"
            aria-labelledby="filterDropdown"
            style="border-radius: 8px; width: 250px;"
            @click.stop
          >
            <!-- Status Filter -->
            <!--<div>
              <label><strong>Status</strong></label>
              <div>
                <label><input type="checkbox" value="active" v-model="statusFilters" /> Active</label>
              </div>
              <div>
                <label><input type="checkbox" value="rusak" v-model="statusFilters" /> Rusak</label>
              </div>
              <div>
                <label><input type="checkbox" value="error" v-model="statusFilters" /> Error</label>
              </div>
            </div>-->
            <!-- Location Filter -->
            <!--<div>
              <label><strong>Satuan</strong></label>
              <div v-for="unit in availableUnits" :key="unit">
                <label><input type="checkbox" :value="unit" v-model="unitFilters" /> {{ unit }}</label>
              </div>
            </div>-->
            <div>
              <label><strong>Kategori</strong></label>
              <v-select
                v-model="categoryFilters"
                :options="availableCategory"
                :searchable="true"
                :multiple="true"
                placeholder="Pilih Kategori"
                :close-on-select="false"
                :clearable="true"
              >
              </v-select>
            </div>
            <!-- Jenis -->
            <div class="mt-3">
              <label><b>Jenis</b></label>
              <div v-for="jenis in availableJenis" :key="jenis">
                <label><input type="checkbox" :value="jenis" v-model="jenisFilter"/>{{ jenis }}</label>
              </div>
            </div>
          </div>
          <input
            type="text"
            name="search"
            v-model="searchQuery"
            @input="debouncedFetchAlats"
            style="background-color: #f3f4f6;"
            class="form-control-sm border-0 mr-2 ml-2"
            placeholder="Search..."
          />
          <a @click="tambahData" class="btn btn-icon-split btn-plus">
            <span class="icon text-white-50">
              <i class="fas fa-plus-circle"></i>
            </span>
            <span class="text">Add Data</span>
          </a>
        </form>
      </div>
    </div>    

    <!-- Data Table -->
    <div class="table-responsive">
      <table class="table table-bordered no-border table-custom">
        <thead>
          <tr class="bg-table">
            <th class="text-center text-black-1">#</th>
            <th class="text-left text-black-1">Kode</th>
            <th class="text-center text-black-1">Jenis</th>
            <th class="text-center text-black-1">Nama</th>
            <th class="text-center text-black-1">Merek</th>
            <th class="text-center text-black-1">Tipe/Ukuran</th>                        
            <th class="text-center text-black-1" style="cursor: pointer; position: relative; vertical-align: middle;">
              Stok Awal
              <span class="sort-icons">
                <i @click="sortStokAwal('desc')" class="fas fa-sort-up"></i>
                <i @click="sortStokAwal('asc')" class="fas fa-sort-down"></i>
              </span>
            </th>
            <th class="text-center text-black-1" style="cursor: pointer; position: relative; vertical-align: middle;">
              Total Stok
              <span class="sort-icons">
                <i @click="sortStokAkhir('desc')" class="fas fa-sort-up"></i>
                <i @click="sortStokAkhir('asc')" class="fas fa-sort-down"></i>
              </span>
            </th>            
            <th class="text-center text-black-1">Action</th>
          </tr>
        </thead>
        <tbody v-if="filteredAlats.length === 0">
          <tr>
            <td colspan="9" class="text-center">Tidak Ada Data</td>
          </tr>
        </tbody>
        <tbody v-for="(categoryGroup, category) in filteredGroupedAlats" :key="category">        
          <tr>
            <td colspan="9" class="bg-teal text-white" style="font-size: 16px;"><strong>{{ category }}</strong></td>
          </tr>
          <tr v-for="(alat, index) in categoryGroup" :key="alat.id" class="tr-center">
            <td class="text-center">{{ index + 1 }}</td>
            <td class="text-left"><img :src="alat.gambar_alat" style="max-width: 50px; max-height: 50px; margin-right: 20px; border-radius: 10px;" />{{ alat.kode_alat }}</td>
            <td class="text-center">{{ alat.jenis || '-' }}</td>
            <td class="text-center">{{ alat.nama_alat }}</td>
            <td class="text-center">{{ alat.merek_alat }}</td>
            <td class="text-center">{{ alat.tipe_alat }}</td>
            <td class="text-center">{{ alat.stok_awal }}</td>
            <td class="text-center" :class="{ 'text-danger': alat.stok_akhir <= 2 }">{{ alat.stok_akhir }}<br>
              <span v-if="alat.stok_akhir <= 2" class="text-center"><small>Minimum Stok</small></span>
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
                  <a class="dropdown-item" @click="viewDetail(alat.id)">
                    <i class="fas fa-eye text-info"></i> Detail
                  </a>
                  <a class="dropdown-item" @click="editData(alat.id)">
                    <i class="fas fa-edit text-primary"></i> Edit
                  </a>
                  <!-- <a class="dropdown-item" @click="deleteData(alat.id)">
                    <i class="fas fa-trash text-danger"></i> Hapus
                  </a> -->
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>      
    </div>
    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3" style="border-radius: 10px; background-color: #f3f4f6; height: 50px; color: #000;">
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
  <!-- Data Mesin -->
  <div v-if="showMesin">
    <!-- Route Mesin -->
    <data-mesin></data-mesin>
  </div>
  </div>
</template>

<script>
import axios from "axios";
import _ from "lodash";
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import * as XLSX from 'xlsx';  // Impor XLSX dari library yang sudah diinstal

export default {
  components: {
    vSelect,
  },
  data() {
    return {
      alats: [], // Data akan diisi dari API
      statusFilters: [],
      unitFilters: [],
      categoryFilters: [],
      searchQuery: '',
      availableUnits: ['Unit', 'Pcs', 'Set'],      
      currentPage: 1,
      rowsPerPage: 10,
      showModal: false, // Mengontrol tampilan modal
      showAlat: true,
      showMesin: false,
      jenisFilter: [],
      showModalInput: false, // Tambahkan variabel untuk mengontrol tampilan modal input       
    };
  },
  computed: {
    availableCategory() {
      return [...new Set(this.alats.map(alat => alat.kategori))];
    },
    availableJenis() {
      return [...new Set(this.alats.map(alat => alat.jenis))];
    },
    filteredAlats() {
      return this.alats.filter(alat => {
        const statusMatch = this.statusFilters.length ? this.statusFilters.includes(alat.status) : true;
        const unitMatch = this.unitFilters.length ? this.unitFilters.includes(alat.unit_alat) : true;
        const categoryMatch = this.categoryFilters.length ? this.categoryFilters.includes(alat.kategori) : true;
        const jenisMatch = this.jenisFilter.length ? this.jenisFilter.includes(alat.jenis) : true;
        const searchMatch = alat.nama_alat.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          alat.merek_alat.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
          alat.kode_alat.toLowerCase().includes(this.searchQuery.toLowerCase());

        return statusMatch && unitMatch && jenisMatch && categoryMatch && searchMatch;
      });
    },
    filteredGroupedAlats() {
      const grouped = this.paginatedAlats.reduce((groups, alat) => {
        const kategori = alat.kategori || 'Uncategorized';
        if (!groups[kategori]) {
          groups[kategori] = [];
        }
        groups[kategori].push(alat);
        return groups;
      }, {});
      return grouped;
    },
    paginatedAlats() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      return this.filteredAlats.slice(start, start + this.rowsPerPage);
    },
    totalPages() {
      return Math.ceil(this.filteredAlats.length / this.rowsPerPage);
    },
    paginationInfo() {
      if (!this.filteredAlats.length) return '0-0 of 0';
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(start + this.rowsPerPage - 1, this.filteredAlats.length);
      return `Showing ${start} to ${end} of ${this.filteredAlats.length} entries`;
    },
  },
  methods: {
    async fetchAlats() {
      try {
        const response = await axios.get(`/api/alats`);
        this.alats = response.data.data.map((alat) => ({
          id: alat.id,
          kode_alat: alat.kode_alat,
          jenis: alat.jenis,
          nama_alat: alat.nama_alat,
          merek_alat: alat.merek_alat,
          tipe_alat: alat.tipe_alat,
          unit_alat: alat.unit_alat,
          status: alat.status,
          stok_awal: alat.stok_awal,
          stok_akhir: alat.stok_akhir,
          gambar_alat: alat.gambar,
          kategori: alat.kategori,
        }));
        //console.log(this.alats);
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    },
    sortStokAwal(order) {
      this.alats.sort((a, b) => {
        if (order === 'asc') {
          return a.stok_awal - b.stok_awal;
        } else {
          return b.stok_awal - a.stok_awal;
        }
      });
    },
    sortStokAkhir(order) {
      this.alats.sort((a, b) => {
        if (order === 'asc') {
          return a.stok_akhir - b.stok_akhir;
        } else {
          return b.stok_akhir - a.stok_akhir;
        }
      });
    },
    debouncedFetchAlats: _.debounce(function () {
      this.fetchAlats();
    }, 300),
    tambahData() {
      this.$router.push(`/admin-mtc/data-alat/input`);
    },
    viewDetail(id) {
      this.$router.push(`/admin-mtc/data-alat/detail/${id}`);
    },
    editData(id) {
      this.$router.push(`/admin-mtc/data-alat/edit/${id}`);
    },
    deleteData() {
      this.showModal = true;
    },
    confirmDelete(id) {
      this.$router.push(`/admin-mtc/data-alat/delete/${id}`);
      this.showModal = false;
    },
    cancelDelete() {
      this.showModal = false;
    },
    prevPage() {
      if (this.currentPage > 1) this.currentPage--;
    },
    nextPage() {
      if (this.currentPage < this.totalPages) this.currentPage++;
    },    
    toggleAlat() {
      this.showMesin = false;
      this.showAlat = !this.showAlat;
    },
    toggleMesin() {
      this.showAlat = false;
      this.showMesin = !this.showMesin;
    },
    tutupModal() {
      this.showModalInput = false;
    },
    // Metode untuk mendownload data ke Excel
    downloadExcel() {
    // Memastikan data yang akan diekspor sudah ada
    if (this.filteredAlats.length === 0) {
      alert("Tidak ada data untuk di-download");
      return;
    }

    // Menyiapkan data yang akan dikonversi, termasuk nomor urut
    const data = this.filteredAlats.map((alat, index) => ({
      No: index + 1,  // Menambahkan nomor urut      
      Kode: alat.kode_alat,
      Jenis: alat.jenis,
      Kategori: alat.kategori,
      Nama: alat.nama_alat,
      Merek: alat.merek_alat,
      Tipe: alat.tipe_alat,
      'Stok Awal': alat.stok_awal,
      'Stok Akhir': alat.stok_akhir,      
    }));

    // Mengonversi data ke dalam format sheet Excel
    const ws = XLSX.utils.json_to_sheet(data);

    // Mengatur header agar menjadi bold
    const range = XLSX.utils.decode_range(ws['!ref']); // Mendapatkan range sheet
    for (let col = range.s.c; col <= range.e.c; col++) {
      const address = { r: range.s.r, c: col }; // Alamat cell header
      const cell = ws[XLSX.utils.encode_cell(address)];
      if (cell) {
        cell.s = { font: { bold: true } }; // Mengatur style font menjadi bold
      }
    }

    // Membuat workbook dari sheet yang sudah dibuat
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Data Master');

    // Menyimpan file Excel dengan nama yang ditentukan
    XLSX.writeFile(wb, 'data_master.xlsx');
  }
  },
  mounted() {
    // Fetch data saat komponen di-mount
    this.fetchAlats();
  },
};

</script>

<style>
.sort-icons {
  position: absolute; /* Membuat ikon diposisikan secara absolut */
  top: 3px; /* Jarak dari atas */
  right: auto; /* Jarak dari kanan */
  font-size: 0.9em; /* Ukuran ikon lebih kecil agar terlihat seperti pangkat */
  display: inline-flex;
  flex-direction: column-reverse;
}

.fa-sort-up, .fa-sort-down {
  margin-left: 5px;
  color: rgba(22, 158, 168, 0.4);
}

.fa-sort-up:hover, .fa-sort-down:hover {
  color: rgba(22, 158, 168);
}

.btn-primary-1 {
  border-radius: 8px;
  border: 2px solid rgba(22, 158, 168); /* Menambahkan border dengan warna yang sama */
  background-color: #fff;
  color: rgba(22, 158, 168);
  transition: background-color 0.3s, color 0.3s, border-color 0.3s; /* Menambahkan transisi untuk efek yang halus */
}

.btn-primary-1:hover {
  background-color: rgba(22, 158, 168);
  color: #fff;
  border-color: rgba(22, 158, 168); /* Mengubah warna border saat di-hover */
  box-shadow: 0 0 0 2px rgba(22, 158, 168, 0.5); /* Menambahkan garis luar dengan box-shadow */
}

.bg-teal {
  background-color: #07757d;
  color: #000;
}

/* Dropdown item saat aktif */
.dropdown-item.active {
  font-weight: bold;
  color: #169ea8;
  background-color: #e9ecef; /* Ganti sesuai preferensi */
}
input[type="checkbox"] {
  /* Hilangkan tampilan default */
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  
  width: 20px;
  height: 20px;
  border: 2px solid #169ea8; /* Warna border default */
  border-radius: 4px;
  background-color: #fff; /* Warna latar default */
  cursor: pointer;
  transition: all 0.3s ease-in-out;
}

input[type="checkbox"]:checked {
  background-color: #169ea8; /* Warna saat checkbox dicentang */
  border-color: #22d3e0; /* Warna border saat checkbox dicentang */
  position: relative;
}

input[type="checkbox"]:checked::after {
  content: '';
  display: block;
  width: 10px;
  height: 10px;
  margin: 4px;
  background-color: #169ea8; /* Warna centang */
  border-radius: 2px;
  transition: all 0.3s ease-in-out;
}

.status-active {
  background-color: rgba(40, 167, 69, 0.1); /* Hijau dengan transparansi */
  color: rgba(40, 167, 69); 
}

.status-rusak {
  background-color: rgba(220, 53, 69, 0.1); /* Merah dengan transparansi */
  color: rgba(220, 53, 69);
}

.status-error {
  background-color: rgba(255, 193, 7, 0.1); /* Kuning dengan transparansi */
  color: rgba(255, 193, 7);
}

.status-pill {
  display: inline-block;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: bold;
  text-align: center;
  cursor: default;
  min-width: 80px;
  transition: all 0.3s ease;
  border: none;
  margin: 5px auto; /* Memberikan jarak antar elemen */
  display: flex;
  justify-content: center;
  align-items: center;
}


.tr-center {
  text-align: center; /* Menyusun konten secara horizontal di tengah */
  vertical-align: middle; /* Menyusun konten secara vertikal di tengah */
}

.no-border th,
.no-border td {
  border: none !important;
}

.text-black-1 {
  color: #000;
}

.table-custom {
  border-radius: 10px;
  overflow: hidden; /* Ensures the border radius is applied correctly */
}

.table-custom th:first-child,
.table-custom td:first-child {
  border-top-left-radius: 10px;
  border-bottom-left-radius: 10px;
}

.table-custom th:last-child,
.table-custom td:last-child {
  border-top-right-radius: 10px;
  border-bottom-right-radius: 10px;
}

.bg-table {
  background-color: #f3f4f6;
}

/* Modal Styling */
.modal {
    display: none; /* Sembunyikan modal secara default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Latar belakang transparan */
  }

  .modal.is-visible {
    display: flex; /* Tampilkan modal saat is-visible aktif */
    justify-content: center;
    align-items: center;
  }

  .modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    max-width: 400px;
    text-align: center;
  }

  .modal-content h2 {
    margin-bottom: 10px;
    font-size: 1.5rem;
    color: #333;
  }

  .modal-content p {
    margin-bottom: 20px;
    color: #666;
  }

  .modal-content button {
    padding: 10px 20px;
    margin: 5px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  #Buttoncancel {
    background-color: #169ea8;
    color: #fff;
  }

  #Buttonconfirm {
    background-color: #f44336;
    color: #fff;
  }

  .btn-show.active {
    background-color: #169EA8; /* Warna tombol saat aktif */
    color: #fff; /* Warna teks tombol saat aktif */
    border: 1px solid #169EA8; /* Tambahkan border agar lebih jelas */
  }

  .btn-show {
    background-color: #fff;
    color: #000;
    border: 1px solid transparent; /* Tambahkan border default */
    transition: background-color 0.3s ease, color 0.3s ease, border 0.3s ease;
  }

  .btn-show:hover {
    background-color: #fff; /* Warna saat hover */
    color: #169EA8;
    border: 1px solid #fff;
  }

  .modal-input {
    display: none; /* Sembunyikan modal secara default */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Latar belakang transparan */
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }

  .modal-input.is-visible {
    display: flex; /* Tampilkan modal saat is-visible aktif */
  }

  .modal-content-input {
    background-color: white;
    padding: 20px;
    border-radius: 20px;
    max-width: 800px;
    width: 100%;
  }

</style>