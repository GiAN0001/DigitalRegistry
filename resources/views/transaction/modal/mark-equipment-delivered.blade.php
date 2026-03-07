<x-modal name="mark-equipment-delivered" maxWidth="max-w-md" focusable>
    <div class="p-6" x-data="markEquipmentDeliveredForm()" x-init="init()">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Mark Equipment as Delivered</h2>

        <form @submit.prevent="submit()" class="space-y-4">
            <div>
                <label for="delivered_by_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Delivered By <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="delivered_by_name"
                    x-model="form.delivered_by_name"
                    placeholder="Enter name of person who delivered"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                    autofocus
                >
                <p class="text-xs text-red-600 mt-1" x-show="errors.delivered_by_name" x-text="errors.delivered_by_name"></p>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <x-secondary-button type="button" @click="closeModal()">Cancel</x-secondary-button>
                <button 
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-800 transition"
                    :disabled="submitting"
                >
                    <span x-show="!submitting">Confirm Delivery</span>
                    <span x-show="submitting">Saving...</span>
                </button>
            </div>
        </form>
    </div>
</x-modal>

<script>
function markEquipmentDeliveredForm() {
    return {
        form: {
            delivered_by_name: ''
        },
        errors: {},
        equipmentId: null,
        submitting: false,

        init() {
            window.addEventListener('show-delivery-form', (event) => {
                this.equipmentId = event.detail.id;
                this.form.delivered_by_name = '';
                this.errors = {};
            });
        },

        closeModal() {
            this.$dispatch('close');
        },

        submit() {
            this.errors = {};

            if (!this.form.delivered_by_name.trim()) {
                this.errors.delivered_by_name = 'Please enter the name of person who delivered';
                return;
            }

            this.submitting = true;

            fetch(`/facility/reservation/equipment/${this.equipmentId}/delivered`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    delivered_by_name: this.form.delivered_by_name
                })
            })
            .then(r => r.json())
            .then(data => {
                this.submitting = false;
                if (data.success) {
                    this.form.delivered_by_name = '';
                    this.$dispatch('close');
                    window.location.reload();
                } else {
                    this.errors.delivered_by_name = data.message || 'Failed to update';
                }
            })
            .catch(e => {
                this.submitting = false;
                console.error('Error:', e);
                this.errors.delivered_by_name = 'Failed to update equipment';
            });
        }
    }
}
</script>