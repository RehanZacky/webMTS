<?php
include 'auth.php';
include '../koneksi.php';

// Hapus berita
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $cek = mysqli_query($koneksi, "SELECT gambar_utama FROM berita WHERE id = $id");
    $row = mysqli_fetch_assoc($cek);
    if ($row && file_exists("../upload/" . $row['gambar_utama'])) {
        unlink("../upload/" . $row['gambar_utama']);
    }
    mysqli_query($koneksi, "DELETE FROM berita WHERE id = $id");
    header("Location: berita_edit.php");
    exit;
}

// Tambah berita
if (isset($_POST['tambah'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
    $penulis = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $video_youtube = mysqli_real_escape_string($koneksi, $_POST['video_youtube']);
    $tanggal = date("Y-m-d");

    $gambar_utama = "";
    if ($_FILES['gambar']['name']) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar_utama = "berita_" . time() . "." . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../upload/$gambar_utama");
    }

    mysqli_query($koneksi, "INSERT INTO berita (judul, isi, penulis, tanggal_post, gambar_utama, video_youtube)
        VALUES ('$judul', '$isi', '$penulis', '$tanggal', '$gambar_utama', '$video_youtube')");
    header("Location: berita_edit.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
            $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
            $penulis = mysqli_real_escape_string($koneksi, $_POST['penulis']);
            $tanggal_post = date('Y-m-d H:i:s');
            
            $gambar_utama = '';
            if (isset($_FILES['gambar_utama']) && $_FILES['gambar_utama']['error'] == 0) {
                $target_dir = "upload/";
                $file_extension = strtolower(pathinfo($_FILES["gambar_utama"]["name"], PATHINFO_EXTENSION));
                $gambar_utama = uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $gambar_utama;
                
                if (move_uploaded_file($_FILES["gambar_utama"]["tmp_name"], $target_file)) {
                    // File uploaded successfully
                } else {
                    $gambar_utama = '';
                }
            }
            
            $query = "INSERT INTO berita (judul, isi, penulis, gambar_utama, tanggal_post) VALUES ('$judul', '$isi', '$penulis',  '$gambar_utama', '$tanggal_post')";
            if (mysqli_query($koneksi, $query)) {
                $success_message = "Berita berhasil ditambahkan!";
            } else {
                $error_message = "Error: " . mysqli_error($koneksi);
            }
        } elseif ($_POST['action'] == 'edit') {
            $id = (int)$_POST['id'];
            $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
            $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
            $penulis = mysqli_real_escape_string($koneksi, $_POST['penulis']);
            
            $gambar_query = "";
            if (isset($_FILES['gambar_utama']) && $_FILES['gambar_utama']['error'] == 0) {
                $target_dir = "upload/";
                $file_extension = strtolower(pathinfo($_FILES["gambar_utama"]["name"], PATHINFO_EXTENSION));
                $gambar_utama = uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $gambar_utama;
                
                if (move_uploaded_file($_FILES["gambar_utama"]["tmp_name"], $target_file)) {
                    $gambar_query = ", gambar_utama = '$gambar_utama'";
                }
            }
            
            $query = "UPDATE berita SET judul = '$judul', isi = '$isi', penulis = '$penulis' $gambar_query WHERE id = $id";
            if (mysqli_query($koneksi, $query)) {
                $success_message = "Berita berhasil diperbarui!";
            } else {
                $error_message = "Error: " . mysqli_error($koneksi);
            }
        } elseif ($_POST['action'] == 'delete') {
            $id = (int)$_POST['id'];
            
            // Get image filename to delete
            $get_image = mysqli_query($koneksi, "SELECT gambar_utama FROM berita WHERE id = $id");
            $image_data = mysqli_fetch_assoc($get_image);
            
            $query = "DELETE FROM berita WHERE id = $id";
            if (mysqli_query($koneksi, $query)) {
                // Delete image file if exists
                if ($image_data['gambar_utama'] && file_exists("upload/" . $image_data['gambar_utama'])) {
                    unlink("upload/" . $image_data['gambar_utama']);
                }
                $success_message = "Berita berhasil dihapus!";
            } else {
                $error_message = "Error: " . mysqli_error($koneksi);
            }
        }
    }
}

// Get all news data
$berita_query = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC");

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            transition: transform 0.3s ease-in-out;
            width: 280px;
        }
        
        .sidebar-closed {
            transform: translateX(-100%);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1000;
            }
            
            .sidebar.sidebar-open {
                transform: translateX(0);
            }
            
            .main-content {
                padding-top: 64px; /* Space for fixed header */
            }
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

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .menu-item {
            transition: all 0.2s ease;
        }

        .menu-item:hover {
            transform: translateX(5px);
        }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }

        .sidebar-overlay {
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar-overlay.active {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }
            
            .grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3 {
                grid-template-columns: 1fr;
            }
            
            .text-3xl {
                font-size: 1.5rem;
            }
            
            .p-6 {
                padding: 1rem;
            }
            
            .space-x-2 {
                gap: 0.25rem;
            }
            
            .flex-1 {
                font-size: 0.75rem;
            }
        }
        
        @media (max-width: 480px) {
            .text-3xl {
                font-size: 1.25rem;
            }
            
            .text-lg {
                font-size: 1rem;
            }
            
            .px-6 {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            
            .py-3 {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
        }

        /* Mobile header styles */
        .mobile-header {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.95);
        }
        
        .menu-toggle-btn {
            transition: all 0.2s ease;
        }
        
        .menu-toggle-btn:hover {
            transform: scale(1.05);
        }
        
        .menu-toggle-btn:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar fixed left-0 top-0 h-full bg-gradient-to-b from-green-600 to-emerald-700 text-white shadow-2xl z-50">
        <!-- Logo -->
        <div class="p-6 border-b border-green-500/30">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-graduation-cap text-green-600 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold">Admin Panel</h1>
                    <p class="text-green-200 text-sm">Kelola Sekolah</p>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="p-4 border-b border-green-500/30">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div>
                    <p class="font-medium"><?= $username ?></p>
                    <p class="text-green-200 text-sm">Administrator</p>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="mt-6 flex-1">
            <div class="px-4">
                <p class="text-green-200 text-xs uppercase tracking-wide font-semibold mb-3">Menu Utama</p>
            </div>
            
            <a href="dashboard_admin.php" class="menu-item flex items-center px-6 py-3 text-green-100 hover:bg-green-700 hover:text-white border-l-4 border-transparent hover:border-white">
                <i class="fas fa-chart-line w-5 h-5 mr-3"></i>
                Dashboard
            </a>
            
            <a href="statistik_edit.php" class="menu-item flex items-center px-6 py-3 text-green-100 hover:bg-green-700 hover:text-white border-l-4 border-transparent hover:border-white">
                <i class="fas fa-chart-bar w-5 h-5 mr-3"></i>
                Statistik
            </a>
            
            <a href="profil_edit.php" class="menu-item flex items-center px-6 py-3 text-green-100 hover:bg-green-700 hover:text-white border-l-4 border-transparent hover:border-white">
                <i class="fas fa-school w-5 h-5 mr-3"></i>
                Profil Sekolah
            </a>
            
            <a href="berita_edit.php" class="menu-item flex items-center px-6 py-3 bg-green-700 text-white border-l-4 border-white">
                <i class="fas fa-newspaper w-5 h-5 mr-3"></i>
                Kelola Berita
            </a>
            
            <a href="prestasi_edit.php" class="menu-item flex items-center px-6 py-3 text-green-100 hover:bg-green-700 hover:text-white border-l-4 border-transparent hover:border-white">
                <i class="fas fa-trophy w-5 h-5 mr-3"></i>
                Prestasi
            </a>
            
            <a href="pegawai_edit.php" class="menu-item flex items-center px-6 py-3 text-green-100 hover:bg-green-700 hover:text-white border-l-4 border-transparent hover:border-white">
                <i class="fas fa-users w-5 h-5 mr-3"></i>
                Guru & Staff
            </a>
            
            <a href="galeri_edit.php" class="menu-item flex items-center px-6 py-3 text-green-100 hover:bg-green-700 hover:text-white border-l-4 border-transparent hover:border-white">
                <i class="fas fa-images w-5 h-5 mr-3"></i>
                Galeri
            </a>
        </nav>

        <!-- Logout -->
        <div class="p-4 border-t border-green-500/30">
            <a href="../logout.php" class="flex items-center px-4 py-3 bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                Logout
            </a>
        </div>
    </div>

    <!-- Sidebar Overlay for mobile -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Mobile Header -->
        <header class="md:hidden mobile-header fixed top-0 left-0 right-0 z-40 border-b border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-4 py-3">
                <!-- Left Section - Menu Icon and Title -->
                <div class="flex items-center space-x-3">
                    <button id="mobileMenuBtn" class="menu-toggle-btn bg-green-600 text-white p-2 rounded-lg shadow-md hover:bg-green-700 transition-all duration-200">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">Kelola Berita</h1>
                        <p class="text-xs text-gray-500">Admin Panel</p>
                    </div>
                </div>

                <!-- Right Section - User Info -->
                <div class="flex items-center space-x-3">
                    <div class="flex items-center space-x-2 text-gray-600">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-green-600 text-sm"></i>
                        </div>
                        <div class="hidden sm:block">
                            <p class="text-sm font-medium text-gray-900"><?= $username ?></p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-4 md:p-6">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold mb-2">Kelola Berita</h1>
                            <p class="text-green-100">Tambah, edit, dan kelola berita sekolah</p>
                        </div>
                        <div class="hidden md:block">
                            <i class="fas fa-newspaper text-6xl text-green-200 opacity-50"></i>
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

            <!-- Add New News Button -->
            <div class="mb-6">
                <button onclick="openModal('addModal')" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Berita Baru
                </button>
            </div>

            <!-- News Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (mysqli_num_rows($berita_query) > 0): ?>
                    <?php while ($berita = mysqli_fetch_assoc($berita_query)): ?>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
                        <div class="relative">
                            <?php if ($berita['gambar_utama']): ?>
                                <img src="../upload/<?= $berita['gambar_utama'] ?>" alt="<?= htmlspecialchars($berita['judul']) ?>" class="w-full h-48 object-cover">
                            <?php else: ?>
                                <div class="w-full h-48 bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-newspaper text-green-600 text-4xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    <?= date('d M Y', strtotime($berita['tanggal_post'])) ?>
                                </span>
                                <?php if ($berita['penulis']): ?>
                                <span class="text-xs text-emerald-600">
                                    <i class="fas fa-user mr-1"></i>
                                    <?= htmlspecialchars($berita['penulis']) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2"><?= htmlspecialchars($berita['judul']) ?></h3>
                            
                            <p class="text-sm text-gray-600 mb-4 line-clamp-3"><?= strip_tags(substr($berita['isi'], 0, 150)) ?>...</p>
                            
                            <div class="flex space-x-2">
                                <button onclick="editBerita(<?= htmlspecialchars(json_encode($berita)) ?>)" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>
                                <a href="berita_edit.php?hapus=<?= $berita['id'] ?>" onclick="return confirm('Yakin hapus berita ini?')" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors text-center">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-16">
                        <i class="fas fa-newspaper text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada berita</h3>
                        <p class="text-gray-500 mb-4">Mulai tambahkan berita sekolah Anda</p>
                        <button onclick="openModal('addModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-plus mr-2"></i>Tambah Berita Pertama
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-4 md:top-10 mx-auto p-4 md:p-5 border w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-lg rounded-md bg-white max-h-screen overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-plus text-green-600 mr-2"></i>
                        Tambah Berita Baru
                    </h3>
                    <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-3 md:space-y-4">
                    <input type="hidden" name="action" value="add">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Berita</label>
                        <input type="text" name="judul" required class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Penulis</label>
                            <input type="text" name="penulis" class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Utama</label>
                        <input type="file" name="gambar_utama" accept="image/*" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Isi Berita</label>
                        <textarea name="isi" rows="8" required class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Tulis isi berita di sini..."></textarea>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-4">
                        <button type="button" onclick="closeModal('addModal')" class="w-full sm:w-auto px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
                            Batal
                        </button>
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-4 md:top-10 mx-auto p-4 md:p-5 border w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-lg rounded-md bg-white max-h-screen overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-edit text-green-600 mr-2"></i>
                        Edit Berita
                    </h3>
                    <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-3 md:space-y-4">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Berita</label>
                        <input type="text" name="judul" id="edit_judul" required class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Penulis</label>
                            <input type="text" name="penulis" id="edit_penulis" class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Utama Baru (Opsional)</label>
                        <input type="file" name="gambar_utama" accept="image/*" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah gambar</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Isi Berita</label>
                        <textarea name="isi" id="edit_isi" rows="8" required class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                    </div>
                    
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
                    Apakah Anda yakin ingin menghapus berita "<span id="delete_name" class="font-semibold"></span>"?
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

    <script>
        // Mobile menu functionality
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        mobileMenuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-open');
            sidebarOverlay.classList.toggle('active');
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('sidebar-open');
            sidebarOverlay.classList.remove('active');
        });

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function editBerita(berita) {
            document.getElementById('edit_id').value = berita.id;
            document.getElementById('edit_judul').value = berita.judul;
            document.getElementById('edit_penulis').value = berita.penulis || '';
            document.getElementById('edit_isi').value = berita.isi;
            openModal('editModal');
        }

        function deleteBerita(id, judul) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_name').textContent = judul;
            openModal('deleteModal');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = ['addModal', 'editModal', 'deleteModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    closeModal(modalId);
                }
            });
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('sidebar-open');
                sidebarOverlay.classList.remove('active');
            }
        });
    </script>
</body>
</html>