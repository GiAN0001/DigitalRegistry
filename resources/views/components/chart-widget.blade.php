<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-md font-bold text-slate-700">{{ $title }}</h3>
    </div>

    <div class="p-6 bg-slate-50 shadow-sm rounded-lg">

        <div class="relative h-80 w-full">
            <canvas id="{{ $chartId }}"></canvas>
        </div>

        <script>
            (function() {
                // ... (data parsing logic kept exactly the same) ...
                const chartData = JSON.parse('{!! $dataJson !!}');
                const chartType = '{{ $type }}';
                const chartId = '{{ $chartId }}';

                window.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById(chartId);

                    if (typeof Chart === 'undefined' || !ctx) {
                        console.error('Chart Initialization Failed.');
                        return;
                    }

                    new Chart(ctx, {
                        type: chartType,
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            // Added layout padding to prevent the chart from touching edges
                            layout: {
                                padding: 20
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    display: false // Kept hidden as per your code/reference
                                },
                                x: {
                                    display: false // Kept hidden as per your code/reference
                                }
                            },
                            plugins: {
                                legend: {
                                    // VISUAL CHANGE: Moves data to the right side
                                    position: 'right',
                                    align: 'center', // Vertically centers the legend
                                    labels: {
                                        usePointStyle: true, // Makes the legend icons square/circle instead of rectangles
                                        boxWidth: 12,
                                        padding: 20, // Adds space between legend items
                                        font: {
                                            size: 10,
                                            weight: '600'
                                        },
                                        color: '#334155' // Slate-700 color for text
                                    }
                                },
                                tooltip: {
                                    // ... (Your existing tooltip logic kept exactly the same) ...
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
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
                });
            })();
        </script>
    </div>
</div>
