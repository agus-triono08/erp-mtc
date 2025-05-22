<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .bg-primary-1 {
            background-color: rgba(22, 158, 168);
            color: #fff;
        }
        .bg-primary-1:hover {
            background-color: rgba(13, 100, 106);
            color: #fff;
        }
        .custom-swal-popup {
            font-family: 'Inter', sans-serif;
        }
        .confirm-button-class {
            background-color: rgba(22, 158, 168) !important;
        }
        .title-class {
            color: #333;
        }
        .icon-class {
            color: rgba(22, 158, 168);
        }
        .custom-text-class {
            color: #666;
        }
    </style>
</head>
<body>
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
                    <form method="POST" action="{{ route('login') }}" id="login">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                                Username
                            </label>
                            <input
                                id="username"
                                type="text"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('username') border-red-500 @enderror"
                                name="username"
                                value="{{ old('username') }}"
                                required
                                autocomplete="username"
                                autofocus
                                placeholder="Enter Username"
                            />
                            @error('username')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                                Password
                            </label>
                            <input
                                id="password"
                                type="password"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Password"
                            />
                            @error('password')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <button
                                class="bg-primary-1 text-white w-full font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline hover:bg-primary-1-dark"
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('login');
        const MAX_ATTEMPTS = 5;
        const COOL_DOWN_TIME = 60 * 1000; // 1 minute
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let attempts = parseInt(localStorage.getItem('loginAttempts')) || 0;
            let lastAttemptTime = parseInt(localStorage.getItem('lastLoginAttempt')) || 0;

            // Check if user is in cool down period
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

            // Validate empty fields
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            if (username === "" || password === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: 'Username atau Password harus diisi.',
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

            // Increment attempts
            attempts++;
            if (attempts > MAX_ATTEMPTS) {
                attempts = 1;
            }
            localStorage.setItem('loginAttempts', attempts);
            localStorage.setItem('lastLoginAttempt', Date.now());

            // Submit form via AJAX
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.removeItem('loginAttempts');
                    localStorage.removeItem('lastLoginAttempt');
                    window.location.href = data.redirect;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal',
                        text: data.message || 'Username atau Password Salah.',
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
                if (error.status === 419) {
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tunggu Sebentar',
                        text: 'Sedang memverifikasi akun',
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
        });
    });
    </script>
</body>
</html>