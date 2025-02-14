<template>
    <div class="container-fluid" style="width: 800px;">
      <!-- Header -->
      <div class="card-header">
        <h6 class="font-weight-bold" style="color: #169ea8; border-radius: 15px;">
          Form Edit Data Alat Error
        </h6>
      </div>
  
      <!-- Form -->
      <div class="card-body" style="border-radius: 15px;">
        <form @submit.prevent="submitAlat">
          
          <!-- No Seri Alat -->
          <div class="form-group">
            <label for="no_seri_alat">
              <b>No Seri Alat</b><sup style="color: red;"> *</sup>
            </label>
            <select id="no_seri_alat" class="form-control" v-model="selectedNoSeri" required disabled>
              <option value="">Pilih No Seri Alat</option>
              <option v-for="no_seri in noseris" :key="no_seri.id" :value="no_seri.no_seri_alat">
                {{ no_seri.no_seri_alat }}
              </option>
            </select>
          </div>
  
          <!-- Informasi Peminjaman -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="pic" style="color: #000;">
                  <b>PIC</b>
                  <sup style="color: red;"> *</sup>
                </label>
                <select id="pic" class="form-control" v-model="selectedPIC" required>
                  <option value="" disabled selected>Pilih PIC</option>
                  <option v-for="pic in PIC" :key="pic.id" :value="pic.nama_staff">
                    {{ pic.nama_staff }}
                  </option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="tanggal_error">
                  <b>Tanggal Error</b><sup style="color: red;"> *</sup>
                </label>
                <input type="date" id="tanggal_error" class="form-control" v-model="noseri.tanggal_error" disabled required />
              </div>
            </div>
          </div>
  
          <!-- Data Peminjam -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="tanggal_perbaikan">
                  <b>Tanggal Perbaikan</b><sup style="color: red;"> *</sup>
                </label>
                <input type="date" id="tanggal_perbaikan" class="form-control" v-model="noseri.tanggal_perbaikan" disabled required />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="layout">
                  <b>Layout</b><sup style="color: red;"> *</sup>
                </label>
                <select id="pic" class="form-control" required>
                  <option value="" disabled selected>Pilih Layout</option>
                  <option value="Test">Test</option>                  
                </select>
              </div>
            </div>            
          </div>
  
          <!-- Tanggal Pengembalian & Kondisi -->
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="kondisi">
                  <b>Kondisi</b><sup style="color: red;"> *</sup>
                </label>
                <select id="pic" class="form-control" v-model="statusNoseri" required>
                  <option value="" disabled selected>Pilih Layout</option>
                  <option value="Ready">Ready</option>
                  <option value="Error">Error</option>
                  <option value="Rusak">Rusak</option>
                </select>
              </div>
            </div>
          </div>
          
          <!-- Detail Error -->
          <div class="form-group">
            <label for="detail_error" style="color: #000;">
              <b>Detail Error</b>
              <sup style="color: red;"> *</sup>
            </label>
            <div class="textarea-wrapper">
              <textarea 
                id="detail_error" 
                class="form-control" 
                v-model="noseri.deskripsi_error"
                rows="1"
                placeholder="Masukkan Detail Kerusakan (Maksimal 100 karakter)"
                maxlength="100"
                required
                >
              </textarea>
              <small class="text-muted char-counter">
                {{ noseri.deskripsi_error ? noseri.deskripsi_error.length : 0 }} / 100
              </small>
            </div>
          </div>
  
          <!-- Tombol Aksi -->
          <div class="form-group d-flex justify-content-between">
            <span></span>
            <div>
              <button type="submit" class="btn btn-plus mr-2">
                <i class="fas fa-save"></i> Simpan
              </button>
              <button @click="tutupModal" type="button" class="btn btn-danger">
                <i class="fas fa-times"></i> Batal
              </button>
            </div>
          </div>
  
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        noseris: [],
        PIC: [],
        noseri: {},
        penggunaNama: '',
        divisiNama: '',
        statusNoseri: '',
        selectedNoSeri: '',
        selectedPIC: '',
      };
    },
    methods: {
      tutupModal() {
        this.$emit('tutup-modal');
      },
  
      // Fetch Data Alat Sudah Dipinjam
      async fetchDataAlatBelumDigunakan() {
        const id = this.$route.params.id;
        try {
          const response = await axios.get(`/api/alats/error/${id}/edit`);
          this.noseri = response.data.data;
          console.log(this.noseri);
        } catch (error) {
          console.error("Gagal mengambil data alat:", error);
        }
      },
  
      // Fetch Data Nomor Seri
      async fetchDataNoSeri() {
        try {
          const response = await axios.get('/api/no-seri');
          this.noseris = response.data.data;
          //console.log(this.noseris);
        } catch (error) {
          console.error("Gagal mengambil data nomor seri:", error);
        }
      },

      // Fetch Data PIC
      async fetchDataPIC() {
        try {
          const response = await axios.get('/api/pic/mtc');
          this.PIC = response.data.data;
          //console.log(this.PIC);
        } catch (error) {
          console.error("Gagal mengambil data nomor seri:", error);
        }
      },
  
      async submitAlat() {
        if (this.noseri.deskripsi_error.length > 100) {
          this.$swal('Error', 'Deskripsi tidak boleh lebih dari 100 karakter');
          return;
        }
        try {
          const response = await axios.put(`/api/alats/${this.noseri.id}`, { status: this.noseri.status });
          alert("Data berhasil diperbarui!");
          this.tutupModal();
        } catch (error) {
          console.error("Gagal memperbarui data:", error);
          alert("Terjadi kesalahan saat memperbarui data.");
        }
      },
    },
    computed: {
      finalKategori() {
        return this.selectedKategori || this.manualKategori;
      },
    },
    mounted() {
      this.fetchDataAlatBelumDigunakan();
      this.fetchDataNoSeri();
      this.fetchDataPIC();
    },
    watch: {
      noseri: {
        handler(val) {
          this.penggunaNama = val.pengguna ? val.pengguna.nama_pengguna : '';
          this.selectedNoSeri = val.no_seri_alat ? val.no_seri_alat.no_seri_alat : '';
          this.divisiNama = val.pengguna ? val.pengguna.divisi : '';
          this.statusNoseri = val.no_seri_alat ? val.no_seri_alat.status : '';
          this.selectedPIC = val.pic ? val.pic.nama_staff : '';
        },
        deep: true,
      },
    },
  };
  </script>
  
      
      <style>
      .icon-hover {
        color: #5a5c69;
        transition: color 0.3s ease;
      }
      
      .icon-hover:hover {
        color: #169ea8;
      }
      .btn-plus {
        background-color: #169EA8;
        color: #fff;
      }
      .btn-plus:hover {
          background-color: #22d3e0;
          color: #fff;
      }
      
      .upload-box {
        border: 2px dashed #169ea8;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        position: relative;
        transition: border-color 0.3s ease;
        max-width: 200px;
        max-height: auto;
      }
      
      .upload-box .fa-image {
        font-size: 36px; /* Ukuran ikon diperbesar */
        margin-bottom: 10px;
        color: #666;
      }
      
      .upload-box.drag-active {
        border-color: #22d3e0;
      }
      
      .upload-box .upload-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
      }
      
      .browse-link {
        color: #169ea8;
        text-decoration: underline;
        cursor: pointer;
      }
      
      .img-preview {
        max-width: 150px;
        max-height: 150px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.8);
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
      
      #confirmButton {
        background-color: #169ea8;
        color: #fff;
      }
      
      #cancelButton {
        background-color: #f44336;
        color: #fff;
      }
      
      .textarea-wrapper {
        position: relative;
      }
      
      textarea {
        padding-bottom: 20px; /* Beri ruang untuk teks di bagian bawah */
      }
      
      .char-counter {
        position: absolute;
        bottom: 5px;
        right: 10px;
        font-size: 12px;
        color: #6c757d; /* Warna teks abu-abu */
        pointer-events: none; /* Supaya tidak mengganggu input */
      }
      
      </style>