<div x-data="{ open: false }" @click.away="open = false" class="relative inline-block text-left">
    
    <button @click="open = !open" type="button" class="
        inline-flex w-full justify-between items-center
        rounded-md border border-gray-300 bg-white 
        px-4 py-2 text-sm font-medium text-slate-700 
        shadow-sm hover:bg-gray-50 
        focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
    ">
        <span>{{ $title }}</span>
        
        <x-lucide-chevron-down 
            class="w-4 h-4 ml-2 transition-transform duration-200" 
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
        class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        style="display: none;"
    >
        <div class="py-1 max-h-60 overflow-y-auto">
            
            <a href="{{ request()->fullUrlWithQuery([$column => null]) }}" 
               class="block px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-100 border-b">
                Clear Filter
            </a>

            @forelse($options as $option)
                <a href="{{ request()->fullUrlWithQuery([$column => $option]) }}" 
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700
                          {{ request($column) == $option ? 'bg-blue-100 font-semibold' : '' }}">
                    {{ $option }}
                </a>
            @empty
                <span class="block px-4 py-2 text-sm text-gray-400 italic">No data found</span>
            @endforelse
            
        </div>
    </div>
</div>