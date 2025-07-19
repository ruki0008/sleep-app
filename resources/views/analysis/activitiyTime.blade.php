<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            睡眠グラフ
        </h2>
    </x-slot>
    <div class="text-center mt-2">
        <span class="bg-blue-300 text-white p-1 rounded">睡眠</span><span class="bg-red-300 text-white p-1 rounded">運動</span>
    </div>
    <div class="px-4">
        <canvas id="sleepChart" class="w-full h-full"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const graphData = @json($graph_data);

        const labels = graphData.map(item => `${item.weekday} (${item.date})`);
        // const sleepData = [];
        
        //typeがsleepなら青、exerciseなら赤
        const backgroundColor = graphData.map(item =>
            item.type === 'sleep'
                ? 'rgba(54, 162, 235, 0.6)'
                : 'rgba(255, 57, 107, 0.5)'
        );

        // const dataset_label = graphData.map(item =>
        //     item.type === 'sleep'
        //         ? '睡眠時間'
        //         : '運動時間'
        // );
        // console.log(backgroundColor, dataset_label);
        
        const dataset = {
            label: '時間',
            data: graphData.map(item => ({
                x: [item.start, item.end],
                y: `${item.weekday} (${item.date})`
            })),
            backgroundColor: backgroundColor,
            borderRadius: 5,
            borderSkipped: false,
            minBarLength: 2 
        };
        
        
        const ctx = document.getElementById('sleepChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [dataset]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const item = graphData[context.dataIndex];
                                return `時間: ${item.start_t}〜${item.end_t}`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>