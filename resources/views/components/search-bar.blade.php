@props([
    'placeholder' => 'Search...',
    'name' => 'q', // Default search key is 'q'
])

@php
    $baseUrl = request()->url();
    // Dynamically exclude the current search key and the page number
    $currentQueries = request()->except([$name, 'page']);
@endphp

<div
    class="relative w-full max-w-md h-10"
    x-data="{
        query: '{{ request($name) }}',
        timeout: null,
        doSearch() {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                let url = new URL('{{ $baseUrl }}');

                @foreach($currentQueries as $key => $value)
                    url.searchParams.set('{{ $key }}', '{{ $value }}');
                @endforeach

                if (this.query) {
                    url.searchParams.set('{{ $name }}', this.query);
                } else {
                    url.searchParams.delete('{{ $name }}');
                }

                window.location.href = url.toString();
            }, 500);
        }
    }"
>
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <x-lucide-search class="w-5 h-5 text-slate-700" />
    </div>

    <input
        type="search"
        x-model="query"
        @input="doSearch()"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'block w-full h-10 pl-10 pr-4 py-2 text-sm font-normal text-slate-500 bg-slate-50 border-none rounded-lg shadow-sm focus:outline-none focus:ring-1 focus:ring-slate-200 placeholder:text-slate-400'
        ]) }}
    >
</div>
