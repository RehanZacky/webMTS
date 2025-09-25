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
            $nama_prestasi = mysqli_real_escape_string($koneksi, $_POST['nama_prestasi']);
            $tingkat = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
            $penyelenggara = mysqli_real_escape_string($koneksi, $_POST['penyelenggara']);
            $tahun = mysqli_real_escape_string($koneksi, $_POST['tahun']);
            $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
            $tanggal_post = date('Y-m-d H:i:s');
            
            $gambar = '';
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                $target_dir = "../upload/gambar_prestasi/";
                
                // Create upload directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $gambar = uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $gambar;
                    
                    if (!move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                        $gambar = '';
                        $message_type = 'error';
                        $message_text = 'Gagal mengupload gambar!';
                    }
                } else {
                    $message_type = 'error';
                    $message_text = 'Format gambar tidak didukung! Gunakan JPG, PNG, atau GIF.';
                }
            }
            
            if (empty($message_text)) {
                $stmt = mysqli_prepare($koneksi, "INSERT INTO prestasi (nama_prestasi, tingkat, penyelenggara, tahun, deskripsi, gambar, tanggal_post) VALUES (?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "sssssss", $nama_prestasi, $tingkat, $penyelenggara, $tahun, $deskripsi, $gambar, $tanggal_post);
                
                if (mysqli_stmt_execute($stmt)) {
                    $message_type = 'success';
                    $message_text = 'Data prestasi berhasil ditambahkan!';
                } else {
                    $message_type = 'error';
                    $message_text = 'Error: ' . mysqli_error($koneksi);
                }
                mysqli_stmt_close($stmt);
            }
            
        } elseif ($_POST['action'] == 'edit') {
            $id = (int)$_POST['id'];
            $nama_prestasi = mysqli_real_escape_string($koneksi, $_POST['nama_prestasi']);
            $tingkat = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
            $penyelenggara = mysqli_real_escape_string($koneksi, $_POST['penyelenggara']);
            $tahun = mysqli_real_escape_string($koneksi, $_POST['tahun']);
            $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
            
            $gambar_update = '';
            $new_gambar = '';
            
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                $target_dir = "../upload/gambar_prestasi/";
                
                // Create upload directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $new_gambar = uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $new_gambar;
                    
                    if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                        // Get old image to delete it later
                        $old_image_query = mysqli_query($koneksi, "SELECT gambar FROM prestasi WHERE id = $id");
                        $old_image_data = mysqli_fetch_assoc($old_image_query);
                        
                        $gambar_update = ", gambar = '$new_gambar'";
                        
                        // Delete old image if exists
                        if ($old_image_data['gambar'] && file_exists($target_dir . $old_image_data['gambar'])) {
                            unlink($target_dir . $old_image_data['gambar']);
                        }
                    }
                } else {
                    $message_type = 'error';
                    $message_text = 'Format gambar tidak didukung! Gunakan JPG, PNG, atau GIF.';
                }
            }
            
            if (empty($message_text)) {
                $query = "UPDATE prestasi SET nama_prestasi = '$nama_prestasi', tingkat = '$tingkat', penyelenggara = '$penyelenggara', tahun = '$tahun', deskripsi = '$deskripsi' $gambar_update WHERE id = $id";
                
                if (mysqli_query($koneksi, $query)) {
                    $message_type = 'success';
                    $message_text = 'Data prestasi berhasil diperbarui!';
                } else {
                    $message_type = 'error';
                    $message_text = 'Error: ' . mysqli_error($koneksi);
                }
            }
            
        } elseif ($_POST['action'] == 'delete') {
            $id = (int)$_POST['id'];
            
            // Get image filename to delete
            $get_image = mysqli_query($koneksi, "SELECT gambar FROM prestasi WHERE id = $id");
            $image_data = mysqli_fetch_assoc($get_image);
            
            $query = "DELETE FROM prestasi WHERE id = $id";
            if (mysqli_query($koneksi, $query)) {
                // Delete image file if exists
                if ($image_data['gambar'] && file_exists("../upload/gambar_prestasi/" . $image_data['gambar'])) {
                    unlink("../upload/gambar_prestasi/" . $image_data['gambar']);
                }
                $message_type = 'success';
                $message_text = 'Data prestasi berhasil dihapus!';
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

// Pagination Setup - 6 prestasi per halaman
$per_page = 6;
$count_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM prestasi");
$count_result = mysqli_fetch_assoc($count_query);
$total = $count_result['total'];
$total_pages = ceil($total / $per_page);
$page = isset($_GET['page']) ? max(1, min($total_pages, intval($_GET['page']))) : 1;
$offset = ($page - 1) * $per_page;

// Get prestasi data with pagination
$prestasi_query = mysqli_query($koneksi, "SELECT * FROM prestasi ORDER BY tahun DESC, tanggal_post DESC LIMIT $per_page OFFSET $offset");

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../upload/logo/Logo_MTS.png" type="image/png">
    <title>Kelola Prestasi - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
            /* Additional CSS for mobile optimization */
    @media (max-width: 640px) {
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    }

    /* Existing line-clamp-3 for larger screens */
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        line-clamp: 3;
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

    /* Card hover effect adjustment for mobile */
    @media (max-width: 640px) {
        .card-hover:hover {
            transform: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    }

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

        .image-preview {
            max-width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
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
                <a href="profil_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-school mr-2"></i>Profil
                </a>
                <a href="pegawai_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-users mr-2"></i>Guru/Staff
                </a>
                <a href="berita_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-newspaper mr-2"></i>Berita
                </a>
                <a href="prestasi_edit.php" class="bg-green-800 text-white block px-3 py-2 rounded-md text-base font-medium">
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
                <a href="berita_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-newspaper mr-2"></i>Berita
                </a>
                <a href="prestasi_edit.php" class="bg-green-800 text-white block px-3 py-2 rounded-md text-base font-medium">
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
                        <h1 class="text-3xl font-bold mb-2">Kelola Prestasi</h1>
                        <p class="text-green-100">Tambah, edit, dan kelola prestasi sekolah</p>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-trophy text-6xl text-green-200 opacity-50"></i>
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

        <!-- Add New Prestasi Button -->
        <div class="mb-6">
            <button onclick="openModal('addModal')" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Prestasi Baru
            </button>
        </div>

<!-- Prestasi Grid - Mobile Optimized: 2 baris untuk mobile, persegi panjang layout -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
    <?php if (mysqli_num_rows($prestasi_query) > 0): ?>
        <?php while ($prestasi = mysqli_fetch_assoc($prestasi_query)): ?>
        <div class="bg-white rounded-lg sm:rounded-xl shadow-lg overflow-hidden card-hover">
            <!-- Mobile: Horizontal layout, Desktop: Vertical layout -->
            <div class="flex sm:block">
                <!-- Image Section -->
                <div class="relative flex-shrink-0 w-28 h-20 sm:w-full sm:h-48">
                    <?php if (!empty($prestasi['gambar']) && file_exists("../upload/gambar_prestasi/" . $prestasi['gambar'])): ?>
                        <img src="../upload/gambar_prestasi/<?= htmlspecialchars($prestasi['gambar']) ?>" 
                             alt="<?= htmlspecialchars($prestasi['nama_prestasi']) ?>" 
                             class="w-full h-full object-cover">
                    <?php else: ?>
                    <div class="w-full h-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-trophy text-green-600 text-lg sm:text-4xl"></i>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Content Section -->
                <div class="flex-1 p-3 sm:p-6">
                    <!-- Badges Row -->
                    <div class="flex items-center justify-between mb-1 sm:mb-2 gap-1">
                        <span class="inline-flex items-center px-1.5 py-0.5 sm:px-2.5 sm:py-0.5 rounded-full text-[10px] sm:text-xs font-medium bg-green-100 text-green-800 truncate">
                            <?= htmlspecialchars($prestasi['tingkat']) ?>
                        </span>
                        <span class="inline-flex items-center px-1.5 py-0.5 sm:px-2.5 sm:py-0.5 rounded-full text-[10px] sm:text-xs font-medium bg-emerald-100 text-emerald-800 hidden sm:inline-flex">
                            <?= date('d M Y', strtotime($prestasi['tanggal_post'])) ?>
                        </span>
                        <!-- Mobile: Show shorter date -->
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-800 sm:hidden">
                            <?= date('M Y', strtotime($prestasi['tanggal_post'])) ?>
                        </span>
                    </div>
                    
                    <!-- Title -->
                    <h3 class="text-sm sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2 line-clamp-1 sm:line-clamp-2">
                        <?= htmlspecialchars($prestasi['nama_prestasi']) ?>
                    </h3>
                    
                    <!-- Organizer -->
                    <?php if (!empty($prestasi['penyelenggara'])): ?>
                    <p class="text-xs sm:text-sm text-green-600 mb-1 sm:mb-2 line-clamp-1">
                        <i class="fas fa-building mr-1"></i>
                        <?= htmlspecialchars($prestasi['penyelenggara']) ?>
                    </p>
                    <?php endif; ?>
                    
                    <!-- Description - Hidden on mobile for space -->
                    <p class="text-sm text-gray-600 mb-2 sm:mb-4 line-clamp-1 sm:line-clamp-3 hidden sm:block">
                        <?= nl2br(htmlspecialchars($prestasi['deskripsi'])) ?>
                    </p>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-1 sm:space-x-2">
                        <button onclick="editPrestasi(<?= htmlspecialchars(json_encode($prestasi), ENT_QUOTES) ?>)" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-2 py-1.5 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm font-medium transition-colors">
                            <i class="fas fa-edit mr-1"></i> 
                            <span class="hidden sm:inline">Edit</span>
                        </button>
                        <button onclick="deletePrestasi(<?= $prestasi['id'] ?>, '<?= htmlspecialchars($prestasi['nama_prestasi'], ENT_QUOTES) ?>')" 
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
            <i class="fas fa-trophy text-gray-300 text-4xl sm:text-6xl mb-4"></i>
            <h3 class="text-lg sm:text-xl font-medium text-gray-900 mb-2">Belum ada prestasi</h3>
            <p class="text-gray-600 mb-4 text-sm sm:text-base">Mulai dengan menambahkan prestasi pertama sekolah Anda</p>
            <button onclick="openModal('addModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-lg transition-colors text-sm sm:text-base">
                <i class="fas fa-plus mr-2"></i>Tambah Prestasi
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
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-plus text-green-600 mr-2"></i>
                        Tambah Prestasi Baru
                    </h3>
                    <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="add">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Prestasi</label>
                        <input type="text" name="nama_prestasi" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                            <input type="text" name="tahun" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tingkat</label>
                            <select name="tingkat" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Pilih Tingkat</option>
                                <option value="Sekolah">Sekolah</option>
                                <option value="Kecamatan">Kecamatan</option>
                                <option value="Kabupaten">Kabupaten</option>
                                <option value="Provinsi">Provinsi</option>
                                <option value="Nasional">Nasional</option>
                                <option value="Internasional">Internasional</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Penyelenggara</label>
                        <input type="text" name="penyelenggara" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Prestasi</label>
                        <input type="file" name="gambar" accept="image/jpeg,image/jpg,image/png,image/gif" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
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
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-edit text-green-600 mr-2"></i>
                        Edit Prestasi
                    </h3>
                    <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Prestasi</label>
                        <input type="text" name="nama_prestasi" id="edit_nama_prestasi" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                            <input type="text" name="tahun" id="edit_tahun" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tingkat</label>
                            <select name="tingkat" id="edit_tingkat" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Pilih Tingkat</option>
                                <option value="Sekolah">Sekolah</option>
                                <option value="Kecamatan">Kecamatan</option>
                                <option value="Kabupaten">Kabupaten</option>
                                <option value="Provinsi">Provinsi</option>
                                <option value="Nasional">Nasional</option>
                                <option value="Internasional">Internasional</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Penyelenggara</label>
                        <input type="text" name="penyelenggara" id="edit_penyelenggara" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Prestasi Baru (Opsional)</label>
                        <input type="file" name="gambar" accept="image/jpeg,image/jpg,image/png,image/gif" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah gambar</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            <i class="fas fa-save mr-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Apakah Anda yakin ingin menghapus prestasi "<span id="delete_name" class="font-semibold"></span>"?
                    <br>Tindakan ini tidak dapat dibatalkan.
                </p>
                
                <form method="POST" class="inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    
                    <div class="flex justify-center space-x-3">
                        <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
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

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            // Reset form if it's add modal
            if (modalId === 'addModal') {
                document.querySelector('#addModal form').reset();
            }
        }

        function editPrestasi(prestasi) {
            document.getElementById('edit_id').value = prestasi.id;
            document.getElementById('edit_nama_prestasi').value = prestasi.nama_prestasi || '';
            document.getElementById('edit_tingkat').value = prestasi.tingkat || '';
            document.getElementById('edit_penyelenggara').value = prestasi.penyelenggara || '';
            document.getElementById('edit_tahun').value = prestasi.tahun || '';
            document.getElementById('edit_deskripsi').value = prestasi.deskripsi || '';
            openModal('editModal');
        }

        function deletePrestasi(id, nama_prestasi) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_name').textContent = nama_prestasi;
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

        // Close modal with ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modals = ['addModal', 'editModal', 'deleteModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (!modal.classList.contains('hidden')) {
                        closeModal(modalId);
                    }
                });
            }
        });

        // Auto-hide alert messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.animate-fade-in');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>