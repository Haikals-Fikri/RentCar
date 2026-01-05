document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const closeBtn = document.querySelector('.close-modal');

    if (!modal || !modalImg || !closeBtn) return;

    const openModal = (imgSrc) => {
        modalImg.src = imgSrc;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    const closeModal = () => {
        modal.classList.remove('active');

        setTimeout(() => {
            modalImg.src = '';
            document.body.style.overflow = '';
        }, 250);
    };

    document.querySelectorAll('.preview-image').forEach(img => {
        img.addEventListener('click', () => {
            openModal(img.dataset.image);
        });
    });

    closeBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', e => {
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });
});
