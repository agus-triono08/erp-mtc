<template>
    <div class="container-fluid">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold" style="color: #169ea8; border-radius: 15px;">Form Input Data Alat Rusak</h6>
      </div>
      <div class="card-body" style="border-radius: 15px;">
        <form @submit.prevent="submitAlat">
  
          <!-- Stok -->
          <div class="row">
            <div class="form-group col-md-6">
              <label for="stok" style="color: #000;">
                <b>Stok Hilang</b>
                <sup style="color: red;"> *</sup>
              </label>
              <input
                type="number"
                id="stok"
                v-model="error.stok_error"
                class="form-control"
                placeholder="Masukkan Jumlah Stok"
                required
              />
            </div>            
  
            <!-- Tanggal Kehilangan -->
            <div class="form-group col-md-6">
              <label for="tanggal_error" style="color: #000;">
                <b>Tanggal Kehilangan</b>
                <sup style="color: red;"> *</sup>
              </label>
              <input
                type="date"
                id="tanggal_error"
                v-model="error.tanggal_error"
                class="form-control"
              />            
            </div>
          </div>

          <div class="row">
            <!-- Nama yang Menghilangkan -->
            <div class="form-group col-md-6">
              <label for="nama_pengguna" style="color: #000;">
                <b>Nama yang Menghilangkan</b>
                <sup style="color: red;"> *</sup>
              </label>
              <input
              type="text"
              id="nama_pengguna"
              v-model="error.nama_pengguna"
              class="form-control"
              placeholder="Masukkan Nama yang Menghilangkan"
              required
              />
            </div>

            <!-- Divisi yang Menghilangkan -->
            <div class="form-group col-md-6">
              <label for="divisi_pengguna" style="color: #000;">
                <b>Divisi yang Menghilangkan</b>
                <sup style="color: red;"> *</sup>
              </label>
              <input
              type="text"
              id="divisi_pengguna"
              v-model="error.divisi"
              class="form-control"
              placeholder="Masukkan Divisi yang Menghilangkan"
              required
              />
            </div>
          </div>
  
          <!-- Detail Kehilangan -->
          <div class="form-group">
            <label for="detail-error" style="color: #000;">
              <b>Detail Kehilangan</b>
            </label>
            <div class="textarea-wrapper">
              <textarea
                id="detail-error"
                v-model="error.detail_error"
                class="form-control"
                rows="3"
                placeholder="Masukkan Deskripsi (Maksimal 500 karakter)"
                maxlength="500"
              ></textarea>
              <small class="text-muted char-counter">
                {{ alat.deskripsi.length }} / 500
              </small>
            </div>
          </div>
          
          <!-- Tanggal Pergantian Alat yang Hilang -->
          <div class="form-group">
            <label for="tanggal_pergantian" style="color: #000;">
              <b>Tanggal Pergantian Alat yang Hilang</b>
              <sup style="color: red;"> *</sup>
            </label>
            <input
            type="date"
            id="tanggal_pergantian"
            v-model="error.tanggal_pergantian"
            class="form-control"
            required
            />
          </div>

          <!-- Detail Pergantian Alat -->
          <div class="form-group">
            <label for="detail-error" style="color: #000;">
              <b>Detail Pergantian Alat</b>
            </label>
            <div class="textarea-wrapper">
              <textarea
                id="detail-error"
                v-model="error.detail_error"
                class="form-control"
                rows="3"
                placeholder="Masukkan Deskripsi (Maksimal 500 karakter)"
                maxlength="500"
              ></textarea>
              <small class="text-muted char-counter">
                {{ alat.deskripsi.length }} / 500
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
        error: {
          stok_error: "",
        },
        alat: {
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
        },
        gambar: null,
        gambarPreview: null,
        dragActive: false,
        showModal: false,
        selectedKodeAlat: '',
        kode_alats: [],
        kategoris: ['CLAMP', 'POWER SUPPLY', 'GLUE GUN', 'TANG', 'WATERPASS', 'SOLDER'],
        formattedHarga: '',
        selectedLocation: '',
        manualLocationInput: '',
        showManualInputLocation: false,
        locations: ['Gedung A', 'Gedung B', 'Gedung C', 'Gedung D'],
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