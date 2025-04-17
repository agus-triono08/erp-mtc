<template>
  <div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Form Input Data Alat Belum Digunakan</h6>
    </div>
    <form @submit.prevent="submitForm" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-12">
          <label style="color: #000;">Alat/Mesin<sup style="color: red;"> *</sup></label>
          <!-- <select v-model="form.tools_id" class="form-control" required>
            <option value="">Pilih Tools</option>
            <option v-for="tool in tools" :key="tool.id" :value="tool.id">
              {{ tool.nama }}
            </option>
          </select> -->
          <v-select
            v-model="form.tools_id"
            required
            placeholder="Pilih Alat/Mesin"
            :options="tools"
            label="nama"
            :searchable="true"
            :reduce="tool => tool.id"
          />
        </div>

        <div class="col-md-12">
          <label style="color: #000;">Layout<sup style="color: red;"> *</sup></label>
          <!-- <select v-model="form.layout_id" class="form-control" required>
            <option value="">Pilih Layout</option>
            <option v-for="layout in layouts" :key="layout.id" :value="layout.id">
              {{ layout.ruang }}
            </option>
          </select> -->
          <v-select
            v-model="form.layout_id"
            required
            placeholder="Pilih Layout"
            :options="layouts"
            label="ruang"
            :searchable="true"
            :reduce="layout => layout.id"
          />

        </div>

        <div class="col-md-12 mt-2">
          <label style="color: #000;">Interval Jadwal Perawatan (bulan)<sup style="color: red;"> *</sup></label>
          <!-- <input type="number" class="form-control" v-model="form.jadwal_perawatan" min="1"> -->
          <select 
            id="jadwal_perawatan"
            v-model="form.jadwal_perawatan"
            class="form-control"
            required
          >
            <option value="" disabled>Pilih Interval Perawatan</option>
            <option value="1">Setiap 1 Bulan</option>
            <option value="3">Setiap 3 Bulan</option>
            <option value="6">Setiap 6 Bulan</option>
            <option value="12">Setiap 12 Bulan</option>
          </select>
        </div>

        <div class="col-md-6 mt-2">
          <label style="color: #000;">No Seri default<sup style="color: red;"> *</sup></label>
          <input type="text" v-model="form.no_seri_default" class="form-control" required placeholder="Masukkan No Seri Default">
        </div>

        <div class="col-md-6 mt-2">
          <label style="color: #000;">Jumlah Stok Masuk<sup style="color: red;"> *</sup></label>
          <input type="number" class="form-control" v-model="form.stok_awal" min="1" required>
        </div>

        <div class="col-md-6 mt-2">
          <label style="color: #000;">Harga Satuan<sup style="color: red;"> *</sup></label>
          <input type="number" class="form-control" v-model="form.harga" min="0" required placeholder="Masukkan Harga Satuan Alat/Mesin">          
        </div>

        <div class="col-md-6 mt-2">
          <label style="color: #000;">Kondisi<sup style="color: red;"> *</sup></label>
          <!-- <input type="text" class="form-control" v-model="form.kondisi" required> -->
          <select 
            id="kondisi"
            v-model="form.kondisi"
            class="form-control"
            required
          >
            <option value="" disabled selected>Pilih Kondisi</option>
            <option value="OK">OK</option>        
          </select>
        </div>        

        <!-- <div class="col-md-6 mt-2">
          <label>PIC (User)</label>
          <select v-model="form.users_id" class="form-control">
            <option value="">Pilih PIC (optional)</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }}
            </option>
          </select>
        </div> -->        

        <!-- <div class="col-md-12 mt-3">
          <button type="submit" class="btn btn-primary w-100">Simpan No Seri & Jadwal</button>
        </div> -->
      </div>
      <div class="form-group d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary mr-2"><i class="bi bi-floppy"></i> Simpan No Seri & Jadwal</button>
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
  components: { 
    VSelect 
  },
  data() {
    return {
      tools: [],
      layouts: [],
      users: [],
      form: {
        tools_id: '',
        layout_id: '',
        stok_awal: 1,
        harga: '',
        kondisi: '',
        jadwal_perawatan: 1,
        // users_id: '',
        no_seri_default: '',
      }
    };
  },
  mounted() {
    this.fetchInitialData();
  },
  methods: {
    async fetchInitialData() {
      try {
        const [toolsRes, layoutsRes] = await Promise.all([
          axios.get('/api/v1/tools'),
          axios.get('/api/v1/layouts')
          // axios.get('/api/v1/users')
        ]);
        this.tools = toolsRes.data;
        this.layouts = layoutsRes.data;
        // this.users = usersRes.data;
      } catch (err) {
        console.error('Gagal fetch data awal:', err);
      }
    },
    async submitForm() {
      try {
        const res = await axios.post('/api/v1/noseri', this.form);
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
