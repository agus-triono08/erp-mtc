<template>
  <div class="container-fluid">
    <h1 class="h3 mb-4 mt-4 text-gray-900"><b>Layout</b></h1>
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
            <th class="text-black-1">Ruang</th>
            <th class="text-black-1">Lantai</th>
            <th class="text-black-1">Rak</th>
            <th class="text-black-1">Aksi</th>
          </tr>
        </thead>
        <tbody v-if="paginatedData.length === 0">
          <tr class="text-center">
            <td colspan="5">Data tidak ditemukan</td>
          </tr>
        </tbody>
        <tbody v-for="(item, index) in paginatedData" :key="index">
          <tr class="text-center">
            <td>{{ index + 1 }}</td>
            <td>{{ item.ruang }}</td>
            <td>{{ item.lantai }}</td>
            <td>{{ item.rak }}</td>
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
                  <a class="dropdown-item" @click="deleteData(index, item)">
                    <i class="fas fa-trash text-danger"></i> Hapus
                  </a>
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
            <div class="form-group">
              <label for="ruang">Ruang</label>
              <input type="text" class="form-control" id="ruang" v-model="form.ruang">
            </div>
            <div class="form-group">
              <label for="lantai">Lantai</label>
              <input type="text" class="form-control" id="lantai" v-model="form.lantai">
            </div>
            <div class="form-group">
              <label for="rak">Rak</label>
              <input type="text" class="form-control" id="rak" v-model="form.rak">
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

export default {
  data() {
    return {
      searchQuery: '',
      layouts: [], // Data layout dari API Laravel
      form: {
        ruang: '',
        lantai: '',
        rak: ''
      },
      currentIndex: null,
      modalTitle: '',
      modalAction: '',
      isModalOpen: false, // Flag untuk modal open/close
      rowsPerPage: 10, // Menentukan jumlah item per halaman
      currentPage: 1 // Halaman saat ini
    }
  },
  mounted() {
    this.getLayouts();
  },
  computed: {
    filteredData() {
      return this.layouts.filter(item => {
        return  item.ruang.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                item.lantai.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                item.rak.toLowerCase().includes(this.searchQuery.toLowerCase());
      });
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
    getLayouts() {
      axios.get('/api/layouts')
        .then(response => {
          this.layouts = response.data;
        })
        .catch(error => {
          console.error(error);
        });
    },
    openModal(action, item = null, index = null) {
      if (action === 'add') {
        this.modalTitle = 'Tambah Data';
        this.modalAction = 'Simpan';
        this.form.ruang = '';
        this.form.lantai = '';
        this.form.rak = '';
        this.currentIndex = null;
        this.currentId = null;
      } else if (action === 'edit' && item) {
        this.modalTitle = 'Edit Data';
        this.modalAction = 'Update';
        this.form.ruang = item.ruang;
        this.form.lantai = item.lantai;
        this.form.rak = item.rak;
        this.currentIndex = index;
        this.currentId = item.id;
      }
      this.isModalOpen = true; // Set modal status open
    },
    closeModal() {
      this.isModalOpen = false; // Set modal status close
    },
    saveData() {
      if (this.currentIndex === null) {
        // Tambah Data
        axios.post('/api/layouts', this.form)
          .then(response => {
            this.layouts.push(response.data);
            this.closeModal();
            this.getLayouts();
            this.currentPage = 1; // Tambahkan kode ini untuk reset pagination
          })
          .catch(error => {
            console.error(error);
          });
      } else {
        // Edit Data
        axios.put(`/api/layouts/${this.currentId}`, this.form)
          .then(response => {
            this.layouts[this.currentIndex] = response.data;
            this.closeModal();
            this.getLayouts();
            this.currentPage = 1; // Tambahkan kode ini untuk reset pagination
          })
          .catch(error => {
            console.error(error);
          });
      }
    },
    deleteData(index, item) {
      axios.delete(`/api/layouts/${item.id}`)
        .then(response => {
          this.layouts.splice(index, 1);
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
        this.getLayouts();
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
