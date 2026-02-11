<x-modal name="edit-request" maxWidth="max-w-[700px]" focusable>
    <div class="p-8 sm:p-8 flex flex-col" 
         :class="isCedulaDocument ? 'h-[806px]' : 'h-[926px]'"
         x-data="editRequestForm()" 
         @load-edit-request.window="loadRequest($event.detail)">
        <div class="flex justify-between items-start mb-4">
            <h2 class="text-3xl font-bold text-gray-900">Edit Document Request</h2>
            <button
                type="button"
                @click="$dispatch('close')"
                class="text-gray-400 hover:text-gray-600 transition-colors"
            >
                <x-lucide-x class="w-6 h-6"/>
            </button>
        </div>

        <div class="space-y-4 flex-1 overflow-y-auto pr-2">
            <!-- Document Type (Read-only) -->
            <div>
                <x-input-label>Document Type</x-input-label>
                <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                    <span x-text="documentType"></span>
                </div>
            </div>

            <!-- Status (Read-only) -->
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

            <!-- Name (Read-only) & Email (Editable) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label>Name</x-input-label>
                    <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                        <span x-text="name"></span>
                    </div>
                </div>
                <div>
                    <x-input-label for="edit-email">Email <span class="text-red-500">*</span></x-input-label>
                    <input type="email" id="edit-email" x-model="email"
                        class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                    <p x-show="errors.email" x-text="errors.email" class="text-red-500 text-xs mt-1"></p>
                </div>
            </div>

            <!-- Contact No. (Editable) & Annual Income (Editable only for Cedula) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="edit-contact-no">Contact No. <span class="text-red-500">*</span></x-input-label>
                    <input type="text" id="edit-contact-no" x-model="contactNo"
                        class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                    <p x-show="errors.contact_no" x-text="errors.contact_no" class="text-red-500 text-xs mt-1"></p>
                </div>
                <div>
                    <x-input-label for="edit-annual-income">Annual Income</x-input-label>
                    <template x-if="isCedulaDocument">
                        <div>
                            <input type="number" id="edit-annual-income" x-model="annualIncome" step="0.01" min="0"
                                class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                            <p x-show="errors.annual_income" x-text="errors.annual_income" class="text-red-500 text-xs mt-1"></p>
                        </div>
                    </template>
                    <template x-if="!isCedulaDocument">
                        <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50">
                            <span x-text="annualIncome ? '₱ ' + parseFloat(annualIncome).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : 'N/A'"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Address (Read-only) -->
            <div>
                <x-input-label>Address</x-input-label>
                <div class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg bg-gray-50 min-h-20">
                    <span x-text="address"></span>
                </div>
            </div>

            <!-- Cedula Fields - Sex and Birthdate (Read-only) -->
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

            <!-- Cedula Fields - Civil Status and Citizenship (Read-only) -->
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

            <!-- Years and Months (Editable, hidden when Cedula) -->
            <template x-if="!isCedulaDocument">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="edit-years-of-stay">Years of Stay</x-input-label>
                        <input type="number" id="edit-years-of-stay" x-model="yearsOfStay" min="0" max="100"
                            class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                        <p x-show="errors.years_of_stay" x-text="errors.years_of_stay" class="text-red-500 text-xs mt-1"></p>
                    </div>
                    <div>
                        <x-input-label for="edit-months-of-stay">Months of Stay</x-input-label>
                        <input type="number" id="edit-months-of-stay" x-model="monthsOfStay" min="0" max="11"
                            class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                        <p x-show="errors.months_of_stay" x-text="errors.months_of_stay" class="text-red-500 text-xs mt-1"></p>
                    </div>
                </div>
            </template>

            <!-- Purpose (Editable, hidden when Cedula) -->
            <template x-if="!isCedulaDocument">
                <div>
                    <x-input-label for="edit-purpose">Purpose <span class="text-red-500">*</span></x-input-label>
                    <select id="edit-purpose" x-model="purposeId"
                        @change="if (purposeId !== otherPurposeId) { otherPurpose = ''; }"
                        class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Purpose</option>
                        @foreach(\App\Models\DocumentPurpose::all() as $purpose)
                            <option value="{{ $purpose->id }}">{{ $purpose->name }}</option>
                        @endforeach
                    </select>
                    <p x-show="errors.purpose_id" x-text="errors.purpose_id" class="text-red-500 text-xs mt-1"></p>

                    <!-- Other Purpose input (appears only when Others is selected) -->
                    <div x-show="purposeId === otherPurposeId" x-transition class="mt-2">
                        <x-input-label for="edit-other-purpose">Please specify <span class="text-red-500">*</span></x-input-label>
                        <input type="text" id="edit-other-purpose" x-model="otherPurpose"
                            class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter purpose" />
                        <p x-show="errors.other_purpose" x-text="errors.other_purpose" class="text-red-500 text-xs mt-1"></p>
                    </div>
                </div>
            </template>

            <!-- Remarks (Editable, hidden when Cedula) -->
            <template x-if="!isCedulaDocument">
                <div>
                    <x-input-label for="edit-remarks">Remarks</x-input-label>
                    <textarea id="edit-remarks" x-model="remarks" rows="3"
                        class="w-full mt-1 px-4 py-2 text-sm text-slate-700 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </template>

            <!-- Fee (Read-only) -->
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
            <x-secondary-button @click="$dispatch('close')">Cancel</x-secondary-button>
            <x-button x-bind:disabled="submitting" @click="submitEdit()">
                <span x-show="!submitting">Save Changes</span>
                <span x-show="submitting">Saving...</span>
            </x-button>
        </div>
    </div>

    <script>
    function editRequestForm() {
        return {
            requestId: '',
            name: '',
            email: '',
            contactNo: '',
            address: '',
            documentType: '',
            status: '',
            purposeId: '',
            purpose: '',
            otherPurpose: '',
            otherPurposeId: '{{ \App\Models\DocumentPurpose::where("name", "Others (please specify)")->first()?->id ?? "8" }}',
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
            editedAt: '',
            createdByName: '',
            signedByName: '',
            transferredForReleaseByName: '',
            releasedByName: '',
            cancelledByName: '',
            editedByName: '',
            isCedulaDocument: false,
            submitting: false,
            errors: {},

            formatDate(date) {
                if (!date) return 'N/A';
                return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            },

            formatDateTime(dateTime) {
                if (!dateTime) return 'N/A';
                return new Date(dateTime).toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
            },

            loadRequest(requestData) {
                console.log('Loading edit request data:', requestData);
                console.log('Purpose ID from API:', requestData.purpose_id);
                
                this.requestId = requestData.id || '';
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
                this.yearsOfStay = requestData.years_of_stay !== null ? String(requestData.years_of_stay) : '';
                this.monthsOfStay = requestData.months_of_stay !== null ? String(requestData.months_of_stay) : '';
                this.remarks = requestData.remarks || '';
                this.fee = requestData.fee || '0.00';
                this.createdAt = requestData.created_at || '';
                this.forSignatureAt = requestData.for_signature_at || '';
                this.forReleaseAt = requestData.for_release_at || '';
                this.dateOfRelease = requestData.date_of_release || '';
                this.dateOfCancel = requestData.date_of_cancel || '';
                this.editedAt = requestData.date_of_edited || '';
                this.createdByName = requestData.created_by || '';
                this.signedByName = requestData.signed_by || '';
                this.transferredForReleaseByName = requestData.transferred_for_release_by || '';
                this.releasedByName = requestData.released_by || '';
                this.cancelledByName = requestData.cancelled_by || '';
                this.editedByName = requestData.updated_by || '';
                this.isCedulaDocument = this.documentType.toLowerCase().includes('cedula');
                this.errors = {};
                this.submitting = false;
                
                // Set purposeId after setting isCedulaDocument to ensure the select is rendered
                this.$nextTick(() => {
                    this.purposeId = requestData.purpose_id ? String(requestData.purpose_id) : '';
                    console.log('Purpose ID set to:', this.purposeId);
                });
            },

            async submitEdit() {
                this.errors = {};
                this.submitting = true;

                const payload = {
                    email: this.email,
                    contact_no: this.contactNo,
                };

                if (this.isCedulaDocument) {
                    payload.annual_income = this.annualIncome;
                } else {
                    payload.years_of_stay = this.yearsOfStay;
                    payload.months_of_stay = this.monthsOfStay;
                    payload.purpose_id = this.purposeId;
                    payload.other_purpose = this.otherPurpose;
                    payload.remarks = this.remarks;
                }

                try {
                    const response = await fetch(`/document-requests/${this.requestId}/update`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (data.errors) {
                            this.errors = {};
                            for (const [key, messages] of Object.entries(data.errors)) {
                                this.errors[key] = messages[0];
                            }
                        } else {
                            alert(data.message || 'Failed to update request');
                        }
                        this.submitting = false;
                        return;
                    }

                    this.$dispatch('close');
                    window.location.reload();
                } catch (error) {
                    console.error('Error updating request:', error);
                    alert('Failed to update request: ' + error.message);
                    this.submitting = false;
                }
            }
        }
    }
    </script>
</x-modal>