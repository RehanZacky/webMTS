<?php
include 'koneksi.php';

// --- LOGIKA PAGINASI UNTUK PRESTASI ---
$per_halaman = 9; // Tampilkan 9 prestasi per halaman
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_aktif = max(1, $halaman_aktif);

// Query untuk menghitung total prestasi
$query_total = "SELECT COUNT(*) as total FROM berita WHERE judul LIKE '%prestasi%' OR isi LIKE '%prestasi%'";
$hasil_total = mysqli_query($koneksi, $query_total);
$data_total = mysqli_fetch_assoc($hasil_total);
$total_prestasi = $data_total['total'];

$total_halaman = ceil($total_prestasi / $per_halaman);
$offset = ($halaman_aktif - 1) * $per_halaman;

// Query untuk mengambil data prestasi sesuai halaman
$query_prestasi = "SELECT * FROM berita WHERE judul LIKE '%prestasi%' OR isi LIKE '%prestasi%' ORDER BY tanggal_post DESC LIMIT $per_halaman OFFSET $offset";
$prestasi_result = mysqli_query($koneksi, $query_prestasi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Prestasi Santri - Pondok Pesantren Roudlotul Quran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeInDown { animation: fadeInDown 0.6s ease-out forwards; }
        /* Style untuk paginasi */
        .pagination a, .pagination span {
            padding: 8px 16px; margin: 0 4px; border-radius: 6px; transition: background-color 0.3s; border: 1px solid #d1d5db;
        }
        .pagination a:hover { background-color: #f3f4f6; }
        .pagination .aktif { background-color: #16a34a; color: white; border-color: #16a34a; }
        .pagination .disabled { color: #9ca3af; cursor: not-allowed; background-color: #f9fafb; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

<header class="bg-green-700 sticky top-0 z-50 shadow-lg">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="index.php" class="flex items-center gap-3">
            <img src="upload\STK-20250718-WA0016.png" alt="Roudlotul Quran" class="h-20 w-20">
            <span class="text-xl font-bold text-white leading-tight">Pondok Pesantren <br> Roudlotul Quran</span>
        </a>
        <nav class="hidden md:flex items-center space-x-8">
            <a href="index.php" class="text-green-100 hover:text-white font-semibold">Beranda</a>
            <a href="profil.php" class="text-green-100 hover:text-white font-semibold">Profil</a>
            <a href="berita.php" class="text-green-100 hover:text-white font-semibold">Berita</a>
            <a href="prestasi.php" class="text-white font-bold border-b-2 border-white pb-1">Prestasi</a>
            <a href="galeri.php" class="text-green-100 hover:text-white font-semibold">Galeri</a>
        </nav>
        <div class="md:hidden">
            <button id="menu-toggle" class="focus:outline-none">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden bg-green-700 px-6 pb-4">
        <a href="index.php" class="block py-2 text-green-100 hover:text-white">Beranda</a>
        <a href="profil.php" class="block py-2 text-green-100 hover:text-white">Profil</a>
        <a href="berita.php" class="block py-2 text-green-100 hover:text-white">Berita</a>
        <a href="prestasi.php" class="block py-2 text-white font-semibold">Prestasi</a>
        <a href="galeri.php" class="block py-2 text-green-100 hover:text-white">Galeri</a>
    </div>
</header>

<main class="py-24">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16 animate-fadeInDown">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                Galeri <span class="text-green-600">Prestasi</span>
            </h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Kumpulan pencapaian dan prestasi membanggakan yang telah diraih oleh para santri Pondok Pesantren Roudlotul Quran dalam berbagai bidang.
            </p>
            <div class="w-24 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full mx-auto mt-6"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                if ($prestasi_result && mysqli_num_rows($prestasi_result) > 0) {
                    while ($p = mysqli_fetch_assoc($prestasi_result)) {
                        ?>
                        <div class="bg-gray-50 rounded-xl overflow-hidden flex flex-col transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                            <a href="berita_detail.php?id=<?= $p['id'] ?>">
                                <img src="upload/<?= htmlspecialchars($p['gambar_utama']) ?>" class="h-48 w-full object-cover" alt="Gambar: <?= htmlspecialchars($p['judul']) ?>">
                            </a>
                            <div class="p-5 flex flex-col flex-grow">
                                <p class="text-xs text-gray-500 mb-2"><?= date('d M Y', strtotime($p['tanggal_post'])) ?></p>
                                <h3 class="text-md font-bold text-gray-800 mb-2 flex-grow">
                                    <a href="berita_detail.php?id=<?= $p['id'] ?>" class="hover:text-green-600">
                                        <?= htmlspecialchars($p['judul']) ?>
                                    </a>
                                </h3>
                                <a href="berita_detail.php?id=<?= $p['id'] ?>" class="text-sm font-semibold text-green-600 hover:underline mt-2 self-start">Lihat Detail →</a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p class='col-span-full text-center text-gray-500'>Belum ada berita prestasi siswa.</p>";
                }
                ?>
            </div>
        </div>

        <div class="mt-16 flex justify-center items-center pagination text-sm font-medium">
            <?php if ($total_halaman > 1): ?>
                <?php if ($halaman_aktif > 1): ?>
                    <a href="?halaman=<?= $halaman_aktif - 1 ?>">«</a>
                <?php else: ?>
                    <span class="disabled">«</span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                    <a href="?halaman=<?= $i ?>" class="<?= ($i == $halaman_aktif) ? 'aktif' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                
                <?php if ($halaman_aktif < $total_halaman): ?>
                    <a href="?halaman=<?= $halaman_aktif + 1 ?>">»</a>
                <?php else: ?>
                    <span class="disabled">»</span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<footer class="bg-gray-800 text-gray-300 mt-16">
    </footer>

<script>
    document.getElementById("menu-toggle").addEventListener("click", function () {
        const menu = document.getElementById("mobile-menu");
        menu.classList.toggle("hidden");
    });
</script>

</body>
</html>