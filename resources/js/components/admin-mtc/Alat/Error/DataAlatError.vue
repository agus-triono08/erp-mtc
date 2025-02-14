<template>
  <div class="container-fluid">
    <!-- Modal Konfirmasi -->
    <div class="modal" :class="{ 'is-visible': showModal }">
        <div class="modal-content">
          <h2>Konfirmasi Penghapusan</h2>
          <p>Apakah Anda yakin ingin menghapusnya?</p>
          <button id="Buttonconfirm" @click="confirmDelete">Ya, Hapus</button>
          <button id="Buttoncancel" @click="cancelDelete">Tidak, Kembali</button>
        </div>
    </div>

    <!-- Modal Input Data -->
    <div id="app" class="modal-input" :class="{'is-visible': showModalInput}">
      <div class="modal-content-input">
        <input-alat-error></input-alat-error>
      </div>
    </div>

    <h1 class="h3 mb-4 text-gray-800"><b>Data Alat Error</b></h1>

    <!-- Filter and Search -->
    <div class="d-flex justify-content-between mb-3">
      <!-- Filter Button -->
      <div>
        
      </div>

      <!-- Search and Add -->
      <div>
        <form class="d-flex align-items-center">
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
            <div>
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
            </div>
            <!-- Location Filter -->
            <div>
              <label><strong>Lokasi Penyimpanan</strong></label>
              <div v-for="location in availableLocations" :key="location">
                <label><input type="checkbox" :value="location" v-model="locationFilters" /> {{ location }}</label>
              </div>
            </div>
            <div>
              <label><strong>Kategori</strong></label>
              <div v-for="category in availableCategory" :key="category">
                <label><input type="checkbox" :value="category" v-model="categoryFilters"/> {{ category }}</label>
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
            placeholder="Search by Code or Name or Merek"
          />
          <a @click="tambahData" class="btn btn-icon-split btn-plus">
            <span class="icon text-white-50">
              <i class="fas fa-plus-circle"></i>
            </span>
            <span class="text">Add Alat Rusak</span>
          </a>
          <!--<router-link to="/admin-mtc/data-alat">
            <a class="btn btn-icon-split btn-plus">
              <span class="icon text-white-50">
                <i class="fas fa-plus-circle"></i>
              </span>
              <span class="text">Add Alat Rusak</span>
            </a>
          </router-link>-->
        </form>
      </div>
    </div>

    <!-- Data Table -->
    <div class="table-responsive">
      <table class="table table-bordered no-border table-custom">
        <thead>
          <tr class="bg-table">
            <th class="text-center text-black-1">#</th>
            <th class="text-left text-black-1">Kode Alat</th>
            <th class="text-center text-black-1">Nama Alat</th>
            <th class="text-center text-black-1">Merek</th>
            <th class="text-center text-black-1">Tipe/Ukuran</th>
            <th class="text-center text-black-1">Lokasi Penyimpanan</th>
            <th class="text-center text-black-1">Status</th>
            <!--<th class="text-center text-black-1" style="cursor: pointer; position: relative; vertical-align: middle;">
              Stok
              <span class="sort-icons">
                <i @click="sortStok('desc')" class="fas fa-sort-up"></i>
                <i @click="sortStok('asc')" class="fas fa-sort-down"></i>
              </span>
            </th>-->
            <th class="text-center text-black-1">Action</th>
          </tr>
        </thead>
        <tbody v-if="filteredAlats.length === 0">
          <tr>
            <td colspan="8" class="text-center">No data available</td>
          </tr>
        </tbody>
        <tbody v-for="(categoryGroup, category) in filteredGroupedAlats" :key="category">        
          <tr>
            <td colspan="9" class="bg-teal text-white" style="font-size: 16px;"><strong>{{ category }}</strong></td>
          </tr>
          <tr v-for="(alat, index) in categoryGroup" :key="alat.id" class="tr-center">
            <td class="text-center">{{ index + 1 }}</td>
            <td class="text-left"><img :src="alat.gambar_alat" style="max-width: 50px; max-height: 50px; margin-right: 20px; border-radius: 10px;" />{{ alat.kode_alat }}</td>
            <td class="text-center">{{ alat.nama_alat }}</td>
            <td class="text-center">{{ alat.merek_alat }}</td>
            <td class="text-center">{{ alat.tipe_alat }}</td>
            <td class="text-center">{{ alat.lokasi_penyimpanan }}</td>
            <td
              class="status-pill parent-element"
              style="margin-top: 1.2em;"
              :class="{
                'status-active': alat.status === 'active', 
                'status-rusak': alat.status === 'rusak', 
                'status-error': alat.status === 'error'}"
            >
              {{ alat.status }}
            </td>
            <!--<td class="text-center" :class="{ 'text-danger': alat.stok < 2 }">{{ alat.stok }}<br>
              <span v-if="alat.stok < 2" class="text-center"><small>Minimum Stock</small></span>
            </td>-->
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
                  <a class="dropdown-item" @click="deleteData(alat.id)">
                    <i class="fas fa-trash text-danger"></i> Hapus
                  </a>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="d-flex justify-content-between align-items-center mt-3" style="border-radius: 10px; background-color: #f3f4f6;">
        <div>
          Rows per page:
          <select v-model="rowsPerPage" class="form-control form-control-sm d-inline-block w-auto">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="15">15</option>
          </select>
        </div>
        <div>          
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
import _ from "lodash";

export default {
  data() {
    return {
      alats: [
        { id: 1, gambar_alat: 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//104/MTA-60139885/oem_oem_full01.jpg', kode_alat: '1-C3-B0-2-01', nama_alat: 'Clamp', merek_alat: 'BISON', tipe_alat: 'Plastik', unit_alat: 'Unit', status: 'error', stok: 3, lokasi_penyimpanan: 'Gedung A', kategori: 'CLAMP' },    
        {id: 9, kode_alat: '1-T0-N0-4-01', kategori: 'TANG', nama_alat: 'Tang', merek_alat: 'NANKAI', tipe_alat: 'Kupas', unit_alat: 'Set', gambar_alat: 'https://images.tokopedia.net/img/cache/700/product-1/2017/10/3/6759330/6759330_6b7f4bf0-dfcd-4d56-8626-e04aa11afd98_700_700.jpg', status: 'error', stok: 10, lokasi_penyimpanan: 'Gedung A' },        
        {id: 11, kode_alat: '1-S6-A3-0-01', kategori: 'SOLDER', nama_alat: 'Solder Blower', merek_alat: 'ATTEN', tipe_alat: '85020D', unit_alat: 'Unit', gambar_alat: 'https://images.tokopedia.net/img/cache/700/hDjmkQ/2023/1/18/63f622d6-59bd-49fc-898a-da0e42c88d8a.jpg', status: 'error', stok: 10, lokasi_penyimpanan: 'Gedung A' },
      ],
      statusFilters: [],
      locationFilters: [],
      categoryFilters: [],
      searchQuery: '',            
      currentPage: 1,
      rowsPerPage: 10,
      showModal: false, // Tambahkan variabel untuk mengontrol tampilan modal
      showModalInput: false, // Tambahkan variabel untuk mengontrol tampilan modal input
    };
  },
  computed: {
    availableCategory() {
      return [...new Set(this.alats.map(alat => alat.kategori))];
    },
    availableLocations() {
      return [...new Set(this.alats.map(alat => alat.lokasi_penyimpanan))];
    },
    filteredAlats() {
      return this.alats.filter(alat => {
        const statusMatch = this.statusFilters.length ? this.statusFilters.includes(alat.status) : true;
        const locationMatch = this.locationFilters.length ? this.locationFilters.includes(alat.lokasi_penyimpanan) : true;
        const categoryMatch = this.categoryFilters.length ? this.categoryFilters.includes(alat.kategori) : true;
        const searchMatch = alat.nama_alat.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          alat.merek_alat.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
          alat.kode_alat.toLowerCase().includes(this.searchQuery.toLowerCase());

        return statusMatch && locationMatch && categoryMatch && searchMatch;
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
      return `${start}-${end} of ${this.filteredAlats.length}`;
    },
  },
  methods: {
    sortStok(order) {
      this.alats.sort((a, b) => {
        if (order === 'asc') {
          return a.stok - b.stok;
        } else {
          return b.stok - a.stok;
        }
      });
    },
    debouncedFetchAlats: _.debounce(function () {
      // Simulate fetch or just rely on reactivity
    }, 300),
    tambahData() {
      this.showModalInput = true;
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

/* Styling untuk status pill */
.status-pill {
  margin: auto; /* Tengahkan baik vertikal maupun horizontal */
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  font-weight: bold;
  border-radius: 20px;
  text-align: center;
  padding: 0.2em 0.6em;
  height: 1rem;
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

  /* Modal Input Styling */
.modal-input {    
  display: none; /* Sembunyikan modal secara default */
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100vh; /* Ubah tinggi menjadi 100vh untuk membuat modal melebar */
  background-color: rgba(0, 0, 0, 0.5); /* Latar belakang transparan */
}

.modal-input.is-visible {
  display: flex; /* Tampilkan modal saat is-visible aktif */
  justify-content: center;
  align-items: center;
}

.modal-content-input {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  max-width: 80%; /* Ubah lebar menjadi 80% untuk membuat modal melebar */
  max-height: 90vh; /* Ubah tinggi menjadi 90vh untuk membuat modal melebar */
  overflow-y: auto; /* Tambahkan overflow-y untuk membuat modal dapat di-scroll */
  text-align: center;
  margin: 20px; /* Tambahkan margin untuk membuat modal tidak menempel di tepi */
}

</style>