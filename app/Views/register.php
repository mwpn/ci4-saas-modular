<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - CI4 Modular SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo dan Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full mb-4">
                <i class="fas fa-rocket text-2xl text-indigo-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Daftar Akun</h1>
            <p class="text-white/80">Buat akun baru untuk mengakses platform</p>
        </div>

        <!-- Form Register -->
        <div class="glass-effect rounded-2xl p-8 shadow-2xl">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span><?= session()->getFlashdata('success') ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('register') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>
                
                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-user mr-2"></i>Nama Lengkap
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?= old('name') ?>"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200"
                           placeholder="Masukkan nama lengkap"
                           required>
                    <?php if (session()->getFlashdata('errors')['name'] ?? false): ?>
                        <p class="text-red-400 text-sm mt-1"><?= session()->getFlashdata('errors')['name'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?= old('email') ?>"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200"
                           placeholder="Masukkan email"
                           required>
                    <?php if (session()->getFlashdata('errors')['email'] ?? false): ?>
                        <p class="text-red-400 text-sm mt-1"><?= session()->getFlashdata('errors')['email'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200 pr-12"
                               placeholder="Masukkan password"
                               required>
                        <button type="button" 
                                onclick="togglePassword('password')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                    <?php if (session()->getFlashdata('errors')['password'] ?? false): ?>
                        <p class="text-red-400 text-sm mt-1"><?= session()->getFlashdata('errors')['password'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-lock mr-2"></i>Konfirmasi Password
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200 pr-12"
                               placeholder="Konfirmasi password"
                               required>
                        <button type="button" 
                                onclick="togglePassword('password_confirmation')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="password_confirmation-eye"></i>
                        </button>
                    </div>
                    <?php if (session()->getFlashdata('errors')['password_confirmation'] ?? false): ?>
                        <p class="text-red-400 text-sm mt-1"><?= session()->getFlashdata('errors')['password_confirmation'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Terms & Conditions -->
                <div class="flex items-start">
                    <input type="checkbox" 
                           id="terms" 
                           name="terms" 
                           class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                           required>
                    <label for="terms" class="ml-2 text-sm text-white">
                        Saya menyetujui 
                        <a href="#" class="text-indigo-300 hover:text-indigo-200 underline">Syarat dan Ketentuan</a>
                        dan 
                        <a href="#" class="text-indigo-300 hover:text-indigo-200 underline">Kebijakan Privasi</a>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-user-plus mr-2"></i>
                    Daftar Akun
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-white/80">
                    Sudah punya akun? 
                    <a href="<?= base_url('login') ?>" class="text-indigo-300 hover:text-indigo-200 font-semibold underline">
                        Masuk di sini
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-white/60 text-sm">
                © 2024 CI4 Modular SaaS. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '-eye');
            
            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak sama!');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password minimal 8 karakter!');
                return false;
            }
        });
    </script>
</body>
</html>
