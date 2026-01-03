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
