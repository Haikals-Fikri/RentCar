document.addEventListener('DOMContentLoaded', function() {
    // Animasi cards
    const bookingCards = document.querySelectorAll('.booking-card');
    bookingCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Countdown timer real-time
    function updateCountdowns() {
        const timers = document.querySelectorAll('.countdown-timer');

        timers.forEach(timer => {
            const expiresAt = new Date(timer.dataset.expires).getTime();
            const now = new Date().getTime();
            const distance = expiresAt - now;

            if (distance < 0) {
                timer.innerHTML = 'EXPIRED';
                timer.style.background = '#dc3545';
                timer.style.color = 'white';
                // Auto reload setelah 10 detik expired
                setTimeout(() => {
                    location.reload();
                }, 10000);
            } else {
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                timer.innerHTML = hours + "h " + minutes + "m " + seconds + "s";
            }
        });
    }

    // Update countdown setiap detik
    setInterval(updateCountdowns, 1000);
    updateCountdowns(); // Jalankan sekali saat load
});
