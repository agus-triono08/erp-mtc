<template>
  <div class="container-fluid" style="width: 700px;">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold" style="color: #169ea8; border-radius: 15px;">
        Form Input Peminjaman
        <button type="button" class="close" @click="tutupModal">&times;</button>
      </h6>
    </div>
    <div class="card-body" style="border-radius: 15px;">
      <form @submit.prevent="submitAlat">
        <!-- Jenis Alat -->
        <div class="form-group">
          <label for="jenis">Jenis</label>
          <select v-model="form.jenis" id="jenis" class="form-control" @change="updateKodeOptions">
            <option value="" disabled selected>Pilih Jenis Alat</option>
            <option value="alat">Alat</option>
            <option value="mesin">Mesin</option>
          </select>
        </div>

        <!-- Kode Alat -->
        <div class="form-group">
          <label for="kode">Kode</label>
          <select v-model="form.kode" id="kode" class="form-control" @change="updateNoSeriOptions" :disabled="!form.jenis">
            <option value="" disabled selected>Pilih Kode</option>
            <option v-for="kode in kodeOptions" :key="kode" :value="kode">{{ kode }}</option>
          </select>
        </div>

        <!-- No Seri -->
        <div class="form-group">
          <label for="noSeri">No Seri</label>
          <select v-model="form.noSeri" id="noSeri" class="form-control" :disabled="!form.kode">
            <option value="" disabled selected>Pilih No Seri</option>
            <option v-for="noSeri in noSeriOptions" :key="noSeri" :value="noSeri">{{ noSeri }}</option>
          </select>
        </div>

        <!-- Tanggal Pinjam -->
        <div class="form-group">
          <label for="tanggalPinjam">Tanggal Pinjam</label>
          <input type="date" v-model="form.tanggalPinjam" id="tanggalPinjam" class="form-control" required />
        </div>

        <!-- Tanggal Kembali -->
        <div class="form-group">
          <label for="tanggalKembali">Tanggal Kembali</label>
          <input type="date" v-model="form.tanggalKembali" id="tanggalKembali" class="form-control" required />
        </div>

        <!-- Tujuan Peminjaman (Text Area) -->
        <div class="form-group">
          <label for="tujuanPeminjaman">Tujuan Peminjaman</label>
          <textarea v-model="form.tujuanPeminjaman" id="tujuanPeminjaman" class="form-control" placeholder="Masukkan Tujuan Peminjaman" rows="4" required></textarea>
        </div>

        <!-- Submit Button -->
        <div class="row align-items-center justify-content-end">
          <button type="submit" class="btn btn-primary mr-3">Submit</button>
          <button type="button" class="btn btn-danger" @click="tutupModal">Batal</button>
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
        jenis: '',
        kode: '',
        noSeri: '',
        tanggalPinjam: '',
        tanggalKembali: '',
        tujuanPeminjaman: '',
      },
      kodeOptions: [],
      noSeriOptions: [],
      alatMesinData: {
        alat: {
          kode: ['A001', 'A002', 'A003'],
          noSeri: {
            A001: ['S001', 'S002'],
            A002: ['S003', 'S004'],
            A003: ['S005', 'S006'],
          }
        },
        mesin: {
          kode: ['M001', 'M002', 'M003'],
          noSeri: {
            M001: ['M001-1', 'M001-2'],
            M002: ['M002-1', 'M002-2'],
            M003: ['M003-1', 'M003-2'],
          }
        }
      }
    }
  },
  methods: {
    tutupModal() {
      this.$emit('tutup-modal');
    },

    // Update the kode options based on the jenis selected
    updateKodeOptions() {
      if (this.form.jenis) {
        this.kodeOptions = this.alatMesinData[this.form.jenis].kode;
        this.form.kode = ''; // Reset kode and noSeri when jenis changes
        this.form.noSeri = '';
        this.noSeriOptions = [];
      }
    },

    // Update the no seri options based on the kode selected
    updateNoSeriOptions() {
      if (this.form.kode) {
        this.noSeriOptions = this.alatMesinData[this.form.jenis].noSeri[this.form.kode] || [];
        this.form.noSeri = ''; // Reset noSeri when kode changes
      }
    },

    submitAlat() {
      // Here, you can submit the form data, for example, to an API.
      console.log('Form Submitted:', this.form);
      // Reset form data
      this.form = {
        jenis: '',
        kode: '',
        noSeri: '',
        tanggalPinjam: '',
        tanggalKembali: '',
        tujuanPeminjaman: '',
      };
      this.kodeOptions = [];
      this.noSeriOptions = [];
    }
  }
}
</script>
