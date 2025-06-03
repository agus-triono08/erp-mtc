<template>
  <div class="container-fluid mt-3">
    <h1 class="h6 mb-2 text-gray-800">
      <i class="fas fa-angle-left icon-hover" @click="kembali" style="cursor: pointer;"> Back to Master Data</i>
    </h1>

    <div class="card-header py-3 mb-2">
      <h6 class="m-0 font-weight-bold" style="color: #169ea8; border-radius: 15px;">Form Edit Informasi Alat</h6>
    </div>

    <form @submit.prevent="submitForm" enctype="multipart/form-data">
      <div class="row">
        <div class="form-group col-md-6">
          <label style="color: black;">
            <b>Alasan Melakukan Perubahan</b>
            <sup style="color: red;"> *</sup>
          </label>
          <div class="textarea-wrapper">
            <textarea
            id="note_perubahan_jadwal"
            v-model="form.note_perubahan_jadwal"
            class="form-control"
            rows="1"
            placeholder="Masukkan Alasan Melakukan Perubahan (Maksimal 100 karakter)"
            maxlength="100"
            ></textarea>
            <small class="text-muted char-counter">
              {{ form.note_perubahan_jadwal.length }} / 100
            </small>
          </div>
        </div>
        <div class="form-group col-md-6">
          <label for="jadwal_perawatan" style="color: #000;">
            <b>Jadwal Perawatan</b>
            <sup style="color: red;"> *</sup>
          </label>
          <select 
            id="jadwal_perawatan"
            v-model="form.jadwal_perawatan"
            class="form-control"
            required>
            <option value="" disabled selected>Pilih Interval Perawatan</option>
            <option value="0">Tanpa Perawatan</option>
            <option value="1">Setiap 1 Bulan</option>
            <option value="3">Setiap 3 Bulan</option>
            <option value="6">Setiap 6 Bulan</option>
            <option value="12">Setiap 12 Bulan</option>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-4">
          <label for="tanggal_mulai_perawatan" style="color: #000;">
            <b>Tanggal Mulai Perawatan</b>
            <span v-if="form.jadwal_perawatan > 0" style="color: red;"> *</span>
          </label>
          <input 
            type="date" 
            v-model="form.jadwal_mulai_perawatan" 
            class="form-control" 
            :required="form.jadwal_perawatan > 0"
            placeholder="Masukkan Tanggal Mulai Perawatan">
          <small class="form-text" style="color: red;" v-if="form.jadwal_perawatan > 0">
            Masukkan Tanggal Mulai Perawatan Awal
          </small>
        </div>
        <div class="form-group col-md-4">
          <label for="waktu_perawatan" style="color: #000;">
            <b>Waktu Perawatan</b>
            <sup style="color: red;"> *</sup>
          </label>
          <input type="time" v-model="form.waktu_perawatan" class="form-control" required placeholder="Masukkan Waktu Perawatan">
          <small class="form-text" style="color: red;">Masukkan Waktu Perawatan Per No Seri</small>
        </div>
        <div class="form-group col-md-4">
          <label for="jumlah_orang_perawatan" style="color: #000;">
            <b>Total PIC Perawatan</b>
            <sup style="color: red;"> *</sup>
          </label>
          <input type="number" v-model="form.jumlah_orang_perawatan" class="form-control" required placeholder="Masukkan Total PIC Perawatan">
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-4">
          <label style="color: black;"><b>Satuan</b></label>
          <select v-model="form.unit" class="form-control">
            <option value="" disabled selected>Pilih Satuan Alat</option>               
            <option value="Pcs">Pcs</option>
            <option value="Unit">Unit</option>
            <option value="Set">Set</option>                
          </select>
        </div>

        <div class="form-group col-md-4">
          <label style="color: black;"><b>Produk</b></label>
          <select v-model="form.pembelian" class="form-control">
            <option value="" disabled selected>Pilih Produk Alat</option>
            <option value="Local">Local</option>
            <option value="Import">Import</option>
          </select>
        </div>

        <div class="form-group col-md-4">
          <label style="color: black;"><b>Sumber</b></label>
          <select v-model="form.sumber" class="form-control">
            <option value="" disabled selected>Pilih Sumber Alat</option>               
            <option value="Stok Baru">Stok Baru</option>
            <option value="Stok Lama">Stok Lama</option>
            <option value="Peminjaman">Peminjaman</option>                
          </select>
        </div>
      </div>

      <div class="form-group">
        <label style="color: black;"><b>Vendor</b></label>
        <textarea
          v-model="form.vendor"
          class="form-control"
          rows="1"
          placeholder="Masukkan Vendor (maks. 100 karakter)"
          maxlength="100"
        ></textarea>
        <small class="text-muted">{{ form.vendor ? form.vendor.length : 0 }} / 100</small>
      </div>

      <div class="form-group">
        <label style="color: black;"><b>Fungsi</b></label>
        <textarea
          v-model="form.fungsi"
          class="form-control"
          rows="1"
          placeholder="Masukkan Fungsi (maks. 100 karakter)"
          maxlength="100"
        ></textarea>
        <small class="text-muted">{{ form.fungsi ? form.fungsi.length : 0 }} / 100</small>
      </div>

      <div class="form-group">
        <label style="color: black;"><b>Deskripsi</b></label>
        <textarea
          v-model="form.deskripsi"
          class="form-control"
          rows="3"
          placeholder="Masukkan Deskripsi (maks. 500 karakter)"
          maxlength="500"
        ></textarea>
        <small class="text-muted">{{ form.deskripsi ? form.deskripsi.length : 0 }} / 500</small>
      </div>

      <div class="form-group">
        <label for="gambar" style="color: #000;">
          <b>Thumbnail Image</b>
          <sup style="color: red;"> *</sup>
        </label>
        <label 
          for="fileInput" 
          class="upload-box-1"
          @dragover.prevent
          @drop.prevent="handleDrop"
          @dragenter="dragActive = true"
          @dragleave="dragActive = false"
          :class="{ 'drag-active': dragActive }"
        >
          <p v-if="!previewImage">
            <i class="fas fa-image"></i><br>
            Drag and drop here <br>or <br><span class="browse-link">Browse</span>
          </p>
          <p v-else>
            <img :src="previewImage" alt="Preview" class="img-preview" />
          </p>
        </label>
        <input 
          id="fileInput"
          type="file"
          class="upload-input"
          @change="onFileChange"
          accept="image/*"
          required
        />
      </div>

      <!-- <button class="btn btn-primary mb-3 float-right">Simpan</button> -->
      <button 
        type="submit" 
        class="btn btn-primary mb-3 float-right"
        :disabled="isSubmitting"
      >
        <span v-if="isSubmitting">
          <i class="fas fa-spinner fa-spin"></i> Menyimpan...
        </span>
        <span v-else>Simpan</span>
      </button>
      
      <!-- Tambahkan element untuk menampilkan error -->
      <div v-if="submitError" class="alert alert-danger mt-3">
        {{ submitError }}
      </div>
    </form>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import vSelect from 'vue-select';

export default {
  name: 'ToolEditForm',
  components: {
    vSelect,
  },
  data() {
    return {
      form: {
        id: '',
        pembelian: '',
        sumber: '',
        unit: '',
        vendor: '',
        fungsi: '',
        deskripsi: '',
        jadwal_perawatan: '',
        waktu_perawatan: '',
        jumlah_orang_perawatan: '',
        gambar: null,
        jadwal_mulai_perawatan: '',
        note_perubahan_jadwal: '',
      },
      previewImage: null, // ✅ buat tampilan preview
      dragActive: false,
      isSubmitting: false,
      submitError: null
    };
  },
  mounted() {
    this.fetchToolData();
  },
  methods: {
    async fetchToolData() {
      const toolId = this.$route.params.id;
      try {
        const res = await axios.get(`/api/v1/tools/${toolId}`);
        const data = res.data;
        
        // Konversi menit ke format HH:mm
        const waktuPerawatan = data.waktu_perawatan 
          ? this.convertMinutesToTime(data.waktu_perawatan)
          : null;

        this.form = {
          ...this.form,
          ...data,
          waktu_perawatan: waktuPerawatan,
          jadwal_perawatan: data.jadwal_perawatan?.toString() || '',
          jumlah_orang_perawatan: data.jumlah_orang_perawatan?.toString() || ''
        };

        if (data.gambar) {
          this.previewImage = `/api/get_image/${data.gambar}`;
        }
      } catch (error) {
        console.error("Gagal mengambil data alat:", error);
      }
    },

    // Helper function untuk konversi menit ke format waktu
    convertMinutesToTime(minutes) {
      if (!minutes) return '00:00';
      const hrs = Math.floor(minutes / 60);
      const mins = minutes % 60;
      return `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}`;
    },

    async submitForm() {
      this.isSubmitting = true;
      this.submitError = null;

      try {
        if (!this.validateForm()) {
          this.isSubmitting = false;
          return;
        }

        const formData = new FormData();
        
        // Konversi waktu perawatan dari HH:mm ke menit
        if (this.form.waktu_perawatan) {
          const [hours, minutes] = this.form.waktu_perawatan.split(':');
          const totalMinutes = parseInt(hours) * 60 + parseInt(minutes);
          formData.append('waktu_perawatan', totalMinutes);
        }

        // Field lainnya
        const fields = ['pembelian', 'sumber', 'unit', 'vendor', 'fungsi', 'deskripsi',
                      'jadwal_perawatan', 'jumlah_orang_perawatan', 'jadwal_mulai_perawatan', 'note_perubahan_jadwal'];
        
        fields.forEach(field => {
          if (this.form[field] !== null && this.form[field] !== undefined) {
            formData.append(field, this.form[field]);
          }
        });

        // Handle gambar
        if (this.form.gambar instanceof File) {
          formData.append('gambar', this.form.gambar);
        }

        // Kirim ke server
        const response = await axios.post(
          `/api/v1/tools/${this.form.id}?_method=PUT`,
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }
        );

        // Handle success
        Swal.fire({
          title: 'Berhasil!',
          text: 'Data berhasil diubah.',
          icon: 'success',
          confirmButtonText: 'OK'
        }).then(() => {
          this.$router.push('/admin-mtc/data-alat');
        });

      } catch (error) {
        console.error('Error:', error);
        this.submitError = this.getErrorMessage(error);
        Swal.fire({
          title: 'Gagal!',
          html: this.submitError,
          icon: 'error',
          confirmButtonText: 'OK'
        });
      } finally {
        this.isSubmitting = false;
      }
    },
    
    validateForm() {
      // Validasi waktu perawatan
      if (this.form.jadwal_perawatan > 0) {
        if (!this.form.waktu_perawatan) {
          this.submitError = 'Waktu perawatan wajib diisi';
          return false;
        }
        
        // Validasi format HH:mm
        if (!this.form.waktu_perawatan.match(/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
          this.submitError = 'Format waktu perawatan tidak valid (HH:mm)';
          return false;
        }
      }

      // Validasi gambar jika diperlukan
      if (this.form.gambar === null && !this.previewImage) {
        this.submitError = 'Thumbnail image wajib diisi';
        return false;
      }
      
      // Validasi field lainnya
      if (this.form.jadwal_perawatan > 0) {
        if (!this.form.waktu_perawatan) {
          this.submitError = 'Waktu perawatan wajib diisi';
          return false;
        }
        if (!this.form.jumlah_orang_perawatan) {
          this.submitError = 'Total PIC perawatan wajib diisi';
          return false;
        }
      }
      
      return true;
    },
    
    getErrorMessage(error) {
      if (error.response) {
        // Error dari server
        if (error.response.data.errors) {
          return Object.values(error.response.data.errors).flat().join('<br>');
        }
        return error.response.data.message || 'Terjadi kesalahan pada server';
      }
      return error.message || 'Terjadi kesalahan jaringan';
    },
    kembali() {
      this.$router.push('/admin-mtc/data-alat');
    },
    onFileChange(e) {
      const file = e.target.files[0];
      this.handleFileSelection(file);
    },
    
    handleDrop(e) {
      this.dragActive = false;
      const file = e.dataTransfer.files[0];
      this.handleFileSelection(file);
    },
    
    handleFileSelection(file) {
      if (!file) return;
      
      // Validasi ukuran file
      if (file.size > 2 * 1024 * 1024) {
        Swal.fire("Ukuran gambar terlalu besar", "Maksimal 2MB", "warning");
        return;
      }
      
      // Validasi tipe file
      if (!file.type.match('image.*')) {
        Swal.fire("Format tidak didukung", "Hanya file gambar yang diperbolehkan", "warning");
        return;
      }
      
      this.setImagePreview(file);
    },
    
    setImagePreview(file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        this.previewImage = e.target.result;
      };
      reader.readAsDataURL(file);
      this.form.gambar = file;
    }
  }
};
</script>

<style scoped>
.upload-box-1 {
  width: 100%;
  min-height: 150px;
  border: 2px dashed #ddd;
  text-align: center;
  padding: 15px;
  cursor: pointer;
}

.drag-active {
  background-color: rgba(0, 0, 0, 0.05);
}

.upload-input {
  position: absolute;
  width: 0;
  height: 0;
  opacity: 0;
  overflow: hidden;
}

.browse-link {
  color: #169ea8;
  cursor: pointer;
}

.upload-box-1 {
  width: 100%;
  min-height: 150px;
  border: 2px dashed #169ea8;
  border-radius: 8px;
  text-align: center;
  padding: 15px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  position: relative; /* Tambahkan ini */
}

.upload-box-1:hover {
  background-color: rgba(22, 158, 168, 0.05);
}

.drag-active {
  background-color: rgba(22, 158, 168, 0.1);
  border-color: #169ea8;
}

.img-preview {
  max-width: 100%;
  max-height: 200px;
  object-fit: contain;
  border-radius: 4px;
  margin-top: 10px;
}

.char-counter {
  text-align: right;
  font-size: 0.9em;
  color: #6c757d;
}
</style>
