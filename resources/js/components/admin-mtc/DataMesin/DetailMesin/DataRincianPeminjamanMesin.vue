<template>
    <div class="container-fluid" style="margin-top: 30px;">
      <!-- Modal Input Data -->
      <div id="app" class="modal-input" :class="{'is-visible': showModalInput}" @click.self="tutupModal">
        <div class="modal-content-input">
          <input-alat-error @tutup-modal="tutupModal"></input-alat-error>
        </div>
      </div>

      <!-- Modal Edit Data -->
      <div id="app" class="modal-input" :class="{'is-visible' :showModalEdit}">
        <div class="modal-content-input">
          <edit-alat-dipinjam @tutup-modal="tutupModal" :id="idEdit"></edit-alat-dipinjam>
        </div>      
      </div>
  
      <!-- Tombol untuk membuka modal -->
      <!--<div class="d-flex justify-content-between mb-4">
        <div></div>
        <div>
          <form class="d-flex align-items-center">
            <a @click="tambahDataError" class="btn btn-icon-split btn-plus">
              <span class="icon text-white-50">
                <i class="fas fa-plus-circle"></i>
              </span>
              <span class="text">Alat Error</span>
            </a>
          </form>
        </div>
      </div>-->
      <diV class="row align-items-center justify-content-end mr-3 mt-3 mb-4">        
        <!-- Search -->
        <div class="search-wrapper">
          <div class="input-group">
            <input type="text" placeholder="search..." class="form-control"
              v-model="searchQuery"
              @input="debouncedFetchAlats"/>
            </div>
          </div>
      </diV>
      <div class="table-responsive text-wrape">
        <table class="table table-border no-border table-custom text-center" style="overflow-x: auto;">
          <thead>
            <tr class="bg-table">
              <th class="text-center text-black-1 tr-center">#</th>
              <th class="text-center text-black-1 tr-center">No. Seri Mesin</th>
              <th class="text-center text-black-1 tr-center">No. Pinjam</th>
              <th class="text-center text-black-1 tr-center">Tgl Pinjam</th>
              <th class="text-center text-black-1 tr-center">Dipinjam Oleh</th>
              <th class="text-center text-black-1 tr-center">Divisi</th>
              <th class="text-center text-black-1 tr-center">Tgl Pengembalian</th>
              <th class="text-center text-black-1 tr-center">Durasi Peminjaman</th>
              <th class="text-center text-black-1">Status</th>
              <th class="text-center text-black-1">Aksi</th>
            </tr>
          </thead>
          <tbody v-if="filtereedData.length===0">
            <tr>
              <td colspan="10" class="text-center">Tidak Ada Data</td>
            </tr>
          </tbody>
          <tbody v-for="(peminjamanmesin, index) in filtereedData" :key="index">
            <tr class="text-center">
              <td class="text-center">{{ index + 1 }}</td>
              <td class="text-center">{{ peminjamanmesin.no_seri_alat ? peminjamanmesin.no_seri_alat.no_seri_alat : '-' }}</td>
              <td class="text-center">{{ peminjamanmesin.no_pinjam || '-' }}</td>
              <td class="text-center">{{ peminjamanmesin.tanggal_pinjam || '-' }}</td>
              <td class="text-center">{{ peminjamanmesin.pengguna ? peminjamanmesin.pengguna.nama_pengguna : '-' }}</td>
              <td class="text-center">{{ peminjamanmesin.pengguna ? peminjamanmesin.pengguna.divisi : '-' }}</td>
              <td class="text-center">{{ peminjamanmesin.tanggal_kembali || '-' }}</td>
              <td class="text-center">
                {{ durasiData[index] !== '-' ? durasiData[index] + ' Hari' : '-' }} <br>
                <small>
                  <i :class="{'fas fa-clock': !durasiDataKembali[index].includes('Hari Lebih'), 'fas fa-exclamation-circle text-danger': durasiDataKembali[index].includes('Hari Lebih')}"></i>
                  <span :class="{'text-danger': durasiDataKembali[index].includes('Hari Lebih')}">
                    {{ durasiDataKembali[index] }}
                  </span>
                </small>
              </td>
              <td 
                class="text-center status-pill parent-element"
                style="margin-top: 20px;"
                :class="{
                          'status-active': peminjamanmesin.status === 'Selesai', 
                          'status-rusak': peminjamanmesin.status === 'Sedang Dipinjam', 
                          'status-error': peminjamanmesin.status === 'Barang Siap Diambil'}"
              >{{ peminjamanmesin.status || '-' }}</td>
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
                    <a class="dropdown-item" @click="editData(peminjamanmesin.id)">
                      <i class="fas fa-edit text-primary"></i> Edit
                    </a>
                  </div>
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
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    props: {
      kodeMesin: String
    },
    data() {
      return {
        user: {
          nama_pengguna:'',
          divisi: ''
        },
        noseri: {
          no_seri_alat: '',
        },
        dataPeminjamanMesin: [
          {
            no_seri_mesin: '45678JKL',
            no_pinjam: 'PNJ004',            
            pengguna: {
              nama_pengguna: 'David Lee',
              divisi: 'HR'
            },
            tanggal_pinjam: '2025-02-20',
            tanggal_kembali: '2025-01-30',
            status: 'Sedang Dipinjam'
          },
        ], // Menyimpan data error
        showModalInput: false, // Tambahkan variabel untuk mengontrol tampilan modal input
        searchQuery: '',
        rowsPerPage: 10,
        currentPage: 1,
        showModalEdit: '',
        idEdit: null,
      };
    },
    computed: {
      durasiData() {
        return this.dataPeminjamanMesin.map(peminjamanmesin => {
          if (peminjamanmesin.tanggal_kembali) {
            const tanggalPinjam = new Date(peminjamanmesin.tanggal_pinjam);
            const tanggalKembali = new Date(peminjamanmesin.tanggal_kembali);
            const selisihHari = Math.abs(tanggalKembali - tanggalPinjam);
            const hari = Math.ceil(selisihHari / (1000 * 60 * 60 * 24));
            return hari;
          }
        });
      },
      durasiDataKembali() {
        return this.dataPeminjamanMesin.map(peminjamanmesin => {
          if (peminjamanmesin.tanggal_kembali) {
            const tanggalTerkini = new Date();
            const tanggalKembali = new Date(peminjamanmesin.tanggal_kembali);
            const selisihHari = Math.abs(tanggalKembali - tanggalTerkini);
            const hari = Math.ceil(selisihHari / (1000 * 60 * 60 * 24));

            // Jika tanggal terkininya kurang dari tanggal kembali
            if (tanggalTerkini < tanggalKembali) {
              return hari + ' Hari Lagi';
            } else {
              // Jika tanggal terkininya lebih dari tanggal kembali
              const excessDays = Math.ceil((tanggalTerkini - tanggalKembali) / (1000 * 60 * 60 * 24));
              return excessDays + ' Hari Lebih';
            }
          }
        });
      },
      totalPages() {
        return Math.ceil(this.dataPeminjamanMesin.length / this.rowsPerPage);
      },
      paginationInfo() {
        const start = (this.currentPage - 1) * this.rowsPerPage + 1;
        const end = Math.min(this.currentPage * this.rowsPerPage, this.dataPeminjamanMesin.length);
        return `Showing ${start} to ${end} of ${this.dataPeminjamanMesin.length} entries`;
      },
      paginatedData() {
        const start = (this.currentPage - 1) * this.rowsPerPage;
        const end = start + this.rowsPerPage;
        return this.dataPeminjamanMesin.slice(start, end);
      },
      filtereedData() {
        return this.dataPeminjamanMesin.filter(peminjamanmesin => {
          const searchQueryLower = this.searchQuery.toLowerCase();
          const noSeriMesin = peminjamanmesin.no_seri_mesin && peminjamanmesin.no_seri_mesin;
          const namaPeminjam = peminjamanmesin.pengguna && peminjamanmesin.pengguna.nama_pengguna;
          const divisi = peminjamanmesin.pengguna && peminjamanmesin.pengguna.divisi;
          const noPinjam = peminjamanmesin.no_pinjam;

          return (
            (noSeriMesin && noSeriMesin.toLowerCase().includes(searchQueryLower)) ||
            (namaPeminjam && namaPeminjam.toLowerCase().includes(searchQueryLower)) ||
            (divisi && divisi.toLowerCase().includes(searchQueryLower)) ||
            (noPinjam && noPinjam.toLowerCase().includes(searchQueryLower))
          );
        });
      },
    },
    methods: {
      async fetchAlatError() {
        try {
          const kodeMesin = this.kodeMesin; // Kode alat di URL
          //console.log(this.kodeMesin);
          const response = await axios.get(`/api/mesins/no-seri/peminjaman/${kodeMesin}`);
          this.dataPeminjamanMesin = response.data; // Menyimpan data alat
          //console.log(this.dataPeminjamanMesin); // Debug data
        } catch (error) {
          console.error("Error fetching alat error detail:", error);
          //alert("Gagal memuat detail data alat error.");
        }
      },
      debouncedFetchAlats: _.debounce(function () {
        this.fetchAlatError();
      }, 300),
      tambahDataError() {
        this.showModalInput = true;
      },
      tutupModal() {
        this.showModalInput = false;
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
      editData(id) {
        this.idEdit = id;
        this.showModalEdit = true;
      },
      tutupModal() {
        this.showModalInput = false;
        this.showModalEdit = false;
      },
    },    
    watch: {
      rowsPerPage() {
        this.currentPage = 1; // Reset ke halaman pertama saat rowsPerPage berubah
      }
    },
    mounted() {
      this.fetchAlatError();
    }
  }
  </script>
  
  <style>
    .no-border {
      border: none;
    }
  
    .no-border th,
    .no-border td {
      border-top: none !important;
      border-bottom: none !important;
    }
  
    .compact-table th,
    .compact-table td {
      padding: 0.1rem 0.3rem;
    }
  
    .compact-table tbody tr {
      margin-bottom: 0;
    }
  
    .compact-table th {
      padding-left: 0.2rem;
      padding-right: 0.2rem;
    }
  
    .compact-table td {
      padding-left: 0.2rem;
      padding-right: 0.2rem;
    }
  
    .text-wrap {
      white-space: normal; /* Atau gunakan pre-wrap jika ingin mempertahankan spasi */
      word-wrap: break-word; /* Memungkinkan kata untuk terputus jika terlalu panjang */
      overflow-wrap: break-word; /* Memastikan kata panjang terputus */
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
      max-width: max-content;
      width: 100%;
    }
  </style>