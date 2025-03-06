<template>
  <div class="container-fluid">
    <!-- Head -->
    <div class="row mb-2 align-items-center" >
      <div class="col-sm-6"><h3 style="font-family: Raleway;">Detail Kondisi Rusak</h3> 
        <h6 style="color: rgb(128, 128, 128);"></h6>
      </div> 
      <div class="col-sm-6 mt-3">
        <ol class="breadcrumb float-sm-right bg-table" style="border-radius: 10px;">
          <li class="breadcrumb-item">
            <a style="color: #169ea8; text-decoration: none;" href="/kondisi-rusak">Kondisi Rusak</a>
          </li>
          <li class="breadcrumb-item active" style="color: red;">
            <span>Detail Kondisi Rusak</span>
          </li>
        </ol>
      </div>
    </div>
    <!-- Detail -->
    <div class="card shadow">
      <div class="row m-1">
        <div class="col-12">
          <h4 class="text-capitalize text-primary text-bold"><b>No Seri #{{ $route.params.id }}</b></h4>
        </div>
        <div class="col-3">
          <dt style="color: #000;">PIC</dt>
          <dd>{{ data[0].pic }}</dd>
        </div>
        <div class="col-3">
          <dt style="color: #000;">Nama Produk</dt>
          <dd>{{ data[0].nama }}</dd>
        </div>
        <div class="col-3">
          <dt style="color: #000;">Target</dt>
          <dd>{{ data[0].tgl_selesai }} <br>
            <small>
              <i :class="{'fas fa-clock': !durasidata[0].includes('hari lewat') && !durasidata[0].includes('hari lagi'), 'fas fa-exclamation-circle text-danger': durasidata[0].includes('hari lewat') || durasidata[0].includes('hari lagi')}"></i>
              <span :class="{'text-danger': durasidata[0].includes('hari lewat') || durasidata[0].includes('hari lagi')}">
                {{ durasidata[0] }}
              </span>
            </small>
          </dd>
        </div>
        <div class="col-3">
          <dt style="color: #000;">Status</dt>
          <dd>{{ data[0].status }}</dd>
        </div>
      </div>        
    </div>
    <!-- Aktivity -->
    <div class="card shadow mt-5 mb-3">
      <div class="m-2">
        <div class="col-12">
          <h4 class="text-capitalize text-primary text-bold"><b>Aktivitas</b></h4>
        </div>        
        <div class="row align-items-center justify-content-end m-3">          
          <!-- <button class="btn btn-primary mr-2" @click="showModal = true">Tambah Aktivitas</button> -->
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
          <table class="table table-border no-border table-custom">
            <thead class="bg-table">
              <tr class="text-center" style="color: #000;">
                <th>#</th>
                <th>Tanggal</th>
                <th>Detail</th>
                <th>Kondisi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in aktivitasList" :key="index" class="text-center">
                <td>{{ index + 1 }}</td>
                <td>{{ item.tanggal }}</td>
                <td>{{ item.detail }}</td>
                <td>
                  <div 
                    class="btn-sts"
                    :class="{
                      'status-rusak': item.kondisi === 'Rusak',
                      'status-active': item.kondisi === 'OK',
                      'status-error': item.kondisi === 'Error',
                    }">
                    {{ item.kondisi }}
                    </div>
                </td>
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
    </div>    
    <!-- Modal -->
    <div class="modal fade show" id="modalAktivitas" tabindex="-1" role="dialog" aria-labelledby="modalAktivitasLabel" v-if="showModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalAktivitasLabel">Tambah Aktivitas</h5>
            <button type="button" class="close" @click="showModal = false" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" v-model="aktivitas.tanggal">
              </div>
              <div class="form-group">
                <label for="detail">Detail</label>
                <textarea class="form-control" id="detail" v-model="aktivitas.detail"></textarea>
              </div>
              <div class="form-group">
                <label for="kondisi">Kondisi</label>
                <select class="form-control" id="kondisi" v-model="aktivitas.kondisi">
                  <option value="">Pilih Kondisi</option>
                  <option value="OK">OK</option>
                  <option value="Rusak">Rusak</option>
                  <option value="Error">Error</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showModal = false">Batal</button>
            <button type="button" class="btn btn-primary" @click="addAktivitas">Simpan</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      showModal: false,
      aktivitas: {
        tanggal: '',
        detail: '',
        kondisi: ''
      },
      aktivitasList: [
        { tanggal: '2025-02-01', detail: 'Contoh detail 1', kondisi: 'Error' },
        { tanggal: '2025-02-02', detail: 'Contoh detail 2', kondisi: 'OK' }
      ],
      data: [
        { no_seri: '1122wscj121', nama: 'Clamp', tgl: '2025-02-01', kondisi: 'Error', detail: 'Sensor tidak berfungsi', pic: 'John Doe', tgl_selesai: '2025-02-05', status: 'Selesai' },        
      ],
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
    }
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
      return Math.ceil(this.aktivitasList.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(start + this.rowsPerPage - 1, this.aktivitasList.length);
      return `Menampilkan ${start} - ${end} dari ${this.aktivitasList.length} data`;
    }
  },
  methods: {
    addAktivitas() {
      this.aktivitasList.push({ ...this.aktivitas });
      this.aktivitas.tanggal = '';
      this.aktivitas.detail = '';
      this.aktivitas.kondisi = '';
      this.showModal = false;
    },
    updatePaginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      this.paginatedData = this.aktivitasList.slice(start, start + this.rowsPerPage);
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
</style>