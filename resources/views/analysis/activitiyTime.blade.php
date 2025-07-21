<x-app-layout>
    @if(session('message'))
        <div class="text-white font-bold bg-blue-400">
            {{ session('message') }}
        </div>
    @endif
    <section class="text-gray-400 body-font relative">
        <h2 class="text-center my-6 font-semibold text-xl leading-tight">
            睡眠、運動グラフ
        </h2>
        {{-- <div class="text-center mt-6">
            @foreach(array_keys($graph_data) as $month)
                <button onclick="changeMonth('{{ $month }}')" class="bg-gray-300 hover:bg-gray-500 text-black px-3 py-1 rounded m-1">
                    {{ \Carbon\Carbon::parse($month . '-01')->format('n月') }}
                </button>
            @endforeach
        </div> --}}
        <div class="text-center my-4">
            {{-- 年ボタン --}}
            @foreach(array_keys($graph_data) as $year)
                <button class="year-btn bg-indigo-300 text-white px-3 py-1 rounded m-1"
                        onclick="showMonths('{{ $year }}')">
                    {{ $year }}年
                </button>
            @endforeach
        </div>

        <div class="text-center mb-4" id="month-buttons"></div>
        <div class="text-center mt-2">
            <span class="bg-blue-300 text-white p-1 rounded">睡眠</span><span class="bg-red-300 text-white p-1 rounded">運動</span>
        </div>
        <div class="px-4">
            <canvas id="sleepChart" class="w-full h-full"></canvas>
        </div>
    </section>

    <section class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto">
        <div class="flex flex-col text-center w-full mb-4">
            <h1 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">データ</h1>
            {{-- <p class="lg:w-2/3 mx-auto leading-relaxed text-base">Banh mi cornhole echo park skateboard authentic crucifix neutra tilde lyft biodiesel artisan direct trade mumblecore 3 wolf moon twee</p> --}}
        </div>
        <div id="activity-table" class="mt-6"></div>
        {{-- <div class="lg:w-2/3 w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                    <tr>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">就寝 開始時間</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">起床 終了時間</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">睡眠 運動の質</th>
                        {{-- <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">Price</th>
                        <th class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th> --}}
                    {{-- </tr>
                </thead>
                <tbody>
                    @foreach($activities as $item)
                    <tr>
                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ $item->start_time }}</td>
                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ $item->end_time }}</td>
                        <td class="border-b-2 border-gray-200 px-4 py-3"><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $item->quality }}</span></td>
                    </tr>  
                    @endforeach
                </tbody>
            </table>
        </div> --}}
        {{-- <button class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">Button</button> --}}
        {{-- </div> --}}
    </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ mix('/js/app.js') }}"></script>
    @routes
    <script>
        const graphData = @json($graph_data);
        const ctx = document.getElementById('sleepChart').getContext('2d');
        let currentYear = null;
        let currentMonth = null;
        let chart = null;

        function generateChartData(data) {
            const labels = data.map(item => `${item.weekday} (${item.date})`);
            const backgroundColor = data.map(item =>
                item.type === 'sleep'
                    ? 'rgba(54, 162, 235, 0.6)'
                    : 'rgba(255, 99, 132, 0.5)'
            );

            return {
                labels: labels,
                datasets: [{
                    label: '時間',
                    data: data.map(item => ({
                        x: [item.start, item.end],
                        y: `${item.weekday} (${item.date})`
                    })),
                    backgroundColor: backgroundColor,
                    borderRadius: 5,
                    borderSkipped: false,
                    minBarLength: 2
                }]
            };
        }

        function showMonths(year) {
            currentYear = year;
            const months = Object.keys(graphData[year]);
            const monthButtons = months.map(month => {
                const label = parseInt(month, 10) + '月';
                return `<button class="month-btn bg-blue-300 text-white px-3 py-1 rounded m-1"
                            onclick="showGraph('${month}')">${label}</button>`;
            }).join('');
            document.getElementById('month-buttons').innerHTML = monthButtons;

            // 最初の月を自動表示
            showGraph(months[0]);
        }

        function showGraph(month) {
            currentMonth = month;
            const data = graphData[currentYear][currentMonth];
            const chartData = generateChartData(data);

            if (chart) {
                chart.data = chartData;
                chart.update();
            } else {
                chart = new Chart(ctx, {
                    type: 'bar',
                    data: chartData,
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
                                title: { display: true, text: '時間' }
                            },
                            y: { title: { display: true, text: '日付（曜日）' } }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const item = data[context.dataIndex];
                                        const item_labels = [`時間: ${item.start_t}〜${item.end_t}`]
                                        if (item.memo) item_labels.push(`メモ: ${item.memo}`);
                                        return item_labels;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            let tableHtml = `
                <table class="table-auto w-full text-left mt-4 border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2">日付</th>
                            <th class="border px-4 py-2">曜日</th>
                            <th class="border px-4 py-2">タイプ</th>
                            <th class="border px-4 py-2">開始</th>
                            <th class="border px-4 py-2">終了</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.forEach(item => {
                tableHtml += `
                    <tr>
                        <td class="border px-4 py-2">
                            <a href="${route('activities.show', { activity: item.id })}" class="text-blue-600 hover:underline">${item.date}</a>
                        </td>
                        <td class="border px-4 py-2">${item.weekday}</td>
                        <td class="border px-4 py-2">
                            ${
                                item.type === 'sleep' ? '睡眠' :
                                item.type === 'exercise' ? '運動' :
                                item.type
                            }
                        </td>
                        <td class="border px-4 py-2">${item.start_t}</td>
                        <td class="border px-4 py-2">${item.end_t}</td>
                    </tr>
                `;
            });

            tableHtml += `
                    </tbody>
                </table>
            `;

            document.getElementById('activity-table').innerHTML = tableHtml;

            // ボタンのハイライト（任意）
            // document.querySelectorAll('.month-btn').forEach(btn => {
            //     btn.classList.remove('bg-blue-600');
            // });
            // if (button) button.classList.add('bg-blue-600');
        }

        // 初期表示：最初の年・月を自動で表示
        const initialYear = Object.keys(graphData)[0];
        showMonths(initialYear);
    </script>
    {{-- <script>
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
    </script> --}}
</x-app-layout>