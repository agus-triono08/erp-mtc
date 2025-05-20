<template>
  <form @submit.prevent="submitForm">
    <h6 class="mb-3 font-weight-bold" style="color: #169ea8; border-radius: 15px;">
      Form Input Permintaan
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
        label="label"
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
      <label for="tgl_permintaan" style="color: #000;">
        <b>Tanggal Permintaan</b>
        <sup style="color: red;"> *</sup>
      </label>
      <input type="date" v-model="form.tgl_permintaan" class="form-control" required />
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
      <label for="detail_permintaan" style="color: #000;">
        <b>Alasan Permintaan Alat/Mesin</b>
        <sup style="color: red;"> *</sup>
      </label>
      <div class="textarea-wrapper">
        <textarea 
          id="detail_permintaan"
          v-model="form.detail_permintaan"
          class="form-control"
          rows="2"
          placeholder="Masukkan Alasan Permintaan Alat/Mesin (Maksimal 100 Karakter)"
          maxlength="100"
        >
        </textarea>
        <small class="text-muted char-counter">
          {{ form.detail_permintaan ? form.detail_permintaan.length : 0 }} / 100
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
        tgl_permintaan: '',
        detail_permintaan: '',
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
      axios.get('/api/v1/tools')
        .then(response => {
          this.tools = response.data.map(tool => {
            const nama = tool?.nama || '-';
            const namaTipe = this.getNamaTipe(tool);
            return {
              ...tool,
              label: `${nama} - ${namaTipe}`
            };
          });
          //console.log(this.tools);
        })
        .catch(error => {
          console.error('Gagal mengambil data alat:', error);
        });
    },
    getNamaTipe(item) {
      const parts = item.kode?.split('-') || [];
      const kodeTipe = parts[3]; // ambil bagian tipe dari kode, misal "T0" dari "1-S3-G0-T0-001"
      const tipe = item.jenis?.kategori
        ?.flatMap(k => k.merek || [])
        .flatMap(m => m.tipe || [])
        .find(t => t.kode_tipe === kodeTipe);
      return tipe ? tipe.nama_tipe : '-';
    },
    submitForm() {
      axios.post('/api/v1/permintaan', this.form)
        .then(response => {
          Swal.fire({
            title: 'Berhasil!',
            text: 'Data berhasil disimpan.',
            icon: 'success',
            confirmButtonText: 'OK'
          });
          this.$emit('tutup-modal');
          this.$emit('refresh-data');
          this.form = {}; // reset form
        })
        .catch(error => {
          console.error('Gagal menyimpan permintaan:', error.response);
          Swal.fire({
            title: 'Gagal!',
            text: 'Gagal menyimpan permintaan.',
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