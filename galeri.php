<?php
// Koneksi ke database
include 'koneksi.php';

// --- LOGIKA PAGINASI ---
// 1. Tentukan berapa gambar per halaman
$per_halaman = 12;

// 2. Ambil halaman saat ini dari URL, default ke halaman 1 jika tidak ada
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_aktif = max(1, $halaman_aktif); // Pastikan halaman tidak kurang dari 1

// 3. Hitung total data (total gambar)
$hasil_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM gambar");
$data_total = mysqli_fetch_assoc($hasil_total);
$total_gambar = $data_total['total'];

// 4. Hitung total halaman
$total_halaman = ceil($total_gambar / $per_halaman);

// 5. Tentukan OFFSET untuk query SQL
$offset = ($halaman_aktif - 1) * $per_halaman;

// 6. Query untuk mengambil data gambar sesuai halaman
$query_gambar = "SELECT * FROM gambar ORDER BY tanggal_upload DESC LIMIT $per_halaman OFFSET $offset";
$galeri_query = mysqli_query($koneksi, $query_gambar);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roudlotul Quran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"/>
    <style>
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-gray-50">

    <header class="bg-white/75 backdrop-blur-lg sticky top-0 z-50 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3">
                <img src="https://placehold.co/40x40/16a34a/white?text=A" alt="Logo Al-Mujahidin" class="h-10 w-10">
                <span class="text-xl font-bold text-gray-800">Pondok Pesantren <br> Roudlotul Quran</span>
            </a>
            <nav class="hidden md:flex items-center space-x-8">
                <a href="index.php#home" class="text-gray-600 hover:text-green-600 font-semibold">Beranda</a>
                <a href="index.php#profil" class="text-gray-600 hover:text-green-600 font-semibold">Profil</a>
                <a href="index.php#artikel" class="text-gray-600 hover:text-green-600 font-semibold">Berita</a>
                <a href="galeri.php" class="text-gray-600 hover:text-green-600 font-semibold">Galeri</a>
            </nav>
        </div>
    </header>

    <main class="py-16 md:py-24">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl font-bold text-gray-800">Galeri Kegiatan</h1>
            <p class="mt-2 text-gray-600 max-w-2xl mx-auto">Semua momen dan kegiatan yang terdokumentasi di Pondok Pesantren Roudlotul Quran.</p>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-12">
                <?php
                if ($galeri_query && mysqli_num_rows($galeri_query) > 0) :
                    while ($foto = mysqli_fetch_assoc($galeri_query)) :
                ?>
                <div class="rounded-lg overflow-hidden shadow-md transform hover:scale-105 transition-transform duration-300">
                    <a href="<?= htmlspecialchars($foto['file_path']) ?>" data-fancybox="gallery" data-caption="<?= htmlspecialchars($foto['deskripsi']) ?>">
                         <img src="<?= htmlspecialchars($foto['file_path']) ?>" alt="<?= htmlspecialchars($foto['deskripsi']) ?>" class="w-full h-full object-cover aspect-square">
                    </a>
                </div>
                <?php
                    endwhile;
                else:
                    echo '<p class="text-center col-span-full text-gray-500">Belum ada foto di galeri.</p>';
                endif;
                ?>
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
                        <li>info@almujahidin.ac.id</li>
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
                <p>© <?= date('Y') ?> Pondok Pesantren Al-Mujahidin. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
      Fancybox.bind("[data-fancybox]", { });
    </script>

</body>
</html>