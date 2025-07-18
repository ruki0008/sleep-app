<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            睡眠グラフ
        </h2>
    </x-slot>

    <div class="py-12 px-4">
        <canvas id="sleepChart" width="800" height="400"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const graphData = @json($graph_data);

        const labels = graphData.map(item => `${item.weekday} (${item.date})`);
        const sleepData = [];

        const dataset = {
            label: '睡眠時間',
            data: graphData.map(item => ({
                x: [item.start, item.end],
                y: `${item.weekday} (${item.date})`
            })),
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderRadius: 5,
            borderSkipped: false,
            minBarLength: 2 
        };
        
        console.log(dataset);
        const ctx = document.getElementById('sleepChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [dataset]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: {
                        type: 'linear',
                        min: 11,
                        max: 35,
                        ticks: {
                            callback: value => {
                                const h = Math.floor(value % 24);
                                return `${('0' + h).slice(-2)}:00`;
                            }
                        },
                        title: {
                            display: true,
                            text: '時間'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: '日付（曜日）'
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</x-app-layout>