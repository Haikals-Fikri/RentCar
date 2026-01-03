document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const submitBtn = form.querySelector('.submit-btn');

            // Form submission handling
            form.addEventListener('submit', function(e) {
                // Add loading state
                form.classList.add('loading');
                submitBtn.innerHTML = '⏳ Memproses...';
            });

            // Add entrance animation
            setTimeout(() => {
                document.querySelector('.login-container').style.opacity = '1';
                document.querySelector('.login-container').style.transform = 'translateY(0)';
            }, 100);
        });

