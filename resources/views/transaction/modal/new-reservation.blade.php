<x-modal name="new-reservation" maxWidth="max-w-[700px]" focusable>
    <div class="p-8">
        <div class="flex justify-between items-start mb-4">
            <h2 class="text-3xl font-bold text-gray-900">Facility Reservation</h2>
            <button type="button" @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <x-lucide-x class="w-6 h-6"/>
            </button>
        </div>
        <form method="POST" action="{{ route('facility.reservation.store') }}" id="reservationForm" x-data="reservationForm()" @submit="handleSubmit($event)" x-cloak>
            @csrf
            
            {{-- Facility Selection --}}
            <div class="mb-6">
                <x-input-label for="facility_id" class="text-sm font-semibold text-slate-700">
                    Facility <span class="text-red-500">*</span>
                </x-input-label>
                <select id="facility_id" name="facility_id" x-model="facilityId"
                    @change="fetchAvailableTimes()"
                    class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                    <option value="">Select Facility</option>
                    @foreach($facilities as $facility)
                        <option value="{{ $facility->id }}">{{ $facility->facility_type }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Resident Type and Purpose Category --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <x-input-label for="resident_type" class="text-sm font-semibold text-slate-800">
                        Resident Type <span class="text-red-500">*</span>
                    </x-input-label>
                    <select id="resident_type" name="resident_type" x-model="residentType" @change="resetResidentFields()"
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                        <option value="">Select Resident Type</option>
                        <option value="resident">Resident</option>
                        <option value="non-resident">Non-Resident</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="purpose_category" class="text-sm font-semibold text-slate-800">
                        Purpose Category <span class="text-red-500">*</span>
                    </x-input-label>
                    <select id="purpose_category" name="purpose_category"
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                        <option value="">Select Purpose Category</option>
                        <option value="Sports">Sports</option>
                        <option value="Non-Sports">Non-Sports</option>
                    </select>
                </div>
            </div>

            {{-- Event Name --}}
            <div class="mb-6">
                <x-input-label for="event_name" class="text-sm font-semibold text-slate-800">
                    Event Name <span class="text-red-500">*</span>
                </x-input-label>
                <input type="text" id="event_name" name="event_name" placeholder="Enter Event Name"
                    class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
            </div>

            <!-- Name with search for Residents -->
            <div class="mb-6" x-show="residentType === 'resident'" x-cloak>
                <x-input-label for="searchInput" class="text-sm font-semibold text-slate-800">Name <span class="text-red-500">*</span></x-input-label>
                <div class="relative">
                    <input 
                        id="searchInput" 
                        type="text" 
                        x-model="searchQuery"
                        @input="if (!selectingResident) searchResidents()"
                        @keydown.escape="open = false"
                        class="w-full mt-1 text-sm text-slate-700 border border-gray-300 rounded-lg px-4 py-3"
                        placeholder="Search resident..." 
                        autocomplete="off" />
                    
                    <!-- Search Results Dropdown -->
                    <div x-show="open && suggestions.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-64 overflow-y-auto">
                        <template x-for="resident in suggestions" :key="resident.id">
                            <div @click="selectResident(resident)" class="px-4 py-2 cursor-pointer hover:bg-blue-100 border-b last:border-b-0">
                                <div class="font-semibold" x-text="resident.full_name"></div>
                                <div class="text-xs text-gray-500" x-text="resident.email"></div>
                            </div>
                        </template>
                    </div>
                </div>
                <!-- Hidden input for resident_id -->
                <input type="hidden" name="resident_id" x-bind:value="residentId" />
            </div>
                    
            <!-- Name field for Non-Residents -->
            <div class="mb-6" x-show="residentType === 'non-resident'" x-cloak>
                <x-input-label for="renter_name_non" class="text-sm font-semibold text-slate-800">Name <span class="text-red-500">*</span></x-input-label>
                <input 
                    id="renter_name_non" 
                    name="renter_name"
                    type="text" 
                    x-model="renterName"
                    class="w-full mt-1 text-sm text-slate-700 border border-gray-300 rounded-lg px-4 py-3"
                    placeholder="Enter name" 
                    autocomplete="off" />
            </div>

            {{-- Email and Contact No. in one row --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <x-input-label for="email" class="text-sm font-semibold text-slate-800">
                        Email Address <span class="text-red-500">*</span>
                    </x-input-label>
                    <input type="email" id="email" name="email" placeholder="Enter Email Address"
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                </div>
                <div>
                    <x-input-label for="contact_no" class="text-sm font-semibold text-slate-800">
                        Contact No. <span class="text-red-500">*</span>
                    </x-input-label>
                    <input type="text" id="contact_no" name="renter_contact" x-model="renterContact" placeholder="Enter Contact No."
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                </div>
            </div>

            {{-- Start Date and End Date --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <x-input-label for="start_date" class="text-sm font-semibold text-slate-800">
                        Start Date <span class="text-red-500">*</span>
                    </x-input-label>
                    <input type="date" id="start_date" name="start_date" x-model="startDate"
                        :min="getTodayDate()"
                        @change="fetchAvailableTimes()"
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                </div>
                <div>
                    <x-input-label for="end_date" class="text-sm font-semibold text-slate-800">
                        End Date <span class="text-red-500">*</span>
                    </x-input-label>
                    <input type="date" id="end_date" name="end_date" x-model="endDate"
                        :min="startDate || getTodayDate()"
                        @change="fetchAvailableTimes()"
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                </div>
            </div>

            {{-- Time Start and Time End --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <x-input-label for="time_start" class="text-sm font-semibold text-slate-800">
                        Time Start <span class="text-red-500">*</span>
                    </x-input-label>
                    <select id="time_start" name="time_start"
                        x-model="timeStart"
                        @change="onTimeStartChange()"
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                        <option value="">Select Start Hour</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="time_end" class="text-sm font-semibold text-slate-800">
                        Time End <span class="text-red-500">*</span>
                    </x-input-label>
                    <select id="time_end" name="time_end"
                        x-model="timeEnd"
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                        <option value="">Select End Hour</option>
                    </select>
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
                            <select :name="'equipment_type[' + index + ']'" x-model="item.type"
                                class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none">
                                <option value="">Select Equipment Type</option>
                                @foreach($equipments as $equipment)
                                    <option value="{{ $equipment->id }}">{{ $equipment->equipment_type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <input type="number" :name="'equipment_quantity[' + index + ']'" min="1" x-model="item.quantity" placeholder="Quantity"
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
            <div class="sticky bottom-0 bg-white pt-4 border-t border-gray-200 flex justify-end gap-3">
                <x-secondary-button type="button" @click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button class="ms-3" type="submit">
                    Confirm
                </x-primary-button>
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
            facilityId: '',
            timeStart: '',
            timeEnd: '',
            startDate: '',
            endDate: '',
            equipmentItems: [{ type: '', quantity: '' }],
            searchQuery: '',
            open: false,
            suggestions: [],
            selectingResident: false,
            searchTimeout: null,
            bookedHours: [],

            async fetchAvailableTimes() {
                this.timeStart = '';
                this.timeEnd = '';
                this.bookedHours = [];

                if (!this.facilityId || !this.startDate) {
                    this.rebuildTimeSelects();
                    return;
                }

                try {
                    const endDate = this.endDate || this.startDate;
                    const url = `/facility/available-times?facility_id=${this.facilityId}&start_date=${this.startDate}&end_date=${endDate}`;
                    const response = await fetch(url);
                    const data = await response.json();
                    
                    this.bookedHours = data.booked_hours || [];
                    this.rebuildTimeSelects();
                } catch (error) {
                    this.bookedHours = [];
                    this.rebuildTimeSelects();
                }
            },

            rebuildTimeSelects() {
                const startSelect = document.getElementById('time_start');
                startSelect.innerHTML = '<option value="">Select Start Hour</option>';

                for (let h = 7; h <= 21; h++) {
                    if (!this.bookedHours.includes(h)) {
                        const displayHour = h === 12 ? 12 : (h > 12 ? h - 12 : h);
                        const period = h >= 12 ? 'PM' : 'AM';
                        const value = String(h).padStart(2, '0') + ':00:00';
                        const label = `${String(displayHour).padStart(2, '0')}:00 ${period}`;
                        const option = document.createElement('option');
                        option.value = value;
                        option.textContent = label;
                        startSelect.appendChild(option);
                    }
                }
                this.rebuildEndSelect();
            },

            rebuildEndSelect() {
                const endSelect = document.getElementById('time_end');
                const startHour = this.timeStart ? Number(this.timeStart.substring(0, 2)) : 7;
                endSelect.innerHTML = '<option value="">Select End Hour</option>';

                for (let h = startHour + 1; h <= 22; h++) {
                    if (!this.bookedHours.includes(h)) {
                        const displayHour = h === 12 ? 12 : (h > 12 ? h - 12 : h);
                        const period = h >= 12 ? 'PM' : 'AM';
                        const value = String(h).padStart(2, '0') + ':00:00';
                        const label = `${String(displayHour).padStart(2, '0')}:00 ${period}`;
                        const option = document.createElement('option');
                        option.value = value;
                        option.textContent = label;
                        endSelect.appendChild(option);
                    }
                }
            },

            handleSubmit(event) {
                event.preventDefault();

                if (!this.residentType) {
                    alert('Please select a resident type');
                    return false;
                }

                if (this.residentType === 'resident' && !this.residentId) {
                    alert('Please select a resident from search results');
                    return false;
                }

                if (this.residentType === 'non-resident') {
                    if (!this.renterName || !this.renterName.trim()) {
                        alert('Please enter name for non-resident');
                        return false;
                    }
                    if (!this.renterContact || !this.renterContact.trim()) {
                        alert('Please enter contact for non-resident');
                        return false;
                    }
                }

                if (!this.facilityId) {
                    alert('Please select a facility');
                    return false;
                }

                const eventName = document.getElementById('event_name').value;
                if (!eventName || !eventName.trim()) {
                    alert('Please enter an event name');
                    return false;
                }

                const email = document.getElementById('email').value;
                if (!email || !email.trim()) {
                    alert('Please enter an email address');
                    return false;
                }

                const purposeCategory = document.getElementById('purpose_category').value;
                if (!purposeCategory) {
                    alert('Please select a purpose category');
                    return false;
                }

                if (!this.startDate) {
                    alert('Please select a start date');
                    return false;
                }
                if (!this.endDate) {
                    alert('Please select an end date');
                    return false;
                }

                if (!this.timeStart) {
                    alert('Please select a start time');
                    return false;
                }
                if (!this.timeEnd) {
                    alert('Please select an end time');
                    return false;
                }

                for (let i = 0; i < this.equipmentItems.length; i++) {
                    if ((this.equipmentItems[i].type || this.equipmentItems[i].quantity) && 
                        (!this.equipmentItems[i].type || !this.equipmentItems[i].quantity)) {
                        alert(`Equipment row ${i + 1}: Please select both type and quantity`);
                        return false;
                    }
                }

                const residentIdField = document.querySelector('input[name="resident_id"]');
                if (residentIdField) {
                    residentIdField.value = this.residentId || '';
                }

                // Submit via fetch
                const formData = new FormData(document.getElementById('reservationForm'));
                
                fetch('{{ route('facility.reservation.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close modal
                        this.$dispatch('close');
                        
                        // Dispatch message event to parent (facility.blade.php)
                        window.dispatchEvent(new CustomEvent('set-success-message', { 
                            detail: 'Reservation created successfully!' 
                        }));
                        
                        // Show success modal
                        setTimeout(() => {
                            window.dispatchEvent(new CustomEvent('open-modal', { 
                                detail: 'success-modal'
                            }));
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }, 300);
                    } else {
                        alert(data.message || 'Failed to create reservation');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
                
                return false;
            },

            onTimeStartChange() {
                if (!this.timeStart) {
                    this.timeEnd = '';
                    return;
                }
                this.rebuildEndSelect();
                
                let startHour = Number(this.timeStart.substring(0, 2));
                let endHour = startHour + 1;
                if (endHour > 22) endHour = 22;
                
                while (this.bookedHours.includes(endHour) && endHour <= 22) {
                    endHour++;
                }
                
                if (endHour <= 22) {
                    this.timeEnd = String(endHour).padStart(2, '0') + ':00:00';
                }
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
                this.equipmentItems.push({ type: '', quantity: '' });
            },

            removeEquipmentRow(index) {
                this.equipmentItems.splice(index, 1);
            },

            selectResident(resident) {
                this.selectingResident = true;
                this.searchQuery = resident.full_name;
                this.residentId = resident.id;
                this.residentContact = resident.contact_number || '';
                this.renterContact = resident.contact_number || '';
                document.getElementById('email').value = resident.email || '';
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
                        this.suggestions = [];
                        this.open = false;
                    }
                }, 300);
            },

            getTodayDate() {
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }
        }
    }
</script>