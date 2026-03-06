<x-modal name="view-equipment" maxWidth="max-w-[700px]" focusable>
    <div class="p-8" x-data="viewEquipmentDetails()" x-init="init()">
        <div class="flex justify-between items-start mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Equipment Details</h2>
            <button type="button" @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">
                <x-lucide-x class="w-6 h-6"/>
            </button>
        </div>

        {{-- Status Badge --}}
        <div class="mb-6" x-show="equipment.equipment_status">
            <span 
                class="px-4 py-2 rounded-full text-sm font-semibold"
                :class="getStatusBadgeColor(equipment.equipment_status)"
                x-text="equipment.equipment_status"
            ></span>
        </div>

        <!-- Scrollable content wrapper -->
        <div class="max-h-[600px] overflow-y-auto pr-2 space-y-6">
            {{-- Reservation Information --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-bold text-slate-800 mb-3">Reservation Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Reservation ID</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="'#' + equipment.id"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Event Name</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="equipment.event_name"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Renter Name</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="equipment.renter_name || 'N/A'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Date</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="formatDate(equipment.start_date)"></p>
                    </div>
                </div>
            </div>

            {{-- Schedule Information --}}
            <div class="bg-gray-50 rounded-lg p-4" x-show="equipment.time_start">
                <h3 class="text-lg font-bold text-slate-800 mb-3">Schedule</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Time Start</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="equipment.time_start"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Time End</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="equipment.time_end"></p>
                    </div>
                </div>
            </div>

            {{-- Equipment Information --}}
            <div class="bg-gray-50 rounded-lg p-4" x-show="equipment.equipments && equipment.equipments.length > 0">
                <h3 class="text-lg font-bold text-slate-800 mb-3">Equipment Borrowed</h3>
                <div class="space-y-2">
                    <template x-for="item in equipment.equipments" :key="item.id">
                        <div class="flex justify-between items-center bg-white rounded-lg p-3 border border-gray-200">
                            <span class="text-sm font-medium text-slate-700" x-text="item.equipment_type"></span>
                            <span class="text-sm text-slate-500">Qty: <span class="font-semibold" x-text="item.quantity_borrowed"></span></span>
                        </div>
                    </template>
                    <template x-if="equipment.delivered_by_name || equipment.date_delivered">
                        <div class="bg-orange-50 rounded-lg p-3 border border-orange-200">
                            <div class="flex justify-between items-center text-sm text-slate-700">
                                <span><strong>Delivered By:</strong> <span x-text="equipment.delivered_by_name || 'Pending'"></span></span>
                                <span><strong>Date:</strong> <span x-text="equipment.date_delivered ? formatDateTime(equipment.date_delivered) : 'Pending'"></span></span>
                            </div>
                        </div>
                    </template>
                    <template x-if="equipment.equipment_status === 'Returned' && (equipment.returned_by_name || equipment.date_returned)">
                        <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                            <div class="flex justify-between items-center text-sm text-slate-700">
                                <span><strong>Returned By:</strong> <span x-text="equipment.returned_by_name || 'Pending'"></span></span>
                                <span><strong>Date:</strong> <span x-text="equipment.date_returned ? formatDateTime(equipment.date_returned) : 'Pending'"></span></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Processing Information --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-bold text-slate-800 mb-3">Processing Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    {{-- For Approval Stage --}}
                    <template x-if="equipment.equipment_status === 'For Approval' && equipment.processed_by">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">For Approval By</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="equipment.processed_by"></p>
                        </div>
                    </template>
                    <template x-if="equipment.equipment_status === 'For Approval' && equipment.created_at">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date For Approval</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(equipment.created_at)"></p>
                        </div>
                    </template>

                    {{-- For Delivery Stage --}}
                    <template x-if="equipment.equipment_status === 'For Delivery' && equipment.transferred_for_payment_by">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Approved By</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="equipment.transferred_for_payment_by"></p>
                        </div>
                    </template>
                    <template x-if="equipment.equipment_status === 'For Delivery' && equipment.for_payment_at">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date Approved</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(equipment.for_payment_at)"></p>
                        </div>
                    </template>

                    {{-- Delivered Stage --}}
                    <template x-if="equipment.equipment_status === 'Delivered' && equipment.transferred_for_payment_by">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Approved By</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="equipment.transferred_for_payment_by"></p>
                        </div>
                    </template>
                    <template x-if="equipment.equipment_status === 'Delivered' && equipment.for_payment_at">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date Approved</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(equipment.for_payment_at)"></p>
                        </div>
                    </template>
                    <template x-if="equipment.equipment_status === 'Delivered' && equipment.transferred_paid_by">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Delivered By</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="equipment.transferred_paid_by"></p>
                        </div>
                    </template>
                    <template x-if="equipment.equipment_status === 'Delivered' && equipment.paid_at">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date Delivered</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(equipment.paid_at)"></p>
                        </div>
                    </template>

                    {{-- Returned Stage --}}
                    <template x-if="equipment.equipment_status === 'Returned' && equipment.transferred_for_payment_by">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Approved By</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="equipment.transferred_for_payment_by"></p>
                        </div>
                    </template>
                    <template x-if="equipment.equipment_status === 'Returned' && equipment.for_payment_at">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date Approved</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(equipment.for_payment_at)"></p>
                        </div>
                    </template>
                    <template x-if="equipment.equipment_status === 'Returned' && equipment.transferred_paid_by">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Delivered By</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="equipment.transferred_paid_by"></p>
                        </div>
                    </template>
                    <template x-if="equipment.equipment_status === 'Returned' && equipment.paid_at">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date Delivered</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(equipment.paid_at)"></p>
                        </div>
                    </template>
                    <template x-if="equipment.equipment_status === 'Returned' && equipment.received_by">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Received By</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="equipment.received_by"></p>
                        </div>
                    </template>
                    <template x-if="equipment.equipment_status === 'Returned' && equipment.date_returned">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date Returned</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(equipment.date_returned)"></p>
                        </div>
                    </template>

                    {{-- Cancelled/Rejected --}}
                    <template x-if="(equipment.equipment_status === 'Cancelled' || equipment.equipment_status === 'Rejected')">
                        <div class="col-span-2">
                            <p class="text-xs text-gray-500 mb-1">Status</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="equipment.equipment_status"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="sticky bottom-0 bg-white pt-4 border-t border-gray-200 flex justify-end gap-3 mt-6">
            <x-secondary-button @click="$dispatch('close')">Close</x-secondary-button>
            
            {{-- Mark as Delivered Button --}}
            <button 
                @click="
                    if (equipment.equipment_status === 'For Delivery') {
                        $dispatch('close');
                        setTimeout(() => {
                            window.dispatchEvent(new CustomEvent('show-delivery-form', { detail: { id: equipment.equipment_id } }));
                            $dispatch('open-modal', 'mark-equipment-delivered');
                        }, 100);
                    }
                "
                :disabled="equipment.equipment_status !== 'For Delivery'"
                :class="{
                    'opacity-50 cursor-not-allowed': equipment.equipment_status !== 'For Delivery',
                    'hover:bg-orange-700': equipment.equipment_status === 'For Delivery'
                }"
                class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:bg-orange-700 active:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                Mark as Delivered
            </button>

            {{-- Mark as Returned Button --}}
            <button 
                @click="
                    if (equipment.equipment_status === 'Delivered') {
                        $dispatch('close');
                        setTimeout(() => {
                            window.dispatchEvent(new CustomEvent('show-return-form', { detail: { id: equipment.equipment_id } }));
                            $dispatch('open-modal', 'mark-equipment-returned');
                        }, 100);
                    }
                "
                :disabled="equipment.equipment_status !== 'Delivered'"
                :class="{
                    'opacity-50 cursor-not-allowed': equipment.equipment_status !== 'Delivered',
                    'hover:bg-green-700': equipment.equipment_status === 'Delivered'
                }"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                Mark as Returned
            </button>
        </div>
    </div>
</x-modal>

<script>
function viewEquipmentDetails() {
    return {
        equipment: {
            id: null,
            event_name: '',
            renter_name: '',
            start_date: '',
            time_start: '',
            time_end: '',
            equipment_status: '',
            equipment_id: null,
            equipments: [],
            processed_by: '',
            transferred_for_payment_by: '',
            transferred_paid_by: '',
            delivered_by_name: '',
            returned_by_name: '',
            received_by: '',
            created_at: '',
            for_payment_at: '',
            date_delivered: '',
            date_returned: '',
            paid_at: ''
        },

        init() {
            window.addEventListener('show-equipment', (event) => {
                const eventData = event.detail;
                fetch(`/facility/equipment/${eventData.id}`)
                    .then(r => r.json())
                    .then(data => {
                        this.equipment = data;
                    })
                    .catch(e => {
                        console.error('Error loading equipment:', e);
                        this.equipment = eventData;
                    });
            });
        },

        formatDate(date) {
            if (!date) return 'N/A';
            return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        },

        formatDateTime(dateTime) {
            if (!dateTime) return 'N/A';
            return new Date(dateTime).toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        },

        getStatusBadgeColor(status) {
            const colors = {
                'For Approval': 'bg-purple-200 text-purple-900',
                'For Delivery': 'bg-blue-200 text-blue-900',
                'Delivered': 'bg-orange-200 text-orange-900',
                'Returned': 'bg-green-200 text-green-900',
                'Rejected': 'bg-gray-200 text-gray-900',
                'Cancelled': 'bg-red-200 text-red-900'
            };
            return colors[status] || 'bg-blue-200 text-blue-900';
        }
    }
}
</script>