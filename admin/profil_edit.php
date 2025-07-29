<?php
include 'auth.php';
include '../koneksi.php';

// Handle form submission for profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'update_profil') {
            $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis']);
            $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
            
            // Check if record exists
            $check = mysqli_query($koneksi, "SELECT id FROM profil WHERE jenis = '$jenis'");
            
            if (mysqli_num_rows($check) > 0) {
                // Update existing record
                $query = "UPDATE profil SET isi = '$isi' WHERE jenis = '$jenis'";
            } else {
                // Insert new record
                $query = "INSERT INTO profil (jenis, isi) VALUES ('$jenis', '$isi')";
            }
            
            if (mysqli_query($koneksi, $query)) {
                $success_message = "Profil sekolah berhasil diperbarui!";
            } else {
                $error_message = "Error: " . mysqli_error($koneksi);
            }
        } elseif ($_POST['action'] == 'update_gambar') {
            $nama_file = '';
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                $target_dir = "../upload/gambar_beranda/";
                
                // Create directory if it doesn't exist
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
                $nama_file = uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $nama_file;
                
                // Check if file is an image
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($file_extension, $allowed_types)) {
                    if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                        // Delete old image if exists
                        $old_image_query = mysqli_query($koneksi, "SELECT nama_file FROM gambar_beranda LIMIT 1");
                        if ($old_image = mysqli_fetch_assoc($old_image_query)) {
                            if ($old_image['nama_file'] && file_exists($target_dir . $old_image['nama_file'])) {
                                unlink($target_dir . $old_image['nama_file']);
                            }
                        }
                        
                        // Update or insert new image
                        $check_query = mysqli_query($koneksi, "SELECT id FROM gambar_beranda LIMIT 1");
                        if (mysqli_num_rows($check_query) > 0) {
                            $query = "UPDATE gambar_beranda SET nama_file = '$nama_file' LIMIT 1";
                        } else {
                            $query = "INSERT INTO gambar_beranda (nama_file) VALUES ('$nama_file')";
                        }
                        
                        if (mysqli_query($koneksi, $query)) {
                            $success_message = "Gambar beranda berhasil diperbarui!";
                        } else {
                            $error_message = "Error: " . mysqli_error($koneksi);
                        }
                    } else {
                        $error_message = "Gagal mengupload gambar!";
                    }
                } else {
                    $error_message = "Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.";
                }
            } else {
                $error_message = "Tidak ada file yang dipilih atau terjadi kesalahan upload!";
            }
        }
    }
}

// Get profil data
$profil_data = [];
$profil_query = mysqli_query($koneksi, "SELECT * FROM profil");
while ($row = mysqli_fetch_assoc($profil_query)) {
    $profil_data[$row['jenis']] = $row['isi'];
}

// Get gambar beranda data
$gambar_query = mysqli_query($koneksi, "SELECT * FROM gambar_beranda LIMIT 1");
$gambar_beranda = mysqli_fetch_assoc($gambar_query);

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Sekolah - Admin Panel</title>
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
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal {
            transition: all 0.3s ease;
        }

        .modal.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .modal:not(.hidden) {
            opacity: 1;
            visibility: visible;
        }

    </style>
</head>
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
                <a href="dashboard_admin.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-line mr-2"></i>Dashboard
                </a>
                    <a href="statistik_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-bar mr-2"></i>Statistik
                  </a>
                <a href="profil_edit.php" class="bg-green-800 text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-school mr-2"></i>Profil
                </a>
                <a href="pegawai_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-users mr-2"></i>Guru/Staff
                </a>
                <a href="berita_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-newspaper mr-2"></i>Berita
                </a>
                <a href="prestasi_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-trophy mr-2"></i>Prestasi
                </a>
                <a href="galeri_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-images mr-2"></i>Galeri
                </a>
                        <a href="../logout.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                    </div>
                </div>

                <!-- Right side items -->
                <div class="flex items-center justify-end space-x-4">
                    <!-- User Info -->
                    <div class="flex items-center text-green-100">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-2">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        <span class="text-sm font-medium"><?= $username ?></span>
                    </div>
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
                <a href="statistik_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-bar mr-2"></i>Statistik
                </a>
                <a href="profil_edit.php" class="bg-green-800 text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-school mr-2"></i>Profil Sekolah
                </a>
                <a href="pegawai_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-users mr-2"></i>Guru/Staff
                </a>
                <a href="berita_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-newspaper mr-2"></i>Berita
                </a>
                <a href="prestasi_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-trophy mr-2"></i>Prestasi
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
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">Edit Profil Sekolah</h1>
                        <p class="text-green-100">Kelola informasi profil sekolah dan gambar beranda</p>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-school text-6xl text-green-200 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($success_message)): ?>
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span><?= $success_message ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span><?= $error_message ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Visi -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <h3 class="text-lg font-semibold mb-4 flex items-center text-green-700">
                        <i class="fas fa-eye mr-2"></i>Visi Sekolah
                    </h3>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="update_profil">
                        <input type="hidden" name="jenis" value="visi">
                        <textarea name="isi" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Masukkan visi sekolah..."><?= htmlspecialchars($profil_data['visi'] ?? '') ?></textarea>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan Visi
                        </button>
                    </form>
                </div>

                <!-- Misi -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <h3 class="text-lg font-semibold mb-4 flex items-center text-green-700">
                        <i class="fas fa-bullseye mr-2"></i>Misi Sekolah
                    </h3>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="update_profil">
                        <input type="hidden" name="jenis" value="misi">
                        <textarea name="isi" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Masukkan misi sekolah..."><?= htmlspecialchars($profil_data['misi'] ?? '') ?></textarea>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan Misi
                        </button>
                    </form>
                </div>

                <!-- Sejarah -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <h3 class="text-lg font-semibold mb-4 flex items-center text-green-700">
                        <i class="fas fa-history mr-2"></i>Sejarah Sekolah
                    </h3>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="update_profil">
                        <input type="hidden" name="jenis" value="sejarah">
                        <textarea name="isi" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Masukkan sejarah sekolah..."><?= htmlspecialchars($profil_data['sejarah'] ?? '') ?></textarea>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan Sejarah
                        </button>
                    </form>
                </div>

                <!-- Sambutan Kepala Sekolah -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <h3 class="text-lg font-semibold mb-4 flex items-center text-green-700">
                        <i class="fas fa-video mr-2"></i>Sambutan Kepala Sekolah
                    </h3>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="update_profil">
                        <input type="hidden" name="jenis" value="sambutan_kepala">
                        <input type="url" name="isi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="https://www.youtube.com/watch?v=..." value="<?= htmlspecialchars($profil_data['sambutan_kepala'] ?? '') ?>">
                        <p class="text-xs text-gray-500">Masukkan link video YouTube untuk sambutan kepala sekolah</p>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan Sambutan
                        </button>
                    </form>
                </div>

                <!-- Tag Line -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <h3 class="text-lg font-semibold mb-4 flex items-center text-green-700">
                        <i class="fas fa-quote-right mr-2"></i>Tag Line Sekolah
                    </h3>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="update_profil">
                        <input type="hidden" name="jenis" value="tag_line">
                        <input type="text" name="isi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Masukkan tag line sekolah..." value="<?= htmlspecialchars($profil_data['tag_line'] ?? '') ?>">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan Tag Line
                        </button>
                    </form>
                </div>

                <!-- Gambar Beranda -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold flex items-center text-green-700">
                            <i class="fas fa-images mr-2"></i>Gambar Beranda
                        </h3>
                        <button onclick="openModal('updateGambarModal')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                            <i class="fas fa-edit mr-2"></i>Ganti Gambar Beranda
                        </button>
                    </div>

                    <!-- Current Image Preview -->
                    <div class="mb-4">
                        <?php if ($gambar_beranda && $gambar_beranda['nama_file']): ?>
                            <div class="relative">
                                <img src="../upload/gambar_beranda/<?= $gambar_beranda['nama_file'] ?>" alt="Gambar Beranda Saat Ini" class="w-full h-48 object-cover rounded-md shadow-sm">
                                <div class="absolute top-2 left-2 bg-green-600 text-white px-2 py-1 rounded text-xs font-medium">
                                    Gambar Aktif
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">Gambar beranda saat ini. Klik "Ganti Gambar Beranda" untuk mengubahnya.</p>
                        <?php else: ?>
                            <div class="w-full h-48 bg-gray-100 flex items-center justify-center rounded-md border-2 border-dashed border-gray-300">
                                <div class="text-center">
                                    <i class="fas fa-image text-gray-400 text-3xl mb-2"></i>
                                    <p class="text-gray-500 text-sm">Belum ada gambar beranda</p>
                                    <p class="text-gray-400 text-xs">Klik "Ganti Gambar Beranda" untuk menambahkan</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
        </div>
    </main>

    <!-- Update Gambar Modal -->
    <div id="updateGambarModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-edit text-green-600 mr-2"></i>
                        Ganti Gambar Beranda
                    </h3>
                    <button onclick="closeModal('updateGambarModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="update_gambar">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Gambar Baru</label>
                        <input type="file" name="gambar" accept="image/*" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB</p>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-2"></i>
                            <div>
                                <p class="text-sm text-yellow-800 font-medium">Perhatian!</p>
                                <p class="text-xs text-yellow-700 mt-1">
                                    Gambar beranda yang lama akan diganti dengan gambar baru. 
                                    Tindakan ini tidak dapat dibatalkan.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal('updateGambarModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            <i class="fas fa-save mr-2"></i>Ganti Gambar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = ['updateGambarModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    closeModal(modalId);
                }
            });
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            
            if (mobileMenu && !mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                mobileMenu.classList.add('hidden');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                document.getElementById('mobileMenu').classList.add('hidden');
            }
        });
    </script>
</body>
</html>