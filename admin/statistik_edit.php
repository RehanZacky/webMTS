<?php
include 'auth.php';
include '../koneksi.php';

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['nilai'] as $id => $nilai) {
        $id = intval($id);
        $nilai = htmlspecialchars($nilai); // cegah XSS
        mysqli_query($koneksi, "UPDATE info_statistik SET nilai = '$nilai' WHERE id = $id");
    }
    $success = "Data statistik berhasil diperbarui.";
}

// Ambil data
$data = mysqli_query($koneksi, "SELECT * FROM info_statistik ORDER BY id ASC");
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Sekolah - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            transform: scale(1.02);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .success-alert {
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Top Navigation -->
    <nav class="bg-gradient-to-r from-green-600 to-emerald-600 shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold text-white flex items-center">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            Admin Panel
                        </h1>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="dashboard_admin.php" class="text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-chart-line mr-2"></i>Dashboard
                        </a>
                        <a href="berita_edit.php" class="text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-newspaper mr-2"></i>Kelola Berita
                        </a>
                        <a href="statistik_edit.php" class="bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-chart-bar mr-2"></i>Statistik
                        </a>
                        <a href="profil_edit.php" class="text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-school mr-2"></i>Profil Sekolah
                        </a>
                        <a href="prestasi_edit.php" class="text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-trophy mr-2"></i>Prestasi
                        </a>
                        <a href="pegawai_edit.php" class="text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-users mr-2"></i>Guru & Staff
                        </a>
                        <a href="galeri_edit.php" class="text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-images mr-2"></i>Galeri
                        </a>
                    </div>
                </div>

                <!-- Right side items -->
                <div class="flex items-center space-x-4">
                    <!-- User Info -->
                    <div class="flex items-center text-green-100">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-2">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        <span class="text-sm font-medium"><?= $username ?></span>
                    </div>
                    <a href="../logout.php" class="text-green-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobileMenuBtn" class="text-green-100 hover:text-white p-2">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobileMenu" class="md:hidden hidden bg-green-700">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="dashboard_admin.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-line mr-2"></i>Dashboard
                </a>
                <a href="berita_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-newspaper mr-2"></i>Kelola Berita
                </a>
                <a href="statistik_edit.php" class="bg-green-800 text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-bar mr-2"></i>Statistik
                </a>
                <a href="profil_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-school mr-2"></i>Profil Sekolah
                </a>
                <a href="prestasi_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-trophy mr-2"></i>Prestasi
                </a>
                <a href="pegawai_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-users mr-2"></i>Guru & Staff
                </a>
                <a href="galeri_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-images mr-2"></i>Galeri
                </a>
                <div class="border-t border-green-600 pt-4">
                    <a href="../logout.php" class="text-red-300 hover:bg-red-600 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl shadow-lg p-6 text-white animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold mb-2 flex items-center">
                            <i class="fas fa-chart-bar mr-3"></i>
                            Kelola Statistik Sekolah
                        </h1>
                        <p class="text-green-100">Perbarui data statistik yang ditampilkan di halaman utama website</p>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-chart-pie text-6xl text-green-200 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Alert -->
        <?php if (isset($success)): ?>
        <div class="mb-6 success-alert">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800"><?= $success ?></p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button onclick="this.parentElement.parentElement.parentElement.parentElement.style.display='none'" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Main Form Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden animate-fade-in" style="animation-delay: 0.2s;">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-edit text-green-500 mr-2"></i>
                    Edit Data Statistik
                </h2>
                <p class="text-sm text-gray-600 mt-1">Masukkan nilai terbaru untuk setiap kategori statistik</p>
            </div>

            <form method="POST" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php 
                    $counter = 0;
                    while ($row = mysqli_fetch_assoc($data)) : 
                        $counter++;
                        $icons = [
                            'fas fa-users',
                            'fas fa-chalkboard-teacher', 
                            'fas fa-user-tie',
                            'fas fa-building'
                        ];
                        $colors = [
                            'text-blue-500 bg-blue-50',
                            'text-green-500 bg-green-50',
                            'text-purple-500 bg-purple-50',
                            'text-orange-500 bg-orange-50'
                        ];
                        $iconClass = $icons[($counter - 1) % 4] ?? 'fas fa-chart-bar';
                        $colorClass = $colors[($counter - 1) % 4] ?? 'text-gray-500 bg-gray-50';
                    ?>
                    <div class="card-hover">
                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <div class="w-8 h-8 rounded-full <?= $colorClass ?> flex items-center justify-center mr-3">
                                    <i class="<?= $iconClass ?> text-sm"></i>
                                </div>
                                <?= htmlspecialchars($row['label']) ?>
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    name="nilai[<?= $row['id'] ?>]" 
                                    value="<?= htmlspecialchars($row['nilai']) ?>" 
                                    class="input-focus w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-lg font-semibold text-gray-800 bg-white shadow-sm" 
                                    required
                                    placeholder="Masukkan nilai..."
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-hashtag text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <button 
                        type="submit" 
                        class="flex-1 sm:flex-none bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold py-3 px-8 rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center"
                    >
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                    
                    <a 
                        href="dashboard_admin.php" 
                        class="flex-1 sm:flex-none bg-gray-100 text-gray-700 font-semibold py-3 px-8 rounded-lg hover:bg-gray-200 transition-all duration-300 flex items-center justify-center border border-gray-300"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
            </form>
        </div>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Form validation and enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = document.querySelectorAll('input[type="text"]');

            // Add number formatting
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Remove non-numeric characters except for spaces and commas
                    let value = this.value.replace(/[^\d\s,]/g, '');
                    this.value = value;
                });

                // Add focus effects
                input.addEventListener('focus', function() {
                    this.parentElement.parentElement.classList.add('transform', 'scale-105');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.parentElement.classList.remove('transform', 'scale-105');
                });
            });

            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
                submitBtn.disabled = true;
                
                // Re-enable after a short delay to prevent double submission
                setTimeout(() => {
                    if (!form.checkValidity()) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                }, 100);
            });
        });

        // Auto-hide success message
        setTimeout(() => {
            const successAlert = document.querySelector('.success-alert');
            if (successAlert) {
                successAlert.style.opacity = '0';
                successAlert.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    successAlert.style.display = 'none';
                }, 300);
            }
        }, 5000);
    </script>
</body>
</html>