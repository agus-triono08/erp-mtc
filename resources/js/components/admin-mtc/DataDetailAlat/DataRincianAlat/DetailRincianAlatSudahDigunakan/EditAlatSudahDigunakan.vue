<template>
    <div class="container-fluid">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold" style="color: #169ea8; border-radius: 15px;">Form Edit Data Alat Sudah Digunakan</h6>
      </div>

      <div class="card-body" style="border-radius: 15px;">
        <form @submit.prevent="submitAlat">
  
          <!-- No Seri Alat -->
            <div class="form-group">
              <label for="no_seri_alat" style="color: #000;">
                <b>No Seri Alat</b>
                <sup style="color: red;"> *</sup>              
              </label>
              <select id="no_seri_alat" class="form-control" v-model="NoSeris" required>
                <option value="">Pilih No Seri Alat</option>
                <option v-for="no_seri in noseris" :key="no_seri.id" :value="no_seri.no_seri_alat">{{ no_seri.no_seri_alat }}</option>
              </select>
            </div>

            <!-- Tanggal Masuk -->
            <div class="form-group">
              <label for="tanggal_permintaan" style="color: #000;">
                <b>Tanggal Permintaan</b>
                <sup style="color: red;"> *</sup>
              </label>
              <input
                type="date"
                id="tanggal_permintaan"
                v-model="noseri.tanggal_permintaan"
                class="form-control"
                required
                disabled
              />            
            </div>

            <!-- Diminta Oleh-->
            <div class="form-group">
              <label for="pengguna" style="color: #000;">
                <b>Diminta Oleh</b>
                <sup style="color: red;"> *</sup>
              </label>
              <input
                type="text"
                id="pengguna"
                v-model="penggunaNama"
                class="form-control"
                required
                disabled        
              />
            </div>

            <!-- Divisi -->
            <div class="form-group">
              <label for="divisi" style="color: #000;">
                <b>Divisi</b>
                <sup style="color: red;"> *</sup>
              </label>
              <input
                type="text"
                id="divisi"
                v-model="divisiNama"
                class="form-control"
                required
                disabled
              />
            </div>
  
            <!-- Kondisi -->
            <div class="form-group">
              <label for="kondisi" style="color: #000;">
                <b>Kondisi</b>
                <sup style="color: red;"> *</sup>
              </label>
              <input
                type="text"
                id="kondisi"
                class="form-control"
                v-model="statusNoseri"
                required
                disabled
              />
            </div>

            <!-- Status -->
            <div class="form-group">
              <label for="kondisi" style="color: #000;">
                <b>Status</b>
                <sup style="color: red;"> *</sup>
              </label>
              <select class="form-control" id="status" v-model="noseri.status" required>
                <option disabled value="">Pilih Status</option>
                <option value="Diterima">Diterima</option>
                <option value="Proses">Proses</option>                
                <option value="Ditolak">Ditolak</option>
              </select>
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
        noseri: {},    
        penggunaNama: '',
        divisiNama: '',
        statusNoseri: '',
        NoSeris: '',
        showModal: false,
        selectedKodeAlat: '',        
        formattedHarga: '',
        showManualInputLocation: false,        
      };
    },
    methods: {
      tutupModal() {
        this.$emit('tutup-modal'); // Mengirim event ke komponen induk
      },
      onFileChange(event) {
        const file = event.target.files[0];
        this.setImagePreview(file);
      },
      handleDrop(event) {
        const file = event.dataTransfer.files[0];
        this.setImagePreview(file);
      },
      setImagePreview(file) {
        this.gambar = file;
        const reader = new FileReader();
        reader.onload = (e) => {
          this.gambarPreview = e.target.result;
        };
        reader.readAsDataURL(file);
      },
      async fetchDataAlatBelumDigunakan() {
        const id = this.$route.params.id;
        try {
          const response = await axios.get(`/api/alats/permintaan/sudah-digunakan/${id}/edit`);
          this.noseri = response.data.data;
          console.log(this.noseri);
        } catch (error) {}
      },
      async fetchDataNoSeri() {
            try {
                const response = await axios.get('/api/no-seri');
                this.noseris = response.data.data;
                console.log(this.noseris)
            } catch (error) {
                console.error(error);
            }
        },
      async submitAlat() {
        const formData = new FormData();
        for (const key in this.alat) {
          formData.append(key, this.alat[key]);
        }
        if (this.gambar) {
          formData.append("gambar", this.gambar);
        }
        if (this.alat.deskripsi.length > 500) {
          alert("Deskripsi tidak boleh lebih dari 500 karakter.");
          return;
        }
  
        try {
          await axios.post("/api/alats", formData, {
            headers: { "Content-Type": "multipart/form-data" },
          });
          alert("Data berhasil disimpan!");
          this.tutupModal(); // Tutup modal setelah data berhasil disimpan
        } catch (error) {
          console.error("Error response:", error.response);
          alert("Terjadi kesalahan saat menyimpan data: " + error.response.data.message);
        }
      },
      resetForm() {
        this.alat = {
          nama_alat: "",
          merek_alat: "",
          tanggal_masuk: "",
          lokasi_penyimpanan: "",
          kondisi: "",
          status: "",
          stok: "",
          deskripsi: "",
          harga_pembelian: 0,
          asal_usul: "",
        };
        this.gambar = null;
        this.gambarPreview = null;
      },
      onKategoriChange(event) {
        if (event.target.value === 'other') {
          this.showManualInput = true;
          this.selectedKategori = '';
        } else {
          this.showManualInput = false;
          this.manualKategori = '';
        }
      },
      formatHarga(event) {
        let value = event.target.value.replace(/\D/g, '');
        this.alat.harga_pembelian = value ? parseInt(value, 10) : 0;
        this.formattedHarga = this.formatRupiah(value);
      },
      formatRupiah(angka) {
        if (!angka) return '';
        let number_string = angka.toString();
        let sisa = number_string.length % 3;
        let rupiah = number_string.substr(0, sisa);
        let ribuan = number_string.substr(sisa).match(/\d{3}/g);
  
        if (ribuan) {
          let separator = sisa ? '.' : '';
          rupiah += separator + ribuan.join('.');
        }
  
        return `Rp. ${rupiah}`;
      },
      onLocationChange(event) {
        if (event.target.value === 'other') {
          this.showManualInputLocation = true;
          this.selectedLocation = '';
        } else {
          this.showManualInputLocation = false;
          this.manualLocation = '';
        }
      },
    },
    computed: {
      finalKategori() {
        return this.showManualInput ? this.manualKategori : this.selectedKategori;
      },
      finalLocation() {
        return this.showManualInputLocation ? this.manualLocation : this.selectedLocation;
      }
    },
    mounted() {
      this.fetchDataAlatBelumDigunakan();
      this.fetchDataNoSeri();
    },
    watch: {
    noseri: {
      handler(val) {
        this.penggunaNama = val.pengguna ? val.pengguna.nama_pengguna : '';
        this.NoSeris = val.no_seri_alat ? val.no_seri_alat.no_seri_alat : '';
        this.divisiNama = val.pengguna ? val.pengguna.divisi : '';
        this.statusNoseri = val.no_seri_alat ? val.no_seri_alat.status : '';         
      },
      deep: true
    }
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