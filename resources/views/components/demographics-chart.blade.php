<div>
    <div class="flex justify-between items-center mb-2">
        <h3 class="text-md font-bold text-slate-700">{{ $title }}</h3>
    </div>
    <div class="p-6 bg-slate-50 shadow-sm rounded-lg">

    <div class="relative h-80 w-full">
        <canvas id="{{ $chartId }}"></canvas>
    </div>

   <script>
    (function() {
        const chartData = JSON.parse('{!! $dataJson !!}');
        const chartOptions = JSON.parse('{!! $optionsJson !!}');
        const chartType = '{{ $type }}';
        const chartId = '{{ $chartId }}';

        let checkChartInterval = setInterval(function() {
            const ctx = document.getElementById(chartId);

            if (typeof Chart !== 'undefined' && ctx) {

                clearInterval(checkChartInterval);

                new Chart(ctx, {
                    type: chartType,
                    data: chartData,
                    options: {
                        ...chartOptions,

                        animation: {

                            duration: 1000,
                            easing: 'easeOutQuart',

                            delay: (context) => {
                                let delay = 0;

                                if (context.type === 'data' && context.mode === 'default') {
                                    delay = context.dataIndex * 300;
                                }
                                return delay;
                            }
                        }
                    }
                });

            } else if (typeof Chart === 'undefined') {
                console.warn("Waiting for Chart.js...");
            }
        }, 50);
    })();
</script>

</div>
</div>
