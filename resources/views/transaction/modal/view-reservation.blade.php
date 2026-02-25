<x-modal name="view-reservation" maxWidth="max-w-[700px]" focusable>
    <div class="p-8" x-data="viewReservation()" x-init="init()">
        <div class="flex justify-between items-start mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Reservation Details</h2>
            <button type="button" @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <x-lucide-x class="w-6 h-6"/>
            </button>
        </div>

        {{-- Status Badge --}}
        <div class="mb-6">
            <span 
                class="px-4 py-2 rounded-full text-sm font-semibold"
                :class="getStatusBadgeColor(reservation.status)"
                x-text="reservation.status"
            ></span>
        </div>

        {{-- Event Information --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold text-slate-800 mb-3">Event Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Event Name</p>
                    <p class="text-sm font-semibold text-slate-700" x-text="reservation.event_name"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Facility</p>
                    <p class="text-sm font-semibold text-slate-700" x-text="reservation.facility_name"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Purpose Category</p>
                    <p class="text-sm font-semibold text-slate-700" x-text="reservation.purpose_category"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Resident Type</p>
                    <p class="text-sm font-semibold text-slate-700" x-text="reservation.resident_type"></p>
                </div>
            </div>
        </div>

        {{-- Renter Information --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold text-slate-800 mb-3">Renter Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Name</p>
                    <p class="text-sm font-semibold text-slate-700" x-text="reservation.renter_name"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Contact No.</p>
                    <p class="text-sm font-semibold text-slate-700" x-text="reservation.renter_contact || 'N/A'"></p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-500 mb-1">Email Address</p>
                    <p class="text-sm font-semibold text-slate-700" x-text="reservation.email"></p>
                </div>
            </div>
        </div>

        {{-- Schedule Information --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold text-slate-800 mb-3">Schedule</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Start Date</p>
                    <p class="text-sm font-semibold text-slate-700" x-text="reservation.start_date"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">End Date</p>
                    <p class="text-sm font-semibold text-slate-700" x-text="reservation.end_date"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Time Start</p>
                    <p class="text-sm font-semibold text-slate-700" x-text="reservation.time_start"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Time End</p>
                    <p class="text-sm font-semibold text-slate-700" x-text="reservation.time_end"></p>
                </div>
            </div>
        </div>

        {{-- Equipment Information --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6" x-show="reservation.equipments && reservation.equipments.length > 0">
            <h3 class="text-lg font-bold text-slate-800 mb-3">Equipment Borrowed</h3>
            <div class="space-y-2">
                <template x-for="equipment in reservation.equipments" :key="equipment.id">
                    <div class="flex justify-between items-center bg-white rounded-lg p-3 border border-gray-200">
                        <span class="text-sm font-medium text-slate-700" x-text="equipment.equipment_type"></span>
                        <span class="text-sm text-slate-500">Qty: <span class="font-semibold" x-text="equipment.quantity_borrowed"></span></span>
                    </div>
                </template>
            </div>
        </div>

        {{-- Processing Information --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold text-slate-800 mb-3">Processing Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <template x-if="reservation.processed_by">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Processed By</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="reservation.processed_by"></p>
                    </div>
                </template>
                <template x-if="reservation.created_at">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Date Created</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(reservation.created_at)"></p>
                    </div>
                </template>
                <template x-if="reservation.transferred_for_payment_by">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Approved For Payment By</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="reservation.transferred_for_payment_by"></p>
                    </div>
                </template>
                <template x-if="reservation.for_payment_at">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Date Approved For Payment</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(reservation.for_payment_at)"></p>
                    </div>
                </template>
                <template x-if="reservation.transferred_paid_by">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Marked Paid By</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="reservation.transferred_paid_by"></p>
                    </div>
                </template>
                <template x-if="reservation.paid_at">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Date Paid</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(reservation.paid_at)"></p>
                    </div>
                </template>
                <template x-if="reservation.cancelled_by">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Cancelled By</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="reservation.cancelled_by"></p>
                    </div>
                </template>
                <template x-if="reservation.date_of_cancelled">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Date Cancelled</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(reservation.date_of_cancelled)"></p>
                    </div>
                </template>
                <template x-if="reservation.rejected_by">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Rejected By</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="reservation.rejected_by"></p>
                    </div>
                </template>
                <template x-if="reservation.date_of_rejected">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Date Rejected</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(reservation.date_of_rejected)"></p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Action Buttons based on Status --}}
        <div class="sticky bottom-0 bg-white pt-4 border-t border-gray-200 flex justify-end gap-3">
            <x-secondary-button @click="$dispatch('close')">Close</x-secondary-button>
            
            {{-- For Approval Status Actions --}}
            <template x-if="reservation.status === 'For Approval'">
                <div class="flex gap-3">
                    <button 
                        @click="updateStatus('Rejected')"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm font-medium hover:bg-gray-600 transition"
                    >
                        Reject
                    </button>
                    <button 
                        type="button"
                        @click="fetchAndApprove(reservation.id)"
                        class="px-4 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition"
                    >
                        Approve for Payment
                    </button>
                </div>
            </template>

            {{-- For Payment Status Actions --}}
            <template x-if="reservation.status === 'For Payment'">
                <div class="flex gap-3">
                    <button 
                        @click="updateStatus('Cancelled')"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-medium hover:bg-red-600 transition"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="updateStatus('Paid')"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600 transition"
                    >
                        Mark as Paid
                    </button>
                </div>
            </template>

            {{-- Paid Status Actions --}}
            <template x-if="reservation.status === 'Paid'">
                <button 
                    @click="updateStatus('Cancelled')"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-medium hover:bg-red-600 transition"
                >
                    Cancel Reservation
                </button>
            </template>
        </div>
    </div>
</x-modal>

<script>
function viewReservation() {
    return {
        reservation: {
            id: null,
            event_name: '',
            facility_name: '',
            purpose_category: '',
            resident_type: '',
            renter_name: '',
            renter_contact: '',
            email: '',
            start_date: '',
            end_date: '',
            time_start: '',
            time_end: '',
            status: '',
            processed_by: '',
            transferred_for_payment_by: '',
            transferred_paid_by: '',
            cancelled_by: '',
            rejected_by: '',
            created_at: '',
            for_payment_at: '',
            paid_at: '',
            date_of_cancelled: '',
            date_of_rejected: '',
            equipments: []
        },

        init() {
            window.addEventListener('show-reservation', (event) => {
                const eventData = event.detail;
                fetch(`/facility/reservation/${eventData.id}`)
                    .then(r => r.json())
                    .then(data => {
                        this.reservation = data;
                        console.log('Reservation loaded:', this.reservation);
                    })
                    .catch(e => {
                        console.error('Error loading reservation:', e);
                        this.reservation = eventData;
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
                'For Approval': 'bg-blue-100 text-blue-700',
                'For Payment': 'bg-orange-100 text-orange-700',
                'Paid': 'bg-green-100 text-green-700',
                'Cancelled': 'bg-red-100 text-red-700',
                'Rejected': 'bg-gray-100 text-gray-700'
            };
            return colors[status] || 'bg-blue-100 text-blue-700';
        },

        async updateStatus(newStatus) {
            if (!confirm(`Are you sure you want to change status to "${newStatus}"?`)) {
                return;
            }

            try {
                const response = await fetch(`/facility/reservation/${this.reservation.id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                const data = await response.json();

                if (data.success) {
                    this.reservation.status = newStatus;
                    alert('Status updated successfully!');
                    window.location.reload();
                } else {
                    alert('Failed to update status: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while updating status');
            }
        },

        fetchAndApprove(id) {
            console.log('fetchAndApprove called with ID:', id);
            if (!id) {
                alert('Reservation ID not found');
                return;
            }
            
            this.$dispatch('close');
            setTimeout(() => {
                this.$dispatch('open-modal', 'approve-for-payment');
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('set-approve-id', { detail: id }));
                }, 200);
            }, 200);
        }
    }
}
</script>