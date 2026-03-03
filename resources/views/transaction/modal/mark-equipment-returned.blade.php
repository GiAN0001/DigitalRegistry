<x-modal name="mark-equipment-returned" maxWidth="md" focusable>
    <div class="p-6" x-data="markEquipmentReturned()" x-init="init()">
        <div class="flex justify-between items-start mb-4">
            <h2 class="text-xl font-bold text-gray-900">Mark as Returned</h2>
            <button type="button" @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">
                <x-lucide-x class="w-5 h-5"/>
            </button>
        </div>
        <form @submit.prevent="submitReturn()">
            <div class="mb-4">
                <label for="returned_by_name" class="block text-sm font-medium text-gray-700 mb-1">Returned By <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    id="returned_by_name" 
                    x-model="returned_by_name" 
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-sm"
                    placeholder="Enter name of person returning equipment"
                    required
                >
                <p class="text-xs text-red-500 mt-1" x-show="error" x-text="error"></p>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <x-secondary-button @click="$dispatch('close')">Cancel</x-secondary-button>
                <button 
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    :disabled="loading"
                >
                    <span x-show="!loading">Confirm Return</span>
                    <span x-show="loading">Processing...</span>
                </button>
            </div>
        </form>
    </div>
</x-modal>

<script>
function markEquipmentReturned() {
    return {
        equipmentId: null,
        returned_by_name: '',
        error: '',
        loading: false,

        init() {
            window.addEventListener('show-return-form', (event) => {
                this.equipmentId = event.detail.id;
                this.returned_by_name = '';
                this.error = '';
                this.loading = false;
            });
        },

        submitReturn() {
            if (!this.returned_by_name.trim()) {
                this.error = 'Returned by name is required.';
                return;
            }

            this.loading = true;
            this.error = '';

            fetch(`/facility/reservation/equipment/${this.equipmentId}/returned`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    returned_by_name: this.returned_by_name
                })
            })
            .then(r => r.json())
            .then(data => {
                this.loading = false;
                if (data.success) {
                    this.$dispatch('close');
                    window.location.reload();
                } else {
                    this.error = data.message || 'Failed to mark as returned.';
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