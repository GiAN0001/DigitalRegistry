<div class="p-6 bg-white shadow-md rounded-lg">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $title }}</h3>
    
    <div class="relative h-96"> 
        <canvas id="{{ $chartId }}"></canvas>
    </div>

    <script>
        (function() {
            // Data and Options passed from PHP
            const chartData = JSON.parse('{!! $dataJson !!}');
            const chartOptions = JSON.parse('{!! $optionsJson !!}');
            const chartType = '{{ $type }}';
            const chartId = '{{ $chartId }}';
            
            // --- CRITICAL FIX: Implement a Polling Mechanism ---
            let checkChartInterval = setInterval(function() {
                const ctx = document.getElementById(chartId);
                
                // Check 1: Is the Chart library loaded? Check 2: Is the canvas element ready?
                if (typeof Chart !== 'undefined' && ctx) {
                    
                    // Library is ready: Stop the checking loop
                    clearInterval(checkChartInterval); 

                    // Initialize the Chart.js instance
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