<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login User - RentCar</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/auth/login_user.css')
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
        <div class="login-container">
            <div class="form-header">
                <h1 class="form-title">Selamat Datang</h1>
                <p class="form-subtitle">Masuk ke akun RentCar Anda</p>
            </div>

            @if ($errors->any())
                <div class="error-container">
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/login-user" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>

                    <input type="email"
                           id="email"
                           name="email"
                           class="form-input @error('email') is-invalid @enderror"
                           placeholder="Masukkan email Anda"
                           value="{{ old('email') }}"
                           required>

                    @error('email')
                        <span class="field-error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>

                    <input type="password"
                           id="password"
                           name="password"
                           class="form-input @error('password') is-invalid @enderror"
                           placeholder="Masukkan password Anda"
                           required>

                    @error('password')
                        <span class="field-error-text">{{ $message }}</span>
                    @enderror
                    <div class="forgot-password">
                        <a href="/forgot-password">Lupa password?</a>
                    </div>
                </div>

                <div class="remember-container">
                    <input type="checkbox" id="remember" name="remember" class="remember-checkbox">
                    <label for="remember" class="remember-label">Ingat saya</label>
                </div>

                <button type="submit" class="submit-btn">
                     Masuk Sekarang
                </button>
            </form>

            <div class="divider">
                <span>atau</span>
            </div>

            <div class="form-links">
                <a href="/register-user" class="form-link primary-link">
                    Belum punya akun? Daftar di sini
                </a>
                <a href="/" class="form-link">
                    ← Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

   @push('script')
    @vite('resources/js/auth/login_user.js')

   @endpush
</body>
</html>
