@props(['active' => false, 'title' => ''])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="relative">
    <button
        @click="open = !open"
        class="
            flex items-center justify-between w-full p-2 text-sm font-medium rounded-lg transition duration-150 group
            {{-- CHANGE 1: Main Button Styling --}}
            {{-- If Active: Blue text, no background. If Inactive: Gray text, Blue background on hover --}}
            {{ $active
                ? 'text-blue-700'
                : 'text-slate-700 hover:bg-blue-700 hover:text-slate-50'
            }}
        "
    >
        <span class="flex items-center text-left">
            <span class="
                w-5 h-5 transition duration-150
                {{-- CHANGE 2: Icon Styling --}}
                {{-- We ensure the icon is Blue if active, otherwise it follows the group hover --}}
                {{ $active ? 'text-blue-700' : 'text-slate-700 group-hover:text-slate-50' }}
            ">
                {{ $icon ?? '' }}
            </span>

            <span class="pl-2 pr-2">{{ $title }}</span>
        </span>

        <svg
            class="
                w-3 h-3 transition-transform duration-200
                {{-- CHANGE 3: Arrow/Chevron Styling --}}
                {{-- Arrow also turns blue if active --}}
                {{ $active ? 'text-blue-700' : 'text-slate-700 group-hover:text-slate-50' }}
            "
            :class="{ 'rotate-180': open }"
            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
    </button>

    <ul x-show="open" x-transition class="py-2 space-y-1 pl-3">
        {{ $slot }}
    </ul>
</div>
