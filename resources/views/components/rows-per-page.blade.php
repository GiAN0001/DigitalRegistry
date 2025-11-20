@props(['options' => [10, 25, 50, 100]])

<div x-data="{ open: false }" @click.away="open = false" class="relative inline-block text-left min-w-[70px]">
    
    <button @click="open = !open" type="button" class="
        flex w-full items-center justify-between
        rounded-lg bg-slate-50 
        text-slate-700 text-sm font-medium
        h-10 p-2
        shadow-sm
        focus:outline-none focus:ring-0 border-none
        hover:bg-slate-100 transition-colors duration-150
    ">
        <span>
            {{ request('per_page', 10) }}
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
        <div class="py-1">
            
            @foreach($options as $option)
                <a href="{{ request()->fullUrlWithQuery(['per_page' => $option, 'page' => 1]) }}" 
                   class="block px-4 py-2 text-sm transition-colors duration-150 text-center
                          {{ request('per_page', 10) == $option 
                             ? 'bg-blue-50 text-blue-700 font-medium' 
                             : 'text-slate-700 hover:bg-gray-300 hover:text-slate-700' 
                          }}">
                    {{ $option }}
                </a>
            @endforeach
            
        </div>
    </div>
</div>