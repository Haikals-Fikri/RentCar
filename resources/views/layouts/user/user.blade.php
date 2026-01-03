<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard User') - RentCar</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite('resources/css/user/D_user.css')
    @stack('styles')
</head>
<body>
    <div class="grid-background"></div>
    <div class="overlay" id="overlay"></div>

    <div class="dashboard-container">
        {{-- ===== SIDEBAR ===== --}}
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2 class="logo">RentCar</h2>
                @auth
                <div class="user-info">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    <div class="user-details">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">Pelanggan</div>
                    </div>
                </div>
                @endauth
            </div>
            <nav class="nav-menu-container">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt nav-icon"></i>
                            <span>Beranda</span>
                        </a>
                    </li>
                    <li class="nav-item">
                         <a href="{{ route('user.profile') }}" class="nav-link {{ request()->routeIs('profile.user') ? 'active' : '' }}">
                             <i class="fas fa-user nav-icon"></i>
                            <span>Profil Saya</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('user.bookings') }}" class="nav-link {{ request()->routeIs('user.bookings*') ? 'active' : '' }}">
                            <i class="fas fa-history nav-icon"></i>
                            <span>Riwayat Sewa</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('user.reviews') }}" class="nav-link {{ request()->routeIs('user.reviews') ? 'active' : '' }}">
                            <i class="fas fa-star nav-icon"></i>
                            <span>Ulasan Saya</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="logout-container">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn nav-link">
                        <i class="fas fa-sign-out-alt nav-icon"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- ===== MAIN CONTENT WRAPPER ===== --}}
        <div class="main-content-wrapper">
            <header class="content-header">
                <button class="mobile-menu-toggle" id="menuToggle">
                    <span></span><span></span><span></span>
                </button>
                {{-- Judul halaman akan diambil dari @section('title') di file view --}}
                <h1 class="page-title">@yield('title')</h1>

                {{-- Anda bisa menambahkan bagian notifikasi di sini jika ingin konsisten di semua halaman --}}
                <div class="header-actions">
                    <button class="action-btn">
                        <span>🔔</span>
                        <span>Notifikasi</span>
                    </button>
                </div>
            </header>

            {{-- Di sinilah konten dari file view akan dimasukkan --}}
            <main class="content-body">
                @yield('content')
            </main>
        </div>
    </div>

    @vite('resources/js/user/D_user.js')
    @stack('scripts')
</body>
</html>
