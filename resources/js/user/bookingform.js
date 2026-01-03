    document.addEventListener('DOMContentLoaded', function() {
        const simImageInput = document.getElementById('sim_image');
        const fileChosenSpan = document.getElementById('file-chosen');

        simImageInput.addEventListener('change', function(){
            fileChosenSpan.textContent = this.files[0] ? this.files[0].name : 'Pilih file...';
        });

        // Script validasi tanggal Anda (sudah benar)
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const today = new Date().toISOString().split('T')[0];
        startDateInput.setAttribute('min', today);

        startDateInput.addEventListener('change', function() {
            endDateInput.setAttribute('min', startDateInput.value);
            if (endDateInput.value < startDateInput.value) {
                endDateInput.value = startDateInput.value;
            }
        });
    });
