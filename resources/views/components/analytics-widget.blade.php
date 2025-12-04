<div class="p-4 bg-white rounded-lg shadow-md flex items-center justify-between">
    <div class="{{ $bgColor }} p-3 rounded-full text-white">
        
        <x-dynamic-component 
            :component="'lucide-' . $iconName" 
            class="w-6 h-6" 
        />
        
    </div>

    <div class="text-right">
        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">
            {{ $title }}
        </p>
        <p class="text-2xl font-bold text-gray-900">
            {{ number_format($value) }}
        </p>
    </div>
</div>