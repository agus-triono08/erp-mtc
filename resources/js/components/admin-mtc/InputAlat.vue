<template>
  <div class="container-fluid mt-3">
    <!-- Page Heading -->
    <h1 class="h6 mb-2 text-gray-800">
        <i 
          class="fas fa-angle-left icon-hover" 
          @click="kembali"
          style="cursor: pointer;"
        > Back to Master Data</i>        
      </h1>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold" style="color: #169ea8; border-radius: 15px;">Form Input Data Mesin</h6>
    </div>
    <div class="card-body" style="border-radius: 15px;">
      <form @submit.prevent="submitAlat">

        <!-- Kategori Alat -->
        <div class="row">
            <div class="form-group col-md-12">
              <label for="kategori" style="color: #000;">
                <b>Kategori</b>
                <sup style="color: red;"> *</sup>
              </label>
              <div>
                <select
                  id="kategori"
                  v-model="selectedKategori"
                  class="form-control"
                  @change="onKategoriChange"
                >
                  <option value="" disabled selected>Pilih Kategori</option>
                  <option value="other">Masukkan Kategori Lainnya</option>
                  <option v-for="kategori in kategoris" :key="kategori" :value="kategori">
                    {{ kategori }}
                  </option>                  
                </select>
              </div>
              <div v-if="showManualInput" class="mt-2">
                <input
                  type="text"
                  v-model="manualKategori"
                  class="form-control"
                  placeholder="Masukkan kategori baru"
                />
              </div>
            </div>
          </div>

          <!-- Nama Alat -->
          <div class="row">
            <div class="form-group col-md-6">
              <label for="nama_alat" style="color: #000;">
                <b>Nama Alat</b>
                <sup style="color: red;"><b> *</b></sup>
              </label>
              <input
                type="text"
                id="nama_alat"
                v-model="alat.nama_alat"
                class="form-control"
                placeholder="Masukkan Nama Alat"
                required
              />
            </div>

            <!-- Merek Alat -->
            <div class="form-group col-md-6">
              <label for="merek_alat" style="color: #000;">
                <b>Merek Alat</b>
                <sup style="color: red;"><b> *</b></sup>
              </label>
              <input
                type="text"
                id="merek_alat"
                v-model="alat.merek_alat"
                class="form-control"
                placeholder="Masukkan Merek Alat"
                required
              />
            </div>            
          </div>

          <div class="row">        
            <!--Tipe/Ukuran Alat-->
            <div class="form-group col-md-6">
              <label for="tipe_alat" style="color: #000;">
                <b>Tipe/Ukuran Alat</b>
                <sup style="color: red;"><b> *</b></sup>
              </label>
              <input 
                type="text"
                id="tipe_alat"
                class="form-control"
                placeholder="Masukkan Tipe/Ukuran Alat"
                required
              />
            </div>

            <!-- Produk -->
            <div class="form-group col-md-6">
              <label for="pembelian" style="color: #000;">
                <b>Produk</b>
              </label>
              <select
              id="pembelian"
              class="form-control"
              >
                <option value="" disabled selected>Pilih Produk Alat</option>
                <option value="Local">Local</option>
                <option value="Import">Import</option>
              </select>
            </div>

            <!-- Serial Number Alat Bawaan -->
            <!--<div class="form-group col-md-6">
              <label for="serial_number" style="color: #000;">
                <b>Serial Number Alat Bawaan</b>
              </label>
              <input 
                type="text"
                id="serial_number"
                class="form-control"
                placeholder="Masukkan Serial Number Alat Bawaan"
                required
              />            
              <span style="font-size: small;">Isi '-' jika tidak memiliki serial number</span>   
            </div>-->         
          </div>

          <!--<div class="row">-->
            <!-- Harga Pembelian -->
            <!--<div class="form-group col-md-6">
              <label for="harga_pembelian" style="color: #000;">
                <b>Harga Pembelian Alat</b>
              </label>
              <input
                type="text"
                id="harga_pembelian"
                v-model="formattedHarga"
                class="form-control"
                placeholder="Rp. -"
                @input="formatHarga"
                required
              />
              <input type="hidden" v-model="alat.harga_pembelian" />
              <span style="font-size: small;">Isi '0' jika tidak memiliki harga pembelian</span>
            </div>-->            

            <!-- Tahun Masuk -->
            <!--<div class="form-group col-md-6">
              <label for="tahun_masuk" style="color: #000;"><b>Tahun Masuk Alat</b></label>
              <input
                type="number"
                id="tahun_masuk"
                v-model="alat.tahun_masuk"
                class="form-control"
                placeholder="Masukkan Tahun Masuk Alat"
                required
              />
            </div>
          </div>-->

          <div class="row">
            <!-- Stok -->
            <div class="form-group col-md-4">
              <label for="stok" style="color: #000;">
                <b>Stok</b>
                <sup style="color: red;"> *</sup>
              </label>
              <input
                type="number"
                id="stok"
                v-model="alat.stok"
                class="form-control"
                placeholder="Masukkan Jumlah Stok"
                required
              />
            </div>         

            <!-- Satuan Alat -->
            <div class="form-group col-md-4">
              <label for="satuan_alat" style="color: #000;"><b>Satuan Alat</b></label>
              <select 
              id="satuan_alat"
              class="form-control"
              >
                <option value="" disabled selected>Pilih Satuan Alat</option>
                <option value="Pcs">Pcs</option>
                <option value="Unit">Unit</option>
                <option value="Set">Set</option>
              </select>
            </div>  
            
            <!-- Sumber Alat -->
            <div class="form-group col-md-4">
              <label for="sumber_alat" style="color: #000;">
                <b>Sumber Alat</b>                
              </label>
              <select
                id="sumber_alat"
                class="form-control"
              >
                <option value="" disabled selected>Pilih Sumber Alat</option>               
                <option value="Stok Baru">Stok Baru</option>
                <option value="Stok Lama">Stok Lama</option>
                <option value="Peminjaman">Peminjaman</option>                
              </select>
            </div> 

            <!-- Status -->
            <!--<div class="form-group col-md-6">
              <label for="status" style="color: #000;">
                <b>Status</b>
                <sup style="color: red;"> *</sup>
              </label>
              <select
                id="status"
                v-model="alat.status"
                class="form-control"
                required
              >
                <option value="" disabled selected>Pilih Status</option>
                <option value="active">Active</option>
                <option value="rusak">Rusak</option>
                <option value="error">Error</option>
              </select>
            </div>-->    
          </div>

          <!--<div class="row">-->
            <!-- Lokasi Penyimpanan -->
            <!--<div class="form-group col-md-6">
              <label for="lokasi_penyimpanan" style="color: #000;">
                <b>Lokasi Penyimpanan</b>
                <sup style="color: red;"> *</sup>
              </label>
              <div>
                <select
                id="lokasi"
                v-model="selectedLocation"
                class="form-control"
                @change="onLocationChange"
                >
                  <option value="" disabled selected>Pilih Lokasi</option>                  
                  <option value="other">Masukkan Lokasi Lainnya</option>
                  <option v-for="location in locations" :key="location.id" :value="location">
                    {{ location }}
                  </option>
                </select>
              </div>
              <div v-if="showManualInputLocation" class="mt-2">
                <input
                  type="text"
                  v-model="manualLocationInput"
                  class="form-control"
                  placeholder="Masukkan Lokasi Penyimpanan"
                />
              </div>
            </div>

          </div>-->

          <!-- Jadwal Perawatan -->
          <div class="row">
            <div class="form-group col-md-12">
              <label for="jadwal_perawatan" style="color: #000;">
                <b>Jadwal Perawatan</b>
                <sup style="color: red;"> *</sup>
              </label>
              <select
                id="jadwal_perawatan"
                v-model="alat.jadwal_perawatan"
                @change="onJadwalChange"
                class="form-control"
                required
              >
                <option value="" disabled selected>Pilih Interval Perawatan</option>
                <option value="3">Setiap 3 Bulan</option>
                <option value="6">Setiap 6 Bulan</option>
                <option value="12">Setiap 12 Bulan</option>
                <option value="other">Lainnya</option>
              </select>
            </div>

            <!-- Manual Input Jadwal (jika memilih "Lainnya") -->
            <div v-if="showManualInputJadwal" class="form-group col-md-12">
              <input
                type="number"
                v-model="manualJadwal"
                class="form-control"
                placeholder="Masukkan interval (bulan)"
              />
            </div>
          </div>

          <div class="row">
            <!-- Asal Usul -->
            <div class="form-group col-md-12">
              <label for="asal_usul" style="color: #000;">
                <b>Vendor</b>
              </label>
              <div class="textarea-wrapper">
                <textarea
                id="asal_usul"
                class="form-control"
                rows="1"
                placeholder="Masukkan Vendor Alat (Maksimal 100 karakter)"
                maxlength="100"
                ></textarea>
                <small class="text-muted char-counter">
                  {{ alat.asal_usul.length }} / 100
                </small>
              </div>
            </div>
          </div>

          <!-- Deskripsi -->
          <div class="form-group">
            <label for="deskripsi" style="color: #000;">
              <b>Deskripsi</b>
            </label>
            <div class="textarea-wrapper">
              <textarea
                id="deskripsi"
                v-model="alat.deskripsi"
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

          <!-- Upload Gambar dengan Drag-and-Drop -->
          <div class="form-group">
            <label for="gambar" style="color: #000;">
              <b>Thumbnail Image</b>
              <sup style="color: red;"> *</sup>
            </label>
            <div 
              class="upload-box"
              @dragover.prevent
              @drop.prevent="handleDrop"
              @dragenter="dragActive = true"
              @dragleave="dragActive = false"
              :class="{ 'drag-active': dragActive }"
            >
              <p v-if="!gambarPreview">
                <i class="fas fa-image"></i><br>
                Drag and drop here <br>or <br><span class="browse-link">Browse</span>
              </p>
              <p v-else>
                <img :src="gambarPreview" alt="Preview" class="img-preview" />
              </p>
              <input 
                type="file"
                class="upload-input"
                @change="onFileChange"
                accept="image/*"
                required
              />
            </div>
          </div>          

          <!-- Kondisi -->
          <!--<div class="form-group">
            <label for="kondisi">Kondisi</label>
            <select
              id="kondisi"
              v-model="alat.kondisi"
              class="form-control"
              required
            >
              <option value="">Pilih Kondisi</option>
              <option value="baru">Baru</option>
              <option value="lama">Lama</option>
              <option value="rusak">Rusak</option>
            </select>
          </div>-->                          

        <!-- Tombol Aksi -->
        <div class="form-group d-flex justify-content-between">
          <span></span>
          <div>
            <button type="submit" class="btn btn-plus mr-2">
              <i class="fas fa-save"></i> Simpan
            </button>
            <!--<button @click="tutupModal" type="button" class="btn btn-danger">
              <i class="fas fa-times"></i> Batal
            </button>-->
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
        jadwal_perawatan: '', // Untuk menyimpan jadwal perawatan yang dipilih
      },
      gambar: null,
      gambarPreview: null,
      dragActive: false,
      showModal: false,
      selectedKodeAlat: '',
      kode_alats: [],
      kategoris: ['CLAMP', 'POWER SUPPLY', 'GLUE GUN', 'TANG', 'WATERPASS', 'SOLDER'], // Contoh data dari database
      formattedHarga: '',
      selectedLocation: '',
      manualLocationInput: '',
      showManualInputLocation: false,
      locations: ['Gedung A', 'Gedung B', 'Gedung C', 'Gedung D'],
      manualKategori: '',
      showManualInput: false,
      selectedKategori: '',
      showManualInputJadwal: false, // Untuk menampilkan input manual jadwal
      manualJadwal: '', // Untuk input manual jadwal
    };
  },
  methods: {
    onJadwalChange(event) {
      if (event.target.value === 'other') {
        this.showManualInputJadwal = true;
        this.alat.jadwal_perawatan = ''; // Kosongkan pilihan jika memilih "Lainnya"
      } else {
        this.showManualInputJadwal = false;
        this.manualJadwal = ''; // Kosongkan input manual
      }
    },
    kembali() {
        this.$router.push('/admin-mtc/data-alat').then(() => {
          window.location.reload();
        });
      },
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