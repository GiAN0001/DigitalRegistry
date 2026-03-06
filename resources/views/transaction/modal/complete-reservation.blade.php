<x-modal name="complete-reservation" maxWidth="md" focusable>
    <div class="p-6" x-data="completeReservation()" x-init="init()">
        <div class="flex justify-between items-start mb-6">
            <h2 class="text-xl font-bold text-gray-900">Complete Reservation</h2>
            <button type="button" @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">
                <x-lucide-x class="w-5 h-5"/>
            </button>
        </div>
        
        <p class="text-center text-gray-700 text-sm mb-2">Are you sure you want to mark this reservation as</p>
        <p class="text-center text-green-600 font-bold text-lg mb-6">Completed?</p>

        <!-- Equipment Status Check -->
        <div x-show="hasUnreturnedEquipment" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <p class="text-red-700 text-sm font-semibold mb-2">⚠️ Cannot Complete Reservation</p>
            <p class="text-red-600 text-xs">All equipment must be returned before completing this reservation.</p>
            <ul class="text-red-600 text-xs mt-2 list-disc list-inside">
                <template x-for="status in unretrurnedStatuses" :key="status">
                    <li x-text="status"></li>
                </template>
            </ul>
        </div>

        <p class="text-xs text-red-500 text-center mb-4" x-show="error" x-text="error"></p>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <x-secondary-button @click="$dispatch('close')">Cancel</x-secondary-button>
            <button 
                type="button"
                @click="submitComplete()"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                :disabled="loading || hasUnreturnedEquipment"
                :class="{ 'opacity-50 cursor-not-allowed': hasUnreturnedEquipment }"
            >
                <span x-show="!loading">Yes, Complete</span>
                <span x-show="loading">Processing...</span>
            </button>
        </div>
    </div>
</x-modal>

<script>
function completeReservation() {
    return {
        reservationId: null,
        error: '',
        loading: false,
        hasUnreturnedEquipment: false,
        unretrurnedStatuses: [],

        init() {
            window.addEventListener('set-complete-id', (event) => {
                this.reservationId = event.detail;
                this.error = '';
                this.loading = false;
                this.checkEquipmentStatus();
            });
        },

        checkEquipmentStatus() {
            fetch(`/facility/reservation/${this.reservationId}/check-equipment`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(r => r.json())
            .then(data => {
                this.hasUnreturnedEquipment = data.hasUnreturned;
                this.unretrurnedStatuses = data.unretrurnedStatuses || [];
            })
            .catch(e => {
                console.error('Error checking equipment status:', e);
                this.hasUnreturnedEquipment = false;
            });
        },

        submitComplete() {
            if (this.hasUnreturnedEquipment) {
                this.error = 'Cannot complete: All equipment must be returned first.';
                return;
            }

            this.loading = true;
            this.error = '';

            fetch(`/facility/reservation/${this.reservationId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: 'Completed' })
            })
            .then(r => r.json())
            .then(data => {
                this.loading = false;
                if (data.success) {
                    this.$dispatch('close');
                    window.dispatchEvent(new CustomEvent('set-success-message', { detail: 'Reservation completed successfully!' }));
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'success-modal' }));
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }, 300);
                } else {
                    this.error = data.message || 'Failed to complete reservation.';
                }
            })
            .catch(e => {
                this.loading = false;
                this.error = 'An error occurred. Please try again.';
                console.error('Error:', e);
            });
        }
    }
}
</script>