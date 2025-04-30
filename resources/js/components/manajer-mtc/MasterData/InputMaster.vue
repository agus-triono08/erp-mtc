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
    <div class="card-header py-3 mb-2">
      <h6 class="m-0 font-weight-bold" style="color: #169ea8; border-radius: 15px;">Form Input Data Master</h6>
    </div>

    <form @submit.prevent="submitForm" enctype="multipart/form-data">
      <!-- Dropdown Jenis -->
      <div class="form-group">
        <label style="color: black;">
          <b>Jenis</b>
          <sup style="color: red;">*</sup>
        </label>
        <select v-model="form.jenis_id" class="form-control" @change="fetchKategori">
          <option disabled value="">Pilih Jenis</option>
          <option v-for="j in jenis" :key="j.id" :value="j.id">
            {{ j.nama_jenis }}
          </option>
        </select>
      </div>

      <!-- Dropdown Kategori -->
      <div class="form-group">
        <label style="color: black;">
          <b>Kategori</b>
          <sup style="color: red;">*</sup>
        </label>
        <v-select
          :options="kategori"
          v-model="form.kategori_id"
          :reduce="option => option.id"
          label="nama_kategori"
          placeholder="Pilih Kategori"
          :searchable="true"
          @input="fetchMerek"
        />
        <!-- <select v-model="form.kategori_id" class="form-control" @change="fetchMerek">
          <option disabled value="">Pilih Kategori</option>
          <option v-for="k in kategori" :key="k.id" :value="k.id">
            {{ k.nama_kategori }}
          </option>
        </select> -->
      </div>
      
      <div class="row">
        <!-- Dropdown Merek -->
        <div class="form-group col-md-6">
          <label style="color: black;">
            <b>Merek</b>
            <sup style="color: red;">*</sup>
          </label>
          <v-select
            :options="merek"
            v-model="form.merek_id"
            :reduce="option => option.id"
            label="nama_merek"
            placeholder="Pilih Merek"
            :searchable="true"
            @input="fetchTipe"
          />
          <!-- <select v-model="form.merek_id" class="form-control" @change="fetchTipe">
            <option disabled value="">Pilih Merek</option>
            <option v-for="m in merek" :key="m.id" :value="m.id">
              {{ m.nama_merek }}
            </option>
          </select> -->
        </div>

        <!-- Dropdown Tipe -->
        <div class="form-group col-md-6">
          <label style="color: black;">
            <b>Tipe</b>
            <sup style="color: red;">*</sup>
          </label>
          <v-select
            :options="tipe"
            v-model="form.tipe_id"
            :reduce="option => option.id"
            label="nama_tipe"
            placeholder="Pilih Tipe"
            :searchable="true"            
          />
          <!-- <select v-model="form.tipe_id" class="form-control">
            <option disabled value="">Pilih Tipe</option>
            <option v-for="t in tipe" :key="t.id" :value="t.id">
              {{ t.nama_tipe }}
            </option>
          </select> -->
        </div>
      </div>      

      <div class="row">
        <!-- Input Fields -->
        <div class="form-group col-md-6">
          <label style="color: black;">
            <b>Nama</b>
            <sup style="color: red;">*</sup>
          </label>
          <input v-model="form.nama" type="text" class="form-control" placeholder="Masukkan Nama Alat" required />
        </div>

        <div class="form-group col-md-6">
          <label style="color: black;">
            <b>Stok</b>
            <sup style="color: red;">*</sup>
          </label>
          <input v-model="form.stok_awal" type="number" class="form-control" placeholder="Masukkan Jumlah Stok" required />
        </div>
      </div>      

      <div class="row">
        <div class="form-group col-md-4">
          <label style="color: black;">
            <b>Satuan</b>
            <!-- <sup style="color: red;">*</sup> -->
          </label>
          <!-- <input v-model="form.unit" type="text" class="form-control" /> -->
          <select 
            id="satuan_alat"
            v-model="form.unit"
            class="form-control"
          >
            <option value="" disabled selected>Pilih Satuan Alat</option>
            <option value="Pcs">Pcs</option>
            <option value="Unit">Unit</option>
            <option value="Set">Set</option>
          </select>
        </div>

        <div class="form-group col-md-4">
          <label style="color: black;">
            <b>Produk</b>
            <!-- <sup style="color: red;">*</sup> -->
          </label>
          <!-- <input v-model="form.pembelian" type="text" class="form-control" /> -->
          <select
            id="pembelian"
            v-model="form.pembelian"
            class="form-control"
          >
            <option value="" disabled selected>Pilih Produk Alat</option>
            <option value="Local">Local</option>
            <option value="Import">Import</option>
          </select>
        </div>

        <div class="form-group col-md-4">
          <label style="color: black;">
            <b>Sumber</b>
            <!-- <sup style="color: red;">*</sup> -->
          </label>
          <!-- <input v-model="form.sumber" type="text" class="form-control" /> -->
          <select
            id="sumber_alat"
            v-model="form.sumber"
            class="form-control"
          >
            <option value="" disabled selected>Pilih Sumber Alat</option>               
            <option value="Stok Baru">Stok Baru</option>
            <option value="Stok Lama">Stok Lama</option>
            <option value="Peminjaman">Peminjaman</option>                
          </select>
        </div>
      </div>

      <!-- <div class="form-group">
        <label>Harga Total</label>
        <input v-model="form.harga_total" type="number" class="form-control" />
      </div> -->            

      <div class="row">
        <div class="form-group col-md-6">
          <label style="color: black;">
            <b>Vendor</b>
            <!-- <sup style="color: red;">*</sup> -->
          </label>
          <!-- <input v-model="form.vendor" type="text" class="form-control" /> -->
          <div class="textarea-wrapper">
            <textarea
            id="vendor"
            v-model="form.vendor"
            class="form-control"
            rows="1"
            placeholder="Masukkan Vendor Darimana (Maksimal 100 karakter)"
            maxlength="100"
            ></textarea>
            <small class="text-muted char-counter">
              {{ form.vendor.length }} / 100
            </small>
          </div>
        </div>

        <div class="form-group col-md-6">
          <label for="layout" style="color: #000;">
            <b>Layout</b>
            <sup style="color: red;"> *</sup>
          </label>
          <select id="layout" v-model="form.layout_id" class="form-control">
            <option value="" disabled selected>Pilih Layout</option>
            <option v-for="layout in Layout" :key="layout.id" :value="layout.id">{{ layout.ruang }}</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label style="color: black;">
          <b>Fungsi</b>
          <!-- <sup style="color: red;">*</sup> -->
        </label>
        <!-- <textarea v-model="form.fungsi" class="form-control"></textarea> -->
        <div class="textarea-wrapper">
          <textarea
            id="fungsi"                
            v-model="form.fungsi"
            class="form-control"
            rows="1"
            placeholder="Masukkan Fungsi Nya (Maksimal 100 Karakter)"
            maxlength="100"
          ></textarea>
          <small class="text-muted char-counter">
            {{ form.fungsi.length }} / 100
          </small>
        </div>
      </div>

      <div class="form-group">
        <label style="color: black;">
          <b>Deskripsi</b>
          <!-- <sup style="color: red;">*</sup> -->
        </label>
        <!-- <textarea v-model="form.deskripsi" class="form-control"></textarea> -->
        <div class="textarea-wrapper">
          <textarea
            id="deskripsi"                
            v-model="form.deskripsi"
            class="form-control"
            rows="3"
            placeholder="Masukkan Deskripsi (Maksimal 500 Karakter)"
            maxlength="500"
          ></textarea>
          <small class="text-muted char-counter">
            {{ form.deskripsi.length }} / 500
          </small>
        </div>
      </div>

      <!-- <div class="form-group">
        <label>Jadwal Perawatan</label>
        <input v-model="form.jadwal_perawatan" type="date" class="form-control" />
      </div> -->

      <div class="form-group">
        <label for="jadwal_perawatan" style="color: #000;">
          <b>Jadwal Perawatan</b>
          <sup style="color: red;"> *</sup>
        </label>
        <!-- <select 
          id="jadwal_perawatan"
          v-model="form.jadwal_perawatan"
          @change="onJadwalChange"
          class="form-control"
          required> -->
        <select 
          id="jadwal_perawatan"
          v-model="form.jadwal_perawatan"
          class="form-control"
          required>
          <option value="" disabled selected>Pilih Interval Perawatan</option>
          <option value="1">Setiap 1 Bulan</option>
          <option value="3">Setiap 3 Bulan</option>
          <option value="6">Setiap 6 Bulan</option>
          <option value="12">Setiap 12 Bulan</option>
          <!-- <option value="other">Lainnya</option> -->
        </select>
      </div>

      <!-- Manual Input Interval Perawatan (Jika tidak ada pilihan di atas) -->
      <!-- <div class="form-group" v-if="showManualInputJadwal">
        <input 
          type="number"
          v-model="form.jadwal_perawatan"
          class="form-control"
          placeholder="Masukkan Interval Jadwal Perawatan (Bulan)"/>
      </div> -->

      <!-- <div class="form-group">
        <label>Gambar</label>
        <input type="file" @change="handleFileUpload" class="form-control" />
      </div> -->
      <!-- Upload Gambar dengan Drag-and-Drop -->
      <div class="form-group">
        <label for="gambar" style="color: #000;">
          <b>Thumbnail Image</b>
          <sup style="color: red;"> *</sup>
        </label>
        <div 
          class="upload-box-1"
          @dragover.prevent
          @drop.prevent="handleDrop"
          @dragenter="dragActive = true"
          @dragleave="dragActive = false"
          :class="{ 'drag-active': dragActive }"
        >
          <p v-if="!form.gambar">
            <i class="fas fa-image"></i><br>
            Drag and drop here <br>or <br><span class="browse-link">Browse</span>
          </p>
          <p v-else>
            <img :src="previewImage" alt="Preview" class="img-preview" />
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

      <button class="btn btn-primary mb-3 float-right">Simpan</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import vSelect from 'vue-select';

export default {
  name: 'ToolForm',
  components: {
    vSelect,
  },
  data() {
    return {
      form: {
        jenis_id: '',
        kategori_id: '',
        merek_id: '',
        tipe_id: '',
        nama: '',
        layout_id: '',
        stok_awal: '',
        unit: '',
        harga_total: '',
        pembelian: '',
        sumber: '',
        vendor: '',
        fungsi: '',
        deskripsi: '',
        jadwal_perawatan: '',
        gambar: null,
      },
      jenis: [],
      Layout: [],
      kategori: [],
      merek: [],
      tipe: [],
      previewImage: null, // ✅ buat tampilan preview
      dragActive: false,
      showManualInputJadwal: false,
    };
  },
  mounted() {
    this.fetchJenis();
    this.fetchLayout();
  },
  methods: {
    async fetchJenis() {
      const res = await axios.get('/api/v1/jenis');
      this.jenis = res.data;
    },
    async fetchKategori() {
      const res = await axios.get(`/api/v1/kategori?jenis_id=${this.form.jenis_id}`);
      this.kategori = res.data;
      this.merek = [];
      this.tipe = [];
      this.form.kategori_id = '';
    },
    async fetchMerek() {
      const res = await axios.get(`/api/v1/merek?kategori_id=${this.form.kategori_id}`);
      this.merek = res.data;
      this.tipe = [];
      this.form.merek_id = '';
    },
    async fetchTipe() {
      const res = await axios.get(`/api/v1/tipe?merek_id=${this.form.merek_id}&kategori_id=${this.form.kategori_id}`);
      this.tipe = res.data;
      this.form.tipe_id = '';
    },
    async fetchLayout() {
      axios.get('/api/v1/layouts')
      .then(response => {
        this.Layout = response.data;
        console.log(this.Layout);
      })
      .catch(error => {
        console.error(error);
      })
    },
    handleFileUpload(e) {
      this.form.gambar = e.target.files[0];
    },
    async submitForm() {
      const formData = new FormData();
      for (let key in this.form) {
        formData.append(key, this.form[key]);
      }

      try {
        const res = await axios.post('/api/v1/tools', formData);
        Swal.fire({
          title: 'Berhasil!',
          text: 'Data berhasil disimpan.',
          icon: 'success',
          confirmButtonText: 'OK'
        }).then(() => {
          this.$router.push('/manajer-mtc/master-data');
        });
      } catch (err) {
        console.error(err.response.data); // Tambahkan ini
        Swal.fire({
          title: 'Gagal!',
          text: 'Data gagal disimpan.',
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    },
    kembali() {
      this.$router.push('/manajer-mtc/master-data').then(() => {
        window.location.reload();
      });
    },
    setImagePreview(file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        this.previewImage = e.target.result; // ✅ Khusus preview saja
      };
      reader.readAsDataURL(file);
      this.form.gambar = file; // ✅ Simpan file aslinya untuk upload
    },
    onFileChange(event) {
      const file = event.target.files[0];
      if (file && file.size > 2 * 1024 * 1024) {
        Swal.fire("Ukuran gambar terlalu besar", "Maksimal 2MB", "warning");
        return;
      }
      this.setImagePreview(file);
    },
    handleDrop(event) {
      const file = event.dataTransfer.files[0];
      this.setImagePreview(file);
    },
    onJadwalChange(event) {
      if (event.target.value === 'other') {
        this.showManualInputJadwal = true;
        this.form.jadwal_perawatan = '';
      } else {
        this.showManualInputJadwal = false;
        this.form.jadwal_perawatan = '';
      }
    },
  },
};
</script>

<style scoped>
.container {
  max-width: 700px;
}
</style>
