<?php
// Koneksi ke database Anda tetap tidak berubah
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pondok Pesantren Al-Mujahidin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Menambahkan style untuk scroll yang lebih halus */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gray-50">

    <header class="bg-white/80 backdrop-blur-lg sticky top-0 z-50 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="flex items-center gap-3">
                <img src="https://placehold.co/40x40/16a34a/white?text=A" alt="Logo Al-Mujahidin" class="h-10 w-10">
                <span class="text-xl font-bold text-gray-800">Pondok Pesantren <br> Al-Mujahidin</span>
            </a>
            <nav class="hidden md:flex items-center space-x-8">
                <a href="#home" class="text-gray-600 hover:text-green-600 font-semibold border-b-2 border-green-600 pb-1">Beranda</a>
                <a href="#artikel" class="text-gray-600 hover:text-green-600 font-semibold">Artikel</a>
                <a href="#galeri" class="text-gray-600 hover:text-green-600 font-semibold">Galeri</a>
            </nav>
        </div>
    </header>

    <main>
        <section id="home" class="h-screen bg-green-700 flex items-center justify-center text-white text-center" style="background-image: linear-gradient(rgba(0, 80, 0, 0.6), rgba(0, 80, 0, 0.6)), url('https://placehold.co/1920x1080'); background-size: cover; background-position: center;">
            <div class="max-w-3xl px-4">
                <h1 class="text-5xl md:text-6xl font-extrabold leading-tight">Selamat Datang di Pondok Pesantren Al-Mujahidin</h1>
                <p class="mt-4 text-lg md:text-xl text-green-200">Mewujudkan sumber daya manusia yang peduli dan berbudaya ramah lingkungan melalui kegiatan madrasah yang berkesinambungan.</p>
                <div class="mt-8 flex justify-center gap-4">
                    <a href="#sambutan" class="bg-white text-green-700 font-bold py-3 px-8 rounded-full hover:bg-gray-100 transition-transform hover:scale-105">Tentang Kami</a>
                    <a href="#galeri" class="border-2 border-white text-white font-bold py-3 px-8 rounded-full hover:bg-white hover:text-green-700 transition-all hover:scale-105">Lihat Galeri</a>
                </div>
            </div>
        </section>

        <section class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <?php
                    // PHP untuk mengambil data statistik, sama seperti kode asli Anda
                    $data_statistik = mysqli_query($koneksi, "SELECT * FROM info_statistik ORDER BY id ASC LIMIT 4");
                    while ($row = mysqli_fetch_assoc($data_statistik)) :
                    ?>
                    <div class="flex flex-col items-center">
                        <div class="bg-green-100 text-green-600 rounded-full p-4 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 14l9-5-9-5-9 5 9 5z" /><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-9.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-9.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222 4 2.222V20M1 12l11 6 9-6" /></svg>
                        </div>
                        <p class="text-4xl font-bold text-gray-800"><?= htmlspecialchars($row['nilai']) ?></p>
                        <p class="text-gray-500"><?= htmlspecialchars($row['label']) ?></p>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>

        <section id="sambutan" class="py-20 bg-gray-50">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-4">Sambutan Pimpinan</h2>
                        <div class="mt-6">
                            <h4 class="font-bold text-lg text-gray-900">KH. Abdullah Wahab, M.Pd.I</h4>
                            <p class="text-gray-500">Pengasuh Pondok Pesantren Al-Mujahidin</p>
                        </div>
                    </div>
                    
                        <?php
    $profil = mysqli_query($koneksi, "SELECT isi FROM profil WHERE jenis = 'sambutan_kepala' LIMIT 1");
    $data_profil = mysqli_fetch_assoc($profil);
    $link = $data_profil['isi'] ?? '';

    // Ekstrak ID dari link YouTube
    parse_str(parse_url($link, PHP_URL_QUERY), $ytParams);
    $videoID = $ytParams['v'] ?? null;

    if ($videoID) {
        echo '<div class="flex justify-center">';
        echo '<div class="w-full max-w-2xl aspect-video">';
        echo "<iframe class='w-full h-full' src='https://www.youtube.com/embed/$videoID' frameborder='0' allowfullscreen></iframe>";
        echo '</div></div>';
    } else {
        echo "<p class='text-gray-500'>Link video tidak valid atau belum diisi.</p>";
    }
    ?>
                    
                </div>
            </div>
        </section>

        <section id="artikel" class="py-20 bg-white">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Artikel & Berita Terbaru</h2>
                <p class="mt-2 text-gray-600 max-w-2xl mx-auto">Ikuti perkembangan dan kegiatan terbaru dari Pondok Pesantren Al-Mujahidin</p>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mt-12 text-left">
                    <?php
                    // PHP untuk mengambil artikel, sama seperti kode sebelumnya
                    $artikel_query = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC LIMIT 3");
                    if (mysqli_num_rows($artikel_query) > 0) :
                        while ($artikel = mysqli_fetch_assoc($artikel_query)) :
                    ?>
                    <div class="bg-gray-50 rounded-lg shadow-md overflow-hidden transform hover:-translate-y-2 transition-transform duration-300">
                        <img src="upload/<?= htmlspecialchars($artikel['gambar_utama']) ?>" class="h-56 w-full object-cover" alt="Gambar Artikel">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2 text-gray-800"><?= htmlspecialchars($artikel['judul']) ?></h3>
                            <p class="text-gray-500 text-sm mb-4">Diposting: <?= date('d F Y', strtotime($artikel['tanggal_post'])) ?></p>
                            <a href="#" class="text-green-600 hover:text-green-800 font-bold">Baca Selengkapnya →</a>
                        </div>
                    </div>
                    <?php
                        endwhile;
                    else:
                        echo '<p class="text-center col-span-full text-gray-500">Belum ada artikel.</p>';
                    endif;
                    ?>
                </div>
                <div class="mt-12">
                    <a href="/semua-artikel" class="bg-green-600 text-white font-bold py-3 px-8 rounded-full hover:bg-green-700 transition-colors">
                        Lihat Semua Artikel
                    </a>
                </div>
            </div>
        </section>

    </main>

    <footer class="bg-gray-800 text-gray-300">
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-2">
                    <h4 class="text-white text-lg font-semibold mb-4">Pondok Pesantren Al-Mujahidin</h4>
                    <p class="text-gray-400">Pondok Pesantren Al-Mujahidin berkomitmen untuk mendidik generasi muda Islami yang berakhlak mulia, berilmu pengetahuan, dan berjiwa pemimpin dalam membangun peradaban yang berkualitas.</p>
                     <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-white"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M..."></path></svg></a>
                        <a href="#" class="text-gray-400 hover:text-white"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M..."></path></svg></a>
                        <a href="#" class="text-gray-400 hover:text-white"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M..."></path></svg></a>
                    </div>
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
                <p>&copy; <?= date('Y') ?> Pondok Pesantren Al-Mujahidin. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>