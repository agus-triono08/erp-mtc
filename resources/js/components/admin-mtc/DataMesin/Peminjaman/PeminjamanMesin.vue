<template>
    <div class="container-fluid">

      <!-- Tombol untuk membuka modal -->
      <div class="d-flex justify-content-between mb-4">
        <div></div>
        <div>
          <form class="d-flex align-items-center">
            <input
              type="text"
              name="search"
              v-model="searchQuery"
              @input="debouncedFetchAlats"
              style="background-color: #f3f4f6; width: max-content;"
              class="form-control-sm border-0 mr-2 ml-2"
              placeholder="Search by Code or Name or No Loan"
            />
          </form>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-border no-border table-custom text-wrape" style="overflow-x: auto;">
          <thead>
            <tr class="bg-table">
              <th class="text-center text-black-1 tr-center">#</th>
              <th class="text-center text-black-1">Kode Mesin</th>
              <th class="text-center text-black-1">No. Pinjam</th>           
              <th class="text-center text-black-1">Dipinjam Oleh</th>
              <th class="text-center text-black-1">Divisi</th>
              <th class="text-center text-black-1" style="cursor: pointer; position: relative; vertical-align: middle;">
                Tanggal Pinjam
                <span class="sort-icons">
                  <i @click="sortTanggalPinjam('desc')" class="fas fa-sort-up"></i>
                  <i @click="sortTanggalPinjam('asc')" class="fas fa-sort-down"></i>
                </span>
              </th>
              <th class="text-center text-black-1" style="cursor: pointer; position: relative; vertical-align: middle;">
                Tanggal Kembali
                <span class="sort-icons">
                  <i @click="sortTanggalkembali('desc')" class="fas fa-sort-up"></i>
                  <i @click="sortTanggalkembali('asc')" class="fas fa-sort-down"></i>
                </span>
              </th>            
              <th class="text-center text-black-1">Durasi</th>
              <th class="text-center text-black-1">Status</th>
              <th class="text-center text-black-1">Action</th>
            </tr>
          </thead>
          <tbody v-if="filteredData.length===0">
            <tr>
              <td colspan="10" class="text-center text-black-1">Tidak Ada Data</td>
            </tr>
          </tbody>
          <tbody v-for="(peminjaman, index) in filteredData" :key="index">
            <tr class="text-center">
              <td class="text-center">{{ index + 1 }}</td>
              <td class="text-left">{{ peminjaman.kode_alat || '-' }}</td>
              <td class="text-center">{{ peminjaman.no_pinjam || '-' }}</td>            
              <td class="text-center">{{ peminjaman.pengguna ? peminjaman.pengguna.nama_pengguna : '-' }}</td>
              <td class="text-center">{{ peminjaman.pengguna ? peminjaman.pengguna.divisi : '-' }}</td>
              <td class="text-center">{{ peminjaman.tanggal_pinjam || '-' }}</td>
              <td class="text-center">{{ peminjaman.tanggal_kembali || '-' }}</td>
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
                'status-active': peminjaman.status == 'Selesai',
                'status-error': peminjaman.status == 'Barang Siap Diambil',
                'status-rusak': peminjaman.status == 'Sedang Dipinjam',
              }"
              >
                {{ peminjaman.status || '-' }}
              </td>
              <td class="text-center">
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
                    <a class="dropdown-item" @click="viewDetail(dataPeminjamanMesin.id)">
                      <i class="fas fa-eye text-info"></i> Detail
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
  import _ from "lodash";
  
  export default {
    props: {
      kodeAlat: String
    },
    data() {
      return {
        user: {
          nama_pengguna: '',
          divisi: '',
        },
        dataPeminjamanMesin: [], // Menyimpan data error
        showModalInput: false, // Tambahkan variabel untuk mengontrol tampilan modal input
        showAlat: true,
        showMesin: false,
        searchQuery: '',
        rowsPerPage: 10,
        currentPage: 1,
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
        filteredData() {
          if (this.searchQuery) {
            return this.paginatedData.filter(peminjamanmesin => {
              return (
                peminjamanmesin.kode_mesin.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                peminjamanmesin.no_pinjam.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                (peminjamanmesin.pengguna && peminjamanmesin.pengguna.nama_pengguna.toLowerCase().includes(this.searchQuery.toLowerCase()))
              );
            });
          } else {
            return this.paginatedData;
          }
        }
      },
    methods: {
      async fetchAlatPeminjaman() {
        try {
        const response = await axios.get(`/api/mesin/peminjaman`, {
          params: {
            search: this.searchQuery
          }
        });
        this.dataPeminjamanMesin = response.data.data.map((peminjaman) => ({
          id: peminjaman.id,
          id_alat: peminjaman.id_mesin,
          id_user: peminjaman.id_pengguna,
          kode_alat: peminjaman.kode_mesin,
          no_pinjam: peminjaman.no_pinjam,
          stok_dipinjam: peminjaman.stok,
          tanggal_pinjam: peminjaman.tanggal_pinjam,
          tanggal_kembali: peminjaman.tanggal_kembali,
          keterangan: peminjaman.keterangan,
          status: peminjaman.status,
          alat: peminjaman.alat,
          pengguna: peminjaman.pengguna,
        })); // Menyimpan data alat
        //console.log(this.dataPeminjamanMesin); // Debug data
      } catch (error) {
        console.error("Error fetching alat error detail:", error);
        //alert("Gagal memuat detail data alat error.");
      }
    },
    debouncedFetchAlats: _.debounce(function () {
      this.fetchAlatPeminjaman();
    }, 300),
      tambahPeminjamanAlat() {
        this.showModalInput = true;
      },
      tutupModal() {
        this.showModalInput = false;
      },
      sortJumlah(order) {
        this.dataPeminjaman.sort((a, b) => {
          if (order === "asc") {
            return a.stok_dipinjam - b.stok_dipinjam;
          } else {
            return b.stok_dipinjam - a.stok_dipinjam;
          }
        });
      },
      sortTanggalPinjam(order) {
        this.dataPeminjaman.sort((a, b) => {
          if (order === "asc") {
            return new Date(a.tanggal_pinjam) - new Date(b.tanggal_pinjam);
          } else {
            return new Date(b.tanggal_pinjam) - new Date(a.tanggal_pinjam);
          }
        });
      },
      sortTanggalkembali(order) {
        this.dataPeminjaman.sort((a, b) => {
          if (order === "asc") {
            return new Date(a.tanggal_kembali) - new Date(b.tanggal_kembali);
          } else {
            return new Date(b.tanggal_kembali) - new Date(a.tanggal_kembali);
          }
        });
      },
      toggleAlat() {
        this.showMesin = false;
        this.showAlat = !this.showAlat;
      },
      toggleMesin() {
        this.showAlat = false;
        this.showMesin = !this.showMesin;
      },
      viewDetail(id) {
        this.$router.push(`/admin-mtc/data-alat/detail-peminjaman/${id}`);
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
    mounted() {
      this.fetchAlatPeminjaman();
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
  </style>