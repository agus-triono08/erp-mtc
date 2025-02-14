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

      <!-- Modal Tambah Data -->
      <!--<diV id="app" class="modal-input" :class="{'is-visible': showModalInput}">
        <div class="modal-content-input">
          <input-data-mesin @tutup-modal="tutupModal"></input-data-mesin>
        </div>
      </diV>-->
        
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
              @input="debouncedFetchMesins"
              style="background-color: #f3f4f6;"
              class="form-control-sm border-0 mr-2 ml-2"
              placeholder="Search by Code or Name or Merek"
            />
            <a @click="tambahData" class="btn btn-icon-split btn-plus">
              <span class="icon text-white-50">
                <i class="fas fa-plus-circle"></i>
              </span>
              <span class="text">Add Data Mesin</span>
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
              <th class="text-left text-black-1">Kode Mesin</th>
              <th class="text-center text-black-1">Nama Mesin</th>
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
          <tbody v-if="filteredMesins.length === 0">
            <tr>
              <td colspan="8" class="text-center">No data available</td>
            </tr>
          </tbody>
          <tbody v-for="(categoryGroup, category) in filteredGroupedMesins" :key="category">        
            <tr>
              <td colspan="8" class="bg-teal text-white" style="font-size: 16px;"><strong>{{ category }}</strong></td>
            </tr>
            <tr v-for="(mesin, index) in categoryGroup" :key="mesin.id" class="tr-center">
              <td class="text-center">{{ index + 1 }}</td>
              <td class="text-left"><img :src="mesin.gambar_mesin" style="max-width: 50px; max-height: 50px; margin-right: 20px; border-radius: 10px;" />{{ mesin.kode_mesin }}</td>
              <td class="text-center">{{ mesin.nama_mesin }}</td>
              <td class="text-center">{{ mesin.merek_mesin }}</td>
              <td class="text-center">{{ mesin.tipe_mesin }}</td>
              <td class="text-center">{{ mesin.stok_awal }}</td>
              <td class="text-center" :class="{ 'text-danger': mesin.stok_akhir <= 2 }">{{ mesin.stok_akhir }}<br>
                <span v-if="mesin.stok_akhir <= 2" class="text-center"><small>Minimum Stok</small></span>
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
                    <a class="dropdown-item" @click="viewDetail(mesin.id)">
                      <i class="fas fa-eye text-info"></i> Detail
                    </a>
                    <a class="dropdown-item" @click="editData(mesin.id)">
                      <i class="fas fa-edit text-primary"></i> Edit
                    </a>
                    <a class="dropdown-item" @click="deleteData(mesin.id)">
                      <i class="fas fa-trash text-danger"></i> Hapus
                    </a>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
  
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
    </div>
  </template>
  
  <script>
  import axios from "axios";
  import _ from "lodash";
  
  export default {
    data() {
      return {
        mesins: [], // Data akan diisi dari API
        selectedMesin: null, // Menyimpan data mesin yang akan diedit
        showModalEdit: false, // Kontrol tampilan modal
        statusFilters: [],
        unitFilters: [],
        categoryFilters: [],
        searchQuery: '',
        availableUnits: ['Unit', 'Pcs', 'Set'],      
        currentPage: 1,
        rowsPerPage: 10,
        showModal: false, // Mengontrol tampilan modal 
        showModalInput: false, // Tambahkan variabel untuk mengontrol tampilan modal input       
        showModalEdit: false,
      };
    },
    computed: {
      availableCategory() {
        return [...new Set(this.mesins.map(mesin => mesin.kategori))];
      },
      filteredMesins() {
        return this.mesins.filter(mesin => {
          const statusMatch = this.statusFilters.length ? this.statusFilters.includes(alat.status) : true;
          const unitMatch = this.unitFilters.length ? this.unitFilters.includes(alat.unit_alat) : true;
          const categoryMatch = this.categoryFilters.length ? this.categoryFilters.includes(mesin.kategori) : true;
          const searchMatch = mesin.nama_mesin.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            mesin.merek_mesin.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
            mesin.kode_mesin.toLowerCase().includes(this.searchQuery.toLowerCase());
  
          return statusMatch && unitMatch && categoryMatch && searchMatch;
        });
      },
      filteredGroupedMesins() {
        const grouped = this.paginatedMesins.reduce((groups, mesin) => {
          const kategori = mesin.kategori || 'Uncategorized';
          if (!groups[kategori]) {
            groups[kategori] = [];
          }
          groups[kategori].push(mesin);
          return groups;
        }, {});
        return grouped;
      },
      paginatedMesins() {
        const start = (this.currentPage - 1) * this.rowsPerPage;
        return this.filteredMesins.slice(start, start + this.rowsPerPage);
      },
      totalPages() {
        return Math.ceil(this.filteredMesins.length / this.rowsPerPage);
      },
      paginationInfo() {
        if (!this.filteredMesins.length) return '0-0 of 0';
        const start = (this.currentPage - 1) * this.rowsPerPage + 1;
        const end = Math.min(start + this.rowsPerPage - 1, this.filteredMesins.length);
        return `Showing ${start} to ${end} of ${this.filteredMesins.length} entries`;
      },
    },
    methods: {
      async fetchMesins() {
        try {
          const response = await axios.get(`/api/mesins`);
          this.mesins = response.data.data.map((mesin) => ({
            id: mesin.id,
            kode_mesin: mesin.kode_mesin,
            nama_mesin: mesin.nama_mesin,
            merek_mesin: mesin.merek_mesin,
            tipe_mesin: mesin.tipe_mesin,
            unit_mesin: mesin.unit_mesin,
            status: mesin.status,
            stok_awal: mesin.stok_awal,
            stok_akhir: mesin.stok_akhir,
            gambar_mesin: mesin.gambar,
            kategori: mesin.kategori,
          }));
          //console.log(this.alats);
        } catch (error) {
          console.error("Error fetching data:", error);
        }
      },
      sortStokAwal(order) {
        this.mesins.sort((a, b) => {
          if (order === 'asc') {
            return a.stok_awal - b.stok_awal;
          } else {
            return b.stok_awal - a.stok_awal;
          }
        });
      },
      sortStokAkhir(order) {
        this.mesins.sort((a, b) => {
          if (order === 'asc') {
            return a.stok_akhir - b.stok_akhir;
          } else {
            return b.stok_akhir - a.stok_akhir;
          }
        });
      },
      debouncedFetchMesins: _.debounce(function () {
        this.fetchMesins();
      }, 300),
      tambahData() {
        this.$router.push(`/admin-mtc/data-mesin/input`);
      },
      viewDetail(id) {
        this.$router.push(`/admin-mtc/data-mesin/detail/${id}`);
      },
      editData(id) {
        this.$router.push(`/admin-mtc/data-mesin/edit/${id}`);
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
      tutupModal() {
        this.showModalInput = false;
      },
      tutupModalEdit() {
        this.showModalEdit = false;
      }      
    },
    mounted() {
      // Fetch data saat komponen di-mount
      this.fetchMesins();
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