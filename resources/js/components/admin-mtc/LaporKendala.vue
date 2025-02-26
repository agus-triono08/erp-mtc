<template>
    <div class="container-fluid">
      <!-- Page Heading -->
      <h1 class="h3 mb-4 text-gray-800">
        <i 
          class="fas fa-arrow-left icon-hover" 
          @click="kembali"
          style="cursor: pointer;"
        ></i>
        Laporan Kendala Teknis
      </h1>

        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold" style="color: #169ea8;">Form Laporan Kendala Teknis</h6>
        </div>
        <div class="card-body">
          <form @submit.prevent="submitForm">
            <!-- Email -->
            <div class="form-group">
              <label for="email">Email</label>
              <input
                type="email"
                id="email"
                v-model="form.email"
                class="form-control"
                required
              />
            </div>
  
            <!-- Nama -->
            <div class="form-group">
              <label for="nama">Nama</label>
              <input
                type="text"
                id="nama"
                v-model="form.nama"
                class="form-control"
                required
              />
            </div>
  
            <!-- Jabatan -->
            <div class="form-group">
              <label for="jabatan">Jabatan</label>
              <select                
                id="jabatan"
                v-model="form.jabatan"  
                class="form-control"              
                required>
                <option value="">Pilih Jabatan</option>
                <option value="manager">Manager</option>
                <option value="assmanager">Assistant Manager</option>
                <option value="spv">SPV</option>
                <option value="assspv">Assistant SPV</option>
                <option value="staff">Staff</option>
              </select>
            </div>
  
            <!-- Bagian -->
            <div class="form-group">
              <label for="bagian">Bagian</label>
              <select                
                id="bagian"
                v-model="form.bagian"  
                class="form-control"              
                required>
                <option value="">Pilih Bagian</option>
                <option value="keuangan">Keuangan</option>
                <option value="penjualan">Penjualan</option>
                <option value="pembelian">Pembelian</option>
                <option value="gbmp">GBMP</option>
                <option value="ppic">PPIC</option>
                <option value="produksi">Produksi</option>
                <option value="sarkes">SarKes</option>
                <option value="qc">QC</option>
                <option value="gudangkarantina">Gudang Karantina</option>
                <option value="perbaikanproduksi">Perbaikan Produksi</option>
                <option value="rnd">RND</option>
                <option value="markom">Markom</option>
                <option value="teknik">Teknik</option>
                <option value="gbj">GBJ</option>
                <option value="documentcontrol">Document Control</option>
                <option value="lab">LAB</option>
                <option value="kesehatan">Kesehatan</option>
                <option value="maintenance">Maintenance</option>
              </select>
            </div>
  
            <!-- Jenis Sistem -->
            <div class="form-group">
              <label for="jenis_sistem">Jenis Sistem</label>
              <select                
                id="jenis_sistem"
                v-model="form.jenis_sistem"  
                class="form-control"              
                required>
                <option value="">Pilih Jenis Sistem</option>
                <option value="erp">ERP</option>
                <option value="zahir">Zahir</option>
                <option value="accurate">Accurate</option>                
              </select>
            </div>
  
            <!-- Jenis Permintaan -->
            <div class="form-group">
              <label for="jenis_permintaan">Jenis Permintaan</label>
              <select                
                id="jenis_permintaan"
                v-model="form.jenis_permintaan"  
                class="form-control"              
                required>
                <option value="">Pilih Jenis Permintaan</option>
                <option value="pembuatan">Pembuatan/Penambahan</option>
                <option value="perubahan">Perubahan</option>
                <option value="perbaikan">Perbaikan Bug/Error</option>
                <option value="btp">Buka Tutup Periode</option>                
              </select>
            </div>
  
            <!-- Keterangan Permasalahan -->
            <div class="form-group">
              <label for="keterangan_permasalahan">Keterangan Permasalahan</label>
              <textarea
                id="keterangan_permasalahan"
                v-model="form.keterangan_permasalahan"
                class="form-control"
                required
              ></textarea>
            </div>
  
            <!-- Lampiran -->
            <div class="form-group">
              <label for="lampiran">Upload Lampiran</label>
              <input
                type="file"
                id="lampiran"
                @change="handleFileUpload"
                class="form-control-file"
                style="display: none"
              />
              <label for="lampiran" class="btn btn-info">Upload Lampiran</label>  <!-- Tombol upload kustom -->
            </div>
  
            <!-- Waktu Kebutuhan -->
            <div class="form-group">
              <label for="waktu_kebutuhan">Waktu Kebutuhan</label>
              <input
                type="datetime-local"
                id="waktu_kebutuhan"
                v-model="form.waktu_kebutuhan"
                class="form-control"
              />
            </div>
  
            <!-- Tombol Aksi -->
            <div class="form-group d-flex justify-content-between">
              <button type="submit" class="btn btn-plus">
                <i class="fas fa-save"></i> Kirim Laporan
              </button>
              <div>
                
              </div>
            </div>
          </form>
        </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        form: {
          email: '',
          nama: '',
          jabatan: '',
          bagian: '',
          jenis_sistem: '',
          jenis_permintaan: '',
          keterangan_permasalahan: '',
          lampiran: null,
          waktu_kebutuhan: ''
        }
      };
    },
    methods: {
      handleFileUpload(event) {
        this.form.lampiran = event.target.files[0];
      },
      async submitForm() {
        const formData = new FormData();
        for (const key in this.form) {
          formData.append(key, this.form[key]);
        }
  
        try {
            await axios.post("/api/tech-issue", formData, {
            headers: { "Content-Type": "multipart/form-data" },
            });
            alert("Data berhasil disimpan!");
            // Arahkan pengguna ke halaman data alat menggunakan window.location.href
            window.location.href = "/admin-mtc/dashboard";
        } catch (error) {
            console.error("Error response:", error.response);
            alert("Terjadi kesalahan saat menyimpan data: " + error.response.data.message);
        }
      },
      kembali() {
          this.$router.push('/admin-mtc/data-alat').then(() => {
              window.location.reload();
          });
      },
    },
  };
  </script>
  