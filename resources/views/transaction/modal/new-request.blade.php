<x-modal name="new-request" maxWidth="max-w-[700px]" focusable>
    <div class="p-8" x-data="documentRequestForm()">
        <form method="POST" action="{{ route('document-request.store') }}" id="documentForm">
            @csrf
            
            <!-- Document Type -->
            <div class="mb-4">
                <x-input-label for="document_type_id">Document Type <span class="text-red-500">*</span></x-input-label>
                <select 
                    id="document_type_id"
                    name="document_type_id"
                    class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-lg"
                    required>
                    <option value="">Select Document Type</option>
                    @foreach(\App\Models\DocumentType::all() as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Name with search -->
            <div class="mb-4">
                <x-input-label for="name">Name <span class="text-red-500">*</span></x-input-label>
                <div class="relative">
                    <x-text-input 
                        id="name" 
                        name="name" 
                        type="text" 
                        required
                        x-model="searchQuery"
                        @input="if (!selectingResident) searchResidents()"
                        @keydown.escape="open = false"
                        class="w-full mt-1 text-sm text-slate-700"
                        placeholder="Search resident..." 
                        autocomplete="off" />
                    
                    <!-- Search Results Dropdown -->
                    <div x-show="open && suggestions.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-64 overflow-y-auto">
                        <template x-for="resident in suggestions" :key="resident.id">
                            <div @click="selectResident(resident)" class="px-4 py-2 cursor-pointer hover:bg-blue-100 border-b last:border-b-0">
                                <div class="font-semibold" x-text="resident.full_name"></div>
                                <div class="text-xs text-gray-500" x-text="resident.email"></div>
                                <div class="text-xs text-gray-500" x-text="resident.address"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Email and Contact -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <x-input-label for="email">Email <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        id="email" 
                        name="email" 
                        type="email" 
                        required 
                        x-model="email" 
                        class="w-full mt-1 text-sm text-slate-700"
                        placeholder="Enter Email"
                        autocomplete="email" />
                </div>
                <div>
                    <x-input-label for="contact_no">Contact No. <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        id="contact_no" 
                        name="contact_no" 
                        type="tel" 
                        required 
                        x-model="contactNo" 
                        class="w-full mt-1 text-sm text-slate-700" 
                        placeholder="Enter Contact No."
                        autocomplete="tel" />
                </div>
            </div>

            <!-- Address -->
            <div class="mb-4">
                <x-input-label for="address">Address <span class="text-red-500">*</span></x-input-label>
                <textarea 
                    id="address" 
                    name="address" 
                    required 
                    rows="3" 
                    x-model="address" 
                    class="w-full px-4 py-3 text-sm text-slate-700 border-1 border-gray-300 rounded-lg focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400 mt-1" 
                    placeholder="Enter Address"
                    autocomplete="street-address"></textarea>
            </div>

            <!-- Years and Months -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <x-input-label for="years_of_stay">Years of Stay</x-input-label>
                    <x-text-input 
                        id="years_of_stay" 
                        name="years_of_stay" 
                        type="number" 
                        min="0" 
                        max="100" 
                        class="w-full mt-1 text-sm text-slate-700" 
                        placeholder="0-100"
                        autocomplete="off" />
                </div>
                <div>
                    <x-input-label for="months_of_stay">Months of Stay</x-input-label>
                    <x-text-input 
                        id="months_of_stay" 
                        name="months_of_stay" 
                        type="number" 
                        min="0" 
                        max="11" 
                        class="w-full mt-1 text-sm text-slate-700" 
                        placeholder="0-11"
                        autocomplete="off" />
                </div>
            </div>

            <!-- Purpose -->
            <div class="mb-4">
                <x-input-label for="purpose_id">Purpose <span class="text-red-500">*</span></x-input-label>
                <select 
                    id="purpose_id"
                    name="purpose_id"
                    class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-lg"
                    @change="checkOtherPurpose($event)"
                    required>
                    <option value="">Select Purpose</option>
                    @foreach(\App\Models\DocumentPurpose::all() as $purpose)
                        <option value="{{ $purpose->id }}">{{ $purpose->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Other Purpose (conditional) -->
            <div class="mb-4" x-show="showOtherPurpose" x-cloak>
                <x-input-label for="other_purpose">Please specify <span class="text-red-500">*</span></x-input-label>
                <x-text-input 
                    id="other_purpose" 
                    name="other_purpose" 
                    type="text" 
                    class="w-full mt-1 text-sm text-slate-700"
                    placeholder="Enter other purpose"
                    x-bind:required="showOtherPurpose"
                    autocomplete="off" />
            </div>

            <!-- Remarks -->
            <div class="mb-6">
                <x-input-label for="remarks">Remarks</x-input-label>
                <textarea 
                    id="remarks" 
                    name="remarks" 
                    rows="2" 
                    class="w-full px-4 py-3 text-sm text-slate-700 border-1 border-gray-300 rounded-lg focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400 mt-1" 
                    placeholder="Enter remarks"
                    autocomplete="off"></textarea>
            </div>

            <!-- Hidden fields -->
            <input type="hidden" name="resident_id" x-model="residentId" />
            <input type="hidden" name="area_id" x-model="areaId" />

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mt-6">
                <button type="button" class="py-3 text-gray-600 hover:text-gray-800" @click="$dispatch('close')">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition shadow-md" :disabled="!residentId">
                    Confirm
                </button>
            </div>
        </form>
    </div>

    <script>
    function documentRequestForm() {
         return {
            open: false,
            searchQuery: '',
            suggestions: [],
            residentId: '',
            areaId: '',
            email: '',
            contactNo: '',
            address: '',
            showOtherPurpose: false,
            searchTimeout: null,
            selectingResident: false,

            checkOtherPurpose(event) {
                const selectedText = event.target.options[event.target.selectedIndex].text.toLowerCase();
                this.showOtherPurpose = selectedText.includes('other');
            },

            handleSubmit(event) {
                if (!this.residentId) {
                    event.preventDefault();
                    alert('Please select a resident from search results');
                    return false;
                }
                
                return true;
            },

            searchResidents() {
                const query = this.searchQuery.trim();
                clearTimeout(this.searchTimeout);
                
                if (query.length < 2) {
                    this.suggestions = [];
                    this.open = false;
                    return;
                }
                
                this.searchTimeout = setTimeout(async () => {
                    try {
                        const url = `/residents/search?q=${encodeURIComponent(query)}`;
                        const response = await fetch(url);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const data = await response.json();
                        this.suggestions = data;
                        this.open = data.length > 0;
                    } catch (error) {
                        this.suggestions = [];
                        this.open = false;
                    }
                }, 300);
            },

            selectResident(resident) {
                this.selectingResident = true;
                this.searchQuery = resident.full_name;
                this.email = resident.email || '';
                this.contactNo = resident.contact_number || '';
                this.address = resident.address || '';
                this.residentId = resident.id;
                this.areaId = resident.household_id || '';
                this.suggestions = [];
                this.open = false;
                setTimeout(() => this.selectingResident = false, 100);
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