document.addEventListener('DOMContentLoaded', function() {
    // ===== Hamburger Menu Toggle =====
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });

        // Tutup menu saat klik link
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', () => {
                if (hamburger.classList.contains('active')) {
                    hamburger.classList.remove('active');
                    navMenu.classList.remove('active');
                }
            });
        });
    }

    // Popup Setup
    function setupPopup(linkId, popupId, closeId) {
        const link = document.getElementById(linkId);
        const popup = document.getElementById(popupId);
        const closeBtn = document.getElementById(closeId);

        if (link && popup && closeBtn) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                popup.classList.add('show');
            });

            closeBtn.addEventListener('click', function() {
                popup.classList.remove('show');
            });

            // Tutup popup jika klik luar konten
            window.addEventListener('click', function(e) {
                if (e.target === popup) {
                    popup.classList.remove('show');
                }
            });
        }
    }

    setupPopup('berita-link', 'notif-popup', 'notif-close');
    setupPopup('visimisi-link', 'visimisi-popup', 'visimisi-close');

    // ===== Smooth Scrolling =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href.length > 1 && document.querySelector(href)) {
                e.preventDefault();
                document.querySelector(href).scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ===== Navbar Scroll Effect =====
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // ===== Car Slider =====
    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
        let currentIndex = 0;
        const slides = sliderContainer.querySelectorAll('.slide');
        const totalSlides = slides.length;
        const slidesWrapper = sliderContainer.querySelector('.slides');
        const nextBtn = sliderContainer.querySelector('.next');
        const prevBtn = sliderContainer.querySelector('.prev');

        function updateSlide() {
            if (slidesWrapper) {
                slidesWrapper.style.transform = `translateX(-${currentIndex * 100}%)`;
            }
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % totalSlides;
                updateSlide();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                updateSlide();
            });
        }

        // Auto slide setiap 5 detik
        setInterval(() => {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateSlide();
        }, 5000);
    }

    // ===== Fade-in Animation on Scroll =====
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
    });

    // ===== Page Load Fade-in =====
    window.addEventListener('load', () => {
        document.body.style.opacity = '1';
    });
});
