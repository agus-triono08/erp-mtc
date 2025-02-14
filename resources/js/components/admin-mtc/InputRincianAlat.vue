<template>
  <div>
    <h1>Input Rincian Alat</h1>
    <form @submit.prevent="submitForm">
      <div>
        <label for="namaAlat">Nama Alat:</label>
        <input type="text" v-model="namaAlat" required />
      </div>
      <div>
        <label for="jumlah">Jumlah:</label>
        <input type="number" v-model="jumlah" required />
      </div>
      <div>
        <label for="kondisi">Kondisi:</label>
        <select v-model="kondisi" required>
          <option value="Baik">Baik</option>
          <option value="Rusak">Rusak</option>
        </select>
      </div>
      <button type="submit">Simpan</button>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      namaAlat: '',
      jumlah: 0,
      kondisi: 'Baik',
    };
  },
  methods: {
    submitForm() {
      // Logic to submit the form data to the server
      const data = {
        namaAlat: this.namaAlat,
        jumlah: this.jumlah,
        kondisi: this.kondisi,
      };
      // Make an API call to save the data
      this.$http.post('/api/rincian-alat', data)
        .then(response => {
          // Handle success
          console.log('Data saved:', response.data);
        })
        .catch(error => {
          // Handle error
          console.error('Error saving data:', error);
        });
    },
  },
};
</script>

<style scoped>
/* Add any necessary styles here */
</style>
