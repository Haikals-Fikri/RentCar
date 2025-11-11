
import { Chart } from 'chart.js/auto';

const chartElement = document.getElementById('userOwnerChart');

if (chartElement) {
    // Ambil data dari atribut data-* di elemen canvas
    const userCount = chartElement.dataset.users;
    const ownerCount = chartElement.dataset.owners;

    const ctx = chartElement.getContext('2d');

    const gradientUser = ctx.createLinearGradient(0, 0, 0, 400);
    gradientUser.addColorStop(0, '#FFD700');
    gradientUser.addColorStop(1, '#FFA500');

    const gradientOwner = ctx.createLinearGradient(0, 0, 0, 400);
    gradientOwner.addColorStop(0, '#00C9FF');
    gradientOwner.addColorStop(1, '#92FE9D');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['User', 'Owner'],
            datasets: [{
                label: 'Jumlah',
                data: [userCount, ownerCount], // Gunakan data dari atribut
                backgroundColor: [gradientUser, gradientOwner],
                borderRadius: 12,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 5000 },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.7)',
                    titleColor: '#FFD700',
                    bodyColor: '#FFF',
                    bodyFont: { size: 14 },
                    padding: 10,
                    cornerRadius: 6
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255,215,0,0.1)' } },
                x: { grid: { display: false } }
            }
        }
    });
}
