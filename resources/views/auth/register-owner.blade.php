<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Owner - RentCar</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/auth/registrasi_owner.css')
</head>
<body>
    <div class="grid-background"></div>

    <nav class="navbar">
        <div class="nav-container">
            <a href="/" class="logo">RentCar</a>
            <a href="/" class="back-btn">← Kembali ke Beranda</a>
        </div>
    </nav>

    <div class="main-container">
        <div class="registration-container">
            <div class="form-header">
                <h1 class="form-title">Registrasi Owner</h1>
                <p class="form-subtitle">Daftarkan diri Anda sebagai pemilik kendaraan di RentCar</p>
            </div>

            @if($errors->any())
                <div class="error-container">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('owner.register') }}" id="registrationForm">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="form-input"
                           placeholder="Masukkan nama lengkap Anda"
                           value="{{ old('name') }}"
                           required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="form-input"
                           placeholder="contoh@email.com"
                           value="{{ old('email') }}"
                           required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-input"
                           placeholder="Minimal 8 karakter"
                           required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           class="form-input"
                           placeholder="Ulangi password Anda"
                           required>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="as_owner" value="1" id="as_owner" checked enabled>
                    <label for="as_owner">Daftar sebagai Owner</label>
                </div>

                <button type="submit" class="submit-btn">
                    🚗 Daftar Sekarang
                </button>
            </form>

            <div class="form-links">
                <a href="{{ route('login-owner') }}" class="form-link primary-link">
                    Sudah punya akun? Login di sini
                </a>
                <a href="/" class="form-link">
                    ← Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
    @push('script')
        @vite('resources/js/auth/registrasi_owner.js')

    @endpush
</body>
</html>
