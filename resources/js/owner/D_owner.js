document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                this.classList.toggle('active');
            });

            overlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                menuToggle.classList.remove('active');
            });

            // Fade out success message after 5 seconds
            const alertContainer = document.querySelector('.alert-container');
            if (alertContainer) {
                setTimeout(() => {
                    alertContainer.style.opacity = '0';
                    alertContainer.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        alertContainer.style.display = 'none';
                    }, 500);
                }, 5000);
            }

            // Add hover effect to table rows
            const tableRows = document.querySelectorAll('.data-table tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 8px rgba(255, 215, 0, 0.1)';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });
        });
