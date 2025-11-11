document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const adminNav = document.getElementById('adminNav');

            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    adminNav.classList.toggle('active');

                    // Toggle menu icon
                    const spans = this.querySelectorAll('span');
                    if (adminNav.classList.contains('active')) {
                        spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                        spans[1].style.opacity = '0';
                        spans[2].style.transform = 'rotate(-45deg) translate(5px, -5px)';
                    } else {
                        spans[0].style.transform = 'none';
                        spans[1].style.opacity = '1';
                        spans[2].style.transform = 'none';
                    }
                });
            }

            // Set active nav link based on current URL
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');

            navLinks.forEach(link => {
                // Ambil path dari href (tanpa mencoba mengevaluasi route Laravel)
                const href = link.getAttribute('href');
                // Cek apakah path saat ini mengandung bagian dari href
                // Ini pendekatan sederhana, lebih baik menambahkan class active dari Laravel
                if (href && currentPath.includes(href.split('/').pop())) {
                    link.classList.add('active');
                } else if (link !== navLinks[0]) {
                    link.classList.remove('active');
                }
            });
        });
