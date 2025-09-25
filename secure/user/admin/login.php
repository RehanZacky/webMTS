<?php
session_start();
include '../../../koneksi.php'; // sesuaikan path jika di luar folder

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' LIMIT 1");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: ../../../admin/dashboard_admin.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../../upload/logo/Logo_MTS.png" type="image/png">
    <title>Portal Admin - Sistem Informasi Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .school-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M30 30c0-11.046-8.954-20-20-20s-20 8.954-20 20 8.954 20 20 20 20-8.954 20-20zm0 0c0 11.046 8.954 20 20 20s20-8.954 20-20-8.954-20-20-20-20 8.954-20 20z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .card-shadow {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .input-focus:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        .btn-hover:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }
        
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        
        .logo-gradient {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #10b981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .border-gradient {
            border-image: linear-gradient(135deg, #10b981, #059669) 1;
        }
        
        .hover-scale:hover {
            transform: scale(1.02);
        }
        
        .input-container {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            transition: color 0.3s ease;
        }
        
        .input-with-icon {
            padding-left: 40px;
        }
        
        .input-container:focus-within .input-icon {
            color: #10b981;
        }
    </style>
</head>
<body class="gradient-bg school-pattern min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md animate-fade-in">
        <!-- Header Card -->
        <div class="bg-white rounded-t-2xl p-8 text-center card-shadow">
            <div class="mb-6">
                <div class="w-20 h-20 logo-gradient rounded-full mx-auto flex items-center justify-center mb-4 hover-scale transition-all duration-300">
                    <i class="fas fa-graduation-cap text-white text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Portal Administrator</h1>
                <p class="text-gray-600 text-sm">Sistem Informasi Manajemen Sekolah</p>
            </div>
            
            <!-- School Info -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 mb-6 border border-green-100">
                <div class="flex items-center justify-center space-x-2 text-gray-700">
                    <i class="fas fa-school text-green-600"></i>
                    <span class="font-medium text-sm">Akses Khusus Administrator</span>
                </div>
            </div>
        </div>

        <!-- Login Form Card -->
        <div class="bg-white rounded-b-2xl p-8 card-shadow">
            <?php if (isset($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                        <p class="text-red-700 text-sm font-medium"><?php echo $error; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username
                    </label>
                    <div class="input-container">
                        <i class="fas fa-user input-icon"></i>
                        <input 
                            type="text" 
                            id="username"
                            name="username" 
                            placeholder="Masukkan username Anda" 
                            required 
                            class="w-full input-with-icon py-3 pr-4 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-200 bg-gray-50 focus:bg-white"
                        />
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="input-container">
                        <i class="fas fa-lock input-icon"></i>
                        <input 
                            type="password" 
                            id="password"
                            name="password" 
                            placeholder="Masukkan password Anda" 
                            required 
                            class="w-full input-with-icon py-3 pr-4 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-200 bg-gray-50 focus:bg-white"
                        />
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 px-4 rounded-xl font-medium btn-hover transition-all duration-200 flex items-center justify-center space-x-2"
                >
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <!-- Footer Info -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="text-center">
                    <p class="text-xs text-gray-500 mb-3 flex items-center justify-center">
                        <i class="fas fa-shield-alt mr-2 text-green-500"></i>
                        Akses terbatas untuk administrator sekolah
                    </p>
                    <div class="grid grid-cols-2 gap-4 text-xs text-gray-400">
                        <div class="flex items-center justify-center space-x-1">
                            <i class="fas fa-clock text-green-500"></i>
                            <span>24/7 Support</span>
                        </div>
                        <div class="flex items-center justify-center space-x-1">
                            <i class="fas fa-phone text-green-500"></i>
                            <span>Hubungi IT</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="mt-6 text-center">
            <p class="text-white text-sm opacity-90 flex items-center justify-center">
                <i class="fas fa-info-circle mr-2"></i>
                Sistem Informasi Sekolah v2.0
            </p>
        </div>
    </div>

    <script>
        // Animation on load
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.animate-fade-in');
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });

        // Enhanced focus effects
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.parentElement.querySelector('label').style.color = '#10b981';
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.parentElement.querySelector('label').style.color = '#374151';
                this.parentElement.style.transform = 'translateY(0)';
            });
        });

        // Button click effect
        const submitBtn = document.querySelector('button[type="submit"]');
        submitBtn.addEventListener('click', function(e) {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = 'translateY(-1px)';
            }, 150);
        });

        // Add floating animation to logo
        const logo = document.querySelector('.logo-gradient');
        setInterval(() => {
            logo.style.transform = 'translateY(-2px)';
            setTimeout(() => {
                logo.style.transform = 'translateY(0)';
            }, 1000);
        }, 2000);
    </script>
</body>
</html>