<template>
  <div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-item-center mb-1 mt-4">
      <h1 class="h6 text-teal">
        <i
          class="fas fa-angle-left text-teal mr-2"
          style="cursor: pointer;"
          @click="goBack"
        > Back to Perawatan</i>
      </h1>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h3" style="color: #000;"><b>Detail Perawatan Alat</b></h1>
      <div class="d-flex align-items-center justify-content-center">
        <div class="card shadow" style="max-width: auto; border-radius: 5px;">
          <div class="card-body text-center" style="border-radius: 5px; height: 30px;">
            <p>
              <span class="m-2" style="color: #169ea8;"> Detail Perawatan</span>/
              <span class="mt-2 mb-2 mr-2 ml-1" style="color: #e6494b;">{{ perawatan.no_rawat || '-' }}</span>
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card shadow mb-0" style="border-radius: 10px;">
          <div class="card-body p-0" style="border-radius: 10px;">
            <div class="d-flex justify-content-between align-items-center" style="margin: 10px;">
              <h5 class="m-0 font-weight-bold" style="color: #169ea8;">Perawatan {{ perawatan.no_rawat || '-' }} </h5>              
            </div>
            <div class="row mb-2 mr-2 ml-2 mt-2">
              <div class="col-3">
                <dd>Kode Alat</dd>
                <dt style="color: #000;" class="mb-2">{{ perawatan.alat ? perawatan.alat.kode_alat : '-' }}</dt>
              </div>
              <div class="col-3">
                <dd>PIC</dd>
                <dt style="color: #000;" class="mb-2">{{ perawatan.staff ? perawatan.staff.nama_staff : '-' }}</dt>
              </div>
              <div class="col-3">
                <dd>Tanggal Perawatan</dd>
                <dt style="color: #000;" class="mb-2">{{ perawatan.tanggal_perawatan || '-' }}</dt>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <br>

    <!-- Card dengan tombol Detail -->
    <div class="card shadow mb-4" style="border-radius: 10px;">
      <div class="card-header py-3 mb-2" style="border-radius: 10px;">
        <!-- Tombol Pinjam -->
        <button
          class = "btn btn-sm btn-show m-1"
          :class = "{ active: showRincian }"
          @click = "toggleRincian"
        >
          <span v-if="showRincian">Rincian</span>
          <span v-else>Rincian</span>
        </button>
      </div>

      <!-- Card Konten Detail -->
      <div id="app" v-if="showRincian && perawatan.no_rawat" class="card-body">
        <detail-rincian-perawatan-alat :no-rawat="perawatan.no_rawat"></detail-rincian-perawatan-alat>
      </div>

    </div>

  </div>
</template>
<script>
import axios from 'axios';
export default {
  props: {
    noRawat: String,
  },
  data() {
    return {
      perawatan: {},
      showRincian: true,
    }
  },
  methods: {
    async fetchPerawatan() {
      try {
        const id = this.$route.params.id;
        const response = await axios.get(`/api/perawatan/alat/${id}`);
        this.perawatan = response.data;
        //console.log(this.perawatan);
      } catch (error) {
        console.error(error);
      }
    },
    goBack() {
      this.$router.push('/manajer-mtc/perawatan');
    },
    toggleRincian() {
      this.showRincian = this.showRincian;
    }
  },
  mounted() {
    this.fetchPerawatan();
  }
}      
</script>