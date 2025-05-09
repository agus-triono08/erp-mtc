<template>
  <div class="container-fluid">
    <!-- Head -->
    <div class="row mb-2 align-items-center" >
      <div class="col-sm-6"><h3 style="font-family: Raleway;" class="text-black-10">Detail Kerusakan Alat/Mesin</h3> 
        <h6 style="color: rgb(128, 128, 128);"></h6>
      </div> 
      <div class="col-sm-6 mt-3">
        <ol class="breadcrumb float-sm-right bg-table" style="border-radius: 10px;">
          <li class="breadcrumb-item">
            <a style="color: #169ea8; text-decoration: none;" href="javascript:history.back()">Kerusakan Alat/Mesin</a>
          </li>
          <li class="breadcrumb-item active" style="color: red;">
            <span>Detail Kerusakan Alat/Mesin</span>
          </li>
        </ol>
      </div>
    </div>
    <!-- Detail -->
    <div class="card shadow">
      <div class="row m-1">
        <div class="col-12">
          <h4 class="text-capitalize text-primary text-bold"><b>No Kerusakan #{{ dataBaru.no_kerusakan }}</b></h4>
        </div>
        <!-- <div class="col-3">
          <dt style="color: #000;">PIC</dt>
          <dd>{{ '-' }}</dd>
        </div> -->
        <div class="col-6">
          <dt style="color: #000;">Nama Produk</dt>
          <dd>{{ dataBaru.no_seri && dataBaru.no_seri.tools && dataBaru.no_seri.tools.nama }}</dd>
          <dt class="text-black-10">Layout</dt>
          <dd v-if="dataBaru.no_seri && dataBaru.no_seri.layout">
            Ruang {{ dataBaru.no_seri.layout.ruang }} / Rak {{ dataBaru.no_seri.layout.rak }} / Lantai {{ dataBaru.no_seri.layout.lantai }} / Koordinat: {{ dataBaru.no_seri.layout.koordinat }}
          </dd>
        </div>
        <!-- <div class="col-3">
          <dt style="color: #000;">Target</dt>
          <dd>{{ dataBaru.tgl_selesai }} <br>
            <small>
              <i :class="{'fas fa-clock': !durasidata[0].includes('hari lewat') && !durasidata[0].includes('hari lagi'), 'fas fa-exclamation-circle text-danger': durasidata[0].includes('hari lewat') || durasidata[0].includes('hari lagi')}"></i>
              <span :class="{'text-danger': durasidata[0].includes('hari lewat') || durasidata[0].includes('hari lagi')}">
                {{ durasidata[0] }}
              </span>
            </small>
          </dd>
        </div> -->
        <div class="col-6">
          <dt style="color: #000;">Status</dt>
          <dd>
            <div 
              class="badge"
              :class="{
                        'status-active': dataBaru.status === 'Selesai',
                        'status-musnah': dataBaru.status === 'Belum',}">
              {{ dataBaru.status }}
            </div>
          </dd>
        </div>
      </div>        
    </div>
    <!-- Aktivity -->
    <div class="card shadow mt-5 mb-3">
      <div class="m-2">
        <div class="col-12">
          <h4 class="text-capitalize text-primary text-bold"><b>Aktivitas Pemusnahan</b></h4>
        </div>        
        <div class="row align-items-center justify-content-end m-3">
          <button class="btn btn-primary mr-3" @click="openAktivitasModal">Tambah Aktivitas</button>          
          <!-- Tombol Tambah Aktivitas akan hilang jika aktivitas sudah selesai -->
          <!-- <button v-if="shouldShowTambahAktivitas" class="btn btn-primary mr-2" @click="showModal = true" :disabled="isAktivitasSelesai || isAllAktivitasCompleted">Tambah Aktivitas</button>       -->
          <!-- Tombol Selesai hanya muncul jika kondisi aktivitas terakhir adalah OK atau Rusak -->
          <!-- <button v-if="isLastAktivitasCompleted && !isAktivitasSelesai" class="btn btn-success mr-2" @click="selesaiAktivitas" :disabled="!isLastAktivitasCompleted">Selesai</button> -->
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
          <table class="table table-border no-border table-custom"  style="overflow-y: auto; min-width: 1300px;">
            <thead class="bg-table">
              <tr class="text-center" style="color: #000;">
                <th>#</th>
                <th>Tanggal Perbaikan</th>
                <th>Waktu Perbaikan</th>
                <th>PIC</th>
                <th>Detail</th>
                <th>Kondisi</th>
                <th>Status</th>
                <th>Alasan Penolakan</th>
                <th>Catatan</th>
              </tr>
            </thead>
            <tbody v-if="dataBaru.rusak_activity && dataBaru.rusak_activity.length === 0">
              <tr>
                <td colspan="9" class="text-center">Tidak Ada Data</td>
              </tr>
            </tbody>
            <tbody>
              <tr v-for="(item, index) in dataBaru.rusak_activity" :key="item.id">
                <td>{{ index + 1 }}</td>
                <td>{{ item.changed_at || '-' }}</td>
                <td>{{ item.waktu_mulai || '-'}} - {{ item.waktu_selesai || '-' }}</td>
                <td>{{ item.nama_pic || '-'}}</td>
                <td>{{ item.detail_kerusakan || '-'}}</td>
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
                <td>
                  <div 
                    class="btn-sts"
                    :class="{
                      'status-rusak': item.status === 'Ditolak',
                      'status-active': item.status === 'Disetujui',
                      'status-error': item.status === 'Menunggu Persetujuan Atasan',
                    }">
                    {{ item.status }}
                    </div>
                </td>
                <td>{{ item.alasan_penolakan || '-'}}</td>
                <td>{{ item.catatan || '-'}}</td>
              </tr>
            </tbody>
          </table>     
          <!-- Pagination -->
          <div class="d-flex justify-content-between align-items-center mt-3 mb-3" style="min-width: 1300px; border-radius: 10px; background-color: #f3f4f6; height: 50px; color: #000;">
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
    <div class="modal fade show" id="modalAktivitasRusak" tabindex="-1" role="dialog" aria-labelledby="modalAktivitasRusakLabel" v-if="showModal" style="overflow-y: auto;">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalAktivitasRusakLabel">Aktivitas Pemusnahan</h5>
            <button type="button" class="close" @click="showModal = false" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="waktu" class="text-black-10"><b>Waktu (Mulai - Selesai) <sup class="text-danger">*</sup></b></label>
                <div class="input-group">
                  <input type="time" class="form-control" id="waktu_mulai" v-model="aktivitas.waktu_mulai" required>
                  <span class="input-group-text">-</span>
                  <input type="time" class="form-control" id="waktu_selesai" v-model="aktivitas.waktu_selesai" required>
                </div>
              </div>
              <div class="form-group">
                <label for="pic" class="text-black-10"><b>PIC</b></label>
                <v-select
                  :options="users"
                  v-model="aktivitas.pic"
                  multiple
                  placeholder="Pilih PIC"
                  :searchable="true"
                  label="nama"
                  :reduce="user => user.id"
                />
              </div>
              <div class="form-group">
                <label for="detail" class="text-black-10"><b>Detail <sup class="text-danger">*</sup></b></label>
                <!-- <textarea class="form-control" id="detail" v-model="aktivitas.detail_kerusakan" required placeholder="Masukkan Detail Perbaikan"></textarea> -->
                <div class="textarea-wrapper">
                  <textarea
                    id="detail_kerusakan"                
                    v-model="aktivitas.detail_kerusakan"
                    class="form-control"
                    rows="3"
                    placeholder="Masukkan Detail Kerusakan (Maksimal 200 Karakter)"
                    maxlength="200"
                  ></textarea>
                  <small class="text-muted char-counter">
                    {{ aktivitas.detail_kerusakan.length }} / 200
                  </small>
                </div>
              </div>
              <!-- <div class="form-group">
                <label for="kondisi" class="text-black-10"><b>Kondisi <sup class="text-danger">*</sup></b></label>
                <select class="form-control" id="kondisi" v-model="aktivitas.kondisi" required>
                  <option value="">Pilih Kondisi</option>
                  <option value="OK">OK</option>
                  <option value="Rusak">Rusak</option>
                </select>
              </div> -->
              <!-- <div class="form-group" v-if="aktivitas.kondisi === 'Rusak'">
                <label for="layout" class="text-black-10"><b>Layout <sup class="text-danger">*</sup></b></label>
                <v-select
                  v-model="aktivitas.layout"
                  required
                  placeholder="Pilih Layout"
                  :options="layouts"
                  label="ruang"
                  :searchable="true"
                  :reduce="layout => layout.id"
                />
              </div> -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" @click="showModal = false">Batal</button>
            <button type="button" class="btn btn-primary" @click="addAktivitas">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <!-- <div class="modal fade show" id="modalAktivitas" tabindex="-1" role="dialog" aria-labelledby="modalAktivitasLabel" v-if="showModal" style="overflow-y: auto;">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalAktivitasLabel">Aktivitas Perbaikan</h5>
            <button type="button" class="close" @click="showModal = false" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group"> 
                <label for="waktu" class="text-black-10"><b>Waktu (Mulai - Selesai) <sup class="text-danger"> *</sup> </b></label> 
                <div class="input-group"> <input type="time" class="form-control" id="waktu_mulai" v-model="aktivitas.waktu_mulai" required>
                  <span class="input-group-text">-</span> 
                  <input type="time" class="form-control" id="waktu_selesai" v-model="aktivitas.waktu_selesai" required> 
                </div> 
              </div>
              <div class="form-group">
                <label for="pic" class="text-black-10"><b>PIC</b></label>
                <v-select
                  :options="users"
                  v-model="aktivitas.pic"
                  multiple
                  placeholder="Pilih PIC"
                  :searchable="true"
                  label="nama"
                  :reduce="user => user.id"
                />
              </div>
              <div class="form-group">
                <label for="detail" class="text-black-10"><b>Detail <sup class="text-danger"> *</sup></b></label>
                <textarea class="form-control" id="detail" v-model="aktivitas.detail" required></textarea>
              </div>
              <div class="form-group">
                <label for="kondisi" class="text-black-10"><b>Kondisi <sup class="text-danger"> *</sup></b></label>
                <select class="form-control" id="kondisi" v-model="aktivitas.kondisi" required>
                  <option value="">Pilih Kondisi</option>
                  <option value="OK">OK</option>
                  <option value="Rusak">Rusak</option>
                  <option value="Error">Error</option>
                </select>
              </div>
              <div class="form-group" v-if="aktivitas.kondisi === 'Rusak'">
                <label for="layout" class="text-black-10"><b>Layout</b><sup style="color: red;"> *</sup></label>                
                <v-select
                  v-model="aktivitas.layout"
                  required
                  placeholder="Pilih Layout"
                  :options="layouts"
                  label="ruang"
                  :searchable="true"
                  :reduce="layout => layout.id"
                />
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" @click="showModal = false">Batal</button>
            <button type="button" class="btn btn-primary" @click="addAktivitas">Simpan</button>
          </div>
        </div>
      </div>
    </div> -->
    <!-- Modal Selesai Aktivitas -->
    <!-- <div class="modal fade show" id="modalSelesaiAktivitas" tabindex="-1" role="dialog" aria-labelledby="modalSelesaiAktivitasLabel" v-if="showSelesaiModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalSelesaiAktivitasLabel">Selesai Aktivitas</h5>
            <button type="button" class="close" @click="showSelesaiModal = false" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Apakah Anda yakin ingin menyelesaikan aktivitas ini?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showSelesaiModal = false">Batal</button>
            <button type="button" class="btn btn-primary" @click="selesaiAktivitas">Selesai</button>
          </div>
        </div>
      </div>
    </div> -->
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
      showModal: false,
      showSelesaiModal: false,
      aktivitas: {
        id: null,
        waktu_mulai: '',
        waktu_selesai: '',
        detail_kerusakan: '',
        // kondisi: 'Rusak',
        // layout: '',
        pic: [],
      },
      aktivitasList: [],
      dataBaru: [],
      dataAktivitas: [],
      layouts: [],
      users: [],
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
      isAktivitasSelesai: false,
    }
  },
  computed: {
    detailItem() {
      return this.dataBaru.find(item => item.no_perbaikan == this.$route.params.id);
    },
    isLastAktivitasCompleted() {
      const lastAktivitas = this.dataAktivitas[this.dataAktivitas.length - 1];
      return lastAktivitas && (lastAktivitas.kondisi === 'OK' || lastAktivitas.kondisi === 'Rusak');
    },
    isAllAktivitasCompleted() {
      return this.dataAktivitas.every(item => item.kondisi === 'OK' || item.kondisi === 'Rusak');
    },
    // Hanya tampilkan tombol "Tambah Aktivitas" jika aktivitas tidak selesai
    shouldShowTambahAktivitas() {
      return !this.isLastAktivitasCompleted && !this.isAllAktivitasCompleted;
    },
    durasidata() {
      return this.dataBaru.map(item => {
        if (item.tgl_selesai && item.tgl_perbaikan) {
          const tglSelesai = new Date(item.tgl_selesai);
          const tglSekarang = new Date(item.tgl_perbaikan);
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
    filteredData() {
      let result = this.dataBaru.filter(item => {
        return (
          item.detail_kerusakan.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          item.kondisi.toLowerCase().includes(this.searchQuery.toLowerCase()) 
        );
      });
      return result;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = start + this.rowsPerPage;
      return this.filteredData.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.dataBaru.length / this.rowsPerPage);
    },
    paginationInfo() {
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(start + this.rowsPerPage - 1, this.dataBaru.length);
      return `Showing ${start} to ${end} of ${this.dataBaru.length} entries`;
    }
  },
  methods: {
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
    async fetchData() {
      const id = this.$route.params.id;
      const res = await fetch(`/api/v1/kerusakan/${id}`);
      const data = await res.json();
      this.dataBaru = data;
      // console.log(this.dataBaru);
    },
    // async fetchDataAktivitas() {
    //   const res = await fetch('/api/v1/activity-perbaikan');
    //   const data = await res.json();
    //   this.dataAktivitas = data.all;
    // },
    async fetchInitialData() {
      try {
        const [layoutsRes, usersRes] = await Promise.all([
          // axios.get('/api/v1/tools'),
          axios.get('/api/v1/layouts'),
          axios.get('/api/v1/users')
        ]);
        // this.tools = toolsRes.data;
        this.layouts = layoutsRes.data;
        this.users = usersRes.data.byPIC;
      } catch (err) {
        console.error('Gagal fetch data awal:', err);
      }
    },
    openAktivitasModal() {
      if (this.dataBaru.rusak_activity && this.dataBaru.rusak_activity.length > 0) {
        const lastAktivitas = this.dataBaru.rusak_activity[this.dataBaru.rusak_activity.length - 1];
        if (lastAktivitas.status === 'Menunggu Persetujuan Atasan') {
          Swal.fire({
            icon: 'error',
            title: 'Tidak dapat menambah Aktivitas',
            text: 'Aktivitas sebelumnya masih Menunggu Persetujuan Atasan.',
          });
          return;
        }
      }
      // Siapkan form modal
      this.aktivitas = {
        id: this.$route.params.id,
        waktu_mulai: '',
        waktu_selesai: '',
        detail_kerusakan: '',
        // kondisi: '',
        // layout: '',
        pic: [],
      };
      this.showModal = true;
    },
    async addAktivitas() {
    // Validasi frontend manual
    if (
      !this.aktivitas.waktu_mulai || 
      !this.aktivitas.waktu_selesai || 
      !this.aktivitas.detail_kerusakan || 
      // !this.aktivitas.kondisi || 
      this.aktivitas.pic.length === 0 
      // (this.aktivitas.kondisi === 'Rusak' && !this.aktivitas.layout)
    ) {
      Swal.fire({
        icon: 'error',
        title: 'Data tidak lengkap',
        text: 'Pastikan semua field wajib telah diisi.',
      });
      return;
    }

    try {
      const payload = {
        ...this.aktivitas,
        pic: this.aktivitas.pic, // array of user IDs
        // layout: this.aktivitas.layout || null,
      };

      const confirm = await Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin mengirim hasil pengecekan?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, kirim!',
        cancelButtonText: 'Tidak, batalkan!',
      });

      if (confirm.isConfirmed) {
        await axios.post('/api/v1/kerusakan/add-activity', payload);
        Swal.fire('Terkirim!', 'Data aktivitas berhasil disimpan.', 'success');
        this.showModal = false;
      }

    } catch (error) {
      let msg = 'Terjadi kesalahan saat mengirim data.';
      if (error.response && error.response.data && error.response.data.message) {
        msg = error.response.data.message;
      }
      Swal.fire({
        icon: 'error',
        title: 'Gagal mengirim',
        text: msg,
      });
    }
  },
    // async addAktivitas() {
    //   try {
    //     const payload = {
    //       id: this.aktivitas.id,
    //       waktu_mulai: this.aktivitas.waktu_mulai,
    //       waktu_selesai: this.aktivitas.waktu_selesai,
    //       detail_kerusakan: this.aktivitas.detail_kerusakan,
    //       kondisi: this.aktivitas.kondisi,
    //       layout: this.aktivitas.layout,
    //       pic: this.aktivitas.pic.join(','),
    //     };

    //     await axios.post('/api/v1/perbaikan/add-activity', payload);
    //     this.showModal = false;
    //     this.$toast.success('Aktivitas berhasil disimpan');
    //   } catch (error) {
    //     console.error(error);
    //     this.$toast.error('Gagal menyimpan aktivitas');
    //   }
    // },
  },
  mounted() {
    this.fetchData();
    // this.fetchDataAktivitas();
    this.fetchInitialData();
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
.text-black-10 {
  color: #000;
}
</style>