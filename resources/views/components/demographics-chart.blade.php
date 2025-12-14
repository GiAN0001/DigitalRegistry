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
            const chartData = JSON.parse('{!! $dataJson !!}');
            const chartOptions = JSON.parse('{!! $optionsJson !!}');
            const chartType = '{{ $type }}';
            const chartId = '{{ $chartId }}';

            // --- CRITICAL FIX: Implement a Polling Mechanism ---
            let checkChartInterval = setInterval(function() {
                const ctx = document.getElementById(chartId);

                if (typeof Chart !== 'undefined' && ctx) {


                    clearInterval(checkChartInterval);

                    new Chart(ctx, {
                        type: chartType,
                        data: chartData,
                        options: chartOptions
                    });

                } else if (typeof Chart === 'undefined') {
                    // Log that we are still waiting (for debugging)
                    console.warn("Waiting for Chart.js to load...");
                }
            }, 50); // Check every 50 milliseconds

        })();
    </script>
</div>
</div>
