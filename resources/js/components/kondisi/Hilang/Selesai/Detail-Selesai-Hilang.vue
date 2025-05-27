<template>
  <div class="container-fluid">
    <!-- Head -->
    <div class="row mb-2 align-items-center" >
      <div class="col-sm-6"><h3 style="font-family: Raleway;" class="text-black-10">Detail Pengantian Alat/Mesin</h3> 
        <h6 style="color: rgb(128, 128, 128);"></h6>
      </div> 
      <div class="col-sm-6 mt-3">
        <ol class="breadcrumb float-sm-right bg-table" style="border-radius: 10px;">
          <li class="breadcrumb-item">
            <a style="color: #169ea8; text-decoration: none;" href="javascript:history.back()">Pengantian Alat/Mesin</a>
          </li>
          <li class="breadcrumb-item active" style="color: red;">
            <span>Detail Pengantian Alat/Mesin</span>
          </li>
        </ol>
      </div>
    </div>
    <!-- Detail -->
    <div class="card shadow">
      <div class="row m-1">
        <div class="col-12">
          <h4 class="text-capitalize text-primary text-bold"><b>No Kehilangan #{{ dataProses.no_kehilangan }}</b></h4>
        </div>
        <div class="col-3">
          <dt style="color: #000;">Nama Peminjam</dt>
          <dd>{{ dataProses.users && dataProses.users.nama }}</dd>
          <dt class="text-black-10">Divisi</dt>
          <dd>{{ dataProses.users && dataProses.users.divisi && dataProses.users.divisi.divisi }}</dd>          
        </div>
        <div class="col-3">
          <dt style="color: #000;">Nama Produk</dt>
          <dd>{{ dataProses.no_seri && dataProses.no_seri.tools && dataProses.no_seri.tools.nama }}</dd>
          <dt class="text-black-10">Layout</dt>
          <dd v-if="dataProses.no_seri && dataProses.no_seri.layout">
            Ruang {{ dataProses.no_seri.layout.ruang }} / Rak {{ dataProses.no_seri.layout.rak }} / Lantai {{ dataProses.no_seri.layout.lantai }} / Koordinat: {{ dataProses.no_seri.layout.koordinat }}
          </dd>
        </div>
        <div class="col-3">
          <dt style="color: #000;">Detail Hilang</dt>
          <dd>{{ dataProses.detail_hilang }}</dd>
          <dt class="text-black-10">Tanggal Kehilangan</dt>
          <dd>{{ dataProses.tgl_kehilangan }}</dd>          
        </div>
        <div class="col-3">
          <dt style="color: #000;">Status</dt>
          <dd>
            <div 
              class="badge"
              :class="{
                        'status-active': dataProses.status === 'Selesai',
                        'status-musnah': dataProses.status === 'Belum',
                        'status-error': dataProses.status === 'Proses'}">
              {{ dataProses.status }}
            </div>
          </dd>
        </div>
      </div>        
    </div>
    <!-- Aktivity -->
    <div class="card shadow mt-5 mb-3">
      <div class="m-2">
        <div class="col-12">
          <h4 class="text-capitalize text-primary text-bold"><b>Aktivitas Kehilangan</b></h4>
        </div>        
        <div class="row align-items-center justify-content-end m-3">
          <!-- <button class="btn btn-primary mr-3" @click="openAktivitasModal">Tambah Aktivitas</button>           -->          
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
                <th>Tanggal Penggantian Alat/Mesin</th>
                <th>No Seri Lama</th>
                <th>No Seri Baru</th>
                <th>Harga</th>
                <th>Status</th>
                <!-- <th>Aksi</th> -->
              </tr>
            </thead>
            <tbody v-if="dataProses.hilang_activity_proses && dataProses.hilang_activity_proses.length === 0">
              <tr>
                <td colspan="7" class="text-center">Tidak Ada Data</td>
              </tr>
            </tbody>
            <tbody>
              <tr v-for="(item, index) in dataProses.hilang_activity_proses" :key="item.id" class="text-center">
                <td>{{ index + 1 }}</td>
                <td>{{ item.tgl_penggantian || '-'}}</td>
                <td>{{ item.no_seri_old || '-'}}</td>
                <td>{{ item.no_seri_new || '-'}}</td>
                <td>{{ item.harga || '-'}}</td>
                <td>
                  <div 
                    class="btn-sts"
                    :class="{
                      'status-rusak': item.status === 'Menunggu Konfirmasi',
                      'status-active': item.status === 'Diterima',
                      'status-error': item.status === 'Serahkan Alat/Mesin',
                    }">
                    {{ item.status }}
                  </div>
                </td>               
                <!-- <td>
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
                      <a v-if="shouldShowDiserahkan" class="dropdown-item" @click="serahkanAlat(index)">
                        <i class="fas fa-share text-primary"></i> Serahkan Alat/Mesin
                      </a>
                      <a v-if="shouldShowNoSeriDiterima" class="dropdown-item" @click="terimaAktivitas(index)">
                        <i class="fas fa-check text-success"></i> Diterima
                      </a>
                    </div>
                  </div>
                </td>    -->
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
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" @click="showModal = false">Batal</button>
            <button type="button" class="btn btn-primary" @click="addAktivitas">Simpan</button>
          </div>
        </div>
      </div>
    </div>
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
        alasan_penolakan: '',
      },
      aktivitasList: [],
      dataProses: [],
      dataAktivitas: [],
      layouts: [],
      users: [],
      searchQuery: '',
      rowsPerPage: 10,
      currentPage: 1,
      isAktivitasSelesai: false,
      isSerah: false,
    }
  },
  computed: {
    detailItem() {
      return this.dataProses.find(item => item.no_perbaikan == this.$route.params.id);
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
    shouldShowDiterima() {
      return this.dataProses.hilang_activity_baru.length > 0 && this.dataProses.hilang_activity_baru[this.dataProses.hilang_activity_baru.length - 1].status === 'Belum';
    },
    shouldShowDiserahkan() {
      return this.dataProses.hilang_activity_proses.length > 0 && this.dataProses.hilang_activity_proses[this.dataProses.hilang_activity_proses.length - 1].status === 'Menunggu Konfirmasi';
    },
    // shouldShowDiserahkan() {
    //   return (
    //     item.hilang_activity_proses &&
    //     item.hilang_activity_proses.length > 0 &&
    //     item.hilang_activity_proses[item.hilang_activity_proses.length - 1].status === 'Menunggu Konfirmasi'
    //   );
    // },
    shouldShowNoSeriDiterima() {
      return this.dataProses.hilang_activity_proses.length > 0 && this.dataProses.hilang_activity_proses[this.dataProses.hilang_activity_proses.length - 1].status === 'Serahkan Alat/Mesin';
    },
    // shouldShowNoSeriDiterima(item) {
    //   return (
    //     item.hilang_activity_proses &&
    //     item.hilang_activity_proses.length > 0 &&
    //     item.hilang_activity_proses[item.hilang_activity_proses.length - 1].status === 'Serahkan Alat/Mesin'
    //   );
    // },
    shouldShowDitolak() {
      return this.dataProses.hilang_activity_baru.length > 0 && this.dataProses.hilang_activity_baru[this.dataProses.hilang_activity_baru.length - 1].status === 'Belum';
    },
    durasidata() {
      return this.dataProses.map(item => {
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
    hasData() {
      return this.dataProses.hilang_activity_proses && this.dataProses.hilang_activity_proses.length > 0;
    },
    filteredData() {
      if (!this.hasData) return [];

      return this.dataProses.hilang_activity_proses.filter(item => {
        return (
          (item.changed_at && item.changed_at.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
          (item.kondisi && item.kondisi.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
          (item.detail_kerusakan && item.detail_kerusakan.toLowerCase().includes(this.searchQuery.toLowerCase()))
        );
      });
    },
    // filteredData() {
    //   let result = this.dataProses.filter(item => {
    //     return (
    //       item.detail_kerusakan.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
    //       item.kondisi.toLowerCase().includes(this.searchQuery.toLowerCase()) 
    //     );
    //   });
    //   return result;
    // },
    paginatedData() {
      const start = (this.currentPage - 1) * this.rowsPerPage;
      const end = start + this.rowsPerPage;
      return this.filteredData.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredData.length / this.rowsPerPage);
    },
    paginationInfo() {
      if (!this.hasData) return 'No data available';
      
      const start = (this.currentPage - 1) * this.rowsPerPage + 1;
      const end = Math.min(start + this.rowsPerPage - 1, this.filteredData.length);
      return `Showing ${start} to ${end} of ${this.filteredData.length} entries`;
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
      const res = await fetch(`/api/v1/kehilangan/${id}`);
      const data = await res.json();
      this.dataProses = data;
      // console.log(this.dataProses);
    },
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
      if (this.dataProses.rusak_activity && this.dataProses.rusak_activity.length > 0) {
        const lastAktivitas = this.dataProses.rusak_activity[this.dataProses.rusak_activity.length - 1];
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
    serahkanAlat(index) {
      Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin melanjutkan aktivitas ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, lanjutkan!',
        cancelButtonText: 'Tidak, batalkan!',
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            const aktivitas = this.dataProses.hilang_activity_proses[index];

            const payload = {
              id: aktivitas.id,
              status: 'Serahkan Alat/Mesin'
            };

            await axios.post('/api/v1/kehilangan/alat-diserahkan', payload);

            // Update nilai di frontend setelah berhasil
            this.dataProses.hilang_activity_proses[index].status = 'Serahkan Alat/Mesin';

            Swal.fire('Berhasil!', 'Aktivitas telah berhasil.', 'success');
          } catch (error) {
            let msg = 'Terjadi kesalahan saat menyimpan data.';
            if (error.response && error.response.data && error.response.data.message) {
              msg = error.response.data.message;
            }
            Swal.fire('Gagal!', msg, 'error');
          }
        }
      });
    },
    terimaAktivitas(index) {
      Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin menerima aktivitas ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, terima!',
        cancelButtonText: 'Tidak, batalkan!',
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            const aktivitas = this.dataProses.hilang_activity_proses[index];

            const payload = {
              id: aktivitas.id,
              status: 'Diterima'
            };

            await axios.post('/api/v1/kehilangan/alat-diterima', payload);

            // Update nilai di frontend setelah berhasil
            this.dataProses.hilang_activity_proses[index].status = 'Diterima';

            Swal.fire('Berhasil!', 'Aktivitas telah diterima.', 'success');
          } catch (error) {
            let msg = 'Terjadi kesalahan saat menyimpan data.';
            if (error.response && error.response.data && error.response.data.message) {
              msg = error.response.data.message;
            }
            Swal.fire('Gagal!', msg, 'error');
          }
        }
      });
    },
    // tolakAktivitas(index) {
    //   Swal.fire({
    //     title: 'Konfirmasi',
    //     text: 'Apakah Anda yakin ingin menolak aktivitas ini?',
    //     icon: 'warning',
    //     showCancelButton: true,
    //     confirmButtonText: 'Ya, tolak!',
    //     cancelButtonText: 'Tidak, batalkan!',
    //     input: 'textarea',
    //     inputPlaceholder: 'Masukkan Alasan Penolakan',
    //     inputValidator: (value) => {
    //       if (!value) {
    //         return 'Harap masukkan catatan!';
    //       }
    //     }
    //   }).then(async (result) => {
    //     if (result.isConfirmed) {
    //       try {
    //         const aktivitas = this.dataProses.hilang_activity_baru[index]; // ← akses langsung dari tabel
    //         const payload = {
    //           id: aktivitas.id, // harus merupakan ID dari hilang_activity_baru
    //           status: 'Ditolak',
    //           alasan_penolakan: result.value
    //         };
    //         await axios.post('/api/v1/kehilangan/pengantian-ditolak', payload);

    //         // Update nilai di frontend setelah berhasil
    //         this.dataProses.hilang_activity_baru[index].status = 'Ditolak';
    //         this.dataProses.hilang_activity_baru[index].alasan_penolakan = result.value;

    //         Swal.fire('Berhasil!', 'Aktivitas telah ditolak.', 'success');
    //       } catch (error) {
    //         let msg = 'Terjadi kesalahan saat menyimpan data.';
    //         if (error.response && error.response.data && error.response.data.message) {
    //           msg = error.response.data.message;
    //         }
    //         Swal.fire('Gagal!', msg, 'error');
    //       }
    //     }
    //   })
    // },
    downloadPJ(item) {
      if (item.bukti_pertanggung_jawaban) {
        const link = document.createElement('a');
        link.href = `/storage/${item.bukti_pertanggung_jawaban}`;
        link.download = item.bukti_pertanggung_jawaban;
        link.click();
        link.remove();
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: 'Berita Acara tidak tersedia.',
        });
      }
    },
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