<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Owner') - RentCar</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Memuat CSS utama & Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite('resources/css/owner/D_owner.css')

    {{-- Placeholder untuk CSS spesifik halaman --}}
    @stack('styles')
</head>
<body>
    <div class="grid-background"></div>
    <div class="overlay" id="overlay"></div>

    <div class="dashboard-container">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="owner-info">
                    <div class="owner-avatar">O</div>
                    <div class="owner-details">
                        <div class="owner-name">{{ session('owner_name', 'Owner') }}</div>
                        <div class="owner-role">Pemilik Kendaraan</div>
                    </div>
                </div>
                <div class="menu-title">MENU</div>
            </div>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('owner.dashboard') }}" class="nav-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                            <span class="nav-icon">🏠</span>
                            <span>Beranda</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('owner.bookings') }}" class="nav-link {{ request()->routeIs('owner.bookings') ? 'active' : '' }}">
                            <span class="nav-icon">📅</span>
                            <span>Pemesanan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('vehicles.create') }}" class="nav-link {{ request()->routeIs('vehicles.create') ? 'active' : '' }}">
                            <span class="nav-icon">➕</span>
                            <span>Tambah Kendaraan</span>
                        </a>
                    </li>
                    {{-- Ganti link # dengan route yang benar jika sudah ada --}}
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <span class="nav-icon">💰</span>
                            <span>Transaksi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('profile.owner') }}" class="nav-link {{ request()->routeIs('profile.owner') ? 'active' : '' }}">
                            <span class="nav-icon">👤</span>
                            <span>Profil</span>
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="#" class="nav-link">
                            <span class="nav-icon">⚙️</span>
                            <span>Pengaturan</span>
                        </a> --}}
                    </li>
                </ul>
            </nav>
            <div class="logout-container">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">
                         <span class="nav-icon">🚪</span>
                         <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-content">
            @yield('content') {{-- <-- KONTEN UNIK DARI SETIAP HALAMAN AKAN MUNCUL DI SINI --}}
        </main>
    </div>

    {{-- Memuat JS utama --}}
    @vite('resources/js/owner/D_owner.js')
    @stack('scripts')
</body>
</html>
