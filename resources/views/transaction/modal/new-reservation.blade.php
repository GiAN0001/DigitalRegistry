<x-modal name="new-reservation" maxWidth="max-w-[700px]" focusable>
    <div class="p-8">
        <div class="flex justify-between items-start mb-4">
            <h2 class="text-3xl font-bold text-gray-900">Facility Reservation</h2>
            <button type="button" @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <x-lucide-x class="w-6 h-6"/>
            </button>
        </div>

        <form method="POST" action="{{ route('facility.reservation.store') }}" id="reservationForm" x-data="reservationForm()" @submit="handleSubmit($event)">
            @csrf
            
            {{-- Facility Selection --}}
            <div class="mb-6">
                <x-input-label for="facility_id" class="text-sm font-semibold text-slate-700">
                    Facility <span class="text-red-500">*</span>
                </x-input-label>
                <select id="facility_id" name="facility_id" required
                    class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                    <option value="">Select Facility</option>
                    @foreach($facilities as $facility)
                        <option value="{{ $facility->id }}">{{ $facility->facility_type }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Resident Type --}}
            <div class="mb-6">
                <x-input-label for="resident_type" class="text-sm font-semibold text-slate-800">
                    Resident Type <span class="text-red-500">*</span>
                </x-input-label>
                <select id="resident_type" name="resident_type" required x-model="residentType" @change="resetResidentFields()"
                    class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                    <option value="">Select Resident Type</option>
                    <option value="resident">Resident</option>
                    <option value="non-resident">Non-Resident</option>
                </select>
            </div>

            {{-- Event Name --}}
            <div class="mb-6">
                <x-input-label for="event_name" class="text-sm font-semibold text-slate-800">
                    Event Name <span class="text-red-500">*</span>
                </x-input-label>
                <input type="text" id="event_name" name="event_name" required placeholder="Enter Event Name"
                    class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
            </div>

            <!-- Name with search for Residents -->
            <div class="mb-4" x-show="residentType === 'resident'" x-cloak>
                <x-input-label for="searchInput">Name <span class="text-red-500">*</span></x-input-label>
                <div class="relative">
                    <x-text-input 
                        id="searchInput" 
                        type="text" 
                        x-bind:required="residentType === 'resident'"
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
                <!-- Hidden input for resident_id -->
                <input type="hidden" name="resident_id" x-model="residentId" />
            </div>

            <!-- Contact No. for Residents (Display only) -->
            <div class="mb-6" x-show="residentType === 'resident'" x-cloak>
                <x-input-label for="resident_contact_display" class="text-sm font-semibold text-slate-800">
                    Contact No.
                </x-input-label>
                <input type="text" id="resident_contact_display" x-model="residentContact" readonly
                    class="w-full px-4 py-3 text-sm text-slate-500 bg-gray-100 border border-gray-300 rounded-lg mt-2">
            </div>

            <!-- Name field for Non-Residents -->
            <div class="mb-4" x-show="residentType === 'non-resident'" x-cloak>
                <x-input-label for="renter_name_non">Name <span class="text-red-500">*</span></x-input-label>
                <x-text-input 
                    id="renter_name_non" 
                    name="renter_name"
                    type="text" 
                    x-model="renterName"
                    x-bind:required="residentType === 'non-resident'"
                    class="w-full mt-1 text-sm text-slate-700"
                    placeholder="Enter name..." 
                    autocomplete="off" />
            </div>

            {{-- Contact No. for Non-Residents --}}
            <div class="mb-6" x-show="residentType === 'non-resident'" x-cloak>
                <x-input-label for="non_resident_contact" class="text-sm font-semibold text-slate-800">
                    Contact No. <span class="text-red-500">*</span>
                </x-input-label>
                <input type="text" id="non_resident_contact" name="renter_contact" x-model="renterContact" x-bind:required="residentType === 'non-resident'" placeholder="Enter Contact No."
                    class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
            </div>

            {{-- Start Date and End Date --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <x-input-label for="start_date" class="text-sm font-semibold text-slate-800">
                        Start Date <span class="text-red-500">*</span>
                    </x-input-label>
                    <input type="date" id="start_date" name="start_date" required
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                </div>
                <div>
                    <x-input-label for="end_date" class="text-sm font-semibold text-slate-800">
                        End Date <span class="text-red-500">*</span>
                    </x-input-label>
                    <input type="date" id="end_date" name="end_date" required
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                </div>
            </div>

            {{-- Time Start and Time End --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <x-input-label for="time_start" class="text-sm font-semibold text-slate-800">
                        Time Start <span class="text-red-500">*</span>
                    </x-input-label>
                    <input type="time" id="time_start" name="time_start" required
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700  focus:outline-none mt-2">
                </div>
                <div>
                    <x-input-label for="time_end" class="text-sm font-semibold text-slate-800">
                        Time End <span class="text-red-500">*</span>
                    </x-input-label>
                    <input type="time" id="time_end" name="time_end" required
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                </div>
            </div>

            {{-- Equipment Section (Dynamic) - Optional --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-slate-800">Equipment <span class="text-gray-400 font-normal">(Optional)</span></h3>
                    <button type="button" @click="addEquipmentRow()" 
                        x-show="equipmentItems.length < {{ count($equipments) }}"
                        class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                        + Add Equipment
                    </button>
                </div>
                <template x-for="(item, index) in equipmentItems" :key="index">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <select :name="'equipment_type[' + index + ']'"
                                class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none">
                                <option value="">Select Equipment Type</option>
                                @foreach($equipments as $equipment)
                                    <option value="{{ $equipment->id }}">{{ $equipment->equipment_type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <input type="number" :name="'equipment_quantity[' + index + ']'" min="1" placeholder="Quantity"
                                class="flex-1 px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none">
                            <button type="button" @click="removeEquipmentRow(index)" x-show="equipmentItems.length > 1"
                                class="w-12 h-12 flex items-center justify-center text-red-600 hover:bg-red-50 rounded-lg transition border-2 border-red-300">
                                <x-lucide-trash-2 class="w-6 h-6"/>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mt-6">
                <button type="button" class="py-3 text-gray-600 hover:text-gray-800" @click="$dispatch('close')">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition shadow-md">
                    Confirm
                </button>
            </div>
        </form>
    </div>
</x-modal>

<script>
    function reservationForm() {
        return {
            residentType: '',
            residentId: '',
            residentContact: '',
            renterName: '',
            renterContact: '',
            equipmentItems: [{}],
            searchQuery: '',
            open: false,
            suggestions: [],
            selectingResident: false,
            searchTimeout: null,

            handleSubmit(event) {
                if (this.residentType === 'resident' && !this.residentId) {
                    event.preventDefault();
                    alert('Please select a resident from search results');
                    return false;
                }
                
                if (this.residentType === 'non-resident') {
                    if (!this.renterName) {
                        event.preventDefault();
                        alert('Please enter name for non-resident');
                        return false;
                    }
                    if (!this.renterContact) {
                        event.preventDefault();
                        alert('Please enter contact for non-resident');
                        return false;
                    }
                }
                
                return true;
            },

            resetResidentFields() {
                this.residentId = '';
                this.residentContact = '';
                this.renterName = '';
                this.renterContact = '';
                this.searchQuery = '';
                this.suggestions = [];
                this.open = false;
            },
            
            addEquipmentRow() {
                this.equipmentItems.push({});
            },
            
            removeEquipmentRow(index) {
                this.equipmentItems.splice(index, 1);
            },
            
            selectResident(resident) {
                this.selectingResident = true;
                this.searchQuery = resident.full_name;
                this.residentId = resident.id;
                this.residentContact = resident.contact_number || '';
                this.suggestions = [];
                this.open = false;
                setTimeout(() => this.selectingResident = false, 100);
            },
            
            searchResidents() {
                const query = this.searchQuery.trim();
                clearTimeout(this.searchTimeout);
                
                if (query.length < 1) {
                    this.suggestions = [];
                    this.open = false;
                    return;
                }
                
                this.searchTimeout = setTimeout(async () => {
                    try {
                        const response = await fetch(`/residents/search?q=${encodeURIComponent(query)}`);
                        if (!response.ok) throw new Error('Search failed');
                        const data = await response.json();
                        this.suggestions = data;
                        this.open = data.length > 0;
                    } catch (error) {
                        console.error('Error:', error);
                        this.suggestions = [];
                        this.open = false;
                    }
                }, 300);
            }
        }
    }
</script>