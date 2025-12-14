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
                const chartType = '{{ $type }}'; // Ensure this is 'pie' or 'doughnut'
                const chartId = '{{ $chartId }}';


                const initChart = () => {
                    const ctx = document.getElementById(chartId);
                    if (typeof Chart === 'undefined' || !ctx) return;


                    const existingChart = Chart.getChart(ctx);
                    if (existingChart) {
                        existingChart.destroy();
                    }

                    new Chart(ctx, {
                        type: chartType,
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,


                            animation: {
                                duration: 2000,
                                easing: 'easeOutQuart',
                                animateRotate: true,
                                animateScale: true
                            },


                            layout: {
                                padding: 20
                            },
                            scales: {
                                y: {
                                    display: false
                                },
                                x: {
                                    display: false
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 10,
                                        padding: 20,
                                        font: {
                                            size: 11,
                                            weight: '600'
                                        },
                                        color: '#334155'
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) label += ': ';
                                            if (context.parsed.y !== null) {
                                                label += new Intl.NumberFormat().format(context.parsed.y);
                                            }
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });
                };

                setTimeout(initChart, 100);

            })();
        </script>
    </div>
</div>
