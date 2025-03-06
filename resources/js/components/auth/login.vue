<template>
  <div class="h-screen flex items-center justify-center bg-gray-100">
    <div class="flex w-full h-full">
      <!-- Left Image Section -->
      <div class="w-3/4 h-full">
        <img
          alt="Maintenance tools and machinery"
          class="w-full h-full object-cover"
          height="1000"
          src="https://storage.googleapis.com/a1aa/image/SixgG-kUIL_MaCdvzimM1cvABg_Cgf95McKToGCgc6c.jpg"
          width="800"
        />
      </div>
      <!-- Right Form Section -->
      <div class="w-1/2 h-full flex items-center justify-center bg-white">
        <div class="w-1/2">
          <h2 class="text-3xl font-semibold mb-6">Login</h2>
          <form id="login" @submit.prevent="handleLogin">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                Username
              </label>
              <input
                v-model="username"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                id="username"
                placeholder="Enter Username"
                type="text"
              />
            </div>
            <div class="mb-6">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                Password
              </label>
              <input
                v-model="password"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
                id="password"
                placeholder="Password"
                type="password"
              />
            </div>
            <div class="flex items-center justify-between">
              <button
                class="bg-primary-1 text-white w-full font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                type="submit"
              >
                Login
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <a href="https://forms.gle/fGiRwPTz4TB8dBFs6" class="report-button" target="_blank">
      <div class="absolute bottom-4 right-4">
        <i class="fas fa-question-circle text-2xl text-gray-500"></i>
      </div>
    </a>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      username: '',
      password: ''
    };
  },
  methods: {
    handleLogin() {
      const MAX_ATTEMPTS = 5;
      const COOL_DOWN_TIME = 60 * 1000;
      let attempts = parseInt(localStorage.getItem('loginAttempts')) || 0;
      let lastAttemptTime = parseInt(localStorage.getItem('lastLoginAttempt')) || 0;

      if (Date.now() - lastAttemptTime < COOL_DOWN_TIME && attempts >= MAX_ATTEMPTS) {
        const remainingTime = Math.round((COOL_DOWN_TIME - (Date.now() - lastAttemptTime)) / 1000);

        Swal.fire({
          icon: 'error',
          title: 'Terlalu Banyak Percobaan',
          text: `Silakan coba lagi setelah ${remainingTime} detik atau Reload Browser.`,
          showCancelButton: false,
          confirmButtonText: 'OK',
          customClass: {
            confirmButton: 'confirm-button-class',
            title: 'title-class',
            icon: 'icon-class',
            popup: 'custom-swal-popup',
            text: 'custom-text-class'
          }
        });
        return;
      }

      if (this.username === "" || this.password === "") {
        Swal.fire({
          icon: 'error',
          title: 'Login Gagal',
          text: `Username atau Password harus diisi.`,
          showCancelButton: false,
          confirmButtonText: 'OK',
          customClass: {
            confirmButton: 'confirm-button-class',
            title: 'title-class',
            icon: 'icon-class',
            popup: 'custom-swal-popup',
            text: 'custom-text-class'
          }
        });
        return;
      }

      attempts++;

      if (attempts > MAX_ATTEMPTS) {
        attempts = 1;
      }

      localStorage.setItem('loginAttempts', attempts);
      localStorage.setItem('lastLoginAttempt', Date.now());

      axios.post('/api/login', {
        username: this.username,
        password: this.password
      })
      .then(response => {
        if (response.data.success) {
          localStorage.removeItem('loginAttempts');
          localStorage.removeItem('lastLoginAttempt');
          window.location.href = response.data.redirect;
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: 'Username atau Password Salah.',
            showCancelButton: false,
            confirmButtonText: 'OK',
            customClass: {
              confirmButton: 'confirm-button-class',
              title: 'title-class',
              icon: 'icon-class',
              popup: 'custom-swal-popup',
              text: 'custom-text-class'
            }
          });
        }
      })
      .catch(error => {
        if (error.response && (error.response.status === 400 || error.response.status === 422)) {
          Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: 'Username atau Password Salah.',
            showCancelButton: false,
            confirmButtonText: 'OK',
            customClass: {
              confirmButton: 'confirm-button-class',
              title: 'title-class',
              icon: 'icon-class',
              popup: 'custom-swal-popup',
              text: 'custom-text-class'
            }
          });
        } else if (error.response && error.response.status === 419) {
          location.reload();
        } else {
          Swal.fire({
            icon: 'warning',
            title: 'Tunggu Sebentar',
            text: 'sedang memverifikasi akun',
            showCancelButton: false,
            confirmButtonText: 'OK',
            customClass: {
              confirmButton: 'confirm-button-class',
              title: 'title-class',
              icon: 'icon-class',
              popup: 'custom-swal-popup',
              text: 'custom-text-class'
            }
          });
        }
      });
    }
  }
};
</script>

<style>
.bg-primary-1 {
  background-color: rgba(22, 158, 168); /* Fix for color transparency */
  color: #fff;
}
.bg-primary-1:hover {
  background-color: rgba(13, 100, 106); /* Fix for color transparency */
  color: #fff;
}
</style>
