<template>
  <form @submit.prevent="submitForm">
    <h6 class="mb-3 font-weight-bold" style="color: #169ea8; border-radius: 15px;">
      Form Input Peminjaman
      <button type="button" class="close" @click="tutupModal">&times;</button>
    </h6>
    <div class="form-group">
      <label for="tools_id" style="color: #000;">
        <b>Nama Alat/Mesin</b>
        <sup style="color: red;"> *</sup>
      </label>
      <v-select
        v-model="form.tools_id"
        :options="tools"
        label="nama"
        :reduce="tool => tool.id"
        placeholder="Pilih Alat/Mesin"
      />
      <!-- <select v-model="form.tools_id" class="form-control" required>
        <option disabled value="">Pilih alat</option>
        <option v-for="tool in tools" :key="tool.id" :value="tool.id">
          {{ tool.nama }}
        </option>
      </select> -->
    </div>

    <div class="form-group">
      <label for="tgl_pinjam" style="color: #000;">
        <b>Tanggal Peminjaman</b>
        <sup style="color: red;"> *</sup>
      </label>
      <input type="date" v-model="form.tgl_pinjam" class="form-control" required />
    </div>

    <div class="form-group">
      <label for="tgl_kembali" style="color: #000;">
        <b>Tanggal Pengembalian</b>
        <sup style="color: red;"> *</sup>
      </label>
      <input type="date" v-model="form.tgl_kembali" class="form-control" required />
    </div>

    <!-- <div class="form-group">
      <label for="status" style="color: #000;">
        <b>Status</b>
        <sup style="color: red;"> *</sup>
      </label>
      <select v-model="form.status" class="form-control">
        <option value="Belum Diproses">Belum Diproses</option>
        <option value="Digunakan">Digunakan</option>
        <option value="Rusak">Rusak</option>
        Tambahkan status lain sesuai kebutuhan
      </select>
    </div> -->

    <div class="form-group">
      <label for="total" style="color: #000;">
        <b>Jumlah</b>
        <sup style="color: red;"> *</sup>
      </label>
      <input type="number" v-model.number="form.total" class="form-control" min="1" required />
    </div>
    
    <div class="form-group">
      <label for="detail_peminjaman" style="color: #000;">
        <b>Alasan Peminjaman Alat/Mesin</b>
        <sup style="color: red;"> *</sup>
      </label>
      <div class="textarea-wrapper">
        <textarea 
          id="detail_peminjaman"
          v-model="form.detail_peminjaman"
          class="form-control"
          rows="2"
          placeholder="Masukkan Alasan Peminjaman Alat/Mesin (Maksimal 100 Karakter)"
          maxlength="100"
        >
        </textarea>
        <small class="text-muted char-counter">
          {{ form.detail_peminjaman ? form.detail_peminjaman.length : 0 }} / 100
        </small>
      </div>
    </div>

    <div class="row align-items-center justify-content-end">
      <button type="submit" class="btn btn-primary mr-3">Simpan</button>
      <button type="button" class="btn btn-danger" @click="tutupModal">Batal</button>
    </div>
  </form>
</template>
<script>
import axios from 'axios';
import vSelect from 'vue-select';
import Swal from 'sweetalert2';

export default {
  components: {
    vSelect,
  },
  data() {
    return {
      tools: [],
      selectedTool: null,
      form: {
        tools_id: '',
        tgl_pinjam: '',
        tgl_kembali: '',
        detail_peminjaman: '',
        // status: '',
        total: 1
      }
    };
  },
  mounted() {
    this.fetchTools();
  },
  methods: {
    fetchTools() {
      axios.get('/api/v1/tools') // sesuaikan endpoint ini
        .then(response => {
          this.tools = response.data;
        })
        .catch(error => {
          console.error('Gagal mengambil data alat:', error);
        });
    },
    submitForm() {
      axios.post('/api/v1/peminjaman', this.form)
        .then(response => {
          Swal.fire({
            title: 'Berhasil!',
            text: 'Data berhasil disimpan.',
            icon: 'success',
            confirmButtonText: 'OK'
          });
          this.$emit('tutup-modal');
          this.form = {}; // reset form
        })
        .catch(error => {
          console.error('Gagal menyimpan peminjaman:', error.response);
          Swal.fire({
            title: 'Gagal!',
            text: 'Gagal menyimpan peminjaman.',
            icon: 'error',
            confirmButtonText: 'OK'
          });
        });
    },
    tutupModal() {
      this.$emit('tutup-modal');
    },
  }
};
</script>