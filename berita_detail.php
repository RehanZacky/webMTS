<?php
include 'koneksi.php';

// Cek apakah ID ada di URL
if (!isset($_GET['id'])) {
    // Redirect ke halaman berita jika tidak ada ID
    header('Location: berita.php');
    exit;
}

$id = intval($_GET['id']);

// Ambil data untuk berita yang sedang dibaca
$data_utama = mysqli_query($koneksi, "SELECT * FROM berita WHERE id = $id");
$berita = mysqli_fetch_assoc($data_utama);

// Jika berita dengan ID tersebut tidak ditemukan, redirect
if (!$berita) {
    header('Location: berita.php');
    exit;
}

// Ambil 4 berita terbaru lainnya (selain yang sedang dibaca) untuk sidebar
$query_lainnya = "SELECT id, judul, gambar_utama, tanggal_post FROM berita WHERE id != $id ORDER BY tanggal_post DESC LIMIT 4";
$berita_lainnya = mysqli_query($koneksi, $query_lainnya);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="upload/logo/Logo_MTS.png" type="image/png">
    <title>Madrasah Tsanawiyah Roudlotul Qur'an</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gray-50 min-h-screen font-sans">

<body class="bg-gray-50 min-h-screen font-sans">
   <header class="bg-green-700 sticky top-0 z-50 shadow-lg">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="index.php" class="flex items-center gap-3">
            <!-- Responsive logo: larger on mobile (h-20) and larger on sm+ (h-28) -->
            <div class="relative h-20 w-20 sm:h-28 sm:w-28">
            <img src="upload/logo/Logo_MTS.png" alt="Logo MTs Roudlotul Quran" class="logo-slide absolute inset-0 h-full w-full object-contain transition-opacity duration-1000 ease-in-out opacity-100">
            <img src="upload/logo/Logo_Ponpes.png" alt="Logo Ponpes Roudlotul Quran" class="logo-slide absolute inset-0 h-full w-full object-contain transition-opacity duration-1000 ease-in-out opacity-0">
            <img src="upload/logo/Logo_Yayasan.png" alt="Logo Yayasan Roudlotul Quran" class="logo-slide absolute inset-0 h-full w-full object-contain transition-opacity duration-1000 ease-in-out opacity-0">
    </div>
            <span class="text-xl font-bold text-white leading-tight">Yayasan Roudlotul Qur'an Az Zuhri <br> Pon.Pes & MTs Tahfidh <br> Roudlotul Qur'an </span>
        </a>

        <nav class="hidden md:flex items-center space-x-8">
            <a href="index.php" class="text-green-100 hover:text-white font-semibold">Beranda</a>
            <a href="profil.php" class="text-green-100 hover:text-white font-semibold">Profil</a>
            <a href="berita.php" class="text-white font-bold">Berita</a>
            <a href="prestasi.php" class="text-green-100 hover:text-white font-semibold">Prestasi</a>
            <a href="galeri.php" class="text-green-100 hover:text-white font-semibold">Galeri</a>
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
        <a href="index.php" class="text-green-100 hover:text-white font-semibold">Beranda</a>
        <a href="profil.php" class="block py-2 text-green-100 hover:text-white">Profil</a>
        <a href="berita.php" class="text-white font-bold">Berita</a>
        <a href="prestasi.php" class="block py-2 text-green-100 hover:text-white">Prestasi</a>
        <a href="galeri.php" class="block py-2 text-green-100 hover:text-white">Galeri</a>
    </div>
</header>

<script>
    // Safe mobile menu toggle: wait for DOM to be ready and guard against missing elements
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('menu-toggle');
        const menu = document.getElementById('mobile-menu');
        if (btn && menu) {
            btn.addEventListener('click', function () {
                menu.classList.toggle('hidden');
            });
        }

        // Logo slider functionality
        const logos = document.querySelectorAll('.logo-slide');
        if (logos.length > 1) { // Only run if there's more than one logo
            let currentLogoIndex = 0;
            setInterval(() => {
                logos[currentLogoIndex].classList.remove('opacity-100');
                logos[currentLogoIndex].classList.add('opacity-0');
                currentLogoIndex = (currentLogoIndex + 1) % logos.length;
                logos[currentLogoIndex].classList.remove('opacity-0');
                logos[currentLogoIndex].classList.add('opacity-100');
            }, 3000);
        }
        // Footer logo rotator
        const footerLogos = document.querySelectorAll('.logo-slide-footer');
        if (footerLogos.length > 1) {
            let currentFooterIndex = 0;
            footerLogos.forEach((el, i) => el.classList.toggle('opacity-100', i === 0));
            setInterval(() => {
                footerLogos[currentFooterIndex].classList.remove('opacity-100');
                footerLogos[currentFooterIndex].classList.add('opacity-0');
                currentFooterIndex = (currentFooterIndex + 1) % footerLogos.length;
                footerLogos[currentFooterIndex].classList.remove('opacity-0');
                footerLogos[currentFooterIndex].classList.add('opacity-100');
            }, 3000);
        }
    });
</script>

<div class="container mx-auto py-8 lg:py-16 px-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-12">

        <main class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6 md:p-8">
            <h1 class="text-2xl sm:text-4xl font-bold text-gray-900 mb-4 leading-tight"><?= htmlspecialchars($berita['judul']) ?></h1>
            
            <p class="text-sm text-gray-500 mb-6">
                Diposting pada <?= date('d F Y', strtotime($berita['tanggal_post'])) ?> oleh <?= htmlspecialchars($berita['penulis']) ?>
            </p>

            <?php if (!empty($berita['gambar_utama'])): ?>
                <div class="mb-8">
                    <img src="upload/gambar_berita/<?= htmlspecialchars($berita['gambar_utama']) ?>" alt="Gambar: <?= htmlspecialchars($berita['judul']) ?>"
                         class="rounded-lg w-full max-h-[500px] object-cover shadow-md" />
                </div>
            <?php endif; ?>

            <article class="prose max-w-none text-justify text-base sm:text-lg leading-relaxed text-gray-800">
                <?= nl2br($berita['isi']) // Menggunakan nl2br tanpa htmlspecialchars agar tag HTML dari editor bisa render ?>
            </article>

            <?php if (!empty($berita['video_youtube'])): ?>
                <?php
                parse_str(parse_url($berita['video_youtube'], PHP_URL_QUERY), $ytParams);
                $videoID = $ytParams['v'] ?? null;
                ?>
                <?php if ($videoID): ?>
                    <div class="mt-10">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Tonton Video</h3>
                        <div class="aspect-video">
                            <iframe class="w-full h-full rounded-lg shadow-md" src="https://www.youtube.com/watch?v=XpmeVNxZ-Ks&list=RDpp4YQPykBMM&index=11&ab_channel=IlleniumVEVO<?= $videoID ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </main>

        <aside class="lg:col-span-1 mt-12 lg:mt-0">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-28">
                <h3 class="text-xl font-bold text-gray-900 border-b pb-3 mb-4">Berita Lainnya</h3>
                <div class="space-y-5">
                    <?php if (mysqli_num_rows($berita_lainnya) > 0): ?>
                        <?php while ($lainnya = mysqli_fetch_assoc($berita_lainnya)): ?>
                            <a href="berita_detail.php?id=<?= $lainnya['id'] ?>" class="group flex items-center gap-4">
                                <img src="upload/gambar_berita/<?= htmlspecialchars($lainnya['gambar_utama']) ?>" alt="<?= htmlspecialchars($lainnya['judul']) ?>" class="w-20 h-20 rounded-md object-cover flex-shrink-0">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 group-hover:text-green-600 transition-colors leading-tight">
                                        <?= htmlspecialchars($lainnya['judul']) ?>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1"><?= date('d M Y', strtotime($lainnya['tanggal_post'])) ?></p>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">Tidak ada berita lainnya.</p>
                    <?php endif; ?>
                </div>
                 <div class="mt-8">
                    <a href="berita.php" class="w-full text-center inline-block bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                        Lihat Semua Berita
                    </a>
                </div>
            </div>
        </aside>

    </div>
</div>

<?php include 'footer.php'; ?>      

</body>
</html>