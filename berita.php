<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <meta charset="UTF-8">
    <title>Berita - Ponpes Roudlotul Quran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white min-h-screen font-sans">

<!-- NAVBAR UTAMA -->
<header class="bg-white/75 backdrop-blur-lg sticky top-0 z-50 shadow-sm">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="index.php" class="flex items-center gap-3">
            <img src="https://placehold.co/40x40/16a34a/white?text=A" alt="Logo" class="h-10 w-10">
            <span class="text-xl font-bold text-gray-800 leading-tight">Pondok Pesantren <br> Roudlotul Quran</span>
        </a>
        <!-- Desktop Menu -->
        <nav class="hidden md:flex items-center space-x-8">
            <a href="index.php" class="text-gray-600 hover:text-green-600 font-semibold">Beranda</a>
            <a href="profil.php" class="text-gray-600 hover:text-green-600 font-semibold">Profil</a>
            <a href="berita.php" class="text-gray-600 hover:text-green-600 font-semibold">Berita</a>
            <a href="galeri.php" class="text-gray-600 hover:text-green-600 font-semibold">Galeri</a>
        </nav>

        <!-- Mobile Hamburger Menu Button -->
        <div class="md:hidden">
            <button id="menu-toggle" class="focus:outline-none">
                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white px-6 pb-4">
        <a href="index.php" class="block py-2 text-gray-700 hover:text-green-600">Beranda</a>
        <a href="profil.php" class="block py-2 text-gray-700 hover:text-green-600">Profil</a>
        <a href="berita.php" class="block py-2 text-gray-700 hover:text-green-600">Berita</a>
        <a href="galeri.php" class="block py-2 text-gray-700 hover:text-green-600">Galeri</a>
    </div>
</header>

<!-- SCRIPT UNTUK MOBILE MENU -->
<script>
    document.getElementById("menu-toggle").addEventListener("click", function () {
        const menu = document.getElementById("mobile-menu");
        menu.classList.toggle("hidden");
    });
</script>


<!-- HEADER -->
<header class="bg-gradient-to-b from-green-700 to-green-400 text-white text-center py-20">
    <h1 class="text-3xl md:text-4xl font-bold px-4">Berita Seputar Kegiatan Madrasah dan Prestasi Siswa</h1>
</header>

<!-- SECTION: Kegiatan Madrasah -->
<section class="px-6 py-10">
    <h2 class="text-xl font-bold text-center text-white bg-green-600 py-2 rounded">Kegiatan Madrasah</h2>

    <div class="relative mt-6">
        <!-- Swiper Container -->
        <div class="swiper">
            <div class="swiper-wrapper">
                <?php
                $berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC");
                $ada_kegiatan = false;
                while ($b = mysqli_fetch_assoc($berita)) :
                    if (
                        !empty($b['gambar_utama']) &&
                        stripos($b['judul'], 'prestasi') === false &&
                        stripos($b['isi'], 'prestasi') === false
                    ):
                        $ada_kegiatan = true;
                ?>
                <div class="swiper-slide">
                    <div class="w-[320px] bg-white rounded shadow-md border border-gray-200 overflow-hidden h-full">
                        <a href="berita_detail.php?id=<?= $b['id'] ?>">
                            <img src="upload/<?= $b['gambar_utama'] ?>" class="h-48 w-full object-cover rounded-t" alt="Gambar Berita">
                        </a>
                        <div class="p-4">
                            <p class="text-sm text-gray-500 mb-1"><?= date('d M Y', strtotime($b['tanggal_post'])) ?> • <?= htmlspecialchars($b['penulis']) ?></p>
                            <h3 class="text-md font-bold text-green-700 mb-1"><?= htmlspecialchars($b['judul']) ?></h3>
                            <p class="text-sm text-justify text-gray-700 overflow-hidden text-ellipsis h-[100px]"><?= nl2br(htmlspecialchars(substr($b['isi'], 0, 150))) ?>...</p>
                            <a href="berita_detail.php?id=<?= $b['id'] ?>" class="text-sm text-green-600 hover:underline mt-2 inline-block">Baca Selengkapnya</a>
                        </div>
                    </div>
                </div>
                <?php
                    endif;
                endwhile;

                if (!$ada_kegiatan) {
                    echo "<p class='text-center text-gray-600'>Belum ada berita kegiatan madrasah.</p>";
                }
                ?>
            </div>

            <!-- Navigation buttons -->
            <div class="swiper-button-prev text-green-700"></div>
            <div class="swiper-button-next text-green-700"></div>
        </div>
    </div>
</section>

<!-- SECTION: Prestasi Siswa -->
<section class="px-6 py-10">
    <h2 class="text-xl font-bold text-center text-white bg-green-600 py-2 rounded">Prestasi Siswa</h2>

    <div class="relative mt-6">
        <!-- Swiper Container -->
        <div class="swiper">
            <div class="swiper-wrapper">
                <?php
                $berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC");
                $ada_prestasi = false;
                while ($b = mysqli_fetch_assoc($berita)) :
                    if (
                        !empty($b['gambar_utama']) &&
                        (stripos($b['judul'], 'prestasi') !== false || stripos($b['isi'], 'prestasi') !== false)
                    ):
                        $ada_prestasi = true;
                ?>
                <div class="swiper-slide">
                    <div class="w-[320px] bg-white rounded shadow-md border border-yellow-300 overflow-hidden h-full">
                        <a href="berita_detail.php?id=<?= $b['id'] ?>">
                            <img src="upload/<?= $b['gambar_utama'] ?>" class="h-48 w-full object-cover rounded-t" alt="Gambar Prestasi">
                        </a>
                        <div class="p-4">
                            <p class="text-sm text-gray-500 mb-1"><?= date('d M Y', strtotime($b['tanggal_post'])) ?> • <?= htmlspecialchars($b['penulis']) ?></p>
                            <h3 class="text-md font-bold text-yellow-700 mb-1"><?= htmlspecialchars($b['judul']) ?></h3>
                            <p class="text-sm text-justify text-gray-700 overflow-hidden text-ellipsis h-[100px]"><?= nl2br(htmlspecialchars(substr($b['isi'], 0, 150))) ?>...</p>
                            <a href="berita_detail.php?id=<?= $b['id'] ?>" class="text-sm text-yellow-600 hover:underline mt-2 inline-block">Baca Selengkapnya</a>
                        </div>
                    </div>
                </div>
                <?php
                    endif;
                endwhile;

                if (!$ada_prestasi) {
                    echo "<p class='text-center text-gray-600'>Belum ada berita prestasi siswa.</p>";
                }
                ?>
            </div>

            <!-- Navigation buttons -->
            <div class="swiper-button-prev text-yellow-700"></div>
            <div class="swiper-button-next text-yellow-700"></div>
        </div>
    </div>
</section>


<!-- FOOTER -->
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

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
  const sliders = document.querySelectorAll('.swiper');

  sliders.forEach((slider) => {
    new Swiper(slider, {
      slidesPerView: 1.2,
      spaceBetween: 16,
      breakpoints: {
        640: { slidesPerView: 2.2 },
        768: { slidesPerView: 3 },
        1024: { slidesPerView: 4 },
      },
      navigation: {
        nextEl: slider.querySelector('.swiper-button-next'),
        prevEl: slider.querySelector('.swiper-button-prev'),
      },
    });
  });
</script>

</body>
</html>
