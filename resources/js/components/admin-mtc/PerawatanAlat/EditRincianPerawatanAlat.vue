<template>
  <div class="container-fluid" style="width: 700px;">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold" style="color: #169ea8; border-radius: 15px;">Form Edit Rincian Perawatan Alat</h6>
    </div>

    <div class="card-body" style="border-radius: 15px;">
      <form>
        <!-- No Seri Alat -->
        <div class="form-group">
          <label for="no_seri_alat" style="color: #000;">
            <b>No Seri Alat</b>
          </label>
          <select id="no_seri_alat" class="form-control" v-model="NoSeris" required disabled>
            <option value="">Pilih No Seri Alat</option>
            <option v-for="no_seri in noseris" :key="no_seri.id" :value="no_seri.no_seri_alat">{{ no_seri.no_seri_alat }}</option>
          </select>
        </div>
      </form>
    </div>

    <div class="row">
      <!-- Waktu Mulai -->
      <div class="form-group col-md-6">
        <label for="waktu_mulai" style="color: #000;">
          <b>Waktu Mulai</b>
          <sup style="color: red;"> *</sup>
        </label>
        <input type="time" class="form-control" id="waktu_mulai" v-model="perawatan.waktu_mulai"/>
      </div>

      <!-- Waktu Selesai -->
      <div class="form-group col-md-6">
        <label for="waktu_selesai" style="color: #000;">
          <b>Waktu Selesai</b>
          <sup style="color: red;"> *</sup>
        </label>
        <input type="time" class="form-control" id="waktu_mulai" v-model="perawatan.waktu_selesai"/>
      </div>
    </div>    

    <!-- Detail Perawatan -->
    <div class="form-group">
      <label for="detail_perawatan" style="color: #000;">
        <b>Detail Perawatan</b>
        <sup style="color: red;"> *</sup>
      </label>
      <div class="textarea-wrapper">
        <textarea
          id="detail-perawatan"
          class="form-control"
          v-model="perawatan.detail_perawatan"
          rows="3"
          required
          placeholder="Masukkan Detail Perawatan (Maksimal 500 karakter)"
          maxlength="500"
        >
        </textarea>
        <small class="text-muted char-counter">
          {{ perawatan.detail_perawatan ? perawatan.detail_perawatan.length : 0 }} / 500
        </small>
      </div>
    </div>

    <!-- Kondisi -->
    <div class="form-group">
      <label for="kondisi" style="color: #000;">
        <b>Kondisi</b>
        <sup style="color: red;"> *</sup>
      </label>
      <select id="status" class="form-control" v-model="StatusNo" required>
        <option value="">Pilih Kondisi</option>
        <option value="Ready">Ready</option>
        <option value="Error">Error</option>
        <option value="Rusak">Rusak</option>
      </select>
    </div>

    <!-- Status -->
    <div class="form-group">
      <label for="status" style="color: #000;">
        <b>Status</b>
        <sup style="color: red;"> *</sup>
      </label>
      <select id="status" class="form-control" v-model="perawatan.status" required>
        <option value="">Pilih Status</option>
        <option value="Sudah">Sudah</option>
        <option value="Belum">Belum</option>
      </select>
    </div>

    <!-- Tombol Aksi -->
    <div class="row align-items-center justify-content-end mr-3 mt-4 mb-2">
      <div class="form-group">
        <button type="submit" class="btn btn-plus mr-2">
          <i class="fas fa-save"></i> Simpan
        </button>
        <button @click="tutupModal" type="button" class="btn btn-danger">
          <i class="fas fa-times"></i> Batal
        </button>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios';
export default {
  data() {
    return {
      NoSeris: '',
      StatusNo: '',
      noseris: [],
      perawatan: [],
    }
  },
  methods: {
    tutupModal() {
      this.$emit('tutup-modal');
    },
    async fetchPerawatan() {
      const id = this.$route.params.id;
      try {
        const response = await axios.get(`/api/perawatan/rincian-alat/${id}/edit`);
        this.perawatan = response.data.data;
        //console.log(this.perawatan);
      } catch (error) {
        console.error(error);
      }
    },
    async fetchDataNoSeri() {
        try {
          const response = await axios.get('/api/no-seri');
          this.noseris = response.data.data;
          //console.log(this.noseris)
        } catch (error) {
          console.error(error);
        }
    },
  },  
  mounted() {
    this.fetchDataNoSeri();
    this.fetchPerawatan();
  },
  watch: {
    perawatan: {
      handler(newVal) { // Use 'newVal' instead of 'val'
        this.NoSeris = newVal.noseri ? newVal.noseri.no_seri_alat : '';
        this.StatusNo = newVal.noseri ? newVal.noseri.status : '';
      },
      deep: true
    }
  }
}
</script>