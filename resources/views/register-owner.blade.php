<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Owner - RentCar</title>
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

        .registration-container {
            max-width: 500px;
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

        /* Error Messages */
        .error-container {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.3);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-container ul {
            list-style: none;
            margin: 0;
        }

        .error-container li {
            color: var(--error-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .error-container li::before {
            content: '⚠️';
            font-size: 0.9rem;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
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

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .checkbox-group input[type="checkbox"] {
            margin-right: 0.75rem;
            width: 20px;
            height: 20px;
            accent-color: var(--primary-yellow);
            cursor: not-allowed; /* Indicate it's disabled for editing */
        }

        .checkbox-group label {
            color: var(--pure-white);
            font-weight: 500;
            font-size: 1rem;
            cursor: default; /* Indicate it's disabled for editing */
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
                margin-top: 80px;
            }

            .registration-container {
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registrationForm');
            const submitBtn = form.querySelector('.submit-btn');

            // Form submission handling
            form.addEventListener('submit', function(e) {
                // Add loading state
                form.classList.add('loading');
                submitBtn.innerHTML = '⏳ Mendaftar...';

                // Validate passwords match
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('password_confirmation').value;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    // You might want to display this error more gracefully, e.g., in the error-container
                    alert('Password dan konfirmasi password tidak cocok!');
                    form.classList.remove('loading');
                    submitBtn.innerHTML = '🚗 Daftar Sekarang';
                    return;
                }
            });

            // Input focus animations
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    // This animation is already handled by the :focus pseudo-class in CSS
                    // No need for JavaScript to directly manipulate transform/opacity on parentElement
                });

                input.addEventListener('blur', function() {
                    // No need for JavaScript to directly manipulate transform/opacity on parentElement
                });
            });

            // Real-time password validation
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');

            function validatePasswords() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (confirmPassword && password !== confirmPassword) {
                    confirmPasswordInput.style.borderColor = 'var(--error-color)';
                } else if (confirmPassword) {
                    confirmPasswordInput.style.borderColor = 'var(--success-color)';
                } else {
                    confirmPasswordInput.style.borderColor = 'rgba(255, 215, 0, 0.2)';
                }
            }

            passwordInput.addEventListener('input', validatePasswords);
            confirmPasswordInput.addEventListener('input', validatePasswords);

            // Add entrance animation
            setTimeout(() => {
                document.querySelector('.registration-container').style.opacity = '1';
                document.querySelector('.registration-container').style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>
