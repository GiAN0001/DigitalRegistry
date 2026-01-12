<button {{ $attributes->merge(['type' => 'submit', 'class' => '
    inline-flex items-center justify-center
    px-2 py-2.5
    bg-blue-700 hover:bg-blue-600
    text-slate-50 text-sm font-medium
    whitespace-nowrap
    rounded-lg
    transition duration-150 ease-in-out
    focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
']) }}>
    
    @if (isset($icon))
        <span class="mr-2 flex items-center">
            {{ $icon }}
        </span>
    @endif

    <span>{{ $slot }}</span>
</button>