<template>
  <div class="container-fluid">
    <h1 class="h3 mb-4 mt-4 text-gray-900"><b>Kondisi</b></h1>
    <ul id="pills-tab" role="tablist" class="nav nav-pills mb-3" style="margin-top: 1rem !important;">
      <li role="presentation" class="nav-item">
        <router-link id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="false" class="nav-link" :class="{ active: $route.name === 'kondisi-baru-error' }" :to="{ name: 'kondisi-baru-error' }">Baru</router-link>
      </li>
      <li role="presentation" class="nav-item">
        <router-link id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="true" class="nav-link" :class="{ active: $route.name === 'kondisi-proses-error' }" :to="{ name: 'kondisi-proses-error' }">Proses</router-link>
      </li>
      <li role="presentation" class="nav-item">
        <router-link id="pills-contact-tab" data-toggle="pill" data-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false" class="nav-link" :class="{ active: $route.name === 'kondisi-selesai-error' }" :to="{ name: 'kondisi-selesai-error' }">Selesai</router-link>
      </li>
    </ul>
    <div class="row align-items-center justify-content-end m-3">      
      <!-- Tambah Data -->
      <!-- <button class="btn btn-sm btn-outline-primary mr-2 ml-1" @click="openModal('add')">
        <i class="fa fa-plus-circle"></i> Tambah Data
      </button> -->
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
            <th class="text-black-1">No Seri</th>
            <th class="text-black-1">Nama</th>
            <th class="text-black-1">Tgl Error</th>
            <!-- <th class="text-black-1">Kondisi</th> -->
            <th class="text-black-1">Detail</th>
            <th class="text-black-1">Target</th>
            <th class="text-black-1">Status</th>
            <th class="text-black-1">Aksi</th>
          </tr>
        </thead>
        <tbody v-if="paginatedData.length === 0">
          <tr class="text-center">
            <td colspan="9">Data tidak ditemukan</td>
          </tr>
        </tbody>
        <tbody v-for="(item, index) in paginatedData" :key="index">
          <tr class="text-center">
            <td>{{ index + 1 }}</td>
            <td>{{ item.no_seri }}</td>
            <td>{{ item.nama }}</td>
            <td>{{ item.tgl }}</td>
            <!-- <td>
              <div 
                class="btn-sts"
                :class="{
                  'status-error': item.kondisi === 'Error',
                }">
                {{ item.kondisi }}
              </div>
            </td> -->
            <td>{{ item.detail }}</td>
            <td>
              {{ item.tgl_selesai }} <br>
              <small>
                <i :class="{'fas fa-clock': !durasidata[index].includes('hari lewat') && !durasidata[index].includes('hari lagi'), 'fas fa-exclamation-circle text-danger': durasidata[index].includes('hari lewat') || durasidata[index].includes('hari lagi')}"></i>
                <span :class="{'text-danger': durasidata[index].includes('hari lewat') || durasidata[index].includes('hari lagi')}">
                  {{ durasidata[index] }}
                </span>
              </small>
            </td>
            <td>
              <div 
                class="btn-sts"
                :class="{
                  'status-rusak': item.status === 'Belum',
                  'status-hilang': item.status === 'Proses',
                  'status-active': item.status === 'Selesai',
                }">
                {{ item.status }}
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
                  <!-- <a class="dropdown-item" @click="perbaikanData(index)">
                    <i class="fas fa-check text-success"></i> Perbaikan
                  </a> -->
                  <router-link :to="{ name: 'kondisi-detail-proses-error', params: { id: item.no_seri } }" class="dropdown-item">
                    <i class="fas fa-eye text-info"></i> Detail
                  </router-link>
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
              <label for="no_seri">No Seri</label>
              <select class="form-control" id="no_seri" v-model="form.no_seri">
                <option value="" disabled>Pilih No Seri</option>
                <option value="1122wscj121">1122wscj121</option>
                <option value="1122wscj122">1122wscj122</option>
                <option value="1122wscj123">1122wscj123</option>
              </select>
            </div>
            <div class="form-group">
              <label for="nama">Nama</label>
              <select class="form-control" id="nama" v-model="form.nama">
                <option value="" disabled>Pilih Nama</option>
                <option value="Clamp">Clamp</option>
                <option value="Sensor">Sensor</option>
                <option value="Koneksi">Koneksi</option>
              </select>
            </div>
            <div class="form-group">
              <label for="tgl">Tgl Error</label>
              <input type="date" class="form-control" id="tgl" v-model="form.tgl">
            </div>
            <!-- <div class="form-group">
              <label for="kondisi">Kondisi</label>
              <select class="form-control" id="kondisi" v-model="form.kondisi">
                <option value="" disabled>Pilih Kondisi</option>
                <option value="Error">Error</option>
              </select>
            </div> -->
            <div class="form-group">
              <label for="detail">Detail</label>
              <input type="text" class="form-control" id="detail" v-model="form.detail">
            </div>
            <div class="form-group">0
              <label for="pic">PIC</label>
              <v-select
                :options="picOptions"
                v-model="form.pic"
                multiple
                label="text"
                :reduce="(pic) => pic.value"
              />
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <select class="form-control" id="status" v-model="form.status">
                <option value="" disabled>Pilih Status</option>
                <option value="Belum">Belum</option>
              </select>
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
import vSelect from 'vue-select';
import Swal from 'sweetalert2';

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
      data: [
        { no_seri: '1122wscj121', nama: 'Clamp', tgl: '2025-02-01', kondisi: 'Error', detail: 'Sensor tidak berfungsi', pic: 'John Doe', tgl_selesai: '2025-02-05', status: 'Proses' },        
      ],
      paginatedData: [],
      searchQuery: '',
      rowsPerPage: 5,
      currentPage: 1,
      isModalOpen: false,
      modalTitle: '',
      modalAction: '',
      form: {
        no_seri: '',
        nama: '',
        tgl: '',
        kondisi: '',
        detail: '',
        pic: '',
        status: ''
      }
    };
  },
  computed: {
    durasidata() {
      return this.data.map(item => {
        if (item.tgl_selesai && item.tgl) {
          const tglSelesai = new Date(item.tgl_selesai);
          const tglSekarang = new Date(item.tgl);
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
    totalPages() {
      return Math.ceil(this.data.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(start + this.rowsPerPage - 1, this.data.length);
      return `Menampilkan ${start} - ${end} dari ${this.data.length} data`;
    }
  },
  methods: {
    openModal(action, item = null, index = null) {
      this.isModalOpen = true;
      this.modalAction = action === 'add' ? 'Simpan' : 'Perbarui';
      this.modalTitle = action === 'add' ? 'Tambah Data' : 'Edit Data';
      if (action === 'edit') {
        this.form = { ...item };
      } else {
        this.form = {
          no_seri: '',
          nama: '',
          tgl: '',
          kondisi: '',
          detail: '',
          pic: '',
          status: ''
        };
      }
    },
    closeModal() {
      this.isModalOpen = false;
    },
    saveData() {
      if (this.modalAction === 'Simpan') {
        this.data.push({ ...this.form });
      } else {
        const index = this.data.findIndex(item => item.no_seri === this.form.no_seri);
        if (index !== -1) {
          this.$set(this.data, index, { ...this.form });
        }
      }
      this.closeModal();
      this.updatePaginatedData();
    },
    deleteData(index) {
      this.data.splice(index, 1);
      this.updatePaginatedData();
    },
    updatePaginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      this.paginatedData = this.data.slice(start, start + this.rowsPerPage);
    },
    perbaikanData(index) {
      Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin memperbaiki data ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, perbaiki!',
        cancelButtonText: 'Tidak, batalkan!',
      }).then((result) => {
        if (result.isConfirmed) {
          this.data.splice(index, 1);
          this.updatePaginatedData();
          Swal.fire('Berhasil!', 'Data telah diperbaiki.', 'success');
        }
      });
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
  },
  mounted() {
    this.updatePaginatedData();
  }
};
</script>

<style scoped>
/* Pastikan backdrop tidak menutupi modal */
.modal-backdrop {
  z-index: 1040 !important; /* Atur backdrop di bawah modal */
}

.modal {
  z-index: 1050 !important; /* Modal di atas backdrop */
}

#pills-tab .nav-link {
  color: #000;
}

#pills-tab .nav-link.active {
  background-color: #169ea8;
  color: #fff;
}
</style>
