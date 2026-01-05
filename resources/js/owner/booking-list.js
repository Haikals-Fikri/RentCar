document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalClose = document.querySelector('.modal-close');

    if (!modal || !modalImage) return;

    /* =========================
       OPEN MODAL
    ========================= */
    function openModal(src) {
        modalImage.src = src;
        modalImage.classList.remove('zoomed');

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    /* =========================
       CLOSE MODAL
    ========================= */
    function closeModal() {
        modal.classList.remove('show');

        // delay dikit biar animasi smooth
        setTimeout(() => {
            modalImage.src = '';
            modalImage.classList.remove('zoomed');
        }, 200);

        document.body.style.overflow = '';
    }

    /* =========================
       EVENT DELEGATION
    ========================= */
    document.addEventListener('click', function (e) {

        /* === CLICK THUMBNAIL === */
        const thumb = e.target.closest('.payment-image-container');
        if (thumb) {
            const img = thumb.querySelector('.payment-thumb');
            if (img) openModal(img.src);
            return;
        }

        /* === CLICK BUTTON LIHAT === */
        const viewBtn = e.target.closest('.btn-mini.view');
        if (viewBtn) {
            e.preventDefault();
            e.stopPropagation();

            const wrapper = viewBtn.closest('.proof-action-wrapper');
            const img = wrapper?.querySelector('.payment-thumb');

            if (img) openModal(img.src);
            return;
        }

        /* === CLOSE BY BACKDROP === */
        if (e.target === modal) {
            closeModal();
        }

        /* === CLOSE BY BUTTON === */
        if (e.target.closest('.modal-close')) {
            closeModal();
        }
    });

    /* =========================
       ESC TO CLOSE
    ========================= */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            closeModal();
        }
    });

    /* =========================
       CLICK IMAGE TO ZOOM
    ========================= */
    modalImage.addEventListener('click', function (e) {
        e.stopPropagation();
        this.classList.toggle('zoomed');
    });

});
