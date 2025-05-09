<template>
  <div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Form Input Data Alat Belum Digunakan</h6>
    </div>
    <form @submit.prevent="submitForm" enctype="multipart/form-data">
      <div class="row">        
        <div class="col-md-6 mt-2">
          <label for="tgl_kembali" style="color: #000;">
            Tanggal Pengembalian Alat/Mesin
            <sup style="color: red;"> *</sup>
          </label>
          <input type="date" v-model="form.tgl_kembali" class="form-control" required placeholder="Masukkan Tanggal Pengembalian">
        </div>

        <div class="col-md-6 mt-2">
          <label style="color: #000;">Alasan Perubahan Alat/Mesin</label>
          <textarea v-model="form.keterangan_perubahan" required></textarea>
        </div>      
      </div>
      <div class="form-group d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary mr-2"><i class="bi bi-floppy"></i> Simpan</button>
        <button type="button" class="btn btn-danger" @click="tutupModal"><i class="bi bi-x-circle"></i> Tutup</button>
      </div>
    </form>
  </div>
</template>

<script>
import { reduce } from 'lodash';
import VSelect from 'vue-select';
import Swal from 'sweetalert2';

export default {
  props: {
    noPinjam: String
  },
  components: { 
    VSelect 
  },
  data() {
    return {
      // tools: [],
      // layouts: [],
      // users: [],
      peminjaman: [],
      form: {
        peminjaman_id: '', // akan diisi setelah fetch dari API
        tgl_kembali: '',
        keterangan_perubahan: '',
        status: '',
      }
    };
  },
  mounted() {
    this.fetchInitialData();
  },
  methods: {
    async fetchInitialData() {
      try {
        const [peminjamanRes] = await Promise.all([
          // axios.get('/api/v1/tools'),
          // axios.get('/api/v1/layouts')
          // axios.get('/api/v1/users')
          axios.get('/api/v1/peminjaman')
        ]);
        // this.tools = toolsRes.data;
        // this.layouts = layoutsRes.data;
        // this.users = usersRes.data;
        this.peminjaman = peminjamanRes.data;
        console.log(peminjamanRes);
      } catch (err) {
        console.error('Gagal fetch data awal:', err);
      }
    },
    async submitForm() {
      try {
        const noPinjam = this.noPinjam; // Kode alat di URL
        console.log(this.noPinjam);

        // Konversi waktu perawatan dari format time ke integer (menit)
        // const [hours, minutes] = this.form.waktu_perawatan.split(':');
        // this.form.waktu_perawatan = parseInt(hours) * 60 + parseInt(minutes); // Mengonversi ke menit

        const res = await axios.post(`/api/v1/perubahan-peminjaman/store/${noPinjam}`, this.form);
        Swal.fire({
          title: 'Berhasil!',
          text: 'No seri & jadwal berhasil disimpan.',
          icon: 'success',
          confirmButtonText: 'OK'
        });
        this.$emit('tutup-modal');
        // this.resetForm();
        // this.$emit('success', res.data); // jika ingin refresh list dari parent
      } catch (err) {
        if (err.response && err.response.status === 422) {
          Swal.fire({
            title: 'Validasi Gagal!',
            text: 'Silakan periksa kembali inputan Anda.',
            icon: 'error',
            confirmButtonText: 'OK'
          });
        } else if (err.response && err.response.status >= 400 && err.response.status < 500) {
          Swal.fire({
            title: 'Gagal!',
            text: 'Gagal menyimpan data.',
            icon: 'error',
            confirmButtonText: 'OK'
          });
        } else {
          console.error(err);
        }
      }
    },
    // resetForm() {
    //   this.form = {
    //     tools_id: '',
    //     layout_id: '',
    //     stok_awal: 1,
    //     harga: '',
    //     kondisi: '',
    //     jadwal_perawatan: 1,
    //     // users_id: '',
    //     no_seri_default: ''
    //   };
    // },
    tutupModal() {
      Swal.fire({
        title: 'Tutup?',
        text: 'Apakah Anda yakin ingin menutup form?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, tutup!',
        cancelButtonText: 'Tidak, batalkan!'
      }).then((result) => {
        if (result.value) {
          this.$emit('tutup-modal');
        }
      });
    },
  }
};
</script>

<style scoped>
label {
  font-weight: 600;
}
</style>
