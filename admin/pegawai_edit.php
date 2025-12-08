<?php
include 'auth.php';
include '../koneksi.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add_pemimpin') {
            $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
            $jabatan = mysqli_real_escape_string($koneksi, trim($_POST['jabatan']));
            $slogan = mysqli_real_escape_string($koneksi, trim($_POST['slogan']));
            
            // Cek duplikasi pemimpin berdasarkan nama dan jabatan
            $check_duplicate = mysqli_query($koneksi, "SELECT id FROM profil_pemimpin WHERE nama = '$nama' AND jabatan = '$jabatan'");
            if (mysqli_num_rows($check_duplicate) > 0) {
                $error_message = "Profil pemimpin dengan nama dan jabatan yang sama sudah ada!";
            } else {
                $foto = '';
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $target_dir = "../upload/gambar_pegawai/";
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
                    $foto = uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $foto;
                    
                    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                        $foto = '';
                    }
                }
                
                $query = "INSERT INTO profil_pemimpin (nama, jabatan, foto, slogan) VALUES ('$nama', '$jabatan', '$foto', '$slogan')";
                if (mysqli_query($koneksi, $query)) {
                    $success_message = "Profil pemimpin berhasil ditambahkan!";
                } else {
                    $error_message = "Error: " . mysqli_error($koneksi);
                }
            }
        } elseif ($_POST['action'] == 'edit_pemimpin') {
            $id = (int)$_POST['id'];
            $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
            $jabatan = mysqli_real_escape_string($koneksi, trim($_POST['jabatan']));
            $slogan = mysqli_real_escape_string($koneksi, trim($_POST['slogan']));
            
            // Cek duplikasi pemimpin berdasarkan nama dan jabatan (kecuali data yang sedang diedit)
            $check_duplicate = mysqli_query($koneksi, "SELECT id FROM profil_pemimpin WHERE nama = '$nama' AND jabatan = '$jabatan' AND id != $id");
            if (mysqli_num_rows($check_duplicate) > 0) {
                $error_message = "Profil pemimpin dengan nama dan jabatan yang sama sudah ada!";
            } else {
                $foto_query = "";
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $target_dir = "../upload/gambar_pegawai/";
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
                    $foto = uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $foto;
                    
                    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                        $foto_query = ", foto = '$foto'";
                    }
                }
                
                $query = "UPDATE profil_pemimpin SET nama = '$nama', jabatan = '$jabatan', slogan = '$slogan' $foto_query WHERE id = $id";
                if (mysqli_query($koneksi, $query)) {
                    $success_message = "Profil pemimpin berhasil diperbarui!";
                } else {
                    $error_message = "Error: " . mysqli_error($koneksi);
                }
            }
        } elseif ($_POST['action'] == 'delete_pemimpin') {
            $id = (int)$_POST['id'];
            
            // Get photo filename to delete
            $get_photo = mysqli_query($koneksi, "SELECT foto FROM profil_pemimpin WHERE id = $id");
            $photo_data = mysqli_fetch_assoc($get_photo);
            
            $query = "DELETE FROM profil_pemimpin WHERE id = $id";
            if (mysqli_query($koneksi, $query)) {
                // Delete photo file if exists
                if ($photo_data['foto'] && file_exists("../upload/gambar_pegawai/" . $photo_data['foto'])) {
                    unlink("../upload/gambar_pegawai/" . $photo_data['foto']);
                }
                $success_message = "Profil pemimpin berhasil dihapus!";
            } else {
                $error_message = "Error: " . mysqli_error($koneksi);
            }
        } elseif ($_POST['action'] == 'add') {
            $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
            $jabatan = mysqli_real_escape_string($koneksi, trim($_POST['jabatan']));
            $tentang = mysqli_real_escape_string($koneksi, trim($_POST['tentang']));
            $urutan = (int)$_POST['urutan'];
            
            // Cek duplikasi pegawai berdasarkan nama dan jabatan
            $check_duplicate = mysqli_query($koneksi, "SELECT id FROM pegawai WHERE nama = '$nama' AND jabatan = '$jabatan'");
            if (mysqli_num_rows($check_duplicate) > 0) {
                $error_message = "Data pegawai dengan nama dan jabatan yang sama sudah ada!";
            } else {
                $foto = '';
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $target_dir = "../upload/gambar_pegawai/";
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
                    $foto = uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $foto;
                    
                    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                        $foto = '';
                        $error_message = "Gagal upload file. Pastikan folder '..upload/' ada dan punya izin tulis.";
                    }
                }
                
                if (!isset($error_message)) {
                    $query = "INSERT INTO pegawai (nama, jabatan, foto, urutan, tentang) VALUES ('$nama', '$jabatan', '$foto', $urutan, '$tentang')";
                    if (mysqli_query($koneksi, $query)) {
                        $success_message = "Data pegawai berhasil ditambahkan!";
                    } else {
                        $error_message = "Error: " . mysqli_error($koneksi);
                    }
                }
            }
        } elseif ($_POST['action'] == 'edit') {
            $id = (int)$_POST['id'];
            $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
            $jabatan = mysqli_real_escape_string($koneksi, trim($_POST['jabatan']));
            $tentang = mysqli_real_escape_string($koneksi, trim($_POST['tentang']));
            $urutan = (int)$_POST['urutan'];
            
            // Cek duplikasi pegawai berdasarkan nama dan jabatan (kecuali data yang sedang diedit)
            $check_duplicate = mysqli_query($koneksi, "SELECT id FROM pegawai WHERE nama = '$nama' AND jabatan = '$jabatan' AND id != $id");
            if (mysqli_num_rows($check_duplicate) > 0) {
                $error_message = "Data pegawai dengan nama dan jabatan yang sama sudah ada!";
            } else {
                $foto_query = "";
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $target_dir = "../upload/gambar_pegawai/";
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
                    $foto = uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $foto;
                    
                    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                        $foto_query = ", foto = '$foto'";
                    }
                }
                
                $query = "UPDATE pegawai SET nama = '$nama', jabatan = '$jabatan', tentang = '$tentang', urutan = $urutan $foto_query WHERE id = $id";
                if (mysqli_query($koneksi, $query)) {
                    $success_message = "Data pegawai berhasil diperbarui!";
                } else {
                    $error_message = "Error: " . mysqli_error($koneksi);
                }
            }
        } elseif ($_POST['action'] == 'delete') {
            $id = (int)$_POST['id'];
            
            // Get photo filename to delete
            $get_photo = mysqli_query($koneksi, "SELECT foto FROM pegawai WHERE id = $id");
            $photo_data = mysqli_fetch_assoc($get_photo);
            
            $query = "DELETE FROM pegawai WHERE id = $id";
            if (mysqli_query($koneksi, $query)) {
                // Delete photo file if exists
                if ($photo_data['foto'] && file_exists("../upload/gambar_pegawai/" . $photo_data['foto'])) {
                    unlink("../upload/gambar_pegawai/" . $photo_data['foto']);
                }
                $success_message = "Data pegawai berhasil dihapus!";
            } else {
                $error_message = "Error: " . mysqli_error($koneksi);
            }
        }
    }
}

// Get all staff data
$pegawai_query = mysqli_query($koneksi, "SELECT * FROM pegawai ORDER BY urutan ASC, nama ASC");

// Get all profil pemimpin data
$pemimpin_query = mysqli_query($koneksi, "SELECT * FROM profil_pemimpin ORDER BY id ASC");

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../upload/logo/Logo_MTS.png" type="image/png">
    <title>Kelola Guru & Staff - Admin Panel</title>
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
<body class="bg-gray-50">
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
                <a href="profil_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-school mr-2"></i>Profil
                </a>
                <a href="pegawai_edit.php" class="bg-green-800 text-white block px-3 py-2 rounded-md text-base font-medium">
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
                <a href="profil_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-school mr-2"></i>Profil Sekolah
                </a>
                <a href="pegawai_edit.php" class= "bg-green-800 text-white block px-3 py-2 rounded-md text-base font-medium">
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
                        <h1 class="text-3xl font-bold mb-2">Kelola Guru, Staff & Pemimpin</h1>
                        <p class="text-green-100">Tambah, edit, dan kelola data guru, staff, serta profil pemimpin sekolah</p>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-users text-6xl text-green-200 opacity-50"></i>
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

        <!-- Action Buttons -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="openModal('addModal')" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Guru/Staff Baru
                </button>
                <button onclick="openModal('addPemimpinModal')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center justify-center">
                    <i class="fas fa-crown mr-2"></i>
                    Tambah Profil Pemimpin
                </button>
            </div>
        </div>

        <!-- Profil Pemimpin Section -->
        <div class="mb-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100">
                    <h2 class="text-lg font-semibold text-emerald-800 flex items-center">
                        <i class="fas fa-crown mr-2"></i>
                        Profil Pemimpin Sekolah
                    </h2>
                </div>
                
                <div class="p-6">
                    <?php if (mysqli_num_rows($pemimpin_query) > 0): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php while ($pemimpin = mysqli_fetch_assoc($pemimpin_query)): ?>
                            <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl p-6 border border-emerald-100 card-hover">
                                <div class="text-center">
                                    <?php if ($pemimpin['foto']): ?>
                                        <img src="../upload/gambar_pegawai/<?= $pemimpin['foto'] ?>" alt="<?= $pemimpin['nama'] ?>" class="w-20 h-20 rounded-full object-cover mx-auto mb-4 border-4 border-emerald-200">
                                    <?php else: ?>
                                        <div class="w-20 h-20 rounded-full bg-emerald-200 flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-crown text-emerald-600 text-2xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <h3 class="font-bold text-lg text-gray-900 mb-1"><?= htmlspecialchars($pemimpin['nama']) ?></h3>
                                    <p class="text-emerald-600 font-medium mb-3"><?= htmlspecialchars($pemimpin['jabatan']) ?></p>
                                    
                                    <?php if ($pemimpin['slogan']): ?>
                                    <div class="bg-white rounded-lg p-3 mb-4 border border-emerald-100">
                                        <p class="text-sm text-gray-600 italic">"<?= htmlspecialchars($pemimpin['slogan']) ?>"</p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="flex space-x-2">
                                        <button onclick="editPemimpin(<?= htmlspecialchars(json_encode($pemimpin)) ?>)" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </button>
                                        <button onclick="deletePemimpin(<?= $pemimpin['id'] ?>, '<?= htmlspecialchars($pemimpin['nama']) ?>')" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <i class="fas fa-crown text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada profil pemimpin</h3>
                            <p class="text-gray-500 mb-4">Mulai tambahkan profil pemimpin sekolah Anda</p>
                            <button onclick="openModal('addPemimpinModal')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm">
                                <i class="fas fa-plus mr-2"></i>Tambah Profil Pemimpin Pertama
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Staff List -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-green-50 border-b border-green-100">
                <h2 class="text-lg font-semibold text-green-800 flex items-center">
                    <i class="fas fa-list mr-2"></i>
                    Daftar Guru & Staff
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                            <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tentang</th>
                            <th class="hidden sm:table-cell px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan</th>
                            <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (mysqli_num_rows($pegawai_query) > 0): ?>
                            <?php while ($pegawai = mysqli_fetch_assoc($pegawai_query)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 md:px-6 py-4 whitespace-nowrap">
                                    <?php if ($pegawai['foto']): ?>
                                        <img src="../upload/gambar_pegawai/<?= $pegawai['foto'] ?>" alt="<?= $pegawai['nama'] ?>" class="h-10 w-10 md:h-12 md:w-12 rounded-full object-cover">
                                    <?php else: ?>
                                        <div class="h-10 w-10 md:h-12 md:w-12 rounded-full bg-green-100 flex items-center justify-center">
                                            <i class="fas fa-user text-green-600 text-sm md:text-base"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 md:px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($pegawai['nama']) ?></div>
                                    <div class="md:hidden text-xs text-gray-500 mt-1"><?= htmlspecialchars($pegawai['jabatan']) ?></div>
                                </td>
                                <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($pegawai['jabatan']) ?></div>
                                </td>
                                <td class="hidden lg:table-cell px-6 py-4">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($pegawai['tentang']) ?></div>
                                </td>
                                <td class="hidden sm:table-cell px-3 md:px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <?= $pegawai['urutan'] ?>
                                    </span>
                                </td>
                                <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col sm:flex-row space-y-1 sm:space-y-0 sm:space-x-2">
                                        <button onclick="editStaff(<?= htmlspecialchars(json_encode($pegawai)) ?>)" class="text-green-600 hover:text-green-900 text-xs sm:text-sm">
                                            <i class="fas fa-edit"></i> <span class="hidden sm:inline">Edit</span>
                                        </button>
                                        <button onclick="deleteStaff(<?= $pegawai['id'] ?>, '<?= htmlspecialchars($pegawai['nama']) ?>')" class="text-red-600 hover:text-red-900 text-xs sm:text-sm">
                                            <i class="fas fa-trash"></i> <span class="hidden sm:inline">Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-users text-4xl mb-2 text-gray-300"></i>
                                        <p class="text-sm">Belum ada data guru atau staff</p>
                                        <button onclick="openModal('addModal')" class="mt-2 text-green-600 hover:text-green-800 text-sm">
                                            <i class="fas fa-plus mr-1"></i>Tambah Data Pertama
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>


    </main>

    <!-- Add Modal -->
    <div id="addModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-plus text-green-600 mr-2"></i>
                        Tambah Guru/Staff Baru
                    </h3>
                    <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="add">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                        <input type="text" name="jabatan" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tentang</label>
                        <textarea name="tentang" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Urutan Tampil</label>
                        <input type="number" name="urutan" min="1" value="1" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                        <input type="file" name="foto" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal('addModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
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

    <!-- Edit Modal -->
    <div id="editModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-4 md:top-20 mx-auto p-4 md:p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white max-h-screen overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-edit text-green-600 mr-2"></i>
                        Edit Data Guru/Staff
                    </h3>
                    <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-3 md:space-y-4">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" id="edit_nama" required class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                        <input type="text" name="jabatan" id="edit_jabatan" required class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pengalaman</label>
                        <textarea name="tentang" id="edit_tentang" rows="3" class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                        <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Urutan Tampil</label>
                            <input type="number" name="urutan" id="edit_urutan" min="1" required class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    
                        <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Baru (Opsional)</label>
                            <input type="file" name="foto" accept="image/*" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Kosongkan jika tidak ingin mengubah foto</p>
                    
                    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-4">
                        <button type="button" onclick="closeModal('editModal')" class="w-full sm:w-auto px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
                            Batal
                        </button>
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                            <i class="fas fa-save mr-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-4 md:p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-500 mb-4 px-2">
                    Apakah Anda yakin ingin menghapus data <span id="delete_name" class="font-semibold"></span>?
                    <br>Tindakan ini tidak dapat dibatalkan.
                </p>
                
                <form method="POST" class="inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    
                    <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                        <button type="button" onclick="closeModal('deleteModal')" class="w-full sm:w-auto px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
                            Batal
                        </button>
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                            <i class="fas fa-trash mr-2"></i>Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Pemimpin Modal -->
    <div id="addPemimpinModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-4 md:top-20 mx-auto p-4 md:p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white max-h-screen overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-crown text-emerald-600 mr-2"></i>
                        Tambah Profil Pemimpin
                    </h3>
                    <button onclick="closeModal('addPemimpinModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-3 md:space-y-4">
                    <input type="hidden" name="action" value="add_pemimpin">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" required class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                        <input type="text" name="jabatan" required class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Slogan/Motto</label>
                        <textarea name="slogan" rows="3" class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Masukkan slogan atau motto pemimpin..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                        <input type="file" name="foto" accept="image/*" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <p class="text-xs text-gray-500 mt-1">Format foto: JPG, PNG, GIF. Maksimal 2MB</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-4">
                        <button type="button" onclick="closeModal('addPemimpinModal')" class="w-full sm:w-auto px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
                            Batal
                        </button>
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 text-sm">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Pemimpin Modal -->
    <div id="editPemimpinModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-4 md:top-20 mx-auto p-4 md:p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white max-h-screen overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-crown text-emerald-600 mr-2"></i>
                        Edit Profil Pemimpin
                    </h3>
                    <button onclick="closeModal('editPemimpinModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-3 md:space-y-4">
                    <input type="hidden" name="action" value="edit_pemimpin">
                    <input type="hidden" name="id" id="edit_pemimpin_id">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" id="edit_pemimpin_nama" required class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                        <input type="text" name="jabatan" id="edit_pemimpin_jabatan" required class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Slogan/Motto</label>
                        <textarea name="slogan" id="edit_pemimpin_slogan" rows="3" class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Baru (Opsional)</label>
                        <input type="file" name="foto" accept="image/*" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah foto</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-4">
                        <button type="button" onclick="closeModal('editPemimpinModal')" class="w-full sm:w-auto px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
                            Batal
                        </button>
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 text-sm">
                            <i class="fas fa-save mr-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Pemimpin Modal -->
    <div id="deletePemimpinModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-4 md:p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-500 mb-4 px-2">
                    Apakah Anda yakin ingin menghapus profil <span id="delete_pemimpin_name" class="font-semibold"></span>?
                    <br>Tindakan ini tidak dapat dibatalkan.
                </p>
                
                <form method="POST" class="inline">
                    <input type="hidden" name="action" value="delete_pemimpin">
                    <input type="hidden" name="id" id="delete_pemimpin_id">
                    
                    <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                        <button type="button" onclick="closeModal('deletePemimpinModal')" class="w-full sm:w-auto px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
                            Batal
                        </button>
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
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

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            // Restore body scroll when modal is closed
            document.body.style.overflow = 'auto';
        }

        function editStaff(staff) {
            document.getElementById('edit_id').value = staff.id;
            document.getElementById('edit_nama').value = staff.nama;
            document.getElementById('edit_jabatan').value = staff.jabatan;
            document.getElementById('edit_tentang').value = staff.tentang;
            document.getElementById('edit_urutan').value = staff.urutan;
            openModal('editModal');
        }

        function deleteStaff(id, name) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_name').textContent = name;
            openModal('deleteModal');
        }

        function editPemimpin(pemimpin) {
            document.getElementById('edit_pemimpin_id').value = pemimpin.id;
            document.getElementById('edit_pemimpin_nama').value = pemimpin.nama;
            document.getElementById('edit_pemimpin_jabatan').value = pemimpin.jabatan;
            document.getElementById('edit_pemimpin_slogan').value = pemimpin.slogan || '';
            openModal('editPemimpinModal');
        }

        function deletePemimpin(id, name) {
            document.getElementById('delete_pemimpin_id').value = id;
            document.getElementById('delete_pemimpin_name').textContent = name;
            openModal('deletePemimpinModal');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = ['addModal', 'editModal', 'deleteModal', 'addPemimpinModal', 'editPemimpinModal', 'deletePemimpinModal'];
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