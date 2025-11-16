@props([
    'placeholder' => 'Search...'
])

<div class="relative w-full max-w-md">
    <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
        <x-lucide-search class="w-5 h-5 text-slate-700" />
    </div>

    <input 
        type="search" 
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'block w-full p-2 pl-10 shadow-sm text-sm text-slate-700 border border-slate-200 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-700'
        ]) }}
    >
</div>