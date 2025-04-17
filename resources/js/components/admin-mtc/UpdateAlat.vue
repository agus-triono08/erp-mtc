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
          <label style="color: black;"><b>Produk</b></label>
          <select v-model="form.pembelian" class="form-control">
            <option value="" disabled selected>Pilih Produk Alat</option>
            <option value="Local">Local</option>
            <option value="Import">Import</option>
          </select>
        </div>

        <div class="form-group col-md-6">
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

      <button class="btn btn-primary mb-3 float-right">Simpan</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  name: 'ToolEditForm',
  data() {
    return {
      form: {
        id: '',
        pembelian: '',
        sumber: '',
        vendor: '',
        fungsi: '',
        deskripsi: '',
        gambar: null, // tetap dimasukkan karena ada pengecekan jika diubah
      },
    };
  },
  mounted() {
    this.fetchToolData();
  },
  methods: {
    async fetchToolData() {
      const toolId = this.$route.params.id;
      const res = await axios.get(`/api/v1/tools/${toolId}`);
      const { id, pembelian, sumber, vendor, fungsi, deskripsi } = res.data;
      this.form = { ...this.form, id, pembelian, sumber, vendor, fungsi, deskripsi };
      // console.log(this.form);
    },
    async submitForm() {
      const formData = new FormData();
      const allowedFields = ['pembelian', 'sumber', 'vendor', 'fungsi', 'deskripsi'];

      allowedFields.forEach((field) => {
        formData.append(field, this.form[field] ?? '');
      });

      try {
        await axios.post(`/api/v1/tools/${this.form.id}?_method=PUT`, formData);
        Swal.fire({
          title: 'Berhasil!',
          text: 'Data berhasil diubah.',
          icon: 'success',
          confirmButtonText: 'OK'
        }).then(() => {
          this.$router.push('/admin-mtc/data-alat');
        });
      } catch (err) {
        console.error(err.response?.data);
        Swal.fire({
          title: 'Gagal!',
          text: 'Data gagal diubah.',
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    },
    kembali() {
      this.$router.push('/admin-mtc/data-alat');
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
  display: none;
}

.browse-link {
  color: #007bff;
  cursor: pointer;
}

.img-preview {
  max-width: 100%;
  height: auto;
  margin-top: 10px;
}

.char-counter {
  text-align: right;
  font-size: 0.9em;
  color: #6c757d;
}
</style>
