<x-modal name="add-equipment" maxWidth="max-w-md" focusable>
    <div class="p-8" x-data="addEquipmentModal()" x-init="fetchTypes()">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Add New Equipment</h2>
        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Equipment Type</label>
                <select x-model="type_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                    <option value="">Select type</option>
                    <template x-for="type in types" :key="type.id">
                        <option :value="type.id" x-text="type.equipment_type"></option>
                    </template>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                <input type="number" x-model="quantity" min="1" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
            </div>
            <div class="flex justify-end gap-3">
                <x-secondary-button @click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button type="submit">Add</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

<script>
function addEquipmentModal() {
    return {
        types: [],
        type_id: '',
        quantity: 1,
        fetchTypes() {
            fetch('/equipment-types')
                .then(r => r.json())
                .then(data => { this.types = data; });
        },
        submit() {
            fetch('/equipment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    type_id: this.type_id,
                    quantity: this.quantity
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.$dispatch('close');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to add equipment.');
                }
            });
        }
    }
}
</script>