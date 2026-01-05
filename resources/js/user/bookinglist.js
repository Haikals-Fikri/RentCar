document.addEventListener('DOMContentLoaded', function () {

    function updateCountdowns() {
        const timers = document.querySelectorAll('.countdown-timer');

        timers.forEach(timer => {
            const expire = timer.getAttribute('data-expire');

            if (!expire) return;

            const expiresAt = parseInt(expire, 10) * 1000;
            const now = Date.now();
            const distance = expiresAt - now;

            if (distance <= 0) {
                timer.textContent = 'EXPIRED';
                return;
            }

            const hours   = Math.floor(distance / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            timer.textContent = hours + 'h ' + minutes + 'm ' + seconds + 's';
        });
    }

    updateCountdowns();
    setInterval(updateCountdowns, 1000);
});
