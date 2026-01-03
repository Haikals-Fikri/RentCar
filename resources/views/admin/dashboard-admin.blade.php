<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RentCar</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite('resources/css/admin/D_admin.css')
    @vite('resources/js/admin/D_admin.js')
</head>
<body>
    <div class="grid-background"></div>

    <header class="admin-header">
        <h1 class="admin-title">RentCar Admin</h1>
        <button class="mobile-menu-toggle" id="mobileMenuToggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </header>

    <nav class="admin-nav" id="adminNav">
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <span>Dashboard Admin</span>
        </a>
        <a href="{{ route('admin.histogram') }}" class="nav-link">
            <span>Statistik Pengguna</span>
        </a>
        <a href="{{ route('admin.users') }}" class="nav-link">
            <span>Data User</span>
        </a>
        <a href="{{ route('admin.owners') }}" class="nav-link">
            <span>Data Owner</span>
        </a>
        <a href="{{ route('admin.booking') }}" class="nav-link">
            <span>Data Booking</span>
        </a>
        <a href="{{ route('logout') }}" class="nav-link logout-link">
            <span>Logout</span>
        </a>
    </nav>

    <main class="admin-content">
        <div class="welcome-message">
            <h2 class="welcome-title">Selamat Datang, Admin RentCar!</h2>
            <p class="welcome-subtitle">
                Kami senang Anda kembali. Di sini Anda dapat mengelola semua aspek platform penyewaan mobil Anda,
                mulai dari data pengguna dan pemilik hingga detail kendaraan dan transaksi.
                Mari kita jaga semuanya tetap teratur dan efisien!
            </p>
        </div>

        @yield('content')
    </main>

   </body>
</html>
