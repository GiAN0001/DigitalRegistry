@props(['name', 'show' => false])

<x-modal :name="$name" :show="$show" maxWidth="max-w-lg" focusable>
    <div class="p-8 sm:p-12">
        {{-- Close Button (X) --}}
        <div class="flex justify-end mb-4">
            <button type="button" @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Error Icon (Warning/X Style) --}}
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border-4 border-red-500 flex items-center justify-center">
                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>

        <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 text-center mb-4">Duplicate!</h2>

        <p class="text-gray-500 text-base sm:text-lg text-center mb-8 italic">
            {{ $slot }}
        </p>

        <div class="flex justify-center">
            {{-- Keeping the button red to match your Success modal's close button --}}
            <button type="button" @click="$dispatch('close')" class="w-full sm:w-auto px-10 py-3 bg-red-600 text-white text-lg font-semibold rounded-xl hover:bg-red-700 transition-colors">
                Close
            </button>
        </div>
    </div>
</x-modal>