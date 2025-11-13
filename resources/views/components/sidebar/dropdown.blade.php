@props(['active' => false, 'title' => ''])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="relative">
    <button 
        @click="open = !open" 
        class="flex items-center justify-between w-full p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-75 group"
    >
        <span class="flex items-center text-left">
            <span class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
                {{ $icon ?? '' }}
            </span>
            
            <span class="ml-3">{{ $title }}</span>
        </span>

        <svg 
            class="w-3 h-3 transition-transform duration-200" 
            :class="{ 'rotate-180': open }" 
            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
    </button>

    <ul x-show="open" x-transition class="py-2 space-y-2 pl-10">
        {{ $slot }}
    </ul>
</div>