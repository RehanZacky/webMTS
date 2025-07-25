<?php
include 'auth.php';
include '../koneksi.php';

// Hapus foto
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $cek = mysqli_query($koneksi, "SELECT file_path FROM gambar WHERE id = $id");
    $row = mysqli_fetch_assoc($cek);
    if ($row && file_exists("../upload/" . $row['file_path'])) {
        unlink("../upload/" . $row['file_path']);
    }
    mysqli_query($koneksi, "DELETE FROM gambar WHERE id = $id");
    $success_message = "Foto berhasil dihapus dari galeri.";
}

// Tambah foto
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $tanggal = date("Y-m-d");

    $file_path = "";
    if ($_FILES['foto']['name']) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $file_path = "galeri_" . time() . "." . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], "../upload/$file_path");
    }

    mysqli_query($koneksi, "INSERT INTO gambar (nama, deskripsi, file_path, tanggal_upload)
        VALUES ('$nama', '$deskripsi', '$file_path', '$tanggal')");
    $success_message = "Foto berhasil ditambahkan ke galeri.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
            $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
            $tanggal_upload = date('Y-m-d H:i:s');
            
            $file_path = '';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $target_dir = "../upload/";
                $file_extension = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
                $file_path = "galeri_" . uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $file_path;
                
                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                    // File uploaded successfully
                } else {
                    $file_path = '';
                }
            }
            
            $query = "INSERT INTO gambar (nama, deskripsi, file_path, tanggal_upload) VALUES ('$nama', '$deskripsi', '$file_path', '$tanggal_upload')";
            if (mysqli_query($koneksi, $query)) {
                $success_message = "Foto berhasil ditambahkan ke galeri!";
            } else {
                $error_message = "Error: " . mysqli_error($koneksi);
            }
        } elseif ($_POST['action'] == 'edit') {
            $id = (int)$_POST['id'];
            $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
            $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
            
            $file_query = "";
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $target_dir = "../upload/";
                $file_extension = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
                $file_path = "galeri_" . uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $file_path;
                
                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                    $file_query = ", file_path = '$file_path'";
                }
            }
            
            $query = "UPDATE gambar SET nama = '$nama', deskripsi = '$deskripsi' $file_query WHERE id = $id";
            if (mysqli_query($koneksi, $query)) {
                $success_message = "Foto berhasil diperbarui!";
            } else {
                $error_message = "Error: " . mysqli_error($koneksi);
            }
        } elseif ($_POST['action'] == 'delete') {
            $id = (int)$_POST['id'];
            
            // Get image filename to delete
            $get_image = mysqli_query($koneksi, "SELECT file_path FROM gambar WHERE id = $id");
            $image_data = mysqli_fetch_assoc($get_image);
            
            $query = "DELETE FROM gambar WHERE id = $id";
            if (mysqli_query($koneksi, $query)) {
                // Delete image file if exists
                if ($image_data['file_path'] && file_exists("../upload/" . $image_data['file_path'])) {
                    unlink("../upload/" . $image_data['file_path']);
                }
                $success_message = "Foto berhasil dihapus dari galeri!";
            } else {
                $error_message = "Error: " . mysqli_error($koneksi);
            }
        }
    }
}

// Get all gallery data
$galeri_query = mysqli_query($koneksi, "SELECT * FROM gambar ORDER BY tanggal_upload DESC");

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri - Admin Panel</title>
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

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .upload-area {
            transition: all 0.3s ease;
            border: 2px dashed #d1d5db;
        }

        .upload-area:hover {
            border-color: #10b981;
            background-color: #f0fdf4;
        }

        .upload-area.dragover {
            border-color: #10b981;
            background-color: #f0fdf4;
            transform: scale(1.02);
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
                  <a href="dashboard_admin.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-line mr-2"></i>Dashboard
                  </a>
                    <a href="statistik_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-bar mr-2"></i>Statistik
                  </a>
                <a href="profil_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
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
                <a href="galeri_edit.php" class="bg-green-800 text-white block px-3 py-2 rounded-md text-base font-medium">
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
                <a href="pegawai_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-users mr-2"></i>Guru/Staff
                </a>
                <a href="berita_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-newspaper mr-2"></i>Berita
                </a>
                <a href="prestasi_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-trophy mr-2"></i>Prestasi
                </a>
                <a href="galeri_edit.php" class="bg-green-800 text-white block px-3 py-2 rounded-md text-base font-medium">
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
                        <h1 class="text-3xl font-bold mb-2">Kelola Galeri</h1>
                        <p class="text-green-100">Tambah, edit, dan kelola foto galeri sekolah</p>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-images text-6xl text-green-200 opacity-50"></i>
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

        <!-- Add New Photo Button -->
        <div class="mb-6 flex justify-between items-center">
            <button onclick="openModal('addModal')" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Foto Baru
            </button>
        </div>

        <!-- Gallery Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (mysqli_num_rows($galeri_query) > 0): ?>
                <?php while ($foto = mysqli_fetch_assoc($galeri_query)): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
                    <div class="relative">
                        <?php if ($foto['file_path']): ?>
                            <img src="../upload/<?= htmlspecialchars($foto['file_path']) ?>" alt="<?= htmlspecialchars($foto['nama']) ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                            <div class="w-full h-48 bg-green-100 flex items-center justify-center">
                                <i class="fas fa-image text-green-600 text-4xl"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="absolute top-2 right-2">
                            <button onclick="viewImage('../upload/<?= htmlspecialchars($foto['file_path']) ?>', '<?= htmlspecialchars($foto['nama']) ?>')" class="bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-800 p-2 rounded-full transition-all">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                <?= date('d M Y', strtotime($foto['tanggal_upload'])) ?>
                            </span>
                        </div>
                        
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2"><?= htmlspecialchars($foto['nama']) ?></h3>
                        
                        <p class="text-sm text-gray-600 mb-4 line-clamp-3"><?= htmlspecialchars($foto['deskripsi']) ?></p>
                        
                        <div class="flex space-x-2">
                            <button onclick="editFoto(<?= htmlspecialchars(json_encode($foto)) ?>)" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                            <button onclick="deleteFoto(<?= $foto['id'] ?>, '<?= htmlspecialchars($foto['nama']) ?>')" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-16">
                    <i class="fas fa-images text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada foto</h3>
                    <p class="text-gray-500 mb-4">Mulai tambahkan foto ke galeri sekolah Anda</p>
                    <button onclick="openModal('addModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-plus mr-2"></i>Tambah Foto Pertama
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Add Modal -->
    <div id="addModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-4 md:top-10 mx-auto p-4 md:p-5 border w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-lg rounded-md bg-white max-h-screen overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-plus text-green-600 mr-2"></i>
                        Tambah Foto Baru
                    </h3>
                    <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-3 md:space-y-4">
                    <input type="hidden" name="action" value="add">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Foto</label>
                        <input type="text" name="nama" required class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="4" class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 resize-none" placeholder="Masukkan deskripsi foto..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">File Foto</label>
                        <div class="upload-area rounded-lg p-6 text-center cursor-pointer" onclick="document.getElementById('fileInput').click()">
                            <div id="uploadContent">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600 font-medium">Klik untuk upload foto</p>
                                <p class="text-sm text-gray-500 mt-2">atau drag & drop file di sini</p>
                                <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG, GIF (Max: 5MB)</p>
                            </div>
                            <div id="previewContent" class="hidden">
                                <img id="imagePreview" class="mx-auto rounded-lg shadow-md max-h-48 object-cover">
                                <p class="text-green-600 font-medium mt-2">Foto siap diupload</p>
                            </div>
                        </div>
                        <input type="file" id="fileInput" name="foto" accept="image/*" required class="hidden">
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
                        Edit Foto
                    </h3>
                    <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-3 md:space-y-4">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Foto</label>
                        <input type="text" name="nama" id="edit_nama" required class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" rows="4" class="w-full px-3 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">File Foto Baru (Opsional)</label>
                        <input type="file" name="foto" accept="image/*" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah foto</p>
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
                    Apakah Anda yakin ingin menghapus foto "<span id="delete_name" class="font-semibold"></span>"?
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

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75 transition-colors z-10">
                <i class="fas fa-times"></i>
            </button>
            <img id="modalImage" class="max-w-full max-h-full object-contain rounded-lg">
            <div class="absolute bottom-4 left-4 right-4 bg-black bg-opacity-50 text-white p-4 rounded-lg">
                <h3 id="modalTitle" class="font-semibold text-lg"></h3>
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
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function editFoto(foto) {
            document.getElementById('edit_id').value = foto.id;
            document.getElementById('edit_nama').value = foto.nama;
            document.getElementById('edit_deskripsi').value = foto.deskripsi || '';
            openModal('editModal');
        }

        function deleteFoto(id, nama) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_name').textContent = nama;
            openModal('deleteModal');
        }

        function viewImage(src, title) {
            document.getElementById('modalImage').src = src;
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // File upload handling
        const fileInput = document.getElementById('fileInput');
        const uploadContent = document.getElementById('uploadContent');
        const previewContent = document.getElementById('previewContent');
        const imagePreview = document.getElementById('imagePreview');
        const uploadArea = document.querySelector('.upload-area');

        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        uploadContent.classList.add('hidden');
                        previewContent.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Drag and drop functionality
        if (uploadArea) {
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    fileInput.dispatchEvent(new Event('change'));
                }
            });
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

            // Close image modal
            const imageModal = document.getElementById('imageModal');
            if (event.target === imageModal) {
                closeImageModal();
            }
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modals = ['addModal', 'editModal', 'deleteModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (!modal.classList.contains('hidden')) {
                        closeModal(modalId);
                    }
                });
                
                const imageModal = document.getElementById('imageModal');
                if (!imageModal.classList.contains('hidden')) {
                    closeImageModal();
                }
            }
        });

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