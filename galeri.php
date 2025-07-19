<?php
// Koneksi ke database
include 'koneksi.php';

// --- LOGIKA PAGINASI ---
$per_halaman = 12;
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_aktif = max(1, $halaman_aktif);

$hasil_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM gambar");
$data_total = mysqli_fetch_assoc($hasil_total);
$total_gambar = $data_total['total'];
$total_halaman = ceil($total_gambar / $per_halaman);

$offset = ($halaman_aktif - 1) * $per_halaman;
$query_gambar = "SELECT * FROM gambar ORDER BY tanggal_upload DESC LIMIT $per_halaman OFFSET $offset";
$galeri_query = mysqli_query($koneksi, $query_gambar);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri - Roudlotul Quran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"/>
    <style>
        html { scroll-behavior: smooth; }
        /* Animasi */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeInDown { animation: fadeInDown 0.6s ease-out forwards; }
        /* Style untuk paginasi */
        .pagination a, .pagination span {
            padding: 8px 16px; margin: 0 4px; border-radius: 6px;
            transition: background-color 0.3s; border: 1px solid #d1d5db;
        }
        .pagination a:hover { background-color: #f3f4f6; }
        .pagination .aktif {
            background-color: #16a34a; color: white; border-color: #16a34a;
        }
        .pagination .disabled {
            color: #9ca3af; cursor: not-allowed; background-color: #f9fafb;
        }
    </style>
</head>
<body class="bg-gray-50">

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
            <a href="galeri.php" class="text-white font-bold">Galeri</a>
        </nav>

        <div class="md:hidden">
            <button id="menu-toggle" class="focus:outline-none">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-green-700 px-6 pb-4">
        <a href="index.php" class="block py-2 text-green-100 hover:text-white">Beranda</a>
        <a href="profil.php" class="block py-2 text-green-100 hover:text-white">Profil</a>
        <a href="berita.php" class="block py-2 text-green-100 hover:text-white">Berita</a>
        <a href="galeri.php" class="block py-2 text-white font-semibold">Galeri</a>
    </div>
</header>

<main class="py-24">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16 animate-fadeInDown">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                Galeri & <span class="text-green-600">Dokumentasi</span>
            </h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Jelajahi momen-momen berharga dari berbagai kegiatan dan acara yang diselenggarakan di lingkungan Pondok Pesantren Roudlotul Quran.
            </p>
            <div class="w-24 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full mx-auto mt-6"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php
                if ($galeri_query && mysqli_num_rows($galeri_query) > 0) :
                    while ($foto = mysqli_fetch_assoc($galeri_query)) :
                ?>
                <a href="<?= htmlspecialchars($foto['file_path']) ?>" data-fancybox="gallery" data-caption="<?= htmlspecialchars($foto['deskripsi']) ?>" class="group block rounded-lg overflow-hidden shadow-sm border border-gray-200 hover:shadow-xl transition-all duration-300">
                     <div class="overflow-hidden">
                        <img src="<?= htmlspecialchars($foto['file_path']) ?>" alt="<?= htmlspecialchars($foto['deskripsi']) ?>" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300">
                     </div>
                     <?php if (!empty($foto['deskripsi'])): ?>
                        <div class="p-3 bg-gray-50">
                            <p class="text-xs text-center text-gray-600 truncate"><?= htmlspecialchars($foto['deskripsi']) ?></p>
                        </div>
                     <?php endif; ?>
                </a>
                <?php
                    endwhile;
                else:
                    echo '<p class="col-span-full text-center text-gray-500 py-10">Belum ada foto di galeri.</p>';
                endif;
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
    <div class="container mx-auto px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-2">
                <h4 class="text-white text-lg font-semibold mb-4">Pondok Pesantren Roudlotul Quran</h4>
                <p class="text-gray-400">Pondok Pesantren Roudlotul Quran berkomitmen untuk mendidik generasi muda Islami yang berakhlak mulia, berilmu pengetahuan, dan berjiwa pemimpin dalam membangun peradaban yang berkualitas.</p>
            </div>
            <div>
                <h4 class="text-white text-lg font-semibold mb-4">Kontak Kami</h4>
                <ul class="space-y-2 text-gray-400">
                    <li>Jl. Raya Pesantren No. 123, Sidoarjo, Jawa Timur 61234</li>
                    <li>+62 31 1234 5678</li>
                    <li>info@roudlotulquran.ponpes.id</li>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-lg font-semibold mb-4">Jam Operasional</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><span class="font-semibold">Senin - Jumat:</span> 07:00 - 16:00 WIB</li>
                    <li><span class="font-semibold">Sabtu - Minggu:</span> 07:00 - 12:00 WIB</li>
                </ul>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t border-gray-700 text-center text-gray-500 text-sm">
            <p>© <?= date('Y') ?> Pondok Pesantren Roudlotul Quran. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
  Fancybox.bind("[data-fancybox]", {});
</script>

<script>
    document.getElementById("menu-toggle").addEventListener("click", function () {
        const menu = document.getElementById("mobile-menu");
        menu.classList.toggle("hidden");
    });
</script>

</body>
</html>