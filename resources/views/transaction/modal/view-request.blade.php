<x-modal name="view-request" maxWidth="max-w-[700px]" focusable>
    <div class="p-8 sm:p-8 flex flex-col" 
         :class="isCedulaDocument ? 'h-[806px]' : 'h-[926px]'"
         x-data="viewRequestForm()" 
         @load-request.window="loadRequest($event.detail)">
        <div class="flex justify-between items-start mb-4">
            <h2 class="text-3xl font-bold text-gray-900">View Document Request</h2>
            <button
                type="button"
                @click="$dispatch('close')"
                class="text-gray-400 hover:text-gray-600 transition-colors"
            >
                <x-lucide-x class="w-6 h-6"/>
            </button>
        </div>

        <div class="space-y-4 flex-1 overflow-y-auto pr-2">
            <!-- Document Type -->
            <div>
                <x-input-label>Document Type</x-input-label>
                <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                    <span x-text="documentType"></span>
                </div>
            </div>

            <!-- Status -->
            <div>
                <x-input-label>Status</x-input-label>
                <div class="w-full mt-1 px-4 py-2 text-sm font-semibold border border-gray-300 rounded-lg bg-gray-50"
                     :class="{
                        'text-yellow-700': status === 'For Fulfillment',
                        'text-blue-700': status === 'For Signature',
                        'text-orange-700': status === 'For Release',
                        'text-green-700': status === 'Released',
                        'text-red-700': status === 'Cancelled'
                     }">
                    <span x-text="status"></span>
                </div>
            </div>

            <!-- Name, Email, Contact -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label>Name</x-input-label>
                    <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                        <span x-text="name"></span>
                    </div>
                </div>
                <div>
                    <x-input-label>Email</x-input-label>
                    <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                        <span x-text="email"></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label>Contact No.</x-input-label>
                    <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                        <span x-text="contactNo"></span>
                    </div>
                </div>
                <div>
                    <x-input-label>Annual Income</x-input-label>
                    <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                        <span x-text="annualIncome ? '₱ ' + parseFloat(annualIncome).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : 'N/A'"></span>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div>
                <x-input-label>Address</x-input-label>
                <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50 min-h-20">
                    <span x-text="address"></span>
                </div>
            </div>

            <!-- Cedula Fields - Sex and Birthdate -->
            <template x-if="isCedulaDocument">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label>Sex</x-input-label>
                        <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                            <span x-text="sex || 'N/A'"></span>
                        </div>
                    </div>
                    <div>
                        <x-input-label>Birthdate</x-input-label>
                        <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                            <span x-text="birthdate ? formatDate(birthdate) : 'N/A'"></span>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Cedula Fields - Civil Status and Citizenship -->
            <template x-if="isCedulaDocument">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label>Civil Status</x-input-label>
                        <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                            <span x-text="civilStatus || 'N/A'"></span>
                        </div>
                    </div>
                    <div>
                        <x-input-label>Citizenship</x-input-label>
                        <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                            <span x-text="citizenship || 'N/A'"></span>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Years and Months (hidden when Cedula) -->
            <template x-if="!isCedulaDocument">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label>Years of Stay</x-input-label>
                        <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                            <span x-text="yearsOfStay || 'N/A'"></span>
                        </div>
                    </div>
                    <div>
                        <x-input-label>Months of Stay</x-input-label>
                        <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                            <span x-text="monthsOfStay || 'N/A'"></span>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Purpose (hidden when Cedula) -->
            <template x-if="!isCedulaDocument">
                <div>
                    <x-input-label>Purpose</x-input-label>
                    <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                        <span x-text="purpose || 'N/A'"></span>
                    </div>
                </div>
            </template>

            <!-- Other Purpose (hidden when Cedula) -->
            <template x-if="!isCedulaDocument && otherPurpose">
                <div>
                    <x-input-label>Other Purpose</x-input-label>
                    <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                        <span x-text="otherPurpose || 'N/A'"></span>
                    </div>
                </div>
            </template>

            <!-- Remarks (hidden when Cedula, shown when not cancelled) -->
            <template x-if="!isCedulaDocument && status !== 'Cancelled'">
                <div>
                    <x-input-label>Remarks</x-input-label>
                    <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50 min-h-20">
                        <span x-text="remarks || 'N/A'"></span>
                    </div>
                </div>
            </template>

            <!-- Reason for Cancellation (only when Cancelled) -->
            <template x-if="status === 'Cancelled'">
                <div>
                    <x-input-label>Reason for Cancellation</x-input-label>
                    <div class="w-full mt-1 px-4 py-2 text-sm text-red-700 border border-red-300 rounded-lg bg-red-50 min-h-20">
                        <span x-text="remarks || 'N/A'"></span>
                    </div>
                </div>
            </template>

            <!-- Fee -->
            <div>
                <x-input-label>Fee</x-input-label>
                <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50 font-semibold">
                    <span x-text="'₱ ' + parseFloat(fee || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="border-t pt-4">
                <x-input-label class="mb-3">Activity Timeline</x-input-label>
                <div class="space-y-3">
                    <!-- For Fulfillment -->
                    <template x-if="createdAt">
                        <div class="flex items-start gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-yellow-500 mt-1"></div>
                                <div class="w-0.5 h-full bg-gray-200" x-show="editedAt || forSignatureAt || dateOfCancel"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-yellow-700">For Fulfillment</p>
                                <p class="text-xs text-slate-500" x-text="formatDateTime(createdAt)"></p>
                                <p class="text-xs text-slate-400" x-show="createdByName">
                                    By: <span x-text="createdByName" class="font-medium"></span>
                                </p>
                            </div>
                        </div>
                    </template>

                    <!-- Edited -->
                    <template x-if="editedAt">
                        <div class="flex items-start gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-purple-500 mt-1"></div>
                                <div class="w-0.5 h-full bg-gray-200" x-show="forSignatureAt || dateOfCancel"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-purple-700">Edited</p>
                                <p class="text-xs text-slate-500" x-text="formatDateTime(editedAt)"></p>
                                <p class="text-xs text-slate-400" x-show="editedByName">
                                    By: <span x-text="editedByName" class="font-medium"></span>
                                </p>
                            </div>
                        </div>
                    </template>

                    <!-- For Signature -->
                    <template x-if="forSignatureAt">
                        <div class="flex items-start gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-blue-500 mt-1"></div>
                                <div class="w-0.5 h-full bg-gray-200" x-show="forReleaseAt || dateOfCancel"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-blue-700">For Signature</p>
                                <p class="text-xs text-slate-500" x-text="formatDateTime(forSignatureAt)"></p>
                                <p class="text-xs text-slate-400" x-show="signedByName">
                                    By: <span x-text="signedByName" class="font-medium"></span>
                                </p>
                            </div>
                        </div>
                    </template>

                    <!-- For Release -->
                    <template x-if="forReleaseAt">
                        <div class="flex items-start gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-orange-500 mt-1"></div>
                                <div class="w-0.5 h-full bg-gray-200" x-show="dateOfRelease || dateOfCancel"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-orange-700">For Release</p>
                                <p class="text-xs text-slate-500" x-text="formatDateTime(forReleaseAt)"></p>
                                <p class="text-xs text-slate-400" x-show="transferredForReleaseByName">
                                    By: <span x-text="transferredForReleaseByName" class="font-medium"></span>
                                </p>
                            </div>
                        </div>
                    </template>

                    <!-- Released -->
                    <template x-if="dateOfRelease">
                        <div class="flex items-start gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-green-500 mt-1"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-green-700">Released</p>
                                <p class="text-xs text-slate-500" x-text="formatDateTime(dateOfRelease)"></p>
                                <p class="text-xs text-slate-400" x-show="releasedByName">
                                    By: <span x-text="releasedByName" class="font-medium"></span>
                                </p>
                            </div>
                        </div>
                    </template>

                    <!-- Cancelled -->
                    <template x-if="dateOfCancel">
                        <div class="flex items-start gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-red-500 mt-1"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-red-700">Cancelled</p>
                                <p class="text-xs text-slate-500" x-text="formatDateTime(dateOfCancel)"></p>
                                <p class="text-xs text-slate-400" x-show="cancelledByName">
                                    By: <span x-text="cancelledByName" class="font-medium"></span>
                                </p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white pt-4 border-t border-gray-200 flex justify-end gap-3 mt-4">
            <x-secondary-button @click="$dispatch('close')">Close</x-secondary-button>
        </div>
    </div>

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
</x-modal>