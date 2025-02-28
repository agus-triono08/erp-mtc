<template>
    <div class="container-fluid">
      <!-- Modal Input Data -->
      <!--<div id="app" class="modal-input" :class="{'is-visible': showModalInput}">
        <div class="modal-content-input">
          <input-alat-sudah-digunakan @tutup-modal="tutupModal"></input-alat-sudah-digunakan>
        </div>
      </div>-->

      <!-- Modal Edit Data -->
      <div id="app" class="modal-input" :class="{'is-visible' :showModalEdit}" @click.self="tutupModal">
        <div class="modal-content-input">
          <edit-alat-sudah-digunakan @tutup-modal="tutupModal" :id="idEdit"></edit-alat-sudah-digunakan>
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
              <th class="text-center text-black-1 tr-center">No. Seri Alat</th>
              <th class="text-center text-black-1 tr-center">Tgl Permintaan</th>
              <th class="text-center text-black-1 tr-center">Diminta Oleh</th>
              <th class="text-center text-black-1 tr-center">Divisi</th>
              <th class="text-center text-black-1">Kondisi</th>
              <th class="text-center text-black-1">Status</th>
              <th class="text-center text-black-1">Aksi</th>
            </tr>
          </thead>
          <tbody v-if="filteredData.length===0">
            <tr>
              <td colspan="6" class="text-center">Tidak Ada Data</td>
            </tr>
          </tbody>
          <tbody v-for="(permintaanalat, index) in filteredData" :key="index">
            <tr class="text-center">
              <td class="text-center">{{ index + 1 }}</td>
              <td class="text-center">{{ permintaanalat.no_seri_alat ? permintaanalat.no_seri_alat.no_seri_alat : '-' }}</td>              
              <td class="text-center">{{ permintaanalat.tanggal_permintaan || '-' }}</td>
              <td class="text-center">{{ permintaanalat.pemohon ? permintaanalat.pemohon.nama_pengguna : '-' }}</td>
              <td class="text-center">{{ permintaanalat.pemohon ? permintaanalat.pemohon.divisi : '-' }}</td>
              <td> 
                <div
                class="btn-sts parent-element"
                :class="{
                          'status-active': permintaanalat.no_seri_alat.status === 'OK', 
                          'status-rusak': permintaanalat.no_seri_alat.status === 'Rusak', 
                          'status-error': permintaanalat.no_seri_alat.status === 'Error'}"
              >{{ permintaanalat.no_seri_alat ? permintaanalat.no_seri_alat.status : '-' }}</div></td>
              <td>
                <div 
                class="btn-sts"
                :class="{
                  'status-active': permintaanalat.status === 'Diterima', 
                  'status-error': permintaanalat.status === 'Proses', 
                  'status-rusak': permintaanalat.status === 'Ditolak'
                }">
              {{ permintaanalat.status }}</div></td>
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
                    <a class="dropdown-item" @click="editData(permintaanalat.id)">
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
      kodeAlat: String
    },
    data() {
      return {
        user: {
          nama_pengguna:'',
          divisi: ''
        },
        noseri: {
          no_seri_alat: '',
          status: ''
        },
        dataPermintaanAlat: [], // Menyimpan data error
        showModalInput: false, // Tambahkan variabel untuk mengontrol tampilan modal input
        idEdit: null,
        showModalEdit: false,
        searchQuery: '',
        rowsPerPage: 10,
        currentPage: 1,
      };
    },
    computed: {
      totalPages() {
        return Math.ceil(this.dataPermintaanAlat.length / this.rowsPerPage);
      },
      paginationInfo() {
        const start = (this.currentPage - 1) * this.rowsPerPage + 1;
        const end = Math.min(this.currentPage * this.rowsPerPage, this.dataPermintaanAlat.length);
        return `Showing ${start} to ${end} of ${this.dataPermintaanAlat.length} entries`;
      },
      paginatedData() {
        const start = (this.currentPage - 1) * this.rowsPerPage;
        const end = start + this.rowsPerPage;
        return this.dataPermintaanAlat.slice(start, end);
      },
      filteredData() {
        return this.dataPermintaanAlat.filter(permintaanalat => {
          const searchQueryLower = this.searchQuery.toLowerCase();
          const noSeriAlat = permintaanalat.no_seri_alat && permintaanalat.no_seri_alat.no_seri_alat;
          const namaPengguna = permintaanalat.pemohon && permintaanalat.pemohon.nama_pengguna;
          const divisi = permintaanalat.pemohon && permintaanalat.pemohon.divisi;

          return (
            (noSeriAlat && noSeriAlat.toLowerCase().includes(searchQueryLower)) ||
            (namaPengguna && namaPengguna.toLowerCase().includes(searchQueryLower)) ||
            (divisi && divisi.toLowerCase().includes(searchQueryLower))
          );
        });
      },
    },
    methods: {
      async fetchAlatError() {
        try {
          const kodeAlat = this.kodeAlat; // Kode alat di URL
          //console.log(this.kodeAlat);
          const response = await axios.get(`/api/alats/permintaan/${kodeAlat}`);
          this.dataPermintaanAlat = response.data; // Menyimpan data alat
          //console.log(this.dataPermintaanAlat); // Debug data
        } catch (error) {
          console.error("Error fetching alat error detail:", error);
          //alert("Gagal memuat detail data alat error.");
        }
      },
      debouncedFetchAlats: _.debounce(function () {
        this.fetchAlatError();
      }, 300),
      tambahData() {
        this.showModalInput = true;
      },
      editData(id) {
        this.idEdit = id;
        this.showModalEdit = true;
      },
      tutupModal() {
        this.showModalInput = false;
        this.showModalEdit = false;
      },
      sortStokError(order) {
        this.errors.sort((a, b) => {
          if (order === 'asc') {
            return a.stok_error - b.stok_error;
          } else {
            return b.stok_error - a.stok_error;
          }
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
      }
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
      width: 55%;
    }

    .btn-sts {
      border: 1px solid transparent;
      transition: background-color 0.3s ease, color 0.3s ease, border 0.3s ease;
      height: 25px;
      width: auto;
      border-radius: 10px;
      text-align: center;
      justify-content: center;
      align-items: center;
    }

    .status-diterima {
      background-color: #34C759;
      color: #34c759;
      padding: 5px 10px;
      border-radius: 5px;
    }

    .status-proses {
      background-color: #808080;
      color: #fff;
      padding: 5px 10px;
      border-radius: 5px;
    }

    .status-ditolak {
      background-color: #FF0000;
      color: #fff;
      padding: 5px 10px;
      border-radius: 5px;
    }
  </style>