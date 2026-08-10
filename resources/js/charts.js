import Chart from 'chart.js/auto';

document.addEventListener('alpine:init', () => {
    Alpine.data('barChart', (config) => ({
        init() {
            new Chart(this.$el.querySelector('canvas'), {
                type: 'bar',
                data: {
                    labels: config.labels,
                    datasets: [
                        {
                            label: 'Verifikasi',
                            data: config.data,
                            backgroundColor: 'rgba(99, 102, 241, 0.75)',
                            hoverBackgroundColor: 'rgba(79, 70, 229, 0.9)',
                            borderRadius: 6,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                },
            });
        },
    }));

    Alpine.data('doughnutChart', (config) => ({
        init() {
            new Chart(this.$el.querySelector('canvas'), {
                type: 'doughnut',
                data: {
                    labels: config.labels,
                    datasets: [
                        {
                            data: config.data,
                            backgroundColor: ['#10b981', '#f59e0b', '#6366f1', '#ef4444'],
                            hoverOffset: 4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                    },
                },
            });
        },
    }));
});
