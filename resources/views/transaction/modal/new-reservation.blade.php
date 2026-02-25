@php
    $jsReservations = \App\Models\FacilityReservation::whereIn('status', ['For Approval', 'For Payment', 'Paid'])
        ->get()
        ->map(function($r) {
            return [
                'facility_id' => $r->facility_id,
                'start_date' => \Carbon\Carbon::parse($r->start_date)->format('Y-m-d'),
                'end_date' => \Carbon\Carbon::parse($r->end_date)->format('Y-m-d'),
                'time_start' => $r->getRawOriginal('time_start'),
                'time_end' => $r->getRawOriginal('time_end'),
                'status' => $r->status,
            ];
        });
@endphp

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
                <select id="facility_id" name="facility_id" required x-model="facilityId"
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
                    <select id="resident_type" name="resident_type" required x-model="residentType" @change="resetResidentFields()"
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
                    <select id="purpose_category" name="purpose_category" required
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
                <input type="text" id="event_name" name="event_name" required placeholder="Enter Event Name"
                    class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
            </div>

            <!-- Name with search for Residents -->
            <div class="mb-6" x-show="residentType === 'resident'" x-cloak>
                <x-input-label for="searchInput" class="text-sm font-semibold text-slate-800">Name <span class="text-red-500">*</span></x-input-label>
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
                    
            <!-- Name field for Non-Residents -->
            <div class="mb-6" x-show="residentType === 'non-resident'" x-cloak>
                <x-input-label for="renter_name_non" class="text-sm font-semibold text-slate-800">Name <span class="text-red-500">*</span></x-input-label>
                <x-text-input 
                    id="renter_name_non" 
                    name="renter_name"
                    type="text" 
                    x-model="renterName"
                    x-bind:required="residentType === 'non-resident'"
                    class="w-full mt-1 text-sm text-slate-700"
                    placeholder="Enter name" 
                    autocomplete="off" />
            </div>

            {{-- Email and Contact No. in one row --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <x-input-label for="email" class="text-sm font-semibold text-slate-800">
                        Email Address <span class="text-red-500">*</span>
                    </x-input-label>
                    <input type="email" id="email" name="email" required placeholder="Enter Email Address"
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                </div>
                <div>
                    <x-input-label for="contact_no" class="text-sm font-semibold text-slate-800">
                        Contact No. <span class="text-red-500">*</span>
                    </x-input-label>
                    <input type="text" id="contact_no" name="renter_contact" x-model="residentType === 'resident' ? residentContact : renterContact" x-bind:required="residentType === 'non-resident'" placeholder="Enter Contact No."
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2" :readonly="residentType === 'resident'">
                </div>
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
                    <select id="time_start" name="time_start" required
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
                    <select id="time_end" name="time_end" required
                        class="w-full px-4 py-3 text-sm text-slate-500 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none mt-2">
                        <option value="">Select End Hour</option>
                        @for($hour = 7; $hour <= 22; $hour++)
                            @php
                                $displayHour = $hour > 12 ? $hour - 12 : ($hour == 0 ? 12 : $hour);
                                $period = $hour >= 12 ? 'PM' : 'AM';
                                $timeValue = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00:00';
                            @endphp
                            <option 
                                value="{{ $timeValue }}"
                                :disabled="getUnavailableEndTimes().includes('{{ $timeValue }}')"
                                :class="getUnavailableEndTimes().includes('{{ $timeValue }}') ? 'text-red-400' : ''"
                            >
                                {{ str_pad($displayHour, 2, '0', STR_PAD_LEFT) }}:00 {{ $period }}
                            </option>
                        @endfor
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
            <div class="sticky bottom-0 bg-white pt-4 border-t border-gray-200 flex justify-end gap-3">
                <x-secondary-button @click="$dispatch('close')">Cancel</x-secondary-button>
                
                <x-primary-button class="ms-3" type="submit">
                    Confirm
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

<script>
    function buildTimeStartOptions() {
        const select = document.getElementById('time_start');
        const facilityId = document.getElementById('facility_id')?.value;
        const startDate = document.getElementById('start_date')?.value;
        const endDate = document.getElementById('end_date')?.value || startDate;
        
        if (!facilityId || !startDate) {
            select.innerHTML = '<option value="">Select Start Hour</option>';
            return;
        }
        
        // Get booked times
        const unavailable = (window.existingReservations || [])
            .filter(r => {
                return r.facility_id == facilityId &&
                    ['For Approval', 'For Payment', 'Paid'].includes(r.status) &&
                    startDate <= r.end_date && endDate >= r.start_date;
            })
            .flatMap(r => {
                let rStartHour = parseInt(r.time_start.split(':')[0]);
                let rEndHour = parseInt(r.time_end.split(':')[0]);
                let times = [];
                for (let h = rStartHour; h < rEndHour; h++) {
                    times.push(String(h).padStart(2, '0') + ':00:00');
                }
                return times;
            });
        
        // Keep the default option
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Select Start Hour';
        select.innerHTML = '';
        select.appendChild(defaultOption);
        
        for (let hour = 6; hour <= 21; hour++) {
            const timeValue = String(hour).padStart(2, '0') + ':00:00';
            
            // Skip if this time is booked
            if (unavailable.includes(timeValue)) {
                continue;
            }
            
            const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
            const period = hour >= 12 ? 'PM' : 'AM';
            
            const option = document.createElement('option');
            option.value = timeValue;
            option.textContent = String(displayHour).padStart(2, '0') + ':00 ' + period;
            select.appendChild(option);
        }
    }

    function buildTimeEndOptions() {
        const select = document.getElementById('time_end');
        const timeStart = document.getElementById('time_start')?.value;
        
        if (!timeStart) {
            select.innerHTML = '<option value="">Select End Hour</option>';
            return;
        }
        
        const startHour = parseInt(timeStart.split(':')[0]);
        const facilityId = document.getElementById('facility_id')?.value;
        const startDate = document.getElementById('start_date')?.value;
        const endDate = document.getElementById('end_date')?.value || startDate;
        
        // Get booked times
        const unavailable = (window.existingReservations || [])
            .filter(r => {
                return r.facility_id == facilityId &&
                    ['For Approval', 'For Payment', 'Paid'].includes(r.status) &&
                    startDate <= r.end_date && endDate >= r.start_date;
            })
            .flatMap(r => {
                let rStartHour = parseInt(r.time_start.split(':')[0]);
                let rEndHour = parseInt(r.time_end.split(':')[0]);
                let times = [];
                for (let h = rStartHour; h < rEndHour; h++) {
                    times.push(String(h).padStart(2, '0') + ':00:00');
                }
                return times;
            });
        
        // Keep the default option
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Select End Hour';
        select.innerHTML = '';
        select.appendChild(defaultOption);
        
        for (let hour = 7; hour <= 22; hour++) {
            const timeValue = String(hour).padStart(2, '0') + ':00:00';
            
            // Skip if this hour is <= start hour OR booked
            if (hour <= startHour || unavailable.includes(timeValue)) {
                continue;
            }
            
            const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
            const period = hour >= 12 ? 'PM' : 'AM';
            
            const option = document.createElement('option');
            option.value = timeValue;
            option.textContent = String(displayHour).padStart(2, '0') + ':00 ' + period;
            select.appendChild(option);
        }
    }
    
    // Rebuild when facility/date changes
    document.getElementById('facility_id')?.addEventListener('change', () => {
        buildTimeStartOptions();
        buildTimeEndOptions();
    });
    document.getElementById('start_date')?.addEventListener('change', () => {
        buildTimeStartOptions();
        buildTimeEndOptions();
    });
    document.getElementById('end_date')?.addEventListener('change', () => {
        buildTimeStartOptions();
        buildTimeEndOptions();
    });
    
    // Rebuild when start time changes
    document.getElementById('time_start')?.addEventListener('change', buildTimeEndOptions);
    
    // Initial build
    buildTimeStartOptions();
</script>

<script>
    function reservationForm() {
        return {
            residentType: '',
            residentId: '',
            residentContact: '',
            renterName: '',
            renterContact: '',
            timeStart: '',
            timeEnd: '',
            startDate: '',
            endDate: '',
            facilityId: '',
            equipmentItems: [{}],
            searchQuery: '',
            open: false,
            suggestions: [],
            selectingResident: false,
            searchTimeout: null,
            errorMessage: '',

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

                const facilityId = document.getElementById('facility_id').value;
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                const timeStart = document.getElementById('time_start').value;
                const timeEnd = document.getElementById('time_end').value;

                const hasConflict = (window.existingReservations || [])
                    .some(r =>
                        r.facility_id == facilityId &&
                        ['For Approval', 'For Payment', 'Paid'].includes(r.status) &&
                        startDate <= r.end_date && 
                        endDate >= r.start_date &&
                        timeStart < r.time_end &&
                        timeEnd > r.time_start
                    );

                if (hasConflict) {
                    event.preventDefault();
                    this.errorMessage = 'This time slot is already booked for the selected facility and date.';
                    this.$dispatch('open-modal', 'reservation-error');
                    return false;
                }
                // === END CONFLICT CHECK ===

                // If no conflict, submit the form
                document.getElementById('reservationForm').submit();
                return false;
            },

            onTimeStartChange() {
                if (!this.timeStart) {
                    this.timeEnd = '';
                    return;
                }

                // Parse start hour and add 1 hour
                let startHour = parseInt(this.timeStart.split(':')[0]);
                let endHour = startHour + 1;

                // Format end hour
                if (endHour > 22) {
                    endHour = 22;
                }

                this.timeEnd = String(endHour).padStart(2, '0') + ':00:00';
                
                // Update the actual select element
                document.getElementById('time_end').value = this.timeEnd;
            },

            formatTime(timeString) {
                if (!timeString) return '';
                const [hour] = timeString.split(':');
                const hourNum = parseInt(hour);
                const displayHour = hourNum > 12 ? hourNum - 12 : (hourNum === 0 ? 12 : hourNum);
                const period = hourNum >= 12 ? 'PM' : 'AM';
                return `${String(displayHour).padStart(2, '0')}:00 ${period}`;
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

            getUnavailableTimes() {
                const facilityId = document.getElementById('facility_id')?.value;
                const startDate = document.getElementById('start_date')?.value;
                const endDate = document.getElementById('end_date')?.value || startDate;

                if (!facilityId || !startDate) return [];

                let unavailable = [];

                (window.existingReservations || [])
                    .filter(r =>
                        r.facility_id == facilityId &&
                        ['For Approval', 'For Payment', 'Paid'].includes(r.status) &&
                        startDate <= r.end_date && endDate >= r.start_date
                    )
                    .forEach(r => {
                        let startHour = parseInt(r.time_start.split(':')[0]);
                        let endHour = parseInt(r.time_end.split(':')[0]);

                        for (let h = startHour; h < endHour; h++) {
                            let timeValue = String(h).padStart(2, '0') + ':00:00';
                            if (!unavailable.includes(timeValue)) {
                                unavailable.push(timeValue);
                            }
                        }
                    });

                return unavailable;
            },

            getUnavailableEndTimes() {
                const facilityId = document.getElementById('facility_id')?.value;
                const startDate = document.getElementById('start_date')?.value;
                const endDate = document.getElementById('end_date')?.value || startDate;

                if (!facilityId || !startDate || !this.timeStart) return [];

                let unavailable = [];
                let selectedStartHour = parseInt(this.timeStart.split(':')[0]);

                // === DISABLE ALL HOURS <= SELECTED START HOUR ===
                for (let h = 6; h <= selectedStartHour; h++) {
                    unavailable.push(String(h).padStart(2, '0') + ':00:00');
                }
                // === END DISABLE ===

                // Find the earliest booked time after selected start time
                let earliestConflict = 23;

                (window.existingReservations || [])
                    .filter(r =>
                        r.facility_id == facilityId &&
                        ['For Approval', 'For Payment', 'Paid'].includes(r.status) &&
                        startDate <= r.end_date && endDate >= r.start_date
                    )
                    .forEach(r => {
                        let rStartHour = parseInt(r.time_start.split(':')[0]);
                        if (rStartHour > selectedStartHour && rStartHour < earliestConflict) {
                            earliestConflict = rStartHour;
                        }
                    });

                // Disable all hours after the earliest conflict
                for (let h = earliestConflict + 1; h <= 22; h++) {
                    let timeValue = String(h).padStart(2, '0') + ':00:00';
                    if (!unavailable.includes(timeValue)) {
                        unavailable.push(timeValue);
                    }
                }

                return unavailable;
            },
            
            selectResident(resident) {
                this.selectingResident = true;
                this.searchQuery = resident.full_name;
                this.residentId = resident.id;
                this.residentContact = resident.contact_number || '';
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
                        console.error('Error:', error);
                        this.suggestions = [];
                        this.open = false;
                    }
                }, 300);
            }
        }
    }

    window.reservations = @json($jsReservations);
    window.existingReservations = @json($jsReservations);
</script>