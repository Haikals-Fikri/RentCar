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
