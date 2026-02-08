@props(['title'])

<div x-data="{ open: false }" @click.away="open = false" class="relative inline-block text-left w-full">
    @php
        $currentValue = request($column);
        // Display Logic: Months get special names, others show raw value
        if($column === 'date' && $currentValue) {
            $displayLabel = \Carbon\Carbon::create()->month($currentValue)->format('F');
        } else {
            $displayLabel = $currentValue ?: $title;
        }
    @endphp
    <button @click="open = !open" type="button" class="
        flex w-full items-center justify-between
        rounded-lg bg-slate-50 
        text-slate-700 text-sm font-medium
        h-10 p-2 border border-slate-50 border-1
        shadow-sm
        hover:bg-slate-100 transition-colors duration-150
        hover:border-slate-200
    ">
        <span class="truncate">
           {{ $displayLabel }}
        </span>
        
        <x-lucide-chevron-down 
            class="w-4 h-4 ml-2 text-slate-500 transition-transform duration-200" 
            ::class="open ? 'rotate-180' : ''" 
        />
    </button>

    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-10 mt-1 w-full origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        style="display: none;"
    >
        <div class="py-1 max-h-60 overflow-y-auto custom-scrollbar">
            
            <a href="{{ request()->fullUrlWithQuery([$column => null]) }}" 
               class="block px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50 border-b border-gray-100">
                Clear Filter
            </a>

            @forelse($options as $key => $value)
                @php
                    // If it's a map (Months), use the $key (1, 2). If not, use the $value (Street Name).
                    $filterValue = $isMap ? $key : $value;
                    $displayValue = $value;
                @endphp
                
                <a href="{{ request()->fullUrlWithQuery([$column => $filterValue, 'page' => 1]) }}" 
                   class="block px-4 py-2 text-sm {{ request($column) == $filterValue ? 'bg-blue-100 text-blue-700 font-bold' : 'text-slate-700 hover:bg-gray-100' }}">
                    {{ $displayValue }}
                </a>
            @empty
                <span class="block px-4 py-2 text-sm text-gray-400 italic">No data found</span>
            @endforelse
            
        </div>
    </div>
</div>