<div
    x-data="{
        current: 0,
        target: {{ $value }},
        time: 1000,
        init() {
            let start = null;
            const step = (timestamp) => {
                if (!start) start = timestamp;
                const progress = Math.min((timestamp - start) / this.time, 1);
                // Calculate current value based on progress
                this.current = Math.floor(progress * this.target);

                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        },
        get formatted() {
            // Replicates PHP's number_format (adds commas)
            return this.current.toLocaleString();
        }
    }"
    class="flex justify-between items-center gap-6 p-4 bg-white rounded-lg shadow-sm"
>

    <div class="">
        <p class="text-xs font-medium text-slate-500 tracking-wider mb-1">
            {{ $title }}
        </p>

        <p class="text-2xl font-bold text-slate-700" x-text="formatted">
            {{ number_format($value) }}
        </p>
    </div>

    <div class="{{ $bgColor }} w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-md shadow-slate-200">
        <x-dynamic-component
            :component="'lucide-' . $iconName"
            class="w-6 h-6"
        />
    </div>

</div>
