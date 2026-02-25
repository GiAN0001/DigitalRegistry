<x-modal name="approve-for-payment" maxWidth="max-w-[500px]" focusable>
    <div class="p-8" x-data="{ id: null, fee: '' }" @set-approve-id.window="id = $event.detail; fee = ''; console.log('ID set to:', id)">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Approve for Payment</h2>
        <form @submit.prevent="
            if (!id) { alert('Reservation ID missing'); return; }
            fetch('/facility/reservation/' + id + '/approve-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ fee: fee })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    $dispatch('close');
                    setTimeout(() => {
                        $dispatch('open-modal', 'success-message');
                    }, 200);
                } else {
                    alert(data.message);
                }
            })
            .catch(e => alert('Error: ' + e))
        ">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fee Amount (₱)</label>
                <input type="number" min="0" step="0.01" x-model="fee"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                    required>
            </div>
            
            <!-- Action Buttons -->
            <div class="sticky bottom-0 bg-white pt-4 border-t border-gray-200 flex justify-end gap-3">
                <x-secondary-button @click="$dispatch('close')">Cancel</x-secondary-button>
                
                <x-primary-button class="ms-3" type="submit">
                    Confirm
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

<script>
function approveForPaymentModal() {
    return {
        id: null,
        fee: '',
        initModal(id) {
            this.id = id;
            this.fee = '';
            console.log('Modal initialized with ID:', id);
        },
        async submit() {
            if (!this.id) {
                alert('Reservation ID missing');
                return;
            }
            try {
                const response = await fetch(`/facility/reservation/${this.id}/approve-payment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ fee: this.fee })
                });
                const data = await response.json();
                if (data.success) {
                    this.$dispatch('close');
                    this.$dispatch('open-modal', 'success-message');
                    location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Error: ' + error);
            }
        }
    }
}
</script>