<div class="p-6 bg-white shadow-md rounded-lg">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $title }}</h3>
    
    <div class="relative h-96"> 
        <canvas id="{{ $chartId }}"></canvas>
    </div>

    <script>
        // Use a self-executing function to isolate variables.
        (function() {
            // ... (data parsing and setup logic remains the same) ...
            const chartData = JSON.parse('{!! $dataJson !!}');
            const chartType = '{{ $type }}';
            const chartId = '{{ $chartId }}';
            
            // Wait for the DOM and Chart.js to be ready
            window.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById(chartId);
                
                if (typeof Chart === 'undefined' || !ctx) {
                    console.error('Chart Initialization Failed: Chart.js library or canvas element not ready.');
                    return;
                }
                
                new Chart(ctx, {
                    type: chartType,
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        
                    
                        scales: {
                            y: {
                                beginAtZero: true,
                                display: false 
                            },
                            x: {
                                display: false 
                            }
                        }, 
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        // Add the actual population count to the tooltip
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