<x-modal name="mark-paid" maxWidth="max-w-[500px]" focusable>
    <div class="p-8" x-data="markPaidForm()" @set-paid-id.window="handleSetId($event.detail)">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Mark as Paid</h2>
        <form @submit.prevent="handleSubmit()">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount Paid (₱)</label>
                <input type="number" min="0" step="0.01" x-model="amountPaid"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mode of Payment</label>
                <input type="text" x-model="modeOfPayment"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                    placeholder="e.g. Cash, Check, Bank Transfer"
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">OR Number</label>
                <input type="text" x-model="orNumber"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                    placeholder="Enter Official Receipt Number"
                    required>
            </div>
            
            <div class="sticky bottom-0 bg-white pt-4 border-t border-gray-200 flex justify-end gap-3">
                <x-secondary-button type="button" @click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button class="ms-3" type="submit">Confirm Payment</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

<script>
    function markPaidForm() {
        return {
            id: null,
            amountPaid: '',
            modeOfPayment: '',
            orNumber: '',

            handleSetId(reservationId) {
                this.id = reservationId;
                this.amountPaid = '';
                this.modeOfPayment = '';
                this.orNumber = '';
            },

            async handleSubmit() {
                if (!this.id) {
                    alert('Reservation ID missing');
                    return;
                }

                try {
                    const response = await fetch('/facility/reservation/' + this.id + '/mark-paid', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({
                            amount_paid: this.amountPaid,
                            mode_of_payment: this.modeOfPayment,
                            or_number: this.orNumber
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.$dispatch('close');
                        
                        // Dispatch success message
                        window.dispatchEvent(new CustomEvent('set-success-message', { 
                            detail: 'Reservation marked as paid successfully!' 
                        }));

                        // Open success modal
                        setTimeout(() => {
                            window.dispatchEvent(new CustomEvent('open-modal', { 
                                detail: 'success-modal' 
                            }));
                            
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }, 300);
                    } else {
                        alert(data.message || 'Failed to mark as paid');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                }
            }
        }
    }
</script>