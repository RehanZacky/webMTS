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
        } elseif ($_POST['action'] == 'add_gambar') {
            $nama_file = '';
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                $target_dir = "upload/";
                $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
                $nama_file = uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $nama_file;
                
                if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                    // File uploaded successfully
                } else {
                    $nama_file = '';
                }
            }
            
            if ($nama_file) {
                $query = "INSERT INTO gambar_beranda (nama_file) VALUES ('$nama_file')";
            } else {
                $error_message = "Gagal mengupload gambar!";
            }
            
            if ($nama_file && mysqli_query($koneksi, $query)) {
                $success_message = "Gambar beranda berhasil ditambahkan!";
            } elseif (!isset($error_message)) {
                $error_message = "Error: " . mysqli_error($koneksi);
            }
        } elseif ($_POST['action'] == 'delete_gambar') {
            $id = (int)$_POST['id'];
            
            // Get image filename to delete
            $get_image = mysqli_query($koneksi, "SELECT nama_file FROM gambar_beranda WHERE id = $id");
            $image_data = mysqli_fetch_assoc($get_image);
            
            $query = "DELETE FROM gambar_beranda WHERE id = $id";
            if (mysqli_query($koneksi, $query)) {
                // Delete image file if exists
                if ($image_data['nama_file'] && file_exists("upload/" . $image_data['nama_file'])) {
                    unlink("upload/" . $image_data['nama_file']);
                }
                $success_message = "Gambar beranda berhasil dihapus!";
            } else {
                $error_message = "Error: " . mysqli_error($koneksi);
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
$gambar_query = mysqli_query($koneksi, "SELECT * FROM gambar_beranda ORDER BY id DESC");

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
                        <a href="index.php" class="text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-chart-line mr-2"></i>Dashboard
                        </a>
                        <a href="berita_edit.php" class="text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-newspaper mr-2"></i>Kelola Berita
                        </a>
                        <a href="statistik_edit.php" class="text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-chart-bar mr-2"></i>Statistik
                        </a>
                        <a href="profil_edit.php" class="bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-school mr-2"></i>Profil Sekolah
                        </a>
                        <a href="prestasi_edit.php" class="text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-trophy mr-2"></i>Prestasi
                        </a>
                        <a href="pegawai_edit.php" class="text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-users mr-2"></i>Guru & Staff
                        </a>
                        <a href="#" class="text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
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
                <a href="index.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-line mr-2"></i>Dashboard
                </a>
                <a href="berita_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-newspaper mr-2"></i>Kelola Berita
                </a>
                <a href="statistik_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-bar mr-2"></i>Statistik
                </a>
                <a href="profil_edit.php" class="bg-green-800 text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-school mr-2"></i>Profil Sekolah
                </a>
                <a href="prestasi_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-trophy mr-2"></i>Prestasi
                </a>
                <a href="pegawai_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-users mr-2"></i>Guru & Staff
                </a>
                <a href="#" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
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
                        <button onclick="openModal('addGambarModal')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                            <i class="fas fa-plus mr-2"></i>Tambah
                        </button>
                    </div>

                    <!-- Images Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <?php if (mysqli_num_rows($gambar_query) > 0): ?>
                    <?php while ($gambar = mysqli_fetch_assoc($gambar_query)): ?>
                        <div class="relative group">
                        <div class="relative">
                            <?php if ($gambar['nama_file']): ?>
                                    <img src="upload/<?= $gambar['nama_file'] ?>" alt="Gambar Beranda" class="w-full h-24 object-cover rounded-md">
                            <?php else: ?>
                                    <div class="w-full h-24 bg-green-100 flex items-center justify-center rounded-md">
                                        <i class="fas fa-image text-green-600 text-2xl"></i>
                                </div>
                            <?php endif; ?>
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-md flex items-center justify-center">
                                    <button onclick="deleteGambar(<?= $gambar['id'] ?>)" class="text-white hover:text-red-300 p-2">
                                        <i class="fas fa-trash text-lg"></i>
                                    </button>
                                </div>
                        </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                        <div class="col-span-full text-center py-8">
                            <i class="fas fa-images text-gray-300 text-3xl mb-2"></i>
                            <p class="text-gray-500 text-sm">Belum ada gambar</p>
                    </div>
                <?php endif; ?>
            </div>
                </div>
        </div>
    </main>

    <!-- Add Gambar Modal -->
    <div id="addGambarModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-plus text-green-600 mr-2"></i>
                        Tambah Gambar Beranda
                    </h3>
                    <button onclick="closeModal('addGambarModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="add_gambar">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar</label>
                        <input type="file" name="gambar" accept="image/*" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal('addGambarModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Delete Gambar Modal -->
    <div id="deleteGambarModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Apakah Anda yakin ingin menghapus gambar ini?
                    <br>Tindakan ini tidak dapat dibatalkan.
                </p>
                
                <form method="POST" class="inline">
                    <input type="hidden" name="action" value="delete_gambar">
                    <input type="hidden" name="id" id="delete_gambar_id">
                    
                    <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                        <button type="button" onclick="closeModal('deleteGambarModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            <i class="fas fa-trash mr-2"></i>Hapus
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


        function deleteGambar(id) {
            document.getElementById('delete_gambar_id').value = id;
            openModal('deleteGambarModal');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = ['addGambarModal', 'deleteGambarModal'];
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