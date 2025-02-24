<template>
    <div class="container-fluid mt-3">
      <!-- Modal Konfirmasi -->
      <div class="modal" :class="{ 'is-visible': showModal }">
        <div class="modal-content">
          <h2>Konfirmasi Pembatalan</h2>
          <p>Apakah Anda yakin ingin membatalkan?</p>
          <button id="confirmButton" @click="confirmBatal">Ya, Batalkan</button>
          <button id="cancelButton" @click="cancelBatal">Tidak, Kembali</button>
        </div>
      </div>
      <!-- Page Heading -->
      <h1 class="h6 mb-2 text-gray-900">
        <i 
          class="fas fa-angle-left icon-hover" 
          @click="kembali"
          style="cursor: pointer;"
        > Back to Master Data</i>        
      </h1>
        
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold" style="color: #169ea8;">Form Edit Data Alat</h6>
        </div>
        <div class="card-body">
          <form @submit.prevent="updateAlat">
            <div class="row">
              <!-- Kategori -->
              <div class="col-md-12">
                <label for="kategori" style="color: #000;">
                  <b>Kategori</b>
                </label>
                <input
                  type="text"
                  id="kategori"
                  v-model="alat.kategori"
                  class="form-control"
                  placeholder="Masukkan Kategori"
                  required
                  disabled
                />
              </div>
            </div>

            <div class="row">
              <!-- Nama Alat -->
              <div class="form-group col-md-6">
                <label for="nama_alat" style="color: #000;">
                  <b>Nama Alat</b>
                </label>
                <input
                  type="text"
                  id="nama_alat"
                  v-model="alat.nama_alat"
                  class="form-control"
                  placeholder="Masukkan Nama Alat"
                  required
                  disabled
                />
              </div>

              <!-- Merek Alat -->
            <div class="form-group col-md-6">
              <label for="merek_alat" style="color: #000;">
                <b>Merek Alat</b>
              </label>
              <input
                type="text"
                id="merek_alat"
                v-model="alat.merek_alat"
                class="form-control"
                placeholder="Masukkan Merek Alat"
                required
                disabled
              />
            </div>
            </div>            
  
            <!-- Gambar -->
            <!--<div class="form-group">
              <label for="gambar">Upload Gambar</label>
              <input
                type="file"
                id="gambar"
                @change="handleFileUpload"
                class="form-control-file"
                style="display: none"
              />
              <label for="gambar" class="btn btn-info">Change Gambar</label>
            </div>-->

            <div class="row">
              <!-- Tipe/Ukuran Alat -->
              <div class="form-group col-md-6">
                <label for="tipe_alat" style="color: #000;">
                  <b>Tipe/Ukuran Alat</b>
                </label>
                <input
                type="text"
                id="tipe_alat"
                v-model="alat.tipe_alat"
                class="form-control"
                placeholder="Masukkan Tipe/Ukuran Alat"
                required
                disabled
                />
              </div>

              <!-- Produk -->
              <div class="form-group col-md-6">
                <label for="pembelian" style="color: #000;">
                  <b>Produk</b>
                </label>
                <input
                type="text"
                id="produk"
                v-model="alat.pembelian"
                class="form-control"
                placeholder="Masukkan Produk Alat"
                required
                disabled
                />
              </div>

              <!-- Serial Number Alat Bawaan -->
              <!--<div class="form-group col-md-6">
                <label for="serial_number_alat_bawaan" style="color: #000;">
                  <b>Serial Number Alat Bawaan</b>
                </label>
                <input
                type="text"
                id="serial_number_alat_bawaan"
                v-model="alat.no_seri_alat"
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
                <span style="font-size: small;">Isi '0' jika tidak memiliki harga pembelian</span>
              </div>-->
              <!-- Tanggal Masuk -->
              <!--<div class="form-group col-md-6">
                <label for="tahun_masuk" style="color: #000;">
                  <b>Tahun Masuk</b>
                </label>
                <input
                  type="number"
                  id="tahun_masuk"
                  v-model="alat.tahun_masuk"
                  class="form-control"
                  required
                  disabled
                />
              </div>
            </div>-->

            <div class="row">              
              <!-- Satuan Alat -->
              <div class="form-group col-md-6">
                <label for="satuan_alat" style="color: #000;">
                  <b>Satuan Alat</b>
                </label>
                <input
                type="text"
                id="satuan_alat"
                v-model="alat.unit_alat"
                class="form-control"
                placeholder="Masukkan Satuan Alat"
                required
                disabled
                />
              </div>

              <!-- Sumber Alat -->
              <div class="form-group col-md-6">
                <label for="sumber_alat" style="color: #000;">
                  <b>Sumber Alat</b>                
                </label>
                <select
                  id="sumber_alat"
                  v-model="alat.sumber_alat"
                  class="form-control"
                  required
                >
                  <option value="" disabled selected>Pilih Sumber Alat</option>               
                  <option value="Stok Baru">Stok Baru</option>
                  <option value="Stok Lama">Stok Lama</option>
                  <option value="Peminjaman">Peminjaman</option>                
                </select>
              </div> 

              <!-- Stok -->
              <!--<div class="form-group col-md-4">
                <label for="stok" style="color: #000;">
                  <b>Stok</b>
                  <sup style="color: red;"> *</sup>
                </label>
                <input
                  type="number"
                  id="stok"
                  class="form-control"
                  required
                />
              </div>-->              

              <!-- Status Alat -->
              <!--<div class="form-group col-md-4">
                <label for="status_alat" style="color: #000;">
                  <b>Status Alat</b>
                  <sup style="color: red;"> *</sup>
                </label>
                <select
                id="status_alat"
                v-model="alat.status"
                class="form-control"
                >
                <option value="" disabled selected>Pilih Status</option>
                <option value="active">Active</option>
                <option value="error">Error</option>                
                <option value="rusak">Rusak</option>
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
                  v-model="alat.lokasi_penyimpanan"
                  class="form-control"
                  @change="onLocationChange"
                  >
                    <option value="" selected disabled>Pilih Lokasi</option>
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
              <div v-if="showManualEditJadwal" class="form-group col-md-12">
                <input
                  type="number"
                  v-model="manualJadwal"
                  class="form-control"
                  placeholder="Masukkan interval (bulan)"
                />
              </div>
            </div>

            <div class="row">
            <!-- Vendor -->
            <div class="form-group col-md-12">
              <label for="asal_usul" style="color: #000;">
                <b>Vendor</b>
              </label>
              <div class="textarea-wrapper">
                <textarea
                id="asal_usul"
                v-model="alat.vendor"
                class="form-control"
                rows="1"
                placeholder="Masukkan Asal Usul Alat (Maksimal 100 karakter)"
                maxlength="100"
                ></textarea>
                <small class="text-muted char-counter">
                  Test / 100
                </small> <!--{{ alat.asal_usul.length }} Penganti Test-->
              </div>
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
                  placeholder="Masukkan Deskripsi (maksimal 500 karakter)"
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
                <button @click="batal" type="button" class="btn btn-danger">
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
        alat: {
          id: null,
          nama_alat: "",
          merek_alat: "",
          lokasi_penyimpanan: "",
          kondisi: "",
          status: "",
          stok: null,
          deskripsi: "",
          gambar: null,
          harga: null, // Nilai asli (tanpa format)
          //asal_usul: "",
        },        
          showModal: false, // Tambahkan variabel untuk mengontrol tampilan modal
          formattedHarga: '', // Nilai yang diformat (dengan format Rupiah)
          selectedLocation: '',
          manualLocationInput: '',
          showManualInputLocation: false,
          showManualEditJadwal: false,
          manualJadwal: false,
          locations: ['Gedung A', 'Gedung B', 'Gedung C', 'Gedung D'],
      };
    },
    methods: {
      async fetchAlatData() {
        const id = this.$route.params.id;
        try {
          const response = await axios.get(`/api/alats/${id}/edit`);
          this.alat = response.data.data;
        } catch (error) {
          console.error("Error fetching alat data:", error);
        }
      },
      handleFileUpload(event) {
        this.alat.gambar = event.target.files[0];
      },
      async updateAlat() {
          const formData = new FormData();
          for (const key in this.alat) {
              formData.append(key, this.alat[key]);
          }
          if (this.alat.gambar) {
              formData.append("gambar", this.alat.gambar);
          }
          if (this.alat.deskripsi.length > 500) {
            alert("Deskripsi tidak boleh lebih dari 500 karakter.");
            return;
          }

          const id = this.$route.params.id;

          try {
              await axios.post(`/api/alats/${id}/update`, formData, {
                  headers: { "Content-Type": "multipart/form-data" },
              });
              window.location.href = "/admin-mtc/data-alat"; // Kembali ke halaman daftar data alat
          } catch (error) {
              console.error("Error updating alat:", error);
          }
      },
      batal() {
        this.showModal = true; // Tampilkan modal saat tombol "Batal" diklik
      },
      confirmBatal() {
        this.resetForm(); // Reset form jika pengguna mengonfirmasi
        this.showModal = false; // Sembunyikan modal
      },
      cancelBatal() {
        this.showModal = false; // Sembunyikan modal jika pengguna membatalkan
      },
      kembali() {
        this.$router.push('/admin-mtc/data-alat').then(() => {
          window.location.reload();
        });
      },
      resetForm() {
        this.alat = {
          nama_alat: "",
          merek_alat: "",
          lokasi_penyimpanan: "",
          kondisi: "",
          status: "",
          stok: "",
          deskripsi: "",
          gambar: null,
        };
      },
      // Fungsi untuk memformat input ke format Rupiah
      formatHarga(event) {
        // Hapus semua karakter selain angka
        let value = event.target.value.replace(/\D/g, '');

        // Simpan nilai asli ke data alat.harga_pembelian
        this.alat.harga_pembelian = value ? parseInt(value, 10) : 0;

        // Format nilai ke format Rupiah
        this.formattedHarga = this.formatRupiah(value);
      },

      // Fungsi untuk mengubah angka ke format Rupiah
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
      onLocationChange (event) {
        if (event.target.value === 'other') {
          this.showManualInputLocation = true;
          this.selectedLocation = '';
        } else {
          this.showManualInputLocation = false;
          this.manualLocation = '';
        }
      },
      onJadwalChange(event) {
        if (event.target.value === 'other') {
          this.showManualEditJadwal = true;
          this.alat.jadwal_perawatan = ''; // Kosongkan pilihan jika memilih "Lainnya"
        } else {
          this.showManualEditJadwal = false;
          this.manualJadwal = ''; // Kosongkan input manual
        }
      },
    },
    mounted() {
      this.fetchAlatData();
    },
    computed: {    
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
  
  .card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
  }
  
  .card-body {
    background-color: #fff;
  }
  
  .card {
    border-radius: 5px;
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
  