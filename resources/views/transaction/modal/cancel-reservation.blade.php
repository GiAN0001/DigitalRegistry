<x-modal name="cancel-reservation" maxWidth="max-w-md" focusable>
    <div class="p-8" x-data="{
        id: null,
        reason: '',
        submit() {
            if (!this.reason.trim()) {
                alert('Please provide a reason for cancellation.');
                return;
            }
            fetch('/facility/reservation/' + this.id + '/cancel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ reason: this.reason })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.$dispatch('close');
                    window.dispatchEvent(new CustomEvent('set-success-message', { detail: 'Reservation cancelled successfully!' }));
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'success-modal' }));
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }, 300);
                } else {
                    alert(data.message || 'Failed to cancel reservation.');
                }
            })
            .catch(e => alert('Error: ' + e));
        }
    }"
    @set-cancel-id.window="id = $event.detail; reason = ''"
    >
        <h2 class="text-xl font-bold text-gray-900 mb-4">Cancel Reservation</h2>
        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Cancellation</label>
                <textarea x-model="reason" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <x-secondary-button @click="$dispatch('close-modal', 'cancel-reservation')">Close</x-secondary-button>
                <x-primary-button type="submit">Cancel Reservation</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>