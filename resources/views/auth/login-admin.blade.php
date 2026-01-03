<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - RentCar</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/auth/login_admin.css')
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
                <h1 class="form-title">Login Admin</h1>
                <p class="form-subtitle">Masuk ke akun administrator Anda</p>
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

            <form method="POST" action="{{ route('login-admin') }}" id="loginForm">
                @csrf

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
                           placeholder="Masukkan password Anda"
                           required>
                </div>

                <button type="submit" class="submit-btn">
                    🔒 Login Admin
                </button>
            </form>

            <div class="form-links">
                <a href="/" class="form-link">
                    ← Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
    @push('script')
        @vite('resources/js/auth/login_admin.js')
    @endpush

</body>
</html>
