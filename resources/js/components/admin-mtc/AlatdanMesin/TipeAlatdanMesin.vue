<template>
  <div class="container-fluid">
    <!-- Loader -->
    <div class="loader" v-if="isLoading">
      <div class="loading-overlay">
        <div class="loading-spinner">
            <span class="sr-only">Loading...</span>          
        </div>
      </div>
    </div>
    <h1 class="h3 mb-4 mt-4 text-gray-900"><b>Inventory Alat/Mesin</b></h1>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="kategori-tab" data-toggle="tab" role="tab" aria-controls="kategori" aria-selected="true" :class="{active: $route.name === 'kategori'}" :to="{name: 'kategori'}">Kategori Alat/Mesin</router-link>
      </li>
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="merek-tab" data-toggle="tab" role="tab" aria-controls="merek" aria-selected="false" :class="{active: $route.name === 'merek'}" :to="{name: 'merek'}">Merek Alat/Mesin</router-link>
      </li>
      <li class="nav-item" role="presentation">
        <router-link class="nav-link" id="tipe-tab" data-toggle="tab" role="tab" aria-controls="tipe" aria-selected="false" :class="{active: $route.name === 'tipe'}" :to="{name: 'tipe'}">Tipe Alat/Mesin</router-link>
      </li>
    </ul>
    <div class="row align-items-center justify-content-end m-3">
      <!-- Tambah Data -->
      <button class="btn btn-sm btn-outline-primary mr-2 ml-1" @click="openModal('add')">
        <i class="fa fa-plus-circle"></i> Tambah Data
      </button>
      <div class="search-wrapper">
        <div class="input-group">
          <input type="text" placeholder="Search..." class="form-control"
            v-model="searchQuery"
            @input="debouncedFetchNoSeri"
          />
        </div>
      </div>
    </div>
    <div class="table-responsive p-3">
      <table class="table table-border no-border table-custom" style="overflow-x: auto;">
        <thead>
          <tr class="bg-table text-center">
            <th class="text-black-1">#</th>
            <th @click="sortBy('kategori')" style="cursor: pointer;">
              Kategori
              <i class="fas" :class="{
                'fa-sort-up': sortKey === 'kategori' && sortDirection === 'asc',
                'fa-sort-down': sortKey === 'kategori' && sortDirection === 'desc'
              }"></i>
            </th>
            <th @click="sortBy('merek')" style="cursor: pointer;">
              Merek
              <i class="fas" :class="{
                'fa-sort-up': sortKey === 'merek' && sortDirection === 'asc',
                'fa-sort-down': sortKey === 'merek' && sortDirection === 'desc'
              }"></i>
            </th>
            <th @click="sortBy('kode_kategori')" style="cursor: pointer;">
              Kode
              <i class="fas" :class="{
                'fa-sort-up': sortKey === 'kode_kategori' && sortDirection === 'asc',
                'fa-sort-down': sortKey === 'kode_kategori' && sortDirection === 'desc'
              }"></i>
            </th>
            <th @click="sortBy('nama_kategori')" style="cursor: pointer;">
              Nama
              <i class="fas" :class="{
                'fa-sort-up': sortKey === 'nama_kategori' && sortDirection === 'asc',
                'fa-sort-down': sortKey === 'nama_kategori' && sortDirection === 'desc'
              }"></i>
            </th>
            <th class="text-black-1">Aksi</th>
          </tr>
        </thead>
        <tbody v-if="paginatedData.length === 0">
          <tr class="text-center">
            <td colspan="5">Data tidak ditemukan</td>
          </tr>
        </tbody>
        <tbody v-for="(item, index) in paginatedData" :key="item.id">
          <tr class="text-center">
            <td>{{ index + 1 }}</td>
            <td>{{ item.kategorimerek && item.kategorimerek.kategori ? item.kategorimerek.kategori.nama_kategori : '-' }}</td>
            <td>{{ item.kategorimerek && item.kategorimerek.merek ? item.kategorimerek.merek.nama_merek : '-' }}</td>
            <td>{{ item.kode_tipe|| '-' }}</td>
            <td>{{ item.nama_tipe || '-' }}</td>
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
                  <a class="dropdown-item" @click="openModal('edit', item, index)">
                    <i class="fas fa-edit text-primary"></i> Edit
                  </a>
                  <!-- <a class="dropdown-item" @click="deleteData(index, item)">
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

    <!-- Modal untuk Input dan Edit Data -->
    <div v-if="isModalOpen" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalTitle }}</h5>
            <button type="button" class="close" @click="closeModal">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- <div class="form-group">
              <label for="jenis" style="color: #000;">
                <b>Jenis</b>
                <sup style="color: red;">*</sup>
              </label>
              <select 
                id="jenis"
                class="form-control"
                v-model="form.jenis"
                required
              >
                <option value="" disabled selected>Pilih Jenis</option>
                <option v-for="jenis in Jenis" :key="jenis.id" :value="jenis.id">{{ jenis.nama_jenis }}</option>              
              </select>
            </div> -->
            <!-- Kategori -->
            <div class="form-group">
              <label for="kategori" style="color: #000;">
                <b>Kategori</b>
                <sup style="color: red;">*</sup>
              </label>
              <v-select
                v-model="form.kategori"
                :options="Kategoris"
                label="nama_kategori"
                placeholder="Pilih Kategori"
                :searchable="true"
                required
              ></v-select>
            </div>
            <!-- Merek -->
            <div class="form-group">
              <label for="merek" style="color: #000;">
                <b>Merek</b>
                <sup style="color: red;">*</sup>
              </label>
              <v-select
                v-model="form.merek"
                :options="Merek"
                label="nama_merek"
                placeholder="Pilih Merek"
                :searchable="true"
                required
              ></v-select>
            </div>
            <div class="form-group">
              <label for="jenis" style="color: #000;">
                <b>Nama</b>
                <sup style="color: red;">*</sup>
              </label>
              <input type="text" class="form-control" id="nama" v-model="form.nama_tipe">
            </div>            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" @click="closeModal">Tutup</button>
            <button type="button" class="btn btn-primary" @click="saveData">{{ modalAction }}</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="isModalOpen" class="modal-backdrop fade show" @click="closeModal"></div> <!-- Backdrop -->
  </div>
</template>

<script>
import axios from 'axios';
import vSelect from 'vue-select';

export default {
  components: {
    vSelect,
  },
  data() {
    return {
      searchQuery: '',
      Tipe: [],
      form: {
        kategori: '',
        merek: '',
        nama_tipe: '',
        kode_tipe: '',
      },
      Kategoris: [],
      Merek: [],
      sortKey: '',
      sortDirection: 'asc',
      currentIndex: null,
      modalTitle: '',
      modalAction: '',
      isModalOpen: false, // Flag untuk modal open/close
      rowsPerPage: 10, // Menentukan jumlah item per halaman
      currentPage: 1, // Halaman saat ini
      isLoading: false,
    }
  },
  mounted() {
    this.isLoading = true;
    this.fetchData();
    this.fetchDataKategori();
    this.fetchDataMerek();
  },
  computed: {
    filteredData() {
      let result = this.Tipe.filter(item => {
        return (          
          item.nama_tipe.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      });

      if (this.sortKey) {
        result.sort((a, b) => {
          let aVal = this.getSortValue(a, this.sortKey);
          let bVal = this.getSortValue(b, this.sortKey);

          aVal = aVal ? aVal.toString().toLowerCase() : '';
          bVal = bVal ? bVal.toString().toLowerCase() : '';

          if (aVal < bVal) return this.sortDirection === 'asc' ? -1 : 1;
          if (aVal > bVal) return this.sortDirection === 'asc' ? 1 : -1;
          return 0;
        });
      }

      return result;
    },
    totalPages() {
      return Math.ceil(this.filteredData.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(this.currentPage * this.rowsPerPage, this.filteredData.length);
      return `Showing ${start} to ${end} of ${this.filteredData.length} entries`;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = start + this.rowsPerPage;
      return this.filteredData.slice(start, end);
    },
  },
  methods: {
    fetchData() {
      axios.get('/api/v1/tipe')
        .then(response => {
          this.Tipe = response.data;
          // console.log(this.Tipe);
          this.isLoading = false;
        })
        .catch(error => {
          console.error(error);
          this.isLoading = false;
        });
    },
    //Mengambil Data Kategori
    async fetchDataKategori() {
      try {
        const response = await axios.get('/api/v1/kategori');
        this.Kategoris = response.data;
        // console.log(this.Kategoris);
      } catch (error) {
        console.error(error);
      }
    },
    //Mengambil Data Merek
    async fetchDataMerek() {
      try {
        const response = await axios.get('/api/v1/merek');
        this.Merek = response.data;
        // console.log(this.Kategoris);
      } catch (error) {
        console.error(error);
      }
    },
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortKey = key;
        this.sortDirection = 'asc';
      }
    },
    getSortValue(item, key) {
      if (key === 'kategori') {
        return item.kategorimerek && item.kategorimerek.kategori ? item.kategorimerek.kategori.nama_kategori : '';
      } else if (key === 'merek') {
        return item.kategorimerek && item.kategorimerek.merek ? item.kategorimerek.merek.nama_merek : '';
      } else if (key === 'kode_kategori') {
        return item.kode_tipe || '';
      } else if (key === 'nama_kategori') {
        return item.nama_tipe || '';
      }
      return item[key];
    },
    openModal(action, item = null, index = null) {
      if (action === 'add') {
        this.modalTitle = 'Tambah Data';
        this.modalAction = 'Simpan';
        this.form.kategori = '';
        this.form.merek = '';
        this.form.kode_tipe = '';
        this.form.nama_tipe = '';
        this.currentIndex = null;
        this.currentId = null;
      } else if (action === 'edit' && item) {
        if (item.kategorimerek && item.kategorimerek.kategori && item.kategorimerek.merek) {
          this.modalTitle = 'Edit Data';
          this.modalAction = 'Update';
          this.form.kategori = item.kategorimerek.kategori;
          this.form.merek = item.kategorimerek.merek;
          this.form.kode_tipe = item.kode_tipe;
          this.form.nama_tipe = item.nama_tipe;
          this.currentIndex = index;
          this.currentId = item.id;
        } else {
          console.error('Invalid item data');
        }
      }
      this.isModalOpen = true; // Set modal status open
    },
    // openModal(action, item = null, index = null) {
    //   if (action === 'add') {
    //     this.modalTitle = 'Tambah Data';
    //     this.modalAction = 'Simpan';
    //     this.form.kategori = '';
    //     this.form.merek = '';
    //     this.form.nama_kategori = '';
    //     this.currentIndex = null;
    //     this.currentId = null;
    //   } else if (action === 'edit' && item) {
    //     this.modalTitle = 'Edit Data';
    //     this.modalAction = 'Update';
    //     this.form.kategori = item.kategorimerek.kategori.nama_kategori;
    //     this.form.merek = item.kategorimerek.merek.nama_merek;
    //     this.form.kode_tipe = item.kode_tipe;
    //     this.form.nama_tipe = item.nama_tipe;
    //     this.currentIndex = index;
    //     this.currentId = item.id;
    //   }
    //   this.isModalOpen = true; // Set modal status open
    // },
    closeModal() {
      this.isModalOpen = false; // Set modal status close
    },
    async createKategoriMerek(kategori, merek, id = null) {
      if (id) {
        return id;
      } else {
        const payload = {
          kategori_id: kategori.id,
          merek_id: merek.id,
        };
        try {
          // Coba cari dulu apakah sudah ada
          const checkResponse = await axios.get('/api/v1/kategori-merek/check', {
            params: payload,
          });

          if (checkResponse.data && checkResponse.data.id) {
            // Sudah ada, pakai ID yang ada
            return checkResponse.data.id;
          } else {
            // Tidak ada, buat baru
            const createResponse = await axios.post('/api/v1/kategori-merek', payload);
            return createResponse.data.id;
          }
        } catch (error) {
          console.error('Error saat cek/buat kategori-merek:', error);
          throw error;
        }
      }
    },
    saveData() {
      const kategori = this.form.kategori;
      const merek = this.form.merek;
      this.createKategoriMerek(kategori, merek)
        .then(kategoriMerekId => {
          const payload = {
            kategori_merek_id: kategoriMerekId,
            kode_tipe: this.form.kode_tipe,
            nama_tipe: this.form.nama_tipe,
          };
          if (this.currentIndex === null) {
            // Tambah Data
            axios.post('/api/v1/tipe', payload)
              .then(response => {
                this.Tipe.push(response.data);
                this.closeModal();
                this.fetchData();
                this.currentPage = 1; // Tambahkan kode ini untuk reset pagination
              })
              .catch(error => {
                console.error(error);
              });
          } else {
            // Edit Data
            axios.put(`/api/v1/tipe/${this.currentId}`, payload)
              .then(response => {
                this.Tipe[this.currentIndex] = response.data;
                this.closeModal();
                this.fetchData();
                this.currentPage = 1; // Tambahkan kode ini untuk reset pagination
              })
              .catch(error => {
                console.error(error);
              });
          }
        })
        .catch(error => {
          console.error(error);
        });
    },
    // saveData() {
    //   const payload = {
    //     kategori: this.form.kategori,
    //     merek: this.form.merek,
    //     nama_kategori: this.form.nama_kategori,
    //   };
    //   if (this.currentIndex === null) {
    //     // Tambah Data
    //     axios.post('/api/v1/kategori', payload)
    //       .then(response => {
    //         this.Tipe.push(response.data);
    //         this.closeModal();
    //         this.fetchData();
    //         this.currentPage = 1; // Tambahkan kode ini untuk reset pagination
    //       })
    //       .catch(error => {
    //         console.error(error);
    //       });
    //   } else {
    //     // Edit Data
    //     axios.put(`/api/v1/kategori/${this.currentId}`, payload)
    //       .then(response => {
    //         this.Tipe[this.currentIndex] = response.data;
    //         this.closeModal();
    //         this.fetchData();
    //         this.currentPage = 1; // Tambahkan kode ini untuk reset pagination
    //       })
    //       .catch(error => {
    //         console.error(error);
    //       });
    //   }
    // },
    deleteData(index, item) {
      axios.delete(`/api/v1/kategori/${item.id}`)
        .then(response => {
          this.Tipe.splice(index, 1);
        })
        .catch(error => {
          console.error(error);
        });
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
    debouncedFetchNoSeri: _.debounce(function () {
        this.fetchData();
      }, 300),
  }
}
</script>

<style scoped>
/* Pastikan backdrop tidak menutupi modal */
.modal-backdrop {
  z-index: 1040 !important; /* Atur backdrop di bawah modal */
}

.modal {
  z-index: 1050 !important; /* Modal di atas backdrop */
}
</style>