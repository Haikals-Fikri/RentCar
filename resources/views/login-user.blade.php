<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login User - RentCar</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-yellow: #FFD700;
            --secondary-yellow: #FFA500;
            --dark-yellow: #B8860B;
            --primary-black: #0A0A0A;
            --secondary-black: #1A1A1A;
            --accent-black: #2A2A2A;
            --pure-white: #FFFFFF;
            --light-gray: #F5F5F5;
            --border-color: rgba(255, 215, 0, 0.2);
            --error-color: #FF6B6B;
            --success-color: #4ECDC4;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--primary-black);
            color: var(--pure-white);
            min-height: 100vh;
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Modern Grid Background */
        .grid-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 25% 25%, rgba(255, 215, 0, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(255, 215, 0, 0.05) 0%, transparent 50%),
                linear-gradient(rgba(255, 215, 0, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 215, 0, 0.02) 1px, transparent 1px);
            background-size: 800px 800px, 600px 600px, 60px 60px, 60px 60px;
            z-index: -1;
            animation: backgroundFloat 30s ease-in-out infinite;
        }

        @keyframes backgroundFloat {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(1deg); }
            66% { transform: translate(-20px, 20px) rotate(-1deg); }
        }

        /* Modern Navbar */
        .navbar {
            background: rgba(10, 10, 10, 0.95);
            backdrop-filter: blur(20px);
            padding: 1.2rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid var(--border-color);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-yellow), var(--secondary-yellow));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            letter-spacing: -0.02em;
        }

        .back-btn {
            color: var(--pure-white);
            text-decoration: none;
            font-weight: 500;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .back-btn:hover {
            background: rgba(255, 215, 0, 0.1);
            color: var(--primary-yellow);
        }

        /* Main Container */
        .main-container {
            margin-top: 100px;
            min-height: calc(100vh - 100px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            background: rgba(26, 26, 26, 0.8);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 3rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 60px rgba(255, 215, 0, 0.1);
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .form-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-yellow), var(--secondary-yellow));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .form-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
        }

        .welcome-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        /* Error Messages */
        .error-container {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.3);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
            animation: shake 0.5s ease-in-out;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-container::before {
            content: '⚠️';
            font-size: 1.2rem;
        }

        .error-text {
            color: var(--error-color);
            font-weight: 500;
            margin: 0;
        }

        /* ----- PERUBAHAN CSS DI SINI ----- */
        /* Style untuk border merah pada input yang error */
        .form-input.is-invalid {
            border-color: var(--error-color) !important;
        }

        /* Style untuk teks error di bawah input */
        .field-error-text {
            color: var(--error-color);
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 0.5rem;
            display: block;
        }

        /* Style untuk error list */
        .error-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .error-list li {
            color: var(--error-color);
            font-weight: 500;
            margin-bottom: 0.3rem;
        }

        .error-list li:last-child {
            margin-bottom: 0;
        }
        /* ----- AKHIR PERUBAHAN CSS ----- */

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: block;
            color: var(--pure-white);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.2rem;
            background: rgba(42, 42, 42, 0.8);
            border: 2px solid rgba(255, 215, 0, 0.2);
            border-radius: 12px;
            color: var(--pure-white);
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-yellow);
            background: rgba(42, 42, 42, 0.9);
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        /* Icon CSS ada di sini, tapi tidak ada elemen .input-icon di HTML Anda.
           Saya biarkan jika Anda ingin menambahkannya nanti.
        */
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 215, 0, 0.7);
            font-size: 1.1rem;
            pointer-events: none;
            margin-top: 12px;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 1.2rem;
            background: linear-gradient(135deg, var(--primary-yellow), var(--secondary-yellow));
            color: var(--primary-black);
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        /* Links */
        .form-links {
            text-align: center;
            space-y: 1rem;
        }

        .form-link {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            padding: 0.5rem;
            border-radius: 8px;
        }

        .form-link:hover {
            color: var(--primary-yellow);
            background: rgba(255, 215, 0, 0.1);
        }

        .primary-link {
            color: var(--primary-yellow) !important;
            font-weight: 600;
        }

        /* Forgot Password */
        .forgot-password {
            text-align: right;
            margin-top: 0.5rem;
        }

        .forgot-password a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--primary-yellow);
        }

        /* Remember Me */
        .remember-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .remember-checkbox {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-yellow);
        }

        .remember-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            cursor: pointer;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
                margin-top: 80px;
            }

            .login-container {
                padding: 2rem;
            }

            .form-title {
                font-size: 2rem;
            }

            .nav-container {
                padding: 0 1rem;
            }
        }

        /* Loading State */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading .submit-btn {
            background: rgba(255, 215, 0, 0.5);
        }

        /* Success Animation */
        .success-animation {
            animation: successPulse 0.6s ease-in-out;
        }

        @keyframes successPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: rgba(255, 255, 255, 0.5);
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 215, 0, 0.2);
        }

        .divider span {
            padding: 0 1rem;
            font-size: 0.9rem;
        }
    </style>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const submitBtn = form.querySelector('.submit-btn');

            // Form submission handling
            form.addEventListener('submit', function(e) {
                // Add loading state
                form.classList.add('loading');
                submitBtn.innerHTML = '⏳ Masuk...';

                // Basic validation (hanya cek kosong, biarkan Laravel urus sisanya)
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;

                if (!email || !password) {
                    e.preventDefault();
                    alert('Mohon lengkapi semua field!');
                    form.classList.remove('loading');
                    submitBtn.innerHTML = '🚗 Masuk Sekarang';
                    return;
                }
            });

            // Input focus animations
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                    // Cek jika ada ikon
                    const icon = this.parentElement.querySelector('.input-icon');
                    if (icon) {
                        icon.style.color = 'var(--primary-yellow)';
                    }
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                    // Cek jika ada ikon
                    const icon = this.parentElement.querySelector('.input-icon');
                    if (icon) {
                        icon.style.color = 'rgba(255, 215, 0, 0.7)';
                    }
                });
            });
            const passwordInput = document.getElementById('password');
            passwordInput.addEventListener('dblclick', function() {
                if (this.type === 'password') {
                    this.type = 'text';
                    setTimeout(() => {
                        this.type = 'password';
                    }, 1000);
                }
            });

            // Add entrance animation
            setTimeout(() => {
                document.querySelector('.login-container').style.transform = 'translateY(0)';
                document.querySelector('.login-container').style.opacity = '1';
            }, 100);

            // Auto-focus on email field
            setTimeout(() => {
                // Fokus hanya jika tidak ada error dari server
                if (!document.querySelector('.is-invalid')) {
                     document.getElementById('email').focus();
                }
            }, 500);
        });
    </script>
</body>
</html>
