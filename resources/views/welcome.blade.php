<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentCar - Premium Car Rental</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/welcome.css')
    @vite('resources/js/welcome.js')
</head>
<body>
    <div class="grid-background"></div>

    <div id="notif-popup" class="notif-popup">
        <div class="notif-content">
            <div>
                <p>📞 Admin: +62 895 0451 7110</p>
                <p>✉️ adminrentcar@gmail.com</p>
            </div>
            <button class="notif-close" id="notif-close">&times;</button>
        </div>
    </div>

    <nav class="navbar" id="navbar">
        <div class="container nav-container">
            <a href="#" class="logo">
                RENTCAR
            </a>

            <ul class="nav-menu">
                <li class="dropdown">
                    <a href="#home">Beranda </a>
                    <ul class="dropdown-menu">
                        <li><a href="#footer">Informasi</a></li>
                        <li><a href="#" id="berita-link">Tentang</a></li>
                        <li><a href="#" id="visimisi-link">Visi Misi</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#benefits">Layanan </a>
                    <ul class="dropdown-menu">
                        <li><a href="#fleet">Mobil</a></li>
                        <li><a href="#partner">Mitra</a></li>
                    </ul>
                </li>

                <li>
                    <a href="https://wa.me/6289504517110?text=Halo%20RentCar%2C%20saya%20butuh%20informasi%20sewa%20mobil%20🚗"
                       target="_blank">
                        Kontak
                    </a>
                </li>

                <!-- REVISI: Dropdown Login dengan pilihan User dan Owner -->
                <li class="dropdown">
                    <a href="#" class="login-btn">Login</a>
                    <ul class="dropdown-menu">
                        <li><a href="/login-user" class="login-option">👤 Login User</a></li>
                        <li><a href="/login-owner" class="login-option">🏢 Login Owner</a></li>
                    </ul>
                </li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <section class="hero" id="home">
        <div class="container hero-container">
            <div class="hero-content">
                <h1 class="hero-title">
                    Awali perjalanan baru Anda<br>
                    bersama <span class="highlight">Kami</span>
                </h1>
                <p class="hero-description">
                    Apapun tujuan Anda, kami menyediakan beragam pilihan mobil premium yang siap menemani perjalanan, dari mobilitas harian di tengah kota hingga petualangan seru ke luar kota.
                </p>
                <div class="hero-buttons">
                    <a href="/login-user" class="btn hero-btn">Mulai Sewa Mobil</a>
                    <a href="/login-owner" class="btn hero-btn-secondary">Login Mitra</a>
                </div>
            </div>
            <div class="car-showcase">
                <img src="/storage/vehicles/mobil3.png" alt="Mobil Brio">
            </div>
        </div>
    </section>

    <div id="visimisi-popup" class="notif-popup">
        <div class="notif-content">
            <button class="notif-close" id="visimisi-close">&times;</button>
            <h3>Tentang RentCar</h3>
            <p><strong>Visi:</strong> Menjadi platform rental mobil premium terpercaya di Indonesia yang memudahkan masyarakat dalam memenuhi kebutuhan transportasi dengan layanan modern dan aman.</p>
            <p><strong>Misi:</strong></p>
            <ul style="margin-left:1rem; padding-left:1rem; margin-bottom: 1rem;">
                <li>Menyediakan pilihan mobil berkualitas dan bervariasi.</li>
                <li>Memberikan layanan customer service profesional 24/7.</li>
                <li>Mempermudah proses booking kendaraan melalui sistem digital.</li>
                <li>Menjalin kemitraan dengan pemilik mobil lokal.</li>
            </ul>
            <p><strong>Latar Belakang:</strong> Website ini dibangun untuk menjawab kebutuhan masyarakat akan rental mobil yang mudah diakses secara online, dengan sistem booking cepat, transparansi harga, dan kemudahan konfirmasi tanpa harus datang langsung ke kantor rental.</p>
        </div>
    </div>

    <section class="section benefits fade-in" id="benefits">
        <div class="container">
            <h2 class="section-title">Kenapa Sewa di RentCar?</h2>
            <h3 style="margin: 2rem 0 1rem; color: var(--pure-white);">🚘 Fitur Untuk Pelanggan</h3>
            <div class="benefit-cards">
                <div class="benefit-card">
                    <h3>🚗 Armada Premium</h3>
                    <p>Pilih mobil favorit dari berbagai merk & jenis premium sesuai kebutuhan.</p>
                </div>
                <div class="benefit-card">
                    <h3>💳 Pembayaran Fleksibel</h3>
                    <p>Bayar via transfer, QRIS, atau kartu kredit. Praktis & aman.</p>
                </div>
                <div class="benefit-card">
                    <h3>📱 Booking Mudah</h3>
                    <p>Sewa mobil cepat & aman langsung dari smartphone kamu, 24/7.</p>
                </div>
            </div>
            <h3 style="margin: 3rem 0 1rem; color: var(--pure-white);">💬 Apa Kata Mereka?</h3>
            <div class="benefit-cards">
                <div class="benefit-card">
                    <p>"Mobilnya bersih, booking cepat, harga transparan. Top banget!"</p>
                    <strong>- Rian, Parepare</strong>
                </div>
                <div class="benefit-card">
                    <p>"Baru pertama sewa di RentCar, pelayanannya ramah & mobil oke."</p>
                    <strong>- Amel, Parepare</strong>
                </div>
                <div class="benefit-card">
                    <p>"Langganan sewa di sini, mobilnya selalu terawat!"</p>
                    <strong>- Dika, Parepare</strong>
                </div>
            </div>
            <div class="benefit-cta">
                <a href="/login-user" class="btn">Sewa Mobil Sekarang</a>
            </div>
        </div>
    </section>

    <section class="section car-slider fade-in" id="fleet">
        <div class="container">
            <h2 class="section-title">Pilihan Mobil Favorit</h2>
            <div class="slider-container">
                <div class="slider">
                    <div class="slides">
                        <div class="slide">
                            <img src="/storage/vehicles/mobil4.png" alt="Honda Brio">
                            <h3>Honda Brio</h3>
                            <span>Rp. 15.000,00/Hari</span>
                        </div>
                        <div class="slide">
                            <img src="/storage/vehicles/mobil2.png" alt="Toyota Avanza">
                            <h3>Toyota Avanza</h3>
                            <span>Rp. 250.000,00/Hari</span>
                        </div>
                        <div class="slide">
                            <img src="/storage/vehicles/innova.png" alt="Toyota Innova">
                            <h3>Toyota Innova Reborn</h3>
                            <span>Rp. 550.000,00/Hari</span>
                        </div>
                    </div>
                </div>
                <button class="slider-btn prev">&#10094;</button>
                <button class="slider-btn next">&#10095;</button>
            </div>
            <div class="benefit-cta">
                <a href="/login-user" class="btn btn-secondary">Lihat Semua Mobil</a>
            </div>
        </div>
    </section>

    <section class="section partner-section fade-in" id="partner">
        <div class="container">
            <h2 class="section-title">Keuntungan Bergabung Dalam Kemitraan</h2>
            <div class="benefit-cards">
                <div class="benefit-card">
                    <h3>📊 Laporan Transparansi</h3>
                    <p>Dapatkan Laporan Keuangan dan Informasi Rental Kamu secara Berkala</p>
                </div>
                <div class="benefit-card">
                    <h3>🌐 Akses Pasar Lebih Luas</h3>
                    <p>Owner Rental Tak Perlu Repot Cari Pelanggan Sendiri</p>
                </div>
                <div class="benefit-card">
                    <h3>📝 Kemudahan Administrasi</h3>
                    <p>Platform Menyediakan Invoice & Bukti Transaksi Otomatis</p>
                </div>
                <div class="benefit-card">
                    <h3>🔔 Pemesanan Prioritas</h3>
                    <p>Pemesanan Langsung Via Sistem Masuk Dashboard Owner</p>
                </div>
            </div>
            <div class="benefit-cta">
                <h3 style="color: var(--pure-white); margin-bottom: 1.5rem;">Tertarik Jadi Partner <span style="color: var(--primary-yellow);">RentCar?</span></h3>
                <div class="partner-buttons">
                    <a href="/register-owner" class="btn">Daftar Mitra</a>
                    <a href="/login-owner" class="btn btn-secondary">Login Mitra</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer fade-in" id="footer">
        <div class="container">
            <div class="footer-container">
                <div class="footer-section">
                    <h3>Berita Terbaru</h3>
                    <ul>
                        <li><a href="#">🎉 Promo Spesial Akhir Tahun - Diskon 30%</a></li>
                        <li><a href="#">🚀 Peluncuran Fitur Booking Online Terbaru</a></li>
                        <li><a href="#">🌧️ Tips Berkendara Aman di Musim Hujan</a></li>
                        <li><a href="#">📍 Ekspansi Layanan ke 10 Kota Baru</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Lokasi Kami</h3>
                    <p><strong>🏢 Kantor Pusat:</strong></p>
                    <p>Jl. Balaikota No.1<br>
                    Jakarta Pusat 10220<br>
                    Kota Parepare, Sulawesi Selatan 91122</p>
                    <p><strong>📞 Telepon:</strong> +62 89 5045 17110</p>
                    <p><strong>✉️ Email:</strong> info@rentcar.co.id</p>
                </div>
                <div class="footer-section">
                    <h3>Layanan Pelanggan</h3>
                    <ul>
                        <li><a href="#">❓ FAQ</a></li>
                        <li><a href="#">📋 Syarat & Ketentuan</a></li>
                        <li><a href="#">🔒 Kebijakan Privasi</a></li>
                        <li><a href="#">💬 Bantuan</a></li>
                        <li><a href="#">📞 Hubungi Kami</a></li>
                    </ul>
                    <p><strong>🕐 Call Center 24/7:</strong><br>89-5045-17110</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 RentCar. Semua hak cipta dilindungi undang-undang.</p>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/6289504517110?text=Halo%20RentCar%2C%20saya%20butuh%20bantuan%20🚗"
       class="wa-float" target="_blank" title="Chat Admin">
        <img src="/storage/vehicles/wa-icon.png" alt="Chat Admin">
    </a>
</body>
</html>
