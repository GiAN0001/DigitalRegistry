<x-modal name="reservation-error" maxWidth="max-w-sm" focusable>
    <div class="p-8 text-center">
        <div class="mb-4">
            <svg class="w-16 h-16 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-800 mb-2">Time Slot Already Booked</h3>
        <p class="text-slate-600 text-sm mb-6" x-text="errorMessage"></p>
        <button 
            @click="$dispatch('close-modal', 'reservation-error')"
            type="button"
            class="px-6 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition"
        >
            Close
        </button>
    </div>
</x-modal>

<script>
    window.reservations = @json($jsReservations);
    window.existingReservations = @json($jsReservations);
</script>