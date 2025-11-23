document.addEventListener('DOMContentLoaded', () => {
    const globalActions = document.getElementById('globalFormActions');

    // Sembunyikan semua form & tombol global saat load
    document.querySelectorAll('.profile-edit-form').forEach(f => f.style.display = 'none');
    document.querySelectorAll('.profile-display').forEach(d => d.style.display = 'block');
    if(globalActions) globalActions.style.display = 'none';

    // Auto-remove alert notifikasi setelah 5 detik
    setTimeout(() => {
        document.querySelectorAll('.profile-alert').forEach(a => a.remove());
    }, 5000);

    // Toggle form edit
    document.querySelectorAll('.profile-btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            const section = btn.closest('.profile-section');
            const form = section.querySelector('.profile-edit-form');
            const display = section.querySelector('.profile-display');

            const isVisible = form.style.display === 'block';
            form.style.display = isVisible ? 'none' : 'block';
            display.style.display = isVisible ? 'block' : 'none';

            if(globalActions) globalActions.style.display = isVisible ? 'none' : 'flex';
        });
    });

    // Tombol simpan
    document.querySelectorAll('.profile-btn-save').forEach(btn => {
        btn.addEventListener('click', () => {
            // Form utama
            const form = btn.closest('form');
            if(form) form.submit(); // Submit form seluruh section sekaligus

            // Sembunyikan semua form & tombol global setelah submit
            document.querySelectorAll('.profile-edit-form').forEach(f => f.style.display = 'none');
            document.querySelectorAll('.profile-display').forEach(d => d.style.display = 'block');
            if(globalActions) globalActions.style.display = 'none';
        });
    });

    // Tombol batal
    document.querySelectorAll('.profile-btn-cancel').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.profile-edit-form').forEach(f => f.style.display = 'none');
            document.querySelectorAll('.profile-display').forEach(d => d.style.display = 'block');
            if(globalActions) globalActions.style.display = 'none';
        });
    });
});
