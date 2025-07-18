<x-app-layout>
    <script>
        const graph_datas = @json($graph_datas);

        const datasets = [];
        const colors = ['rgba(54, 162, 235, 0.6)', 'rgba(255, 99, 132, 0.6)', 'rgba(255, 206, 86, 0.6)'];
        let colorIndex = 0;

        // 全体のラベル（日付 + 曜日）を取得
        const allDates = new Set();

        // 各ユーザーのデータセットを作成
        for (const [username, records] of Object.entries(graph_datas)) {
            const sleepData = records.map(item => {
                const label = `${item.weekday} (${item.date})`;
                allDates.add(label);
                return {
                    x: item.start,
                    x2: item.end,
                    y: label
                };
            });

            datasets.push({
                label: `${username}の睡眠`,
                data: sleepData,
                backgroundColor: colors[colorIndex % colors.length],
                borderRadius: 5,
                borderSkipped: false
            });

            colorIndex++;
        }

        const ctx = document.getElementById('sleepChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                datasets: datasets
            },
            options: {
                indexAxis: 'y',
                parsing: {
                    xAxisKey: 'x',
                    x2AxisKey: 'x2',
                    yAxisKey: 'y'
                },
                scales: {
                    x: {
                        type: 'linear',
                        min: 11,
                        max: 35,
                        title: {
                            display: true,
                            text: '時間'
                        },
                        ticks: {
                            callback: value => {
                                const h = Math.floor(value % 24);
                                return `${('0' + h).slice(-2)}:00`;
                            }
                        }
                    },
                    y: {
                        stacked: true, // 重ねるなら true、並列なら false
                        title: {
                            display: true,
                            text: '日付（曜日）'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const start = ctx.raw.x;
                                const end = ctx.raw.x2;
                                const h = Math.floor(end - start);
                                const m = Math.round((end - start - h) * 60);
                                return `${ctx.dataset.label}: ${start} - ${end}（${h}時間${m}分）`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>