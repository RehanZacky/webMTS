<?php
// Koneksi ke database
include 'koneksi.php';

// Mengambil data dari tabel `pegawai` dan diurutkan berdasarkan kolom `urutan`
$query_pegawai = "SELECT * FROM pegawai ORDER BY urutan ASC";
$pegawai_result = mysqli_query($koneksi, $query_pegawai);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pegawai - Pondok Pesantren Al-Mujahidin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* CSS untuk animasi (opsional, bisa dihapus jika tidak ingin animasi sama sekali) */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
        .animate-fadeInDown { animation: fadeInDown 0.6s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-50">

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

    <main class="pt-24 pb-16 md:pt-32 md:pb-24">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 animate-fadeInDown">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    Profil Guru & <span class="text-green-600">Staff</span>
                </h1>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Tenaga pendidik dan staf yang berdedikasi di Pondok Pesantren Al-Mujahidin, siap membimbing santri menuju kesuksesan dunia dan akhirat.
                </p>
                <div class="w-24 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full mx-auto mt-6"></div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12 animate-fadeInUp">
                <?php if ($pegawai_result && mysqli_num_rows($pegawai_result) > 0) : ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php while ($pegawai = mysqli_fetch_assoc($pegawai_result)) : ?>
                            <div class="bg-gray-50 rounded-xl p-8 text-center flex flex-col items-center transition-all duration-300 hover:shadow-xl hover:bg-white hover:-translate-y-1">
                                <img src="<?= htmlspecialchars($pegawai['foto']) ?>" alt="Foto <?= htmlspecialchars($pegawai['nama']) ?>" class="w-28 h-28 rounded-full object-cover mb-4 ring-4 ring-green-100">
                                <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($pegawai['nama']) ?></h3>
                                <p class="text-green-600 font-semibold mb-3"><?= htmlspecialchars($pegawai['jabatan']) ?></p>
                                <p class="text-gray-500 text-sm flex-grow">
                                    <?= htmlspecialchars($pegawai['pengalaman_kerja']) ?>
                                </p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else : ?>
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <div class="text-gray-400 text-3xl">👥</div>
                        </div>
                        <p class="text-gray-500 text-lg">Data pegawai belum tersedia.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-gray-300 mt-16">
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                 <div class="lg:col-span-2">
                    <h4 class="text-white text-lg font-semibold mb-4">Pondok Pesantren Al-Mujahidin</h4>
                    <p class="text-gray-400">Pondok Pesantren Al-Mujahidin berkomitmen untuk mendidik generasi muda Islami yang berakhlak mulia, berilmu pengetahuan, dan berjiwa pemimpin dalam membangun peradaban yang berkualitas.</p>
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

    <script>
        document.getElementById("menu-toggle").addEventListener("click", function () {
            const menu = document.getElementById("mobile-menu");
            menu.classList.toggle("hidden");
        });
    </script>
</body>
</html>