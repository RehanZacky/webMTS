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
            $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
            $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
            $penulis = mysqli_real_escape_string($koneksi, $_POST['penulis']);
            $tanggal_post = date('Y-m-d H:i:s');
            
            $gambar_utama = '';
            if (isset($_FILES['gambar_utama']) && $_FILES['gambar_utama']['error'] == 0) {
                $target_dir = "../upload/gambar_berita/";
                
                // Create upload directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES["gambar_utama"]["name"], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $gambar_utama = "berita_" . uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $gambar_utama;
                
                    if (!move_uploaded_file($_FILES["gambar_utama"]["tmp_name"], $target_file)) {
                    $gambar_utama = '';
                        $message_type = 'error';
                        $message_text = 'Gagal mengupload gambar!';
                    }
                } else {
                    $message_type = 'error';
                    $message_text = 'Format gambar tidak didukung! Gunakan JPG, PNG, atau GIF.';
                }
            }
            
            if (empty($message_text)) {
                $stmt = mysqli_prepare($koneksi, "INSERT INTO berita (judul, isi, penulis, gambar_utama, tanggal_post) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "sssss", $judul, $isi, $penulis, $gambar_utama, $tanggal_post);
                
                if (mysqli_stmt_execute($stmt)) {
                    $message_type = 'success';
                    $message_text = 'Berita berhasil ditambahkan!';
                } else {
                    $message_type = 'error';
                    $message_text = 'Error: ' . mysqli_error($koneksi);
                }
                mysqli_stmt_close($stmt);
            }
            
        } elseif ($_POST['action'] == 'edit') {
            $id = (int)$_POST['id'];
            $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
            $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
            $penulis = mysqli_real_escape_string($koneksi, $_POST['penulis']);
            
            $gambar_update = '';
            $new_gambar = '';
            
            if (isset($_FILES['gambar_utama']) && $_FILES['gambar_utama']['error'] == 0) {
                $target_dir = "../upload/gambar_berita/";
                
                // Create upload directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES["gambar_utama"]["name"], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $new_gambar = "berita_" . uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $gambar_utama;
                
                    if (move_uploaded_file($_FILES["gambar_utama"]["tmp_name"], $target_file)) {
                        // Get old image to delete it later
                        $old_image_query = mysqli_query($koneksi, "SELECT gambar_utama FROM berita WHERE id = $id");
                        $old_image_data = mysqli_fetch_assoc($old_image_query);
                        
                        $gambar_update = ", gambar_utama = '$new_gambar'";
                        
                        // Delete old image if exists
                        if ($old_image_data['gambar_utama'] && file_exists($target_dir . $old_image_data['gambar_utama'])) {
                            unlink($target_dir . $old_image_data['gambar_utama']);
                        }
                    }
                } else {
                    $message_type = 'error';
                    $message_text = 'Format gambar tidak didukung! Gunakan JPG, PNG, atau GIF.';
                }
            }
            
            if (empty($message_text)) {
                $query = "UPDATE berita SET judul = '$judul', isi = '$isi', penulis = '$penulis' $gambar_update WHERE id = $id";
                
                if (mysqli_query($koneksi, $query)) {
                    $message_type = 'success';
                    $message_text = 'Berita berhasil diperbarui!';
                } else {
                    $message_type = 'error';
                    $message_text = 'Error: ' . mysqli_error($koneksi);
                }
            }
            
        } elseif ($_POST['action'] == 'delete') {
            $id = (int)$_POST['id'];
            
            // Get image filename to delete
            $get_image = mysqli_query($koneksi, "SELECT gambar_utama FROM berita WHERE id = $id");
            $image_data = mysqli_fetch_assoc($get_image);
            
            $query = "DELETE FROM berita WHERE id = $id";
            if (mysqli_query($koneksi, $query)) {
                // Delete image file if exists
                if ($image_data['gambar_utama'] && file_exists("../upload/gambar_berita/" . $image_data['gambar_utama'])) {
                    unlink("../upload/gambar_berita/" . $image_data['gambar_utama']);
                }
                $message_type = 'success';
                $message_text = 'Berita berhasil dihapus!';
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

// Get all news data
$berita_query = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC");

$username = $_SESSION['username'];
//Pagination Setup
$per_page = 6;
$total = mysqli_num_rows($berita_query);
$total_pages = ceil($total / $per_page);
$page = isset($_GET['page']) ? max(1, min($total_pages, intval($_GET['page']))) : 1;
$offset = ($page - 1) * $per_page;

$berita_query = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC LIMIT $per_page OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../upload/logo/Logo_MTS.png" type="image/png">
    <title>Kelola Berita - Admin Panel</title>
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

        .aspect-\[16\/9\] {
            aspect-ratio: 16 / 9;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Top Navigation -->
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
                <a href="berita_edit.php" class="bg-green-800 text-white block px-3 py-2 rounded-md text-base font-medium"">
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
                <a href="pegawai_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-users mr-2"></i>Guru/Staff
                </a>
                <a href="berita_edit.php" class="bg-green-800 text-white block px-3 py-2 rounded-md text-base font-medium">
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

        <!-- Add New News Button -->
        <div class="mb-6">
            <button onclick="openModal('addModal')" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Berita Baru
            </button>
        </div>

<!-- Grid Berita - Mobile Optimized: Horizontal layout untuk mobile, vertical untuk desktop -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
    <?php if (mysqli_num_rows($berita_query) > 0): ?>
        <?php while ($berita = mysqli_fetch_assoc($berita_query)): ?>
        <div class="bg-white rounded-lg sm:rounded-xl shadow-lg overflow-hidden card-hover">
            <!-- Mobile: Horizontal layout, Desktop: Vertical layout -->
            <div class="flex sm:block">
                <!-- Image Section -->
                <div class="relative flex-shrink-0 w-28 h-20 sm:w-full sm:h-48">
                    <?php if (!empty($berita['gambar_utama']) && file_exists("../upload/gambar_berita/" . $berita['gambar_utama'])): ?>
                        <a href="../berita_detail.php?id=<?= $berita['id'] ?>">
                        <img src="../upload/gambar_berita/<?= htmlspecialchars($berita['gambar_utama']) ?>" 
                            alt="<?= htmlspecialchars($berita['judul']) ?>" 
                            class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition">
                        </a>
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
                        <?= htmlspecialchars($berita['judul']) ?>
                        </h3>
                        <span class="inline-flex items-center px-1.5 py-0.5 sm:px-2.5 sm:py-0.5 rounded-full text-[10px] sm:text-xs font-medium bg-emerald-100 text-emerald-800 hidden sm:inline-flex">
                            <?= date('d M Y', strtotime($berita['tanggal_post'])) ?>
                        </span>
                        <!-- Mobile: Show shorter date -->
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-800 sm:hidden">
                            <?= date('M Y', strtotime($berita['tanggal_post'])) ?>
                        </span>
                    </div>
                    
                    <!-- Description - Shortened for both mobile and desktop -->
                    <?php if (!empty($berita['isi'])): ?>
                        <?php
                        // Function to create excerpt from text
                        $excerpt = strip_tags($berita['isi']);
                        $excerpt = str_replace(array("\r", "\n"), ' ', $excerpt);
                        $excerpt = preg_replace('/\s+/', ' ', $excerpt);
                        $excerpt = trim($excerpt);
                        
                        // Different lengths for mobile and desktop
                        $mobile_length = 40;
                        $desktop_length = 100;
                        ?>
                        <!-- Desktop version -->
                        <p class="text-sm text-gray-600 mb-2 sm:mb-4 hidden sm:block">
                            <?= htmlspecialchars(substr($excerpt, 0, $desktop_length)) ?><?= strlen($excerpt) > $desktop_length ? '...' : '' ?>
                        </p>
                        <!-- Mobile version -->
                        <p class="text-xs text-gray-500 mb-2 sm:hidden">
                            <?= htmlspecialchars(substr($excerpt, 0, $mobile_length)) ?><?= strlen($excerpt) > $mobile_length ? '...' : '' ?>
                        </p>
                    <?php else: ?>
                        <!-- If no content, just show date on mobile -->
                        <p class="text-xs text-gray-500 mb-2 sm:hidden">
                            <?= date('d M Y', strtotime($berita['tanggal_post'])) ?>
                        </p>
                    <?php endif; ?>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-1 sm:space-x-2">
                        <button onclick="editBerita(<?= htmlspecialchars(json_encode($berita), ENT_QUOTES) ?>)" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-2 py-1.5 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm font-medium transition-colors">
                            <i class="fas fa-edit mr-1"></i> 
                            <span class="hidden sm:inline">Edit</span>
                        </button>
                        <button onclick="deleteBerita(<?= $berita['id'] ?>, '<?= htmlspecialchars($berita['judul'], ENT_QUOTES) ?>')" 
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
            <i class="fas fa-newspaper text-gray-300 text-4xl sm:text-6xl mb-4"></i>
            <h3 class="text-lg sm:text-xl font-medium text-gray-900 mb-2">Belum ada berita</h3>
            <p class="text-gray-600 mb-4 text-sm sm:text-base">Mulai dengan menambahkan berita pertama sekolah Anda</p>
            <button onclick="openModal('addModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-lg transition-colors text-sm sm:text-base">
                <i class="fas fa-plus mr-2"></i>Tambah Berita
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