<?php
include 'auth.php';
include '../koneksi.php';

// Handle form submission with POST-REDIRECT-GET pattern
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $redirect_url = $_SERVER['PHP_SELF'];
    $message_type = '';
    $message_text = '';
    
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
            $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
            $tanggal_post = date('Y-m-d H:i:s');
            
            $file_path = '';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $target_dir = "../upload/gambar_galeri/";
                
                // Create upload directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $file_path = "galeri_" . uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $file_path;
                
                    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                    $file_path = '';
                        $message_type = 'error';
                        $message_text = 'Gagal mengupload gambar!';
                    }
                } else {
                    $message_type = 'error';
                    $message_text = 'Format gambar tidak didukung! Gunakan JPG, PNG, atau GIF.';
                }
            }
            
            if (empty($message_text)) {
                $stmt = mysqli_prepare($koneksi, "INSERT INTO galeri (nama, deskripsi, file_path, tanggal_post) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "ssss", $nama, $deskripsi, $file_path, $tanggal_post);
                
                if (mysqli_stmt_execute($stmt)) {
                    $message_type = 'success';
                    $message_text = 'Foto berhasil ditambahkan ke galeri!';
                } else {
                    $message_type = 'error';
                    $message_text = 'Error: ' . mysqli_error($koneksi);
                }
                mysqli_stmt_close($stmt);
            }
            
        } elseif ($_POST['action'] == 'edit') {
            $id = (int)$_POST['id'];
            $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
            $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
            
            $file_update = '';
            $new_file = '';
            
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $target_dir = "../upload/gambar_galeri/";
                
                // Create upload directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $new_file = "galeri_" . uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $file_path;
                
                    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                        // Get old image to delete it later
                        $old_image_query = mysqli_query($koneksi, "SELECT file_path FROM galeri WHERE id = $id");
                        $old_image_data = mysqli_fetch_assoc($old_image_query);
                        
                        $file_update = ", file_path = '$new_file'";
                        
                        // Delete old image if exists
                        if ($old_image_data['file_path'] && file_exists($target_dir . $old_image_data['file_path'])) {
                            unlink($target_dir . $old_image_data['file_path']);
                        }
                    }
                } else {
                    $message_type = 'error';
                    $message_text = 'Format gambar tidak didukung! Gunakan JPG, PNG, atau GIF.';
                }
            }
            
            if (empty($message_text)) {
                $query = "UPDATE galeri SET nama = '$nama', deskripsi = '$deskripsi' $file_update WHERE id = $id";
                
                if (mysqli_query($koneksi, $query)) {
                    $message_type = 'success';
                    $message_text = 'Foto berhasil diperbarui!';
                } else {
                    $message_type = 'error';
                    $message_text = 'Error: ' . mysqli_error($koneksi);
                }
            }
            
        } elseif ($_POST['action'] == 'delete') {
            $id = (int)$_POST['id'];
            
            // Get image filename to delete
            $get_image = mysqli_query($koneksi, "SELECT file_path FROM galeri WHERE id = $id");
            $image_data = mysqli_fetch_assoc($get_image);
            
            $query = "DELETE FROM galeri WHERE id = $id";
            if (mysqli_query($koneksi, $query)) {
                // Delete image file if exists
                if ($image_data['file_path'] && file_exists("../upload/gambar_galeri/" . $image_data['file_path'])) {
                    unlink("../upload/gambar_galeri/" . $image_data['file_path']);
                }
                $message_type = 'success';
                $message_text = 'Foto berhasil dihapus dari galeri!';
            } else {
                $message_type = 'error';
                $message_text = 'Error: ' . mysqli_error($koneksi);
            }
        }
        
        // Redirect to prevent form resubmission
        $redirect_url .= '?msg=' . urlencode($message_text) . '&type=' . $message_type;
        header("Location: " . $redirect_url);
        exit();
    }
}

// Handle GET messages from redirect
$success_message = '';
$error_message = '';

if (isset($_GET['msg']) && isset($_GET['type'])) {
    if ($_GET['type'] == 'success') {
        $success_message = htmlspecialchars($_GET['msg']);
    } else {
        $error_message = htmlspecialchars($_GET['msg']);
    }
}

// Get all gallery data
$galeri_query = mysqli_query($koneksi, "SELECT * FROM galeri ORDER BY tanggal_post DESC");

$username = $_SESSION['username'];
//page setup
$per_page = 6; // 6 gambar per halaman
$total = mysqli_num_rows($galeri_query);
$total_pages = ceil($total / $per_page);
$page = isset($_GET['page']) ? max(1, min($total_pages, intval($_GET['page']))) : 1;
$offset = ($page - 1) * $per_page;

$galeri_query = mysqli_query($koneksi, "SELECT * FROM galeri ORDER BY tanggal_post DESC LIMIT $per_page OFFSET $offset");
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
/* Galeri Mobile Horizontal Layout CSS - Tambahkan atau ganti di bagian <style> */

/* Base card hover effects */
.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Desktop hover effects */
@media (min-width: 1024px) {
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
}

/* Line clamp utilities */
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Mobile specific optimizations */
@media (max-width: 640px) {
    /* Ensure consistent card height */
    .card-hover > div {
        min-height: 5rem; /* 80px - consistent dengan gambar */
    }
    
    /* Image container optimizations */
    .card-hover img {
        transition: transform 0.2s ease;
    }
    
    .card-hover:hover img {
        transform: scale(1.05);
    }
    
    /* Button optimizations */
    .card-hover button {
        font-weight: 500;
    }
    
    /* Compact spacing */
    .grid {
        gap: 0.75rem; /* Lebih compact untuk mobile */
    }
}

/* Tablet and desktop optimizations */
@media (min-width: 641px) {
    /* Standard desktop layout dengan aspect ratio */
    .card-hover .sm\\:h-48 {
        aspect-ratio: 16 / 9;
        height: auto;
    }
    
    .card-hover img {
        object-position: center;
    }
}

/* Image modal optimizations */
.image-modal-overlay {
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

/* Badge positioning */
.card-hover .absolute {
    z-index: 10;
}

/* Custom font sizes for ultra-small screens */
@media (max-width: 360px) {
    .text-xs {
        font-size: 0.65rem; /* 10.4px */
    }
    
    .text-sm {
        font-size: 0.75rem; /* 12px */
    }
    
    /* Smaller padding for very small screens */
    .card-hover .p-3 {
        padding: 0.5rem; /* 8px */
    }
    
    /* Tighter button spacing */
    .card-hover .space-x-1 > * + * {
        margin-left: 0.25rem; /* 4px */
    }
}

/* Loading state untuk images */
.card-hover img {
    background-color: #f3f4f6;
    background-image: linear-gradient(45deg, #f9fafb 25%, transparent 25%), 
                      linear-gradient(-45deg, #f9fafb 25%, transparent 25%), 
                      linear-gradient(45deg, transparent 75%, #f9fafb 75%), 
                      linear-gradient(-45deg, transparent 75%, #f9fafb 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
}

/* Accessibility improvements */
.card-hover:focus-within {
    outline: 2px solid #10b981;
    outline-offset: 2px;
}

.card-hover button:focus {
    outline: 2px solid #10b981;
    outline-offset: 1px;
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
        <?php if (!empty($success_message)): ?>
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span><?= $success_message ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
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

       <!-- Galeri Grid - Mobile Optimized: Horizontal layout untuk mobile, vertical untuk desktop -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
    <?php if (mysqli_num_rows($galeri_query) > 0): ?>
        <?php while ($galeri = mysqli_fetch_assoc($galeri_query)): ?>
        <div class="bg-white rounded-lg sm:rounded-xl shadow-lg overflow-hidden card-hover">
            <!-- Mobile: Horizontal layout, Desktop: Vertical layout -->
            <div class="flex sm:block">
                <!-- Image Section -->
                <div class="relative flex-shrink-0 w-28 h-20 sm:w-full sm:h-48">
                    <?php if (!empty($galeri['file_path']) && file_exists("../upload/gambar_galeri/" . $galeri['file_path'])): ?>
                        <img src="../upload/gambar_galeri/<?= htmlspecialchars($galeri['file_path']) ?>" 
                             alt="<?= htmlspecialchars($galeri['nama']) ?>" 
                             class="w-full h-full object-cover cursor-pointer"
                             onclick="viewImage('../upload/gambar_galeri/<?= htmlspecialchars($galeri['file_path']) ?>', '<?= htmlspecialchars($galeri['nama'], ENT_QUOTES) ?>')">
                    <?php else: ?>
                    <div class="w-full h-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-image text-green-600 text-lg sm:text-4xl"></i>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Content Section -->
                <div class="flex-1 p-3 sm:p-6">
                    <!-- Badges Row -->
                    <div class="flex items-center justify-between mb-1 sm:mb-2 gap-1">
                        <!-- Title -->
                        <h3 class="text-sm sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2 line-clamp-1 sm:line-clamp-2">
                        <?= htmlspecialchars($galeri['nama']) ?>
                        </h3>
                        <span class="inline-flex items-center px-1.5 py-0.5 sm:px-2.5 sm:py-0.5 rounded-full text-[10px] sm:text-xs font-medium bg-emerald-100 text-emerald-800 hidden sm:inline-flex">
                            <?= date('d M Y', strtotime($galeri['tanggal_post'])) ?>
                        </span>
                        <!-- Mobile: Show shorter date -->
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-800 sm:hidden">
                            <?= date('M Y', strtotime($galeri['tanggal_post'])) ?>
                        </span>
                    </div>
                    

                    
                    <!-- Description - Hidden on mobile for space, visible on desktop -->
                    <?php if (!empty($galeri['deskripsi'])): ?>
                    <p class="text-sm text-gray-600 mb-2 sm:mb-4 line-clamp-1 sm:line-clamp-3 hidden sm:block">
                        <?= nl2br(htmlspecialchars($galeri['deskripsi'])) ?>
                    </p>
                    <?php endif; ?>
                    
                    <!-- Mobile: Show short description or date -->
                    <p class="text-xs text-gray-500 mb-2 line-clamp-1 sm:hidden">
                        <?= date('d M Y', strtotime($galeri['tanggal_post'])) ?>
                        <?php if (!empty($galeri['deskripsi'])): ?>
                        • <?= htmlspecialchars(substr($galeri['deskripsi'], 0, 30)) ?><?= strlen($galeri['deskripsi']) > 30 ? '...' : '' ?>
                        <?php endif; ?>
                    </p>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-1 sm:space-x-2">
                        <button onclick="editGaleri(<?= htmlspecialchars(json_encode($galeri), ENT_QUOTES) ?>)" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-2 py-1.5 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm font-medium transition-colors">
                            <i class="fas fa-edit mr-1"></i> 
                            <span class="hidden sm:inline">Edit</span>
                        </button>
                        <button onclick="deleteGaleri(<?= $galeri['id'] ?>, '<?= htmlspecialchars($galeri['nama'], ENT_QUOTES) ?>')" 
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white px-2 py-1.5 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm font-medium transition-colors">
                            <i class="fas fa-trash mr-1"></i> 
                            <span class="hidden sm:inline">Hapus</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-span-full text-center py-8 sm:py-16">
            <i class="fas fa-images text-gray-300 text-4xl sm:text-6xl mb-4"></i>
            <h3 class="text-lg sm:text-xl font-medium text-gray-900 mb-2">Belum ada gambar</h3>
            <p class="text-gray-600 mb-4 text-sm sm:text-base">Mulai dengan menambahkan gambar pertama sekolah Anda</p>
            <button onclick="openModal('addModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-lg transition-colors text-sm sm:text-base">
                <i class="fas fa-plus mr-2"></i>Tambah Gambar
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
    <div class="flex justify-center items-center mt-6 space-x-2 text-sm font-normal">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="flex items-center px-4 py-3 sm:px-6 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-300 shadow-md hover:shadow-lg">
                &lt; Sebelumnya
            </a>
        <?php else: ?>
            <span class="flex items-center px-4 py-3 sm:px-6 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                &lt; Sebelumnya
            </span>
        <?php endif; ?>

        <span class="bg-white rounded-lg px-4 py-3 sm:px-6 shadow-md border border-gray-200">
            Halaman <?= $page ?> dari <?= $total_pages ?>
        </span>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>" class="flex items-center px-4 py-3 sm:px-6 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-300 shadow-md hover:shadow-lg">
                Selanjutnya &gt;
            </a>
        <?php else: ?>
            <span class="flex items-center px-4 py-3 sm:px-6 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                Selanjutnya &gt;
            </span>
        <?php endif; ?>
    </div>
<?php endif; ?>
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

        function editGaleri(foto) {
            document.getElementById('edit_id').value = foto.id;
            document.getElementById('edit_nama').value = foto.nama;
            document.getElementById('edit_deskripsi').value = foto.deskripsi || '';
            openModal('editModal');
        }

        function deleteGaleri(id, nama) {
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