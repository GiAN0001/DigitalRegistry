<x-modal name="view-request" maxWidth="max-w-[700px]" focusable>
    <div class="p-8" x-data="viewRequestForm()" @load-request.window="loadRequest($event.detail)">
        <div class="flex justify-between items-start mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Document Request Details</h2>
            <button type="button" @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <x-lucide-x class="w-6 h-6"/>
            </button>
        </div>

        {{-- Status Badge --}}
        <div class="mb-6">
            <span 
                class="px-4 py-2 rounded-full text-sm font-semibold"
                :class="getStatusBadgeColor(status)"
                x-text="status"
            ></span>
        </div>

        <div class="max-h-[600px] overflow-y-auto pr-2 space-y-6">
            {{-- Request Information --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-bold text-slate-800 mb-3">Request Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Document Type</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="documentType"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Fee</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="'₱ ' + parseFloat(fee || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                    </div>
                </div>
            </div>

            {{-- Resident Information --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-bold text-slate-800 mb-3">Resident Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Name</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="name"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Email</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="email"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Contact No.</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="contactNo"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Annual Income</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="annualIncome ? '₱ ' + parseFloat(annualIncome).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : 'N/A'"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 mb-1">Address</p>
                        <p class="text-sm font-semibold text-slate-700" x-text="address"></p>
                    </div>
                </div>
            </div>

            {{-- Cedula Information (only for Cedula documents) --}}
            <template x-if="isCedulaDocument">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-bold text-slate-800 mb-3">Personal Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Sex</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="sex || 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Birthdate</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="birthdate ? formatDate(birthdate) : 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Civil Status</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="civilStatus || 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Citizenship</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="citizenship || 'N/A'"></p>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Stay Information (only for non-Cedula documents) --}}
            <template x-if="!isCedulaDocument">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-bold text-slate-800 mb-3">Residency Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Years of Stay</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="yearsOfStay || 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Months of Stay</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="monthsOfStay || 'N/A'"></p>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Purpose Information (only for non-Cedula documents) --}}
            <template x-if="!isCedulaDocument">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-bold text-slate-800 mb-3">Request Purpose</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Purpose</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="purpose || 'N/A'"></p>
                        </div>
                        <template x-if="otherPurpose">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Other Purpose</p>
                                <p class="text-sm font-semibold text-slate-700" x-text="otherPurpose"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Remarks (only for non-Cedula, non-Cancelled) --}}
            <template x-if="!isCedulaDocument && status !== 'Cancelled'">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-bold text-slate-800 mb-3">Remarks</h3>
                    <p class="text-sm font-semibold text-slate-700" x-text="remarks || 'N/A'"></p>
                </div>
            </template>

            {{-- Cancellation Reason (only when Cancelled) --}}
            <template x-if="status === 'Cancelled'">
                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                    <h3 class="text-lg font-bold text-red-800 mb-3">Reason for Cancellation</h3>
                    <p class="text-sm font-semibold text-red-700" x-text="remarks || 'N/A'"></p>
                </div>
            </template>

            {{-- Processing Information --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-bold text-slate-800 mb-3">Processing Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <template x-if="createdByName">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Created By</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="createdByName"></p>
                        </div>
                    </template>
                    <template x-if="createdAt">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date Created</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(createdAt)"></p>
                        </div>
                    </template>
                    <template x-if="signedByName">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Signed By</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="signedByName"></p>
                        </div>
                    </template>
                    <template x-if="forSignatureAt">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date Signed</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(forSignatureAt)"></p>
                        </div>
                    </template>
                    <template x-if="transferredForReleaseByName">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Transferred For Release By</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="transferredForReleaseByName"></p>
                        </div>
                    </template>
                    <template x-if="forReleaseAt">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date For Release</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(forReleaseAt)"></p>
                        </div>
                    </template>
                    <template x-if="releasedByName">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Released By</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="releasedByName"></p>
                        </div>
                    </template>
                    <template x-if="dateOfRelease">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date Released</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(dateOfRelease)"></p>
                        </div>
                    </template>
                    <template x-if="cancelledByName">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Cancelled By</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="cancelledByName"></p>
                        </div>
                    </template>
                    <template x-if="dateOfCancel">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date Cancelled</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(dateOfCancel)"></p>
                        </div>
                    </template>
                    <template x-if="editedByName">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Updated By</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="editedByName"></p>
                        </div>
                    </template>
                    <template x-if="editedAt">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date Updated</p>
                            <p class="text-sm font-semibold text-slate-700" x-text="formatDateTime(editedAt)"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="sticky bottom-0 bg-white pt-4 border-t border-gray-200 flex justify-end gap-3 mt-6">
            <x-secondary-button @click="$dispatch('close')">Close</x-secondary-button>
        </div>
    </div>
</x-modal>

<script>
function viewRequestForm() {
    return {
        requestId: '',
        name: '',
        email: '',
        contactNo: '',
        address: '',
        documentType: '',
        status: '',
        purpose: '',
        otherPurpose: '',
        sex: '',
        birthdate: '',
        civilStatus: '',
        citizenship: '',
        annualIncome: '',
        yearsOfStay: '',
        monthsOfStay: '',
        remarks: '',
        fee: '0.00',
        createdAt: '',
        forSignatureAt: '',
        forReleaseAt: '',
        dateOfRelease: '',
        dateOfCancel: '',
        createdByName: '',
        signedByName: '',
        transferredForReleaseByName: '',
        releasedByName: '',
        cancelledByName: '',
        editedAt: '',
        editedByName: '',
        isCedulaDocument: false,

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
                'For Fulfillment': 'bg-yellow-100 text-yellow-700',
                'For Signature': 'bg-blue-100 text-blue-700',
                'For Release': 'bg-orange-100 text-orange-700',
                'Released': 'bg-green-100 text-green-700',
                'Cancelled': 'bg-red-100 text-red-700'
            };
            return colors[status] || 'bg-blue-100 text-blue-700';
        },

        loadRequest(requestData) {
            console.log('Loading request data:', requestData);
            this.requestId = String(requestData.id || '').padStart(3, '0');
            this.name = requestData.resident_name || requestData.name || '';
            this.email = requestData.email || '';
            this.contactNo = requestData.contact_no || '';
            this.address = requestData.address || '';
            this.documentType = requestData.document_type || '';
            this.status = requestData.status || '';
            this.purpose = requestData.purpose_name || '';
            this.otherPurpose = requestData.other_purpose || '';
            this.sex = requestData.sex || '';
            this.birthdate = requestData.birthdate || '';
            this.civilStatus = requestData.civil_status || '';
            this.citizenship = requestData.citizenship || '';
            this.annualIncome = requestData.annual_income || '';
            this.yearsOfStay = requestData.years_of_stay || '';
            this.monthsOfStay = requestData.months_of_stay || '';
            this.remarks = requestData.remarks || '';
            this.fee = requestData.fee || '0.00';
            this.createdAt = requestData.created_at || '';
            this.forSignatureAt = requestData.for_signature_at || '';
            this.forReleaseAt = requestData.for_release_at || '';
            this.dateOfRelease = requestData.date_of_release || '';
            this.dateOfCancel = requestData.date_of_cancel || '';
            this.createdByName = requestData.created_by || '';
            this.signedByName = requestData.signed_by || '';
            this.transferredForReleaseByName = requestData.transferred_for_release_by || '';
            this.releasedByName = requestData.released_by || '';
            this.cancelledByName = requestData.cancelled_by || '';
            this.editedAt = requestData.date_of_edited || '';
            this.editedByName = requestData.updated_by || '';
            this.isCedulaDocument = this.documentType.toLowerCase().includes('cedula');
            console.log('Is Cedula:', this.isCedulaDocument);
        }
    }
}
</script>

<style>
[x-cloak] {
    display: none !important;
}
</style>