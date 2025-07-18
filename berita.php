<?php
include 'koneksi.php';

// --- LOGIKA BARU YANG LEBIH EFISIEN ---
$semua_berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC");

$kegiatan_html = [];
$prestasi_html = [];

if ($semua_berita) {
    while ($b = mysqli_fetch_assoc($semua_berita)) {
        $is_prestasi = (stripos($b['judul'], 'prestasi') !== false || stripos($b['isi'], 'prestasi') !== false);
        
        ob_start();
        ?>
        <div class="bg-gray-50 rounded-xl overflow-hidden flex flex-col transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <a href="berita_detail.php?id=<?= $b['id'] ?>">
                <img src="upload/<?= htmlspecialchars($b['gambar_utama']) ?>" class="h-48 w-full object-cover" alt="Gambar: <?= htmlspecialchars($b['judul']) ?>">
            </a>
            <div class="p-5 flex flex-col flex-grow">
                <p class="text-xs text-gray-500 mb-2"><?= date('d M Y', strtotime($b['tanggal_post'])) ?></p>
                <h3 class="text-md font-bold text-gray-800 mb-2 flex-grow">
                    <a href="berita_detail.php?id=<?= $b['id'] ?>" class="hover:text-green-600">
                        <?= htmlspecialchars($b['judul']) ?>
                    </a>
                </h3>
                <a href="berita_detail.php?id=<?= $b['id'] ?>" class="text-sm font-semibold text-green-600 hover:underline mt-2 self-start">Baca Selengkapnya →</a>
            </div>
        </div>
        <?php
        $kartu_html = ob_get_clean();
        if ($is_prestasi) {
            $prestasi_html[] = $kartu_html;
        } else {
            $kegiatan_html[] = $kartu_html;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita - Pondok Pesantren Roudlotul Quran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
         @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeInDown { animation: fadeInDown 0.6s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

<header class="bg-white/75 backdrop-blur-lg sticky top-0 z-50 shadow-sm">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="index.php" class="flex items-center gap-3">
            <img src="https://placehold.co/40x40/16a34a/white?text=A" alt="Logo" class="h-10 w-10">
            <span class="text-xl font-bold text-gray-800 leading-tight">Pondok Pesantren <br> Roudlotul Quran</span>
        </a>

        <nav class="hidden md:flex items-center space-x-8">
            <a href="index.php" class="text-gray-600 hover:text-green-600 font-semibold">Beranda</a>
            <a href="profil.php" class="text-gray-600 hover:text-green-600 font-semibold">Profil</a>
            <a href="berita.php" class="text-green-600 font-bold border-b-2 border-green-600 pb-1">Berita</a>
            <a href="galeri.php" class="text-gray-600 hover:text-green-600 font-semibold">Galeri</a>
        </nav>

        <div class="md:hidden">
            <button id="menu-toggle" class="focus:outline-none">
                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-white px-6 pb-4">
        <a href="index.php" class="block py-2 text-gray-700 hover:text-green-600">Beranda</a>
        <a href="profil.php" class="block py-2 text-gray-700 hover:text-green-600">Profil</a>
        <a href="berita.php" class="block py-2 text-green-600 font-semibold">Berita</a>
        <a href="galeri.php" class="block py-2 text-gray-700 hover:text-green-600">Galeri</a>
    </div>
</header>

<main class="py-24">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16 animate-fadeInDown">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                Artikel & <span class="text-green-600">Berita</span>
            </h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Ikuti informasi terkini seputar kegiatan, pengumuman, dan pencapaian membanggakan dari para santri di Pondok Pesantren Roudlotul Quran.
            </p>
            <div class="w-24 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full mx-auto mt-6"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            
            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Kegiatan Madrasah</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php
                    if (!empty($kegiatan_html)) {
                        foreach ($kegiatan_html as $kartu) {
                            echo $kartu;
                        }
                    } else {
                        echo "<p class='col-span-full text-center text-gray-500'>Belum ada berita kegiatan madrasah.</p>";
                    }
                    ?>
                </div>
            </section>
            
            <hr class="my-12 border-gray-200">

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Prestasi Siswa</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                     <?php
                    if (!empty($prestasi_html)) {
                        foreach ($prestasi_html as $kartu) {
                            echo $kartu;
                        }
                    } else {
                        echo "<p class='col-span-full text-center text-gray-500'>Belum ada berita prestasi siswa.</p>";
                    }
                    ?>
                </div>
            </section>
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

<script>
    document.getElementById("menu-toggle").addEventListener("click", function () {
        const menu = document.getElementById("mobile-menu");
        menu.classList.toggle("hidden");
    });
</script>

</body>
</html>