<template>
  <div class="container-fluid" style="width: 700px;">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold" style="color: #169ea8; border-radius: 15px;">Form Input Perawatan Data Alat</h6>
    </div>
    <div class="card-body" style="border-radius: 15px;">
      <form @submit.prevent="submitAlat">

        <!-- Kode Alat -->
        <div class="form-group">
          <label for="kode_alat" style="color: #000;">
            <b>Kode Alat</b>
            <sup style="color: red;"> *</sup>
          </label>
          <select class="form-control" @change="getNoSeri($event)">
            <option value="">Pilih Kode Alat</option>
            <option v-for="kode_alat in kode_alats" :key="kode_alat.id" :value="kode_alat.kode_alat">{{ kode_alat.kode_alat }}</option>
          </select>
        </div>

        <!-- No Seri Alat -->
        <div class="form-group">
          <label for="no_seri_alat" style="color: #000;">
            <b>No Seri Alat</b> 
            <sup style="color: red;"> *</sup>             
          </label>
          <select id="no_seri_alat" class="form-control" required>
            <option value="">Pilih No Seri Alat</option>
            <option v-for="no_seri in noseris" :key="no_seri.id" :value="no_seri.no_seri_alat">{{ no_seri.no_seri_alat }}</option>
          </select>
        </div>

        <!-- PIC -->
        <div class="form-group">
          <label for="pic" style="color: #000;">
            <b>PIC</b>
            <sup style="color: red;"> *</sup>
          </label>
          <select id="pic" class="form-control" required>
            <option value="" disabled selected>Pilih PIC</option>
            <option v-for="pic in PIC" :key="pic.id" :value="pic.nama_staff">
              {{ pic.nama_staff }}
            </option>
          </select>
        </div>

        <!-- Tanggal Perawatan -->
        <div class="form-group">
            <label for="tanggal_perawatan" style="color: #000;">
            <b>Tanggal Perawatan</b>
            <sup style="color: red;"> *</sup>
            </label>
            <input
            type="date"
            id="tanggal_perawatan"
            class="form-control"
            required
            />            
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="status" style="color: #000;">
            <b>Status</b>
            <sup style="color: red;"> *</sup>
            </label>
            <select id="status" class="form-control" required>
            <option value="Belum" disabled selected>Belum</option>              
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
      PIC: [],
      NoSeris: '',
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
    kode_alats: [],
    kode_alat: "",
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
    async getNoSeri(event) {
      this.alat.kode_alat = event.target.value;
      try {
        const response = await axios.get(`/api/no-seri/${this.alat.kode_alat}`);
        this.noseris = response.data;
        // console.log(this.noseris)
      } catch (error) {
        console.error("Gagal mengambil data nomor seri:", error.response ? error.response.data : error.message);
      }
    },
    async fetchAlat() {
      try {
        const response = await axios.get('/api/alats');
        this.kode_alats = response.data.data;
        //console.log(this.kode_alats)
      } catch (error) {
        console.error(error);
      }
    },
    // async fetchDataNoSeri() {
    //   try {
    //     const response = await axios.get('/api/no-seri');
    //     this.noseris = response.data.data;
    //     //console.log(this.noseris)
    //   } catch (error) {
    //     console.error(error);
    //   }
    // },
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
mounted () {
  // this.fetchDataNoSeri();  
  this.fetchDataPIC();
  this.fetchAlat();
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