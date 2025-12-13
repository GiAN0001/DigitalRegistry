@props(['active' => false, 'title' => ''])

<div
    x-data="{
        open: false,
        init() {
            // Generate a unique ID for this specific dropdown (e.g., 'menu_state_transactions')
            let storageKey = 'menu_state_' + '{{ $title }}'.replace(/[^a-zA-Z0-9]/g, '_').toLowerCase();

            // Check if there is a saved state in the browser
            let storedState = localStorage.getItem(storageKey);

            // LOGIC:
            // 1. If the current page is Active (passed from Laravel), force it OPEN.
            if (@json($active)) {
                this.open = true;
                localStorage.setItem(storageKey, 'true'); // Sync storage to match
            }
            // 2. If not active, check if the user left it open previously
            else if (storedState === 'true') {
                this.open = true;
            }

            // Watch for clicks: When 'open' changes, save it to LocalStorage
            this.$watch('open', value => localStorage.setItem(storageKey, value));
        }
    }"
    class="relative"
>
    <button
        @click="open = !open"
        class="
            flex items-center justify-between w-full p-2 text-sm font-medium rounded-lg transition duration-150 group
            {{ $active
                ? 'text-blue-700'
                : 'text-slate-700 hover:bg-blue-700 hover:text-slate-50'
            }}
        "
    >
        <span class="flex items-center text-left">
            <span class="
                w-5 h-5 transition duration-150
                {{ $active ? 'text-blue-700' : 'text-slate-700 group-hover:text-slate-50' }}
            ">
                {{ $icon ?? '' }}
            </span>

            <span class="pl-2 pr-2">{{ $title }}</span>
        </span>

        {{-- Arrow Icon --}}
        <svg
            class="
                w-3 h-3 transition-transform duration-200
                {{ $active ? 'text-blue-700' : 'text-slate-700 group-hover:text-slate-50' }}
            "
            :class="{ 'rotate-180': open }"
            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
    </button>

    {{-- Dropdown Content --}}
    <ul x-show="open" x-cloak x-transition class="py-2 space-y-1 pl-3">
        {{ $slot }}
    </ul>
</div>
