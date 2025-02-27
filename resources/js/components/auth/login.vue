<template>
  <div class="h-screen flex items-center justify-center bg-gray-100">
    <div class="flex w-full h-full">
      <!-- Left Image Section -->
      <div class="w-1/2 h-full">
        <img
          alt="Person wearing blue medical gloves"
          class="w-full h-full object-cover"
          height="1000"
          src="https://storage.googleapis.com/a1aa/image/K_IRPPrXque6Q5iWUDYJuJ6XgLh1ZVYEhJZNLOERyuA.jpg"
          width="800"
        />
      </div>
      <!-- Right Form Section -->
      <div class="w-1/2 h-full flex items-center justify-center bg-white">
        <div class="w-3/4">
          <h2 class="text-3xl font-semibold mb-6">Login</h2>
          <form @submit.prevent="handleLogin">
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
                class="bg-teal-600 hover:bg-teal-800 text-white w-full font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                type="submit"
              >
                Login
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="absolute bottom-4 right-4">
      <i class="fas fa-question-circle text-2xl text-gray-500"></i>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: '',
      password: ''
    };
  },
  methods: {
    handleLogin() {
      // Logic for handling login goes here
      console.log('Username:', this.username);
      console.log('Password:', this.password);
      // You can add your authentication logic here
    }
  }
};
</script>