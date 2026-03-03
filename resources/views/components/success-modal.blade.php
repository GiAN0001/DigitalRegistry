@props(['name', 'show' => false])

<x-modal :name="$name" :show="$show" maxWidth="max-w-lg" focusable>
    <div class="p-8 sm:p-8">
        {{-- Close Button (X) --}}
        <div class="flex justify-end mb-4">
            <button type="button" @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Success Icon --}}
        <div class="flex justify-center mb-4">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border-4 border-green-500 flex items-center justify-center">
                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>

        <h2 class="text-xl font-semibold text-slate-700 text-center mb-2">Success!</h2>

        <p class="text-slate-400 text-m font-semibold text-center mb-8">
            {{ $slot }}
        </p>

        <div class="flex justify-center">
            <x-secondary-button @click="$dispatch('close')">Cancel</x-secondary-button>
        </div>
    </div>
</x-modal>