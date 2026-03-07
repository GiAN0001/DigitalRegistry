<x-app-layout>
    <div x-data="{ successMessage: '' }"></div>
    <div class="sub-content">
        

        {{-- Analytics Widgets for Equipment Types --}}
        <div class="max-w-full">
            <div class="dashboard-grid">
                @forelse($equipments as $equipment)
                    <div class="col-span-1 md:col-span-1 lg:col-span-3">
                        @php
                            $iconMap = [
                                'Plastic Chair' => 'armchair',
                                'Folding Table' => 'rectangle-horizontal',
                                'Tent (10x10)' => 'tent-tree',
                            ];
                            $iconName = $iconMap[$equipment->equipment_type] ?? 'package';
                        @endphp
                        <x-analytics-widget 
                            :title="$equipment->equipment_type" 
                            :value="$equipment->total_quantity" 
                            :icon-name="$iconName"
                            bg-color="bg-blue-500"
                        />
                    </div>
                @empty
                    <div class="col-span-1">
                        <p class="text-gray-500">No equipment available</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <div x-data="{ activeTab: localStorage.getItem('activeFacilityTab') || 'reservation-calendar' }">
            <!-- Search Bar: Only show in Reservation History tab -->
            <div class="flex flex-wrap items-center gap-2 mt-4 mb-4"
                x-show="activeTab === 'reservation-history'">
                <form method="GET" action="{{ route('transaction.facility') }}" class="w-full md:flex-1">
                    <input type="hidden" name="tab" value="reservation-history">
                    <x-search-bar name="search" placeholder="Search by Name or Reservation ID" class="w-full md:flex-1" :value="request('search')" />
                </form>
            </div>

            <!-- Tabs -->
            <div class="flex mt-4 mb-6 border-b-2 border-blue-600">
                <button class="facility-tab-button text-blue-600 font-semibold text-sm p-3 border-b-2 border-blue-600"
                        data-tab="reservation-calendar"
                        @click="activeTab = 'reservation-calendar'; localStorage.setItem('activeFacilityTab', 'reservation-calendar')">
                    Reservation Calendar
                </button>
                <button class="facility-tab-button text-slate-600 font-semibold text-sm p-3"
                        data-tab="reservation-history"
                        @click="activeTab = 'reservation-history'; localStorage.setItem('activeFacilityTab', 'reservation-history')">
                    Reservation History
                </button>
            </div>
        </div>

        <!-- Tab Content: Reservation Calendar -->
        <div id="reservation-calendar-content" class="facility-tab-content">
            {{-- Calendar Section --}}
            <div class="flex gap-6 items-start" x-data="{ 
                selectedDate: '{{ \Carbon\Carbon::now()->format('Y-m-d') }}', 
                selectedDateFormatted: '{{ \Carbon\Carbon::now()->format('F d') }}',
                calendarHeight: 0,
                updateCalendarHeight() {
                    this.$nextTick(() => {
                        const calendarEl = this.$refs.calendarContainer;
                        if (calendarEl) {
                            this.calendarHeight = calendarEl.offsetHeight;
                        }
                    });
                }
            }" x-init="$nextTick(() => updateCalendarHeight())" @resize.window="updateCalendarHeight()" @calendar-rendered.window="updateCalendarHeight()">
                {{-- Main Calendar --}}
                <div class="flex-1 bg-white rounded-lg shadow-sm p-6" x-ref="calendarContainer">
                    <div x-data="calendar()" x-init="init(); $nextTick(() => { $dispatch('calendar-rendered'); })">
                        {{-- Calendar Header --}}
                        <div class="flex items-center justify-between mb-6">
                            {{-- Left: Current Date Display --}}
                            <div class="flex items-center gap-4">
                                <div class="bg-blue-100 border border-blue-700 rounded-xl px-4 py-2 text-center min-w-[80px]">
                                    <div class="text-base font-semibold text-blue-700 uppercase border-b border-blue-700 pb-1 mb-1" x-text="currentMonth.substring(0, 3)"></div>
                                    <div class="text-xl font-bold text-blue-700" x-text="currentDay"></div>
                                </div>
                                <h2 class="text-2xl font-bold text-blue-600" x-text="currentMonth + ' ' + currentYear"></h2>
                            </div>

                            {{-- Right: Navigation and Actions --}}
                            <div class="flex items-center gap-4 relative">
                                {{-- Month Navigation --}}
                                <div class="flex items-center gap-2 border border-gray-300 rounded-lg px-2 py-1">
                                    <button @click="previousMonth()" class="p-2 hover:bg-gray-100 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </button>
                                    <button @click="showPicker = !showPicker" class="font-medium text-center min-w-24 hover:bg-gray-100 rounded px-2 py-1" x-text="currentMonth"></button>
                                    <button @click="nextMonth()" class="p-2 hover:bg-gray-100 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                </div>
                                    
                                {{-- Month/Year Picker Dropdown --}}
                                <div x-show="showPicker" @click.outside="showPicker = false" class="absolute top-full left-0 mt-2 bg-white border border-gray-300 rounded-lg shadow-lg p-4 z-50 w-64">
                                    {{-- Year Selector --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Year</label>
                                        <div class="flex items-center justify-between gap-2 mb-3">
                                            <button @click="yearStart -= 10" class="px-2 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">−</button>
                                            <span class="text-sm font-semibold" x-text="yearStart + ' - ' + (yearStart + 9)"></span>
                                            <button @click="yearStart += 10" class="px-2 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">+</button>
                                        </div>
                                        <div class="grid grid-cols-4 gap-2">
                                            <template x-for="i in 10" :key="i">
                                                <button 
                                                    @click="currentDate.setFullYear(yearStart + i - 1); updateMonth(); generateCalendar()"
                                                    :class="(yearStart + i - 1) === currentYear ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300'"
                                                    class="py-2 rounded font-semibold text-sm transition"
                                                    x-text="yearStart + i - 1"
                                                ></button>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    {{-- Month Selector --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Month</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <template x-for="(month, index) in ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']" :key="month">
                                                <button 
                                                    @click="currentDate.setMonth(index); updateMonth(); generateCalendar(); showPicker = false"
                                                    :class="index === currentDate.getMonth() ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300'"
                                                    class="py-2 rounded font-semibold text-sm transition"
                                                    x-text="month"
                                                ></button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <button 
                                    x-data
                                    x-on:click.prevent="$dispatch('open-modal', 'new-reservation')"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition"
                                >
                                    Add New Reservation
                                </button>
                                <button 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition"
                                    x-data
                                    @click="$dispatch('open-modal', 'add-equipment')"
                                >
                                    Add New Equipment
                                </button>
                            </div>
                        </div>

                        {{-- Calendar Grid --}}
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            {{-- Day Headers --}}
                            <div class="grid grid-cols-7 bg-blue-100">
                                <template x-for="(day, index) in dayNames" :key="day">
                                    <div 
                                        class="p-3 text-center border border-blue-500 font-bold text-blue-700 text-sm"
                                        :class="{
                                            'rounded-tl-lg': index === 0,
                                            'rounded-tr-lg': index === 6
                                        }"
                                        x-text="day"
                                    ></div>
                                </template>
                            </div>
                            
                            {{-- Calendar Days --}}
                            <div class="grid grid-cols-7">
                                <template x-for="(day, index) in calendarDays" :key="index">
                                    <div 
                                        @click="selectedDate = day.fullDate; selectedDateFormatted = formatDate(day.fullDate)"
                                        class="min-h-[160px] p-2 border border-gray-200 transition cursor-pointer flex flex-col"
                                        :class="{
                                            'bg-gray-50': !day.isCurrentMonth,
                                            'bg-white hover:bg-gray-100': day.isCurrentMonth && !day.isToday,
                                            'bg-blue-50': day.isToday,
                                            'rounded-bl-lg': index === calendarDays.length - 7,
                                            'rounded-br-lg': index === calendarDays.length - 1
                                        }"
                                    >
                                        <div class="flex justify-between items-start mb-2">
                                            <span 
                                                class="text-sm font-semibold"
                                                :class="{
                                                    'text-slate-400': !day.isCurrentMonth,
                                                    'text-gray-900': day.isCurrentMonth && !day.isToday,
                                                    'bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs': day.isToday
                                                }"
                                                x-text="day.date"
                                            ></span>
                                        </div>
                                        
                                        {{-- Event Cards --}}
                                        <div class="space-y-1 flex-1 overflow-hidden flex flex-col">
                                            <template x-for="event in day.events" :key="event.id">
                                                <div 
                                                    class="text-xs px-2 py-1 rounded cursor-pointer hover:opacity-80 transition-opacity truncate"
                                                    :class="getStatusColor(event.status)"
                                                >
                                                    <div class="font-semibold truncate" x-text="event.title"></div>
                                                    <div class="text-[10px] opacity-80" x-text="event.time"></div>
                                                </div>
                                            </template>
                                            <template x-if="day.moreCount > 0">
                                                <div class="text-xs text-gray-500 pl-2 font-medium" x-text="'+' + day.moreCount + ' More'"></div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Selected Date Events Sidebar --}}
                <div class="w-80 bg-white rounded-lg shadow-sm p-6 flex flex-col" 
                     :style="calendarHeight > 0 ? { 'height': calendarHeight + 'px' } : {}">
                    <div class="bg-blue-100 rounded-lg p-4 text-center mb-4 flex-shrink-0">
                        <h3 class="text-lg font-bold text-blue-700" x-text="selectedDateFormatted + ' Events'"></h3>
                    </div>
                    
                    @php
                        $eventDates = $reservations->pluck('start_date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))->unique()->values()->toArray();
                    @endphp
                    
                    <div class="overflow-y-auto space-y-3 flex-1 min-h-0">
                    {{-- Events for selected date --}}
                    @foreach($reservations as $reservation)
                        @php
                            $rStart = \Carbon\Carbon::parse($reservation->start_date)->format('Y-m-d');
                            $rEnd   = \Carbon\Carbon::parse($reservation->end_date)->format('Y-m-d');
                        @endphp
                        <div x-show="selectedDate >= '{{ $rStart }}' && selectedDate <= '{{ $rEnd }}'">
                            <x-event-card :event="$reservation" />
                        </div>
                    @endforeach

                    {{-- No Events Message --}}
                    <div 
                        x-show="
                            @if($reservations->isEmpty())
                                true
                            @else
                                {{ $reservations->map(fn($r) => 
                                    "(selectedDate < '" . \Carbon\Carbon::parse($r->start_date)->format('Y-m-d') . "' || selectedDate > '" . \Carbon\Carbon::parse($r->end_date)->format('Y-m-d') . "')"
                                )->implode(' && ') }}
                            @endif
                        "
                        class="text-center text-gray-500 py-4"
                    >
                        No Event
                    </div>
                </div>
                </div>
            </div>

            {{-- Equipment Reservations Table --}}
            <div class="bg-white p-6 shadow rounded-lg mt-6 scroll-mt-32" id="equipment-reservations">
                <div class="mb-4">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Equipment Reservations</h2>
                </div>

                {{-- Tabs --}}
                <div class="flex border-b-2 mb-6 overflow-x-auto">
                    <button type="button" onclick="loadEquipmentTab('all')"
                        class="p-3 font-medium text-sm transition whitespace-nowrap equipment-tab-btn {{ $tab === 'all' ? 'border-b-2 border-slate-600 text-slate-600' : 'text-gray-500 hover:text-slate-600' }}" data-tab="all">
                        All
                    </button>
                    <button type="button" onclick="loadEquipmentTab('For Approval')"
                        class="p-3 font-medium text-sm transition whitespace-nowrap equipment-tab-btn {{ $tab === 'For Approval' ? 'border-b-2 border-purple-600 text-purple-600' : 'text-gray-500 hover:text-purple-600' }}" data-tab="For Approval">
                        For Approval
                    </button>
                    <button type="button" onclick="loadEquipmentTab('For Delivery')"
                        class="p-3 font-medium text-sm transition whitespace-nowrap equipment-tab-btn {{ $tab === 'For Delivery' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-blue-600' }}" data-tab="For Delivery">
                        For Delivery
                    </button>
                    <button type="button" onclick="loadEquipmentTab('Delivered')"
                        class="p-3 font-medium text-sm transition whitespace-nowrap equipment-tab-btn {{ $tab === 'Delivered' ? 'border-b-2 border-orange-600 text-orange-600' : 'text-gray-500 hover:text-orange-600' }}" data-tab="Delivered">
                        Delivered
                    </button>
                    <button type="button" onclick="loadEquipmentTab('Returned')"
                        class="p-3 font-medium text-sm transition whitespace-nowrap equipment-tab-btn {{ $tab === 'Returned' ? 'border-b-2 border-green-600 text-green-600' : 'text-gray-500 hover:text-green-600' }}" data-tab="Returned">
                        Returned
                    </button>
                    <button type="button" onclick="loadEquipmentTab('Rejected')"
                        class="p-3 font-medium text-sm transition whitespace-nowrap equipment-tab-btn {{ $tab === 'Rejected' ? 'border-b-2 border-gray-600 text-gray-600' : 'text-gray-500 hover:text-gray-600' }}" data-tab="Rejected">
                        Rejected
                    </button>
                    <button type="button" onclick="loadEquipmentTab('Cancelled')"
                        class="p-3 font-medium text-sm transition whitespace-nowrap equipment-tab-btn {{ $tab === 'Cancelled' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-500 hover:text-red-600' }}" data-tab="Cancelled">
                        Cancelled
                    </button>
                </div>

                <div class="overflow-x-auto" x-data>
                    <table class="min-w-full divide-y">
                        <thead class="{{ match($tab) {
                            'For Approval' => 'bg-purple-100',
                            'For Delivery' => 'bg-blue-100',
                            'Delivered' => 'bg-orange-100',
                            'Returned' => 'bg-green-100',
                            'Rejected' => 'bg-gray-200',
                            'Cancelled' => 'bg-red-100',
                            default => 'bg-gray-100'
                        } }}">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase rounded-l-lg">Reservation ID</th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase">Name</th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase">Event Name</th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase">Date</th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-slate-700 uppercase">Status</th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-slate-700 uppercase rounded-r-lg">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($equipmentReservationsPaginated as $row)
                                <tr class="border-t hover:bg-gray-50 transition">
                                    <td class="px-3 py-3 text-sm font-bold text-slate-700">#{{ $row->reservation_id }}</td>
                                    <td class="px-3 py-3 text-sm text-slate-700">{{ $row->resident_name ?? 'Non-resident' }}</td>
                                    <td class="px-3 py-3 text-sm text-slate-700">{{ $row->event_name }}</td>
                                    <td class="px-3 py-3 text-sm text-slate-700">{{ \Carbon\Carbon::parse($row->start_date)->format('M d, Y') }}</td>
                                    <td class="px-3 py-3 text-sm font-semibold text-center">
                                        <span
                                            @php
                                                $color = match($row->equipment_status) {
                                                    'For Approval' => 'bg-purple-200 text-purple-900',
                                                    'For Delivery' => 'bg-blue-200 text-blue-900',
                                                    'Delivered'    => 'bg-orange-200 text-orange-900',
                                                    'Returned'     => 'bg-green-200 text-green-900',
                                                    'Rejected'     => 'bg-gray-200 text-gray-900',
                                                    'Cancelled'    => 'bg-red-200 text-red-900',
                                                    default        => 'bg-blue-200 text-blue-900'
                                                };
                                            @endphp
                                            class="px-3 py-1 rounded-full text-xs font-semibold {{ $color }}"
                                        >
                                            {{ $row->equipment_status }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <button 
                                            class="text-blue-500 font-medium text-xs hover:text-blue-700"
                                            @click="
                                                window.dispatchEvent(new CustomEvent('show-equipment', { detail: { id: {{ $row->reservation_id }} } }));
                                                $dispatch('open-modal', 'view-equipment');
                                            "
                                            type="button"
                                        >
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-gray-500">No equipment reservations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($equipmentReservationsPaginated->lastPage() > 1)
                    <div class="mt-6">
                        {{ $equipmentReservationsPaginated->appends(['tab' => $tab])->links() }}
                    </div>
                @endif
            </div>
        </div>

            <!-- Tab Content: Reservation History -->
            <div id="reservation-history-content" class="facility-tab-content hidden">
                <div class="p-6 bg-white shadow-md rounded-lg">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Reservation History</h2>

                    <!-- Filter Buttons -->
                    <div class="flex flex-wrap gap-3 mb-6">
                        <button class="facility-history-filter-btn px-6 py-2 bg-blue-600 text-white rounded-lg font-medium text-sm" data-filter="all">All</button>
                        <button class="facility-history-filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="Completed">Completed</button>
                        <button class="facility-history-filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="Rejected">Rejected</button>
                        <button class="facility-history-filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="Cancelled">Cancelled</button>
                    </div>

                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Reservation ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Event Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" x-data>
                                @forelse ($historyReservations as $reservation)
                                    <tr class="hover:bg-gray-50 facility-history-row" data-status="{{ $reservation->status }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                            #{{ $reservation->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $reservation->renter_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $reservation->event_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($reservation->start_date)->format('F d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'Completed' => 'bg-green-200 text-green-800',
                                                    'Cancelled' => 'bg-red-200 text-red-700',
                                                    'Rejected' => 'bg-gray-200 text-gray-700',
                                                ];
                                                $statusClass = $statusColors[$reservation->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ $reservation->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button
                                                class="text-blue-500 hover:text-blue-700 font-medium"
                                                @click="
                                                    $dispatch('open-modal', 'view-reservation');
                                                    $dispatch('show-reservation', {
                                                        id: {{ $reservation->id }},
                                                        event_name: '{{ addslashes($reservation->event_name) }}',
                                                        facility_name: '{{ addslashes($reservation->facility->facility_type ?? 'N/A') }}',
                                                        purpose_category: '{{ $reservation->purpose_category }}',
                                                        resident_type: '{{ ucfirst($reservation->resident_type) }}',
                                                        renter_name: '{{ addslashes($reservation->renter_name) }}',
                                                        renter_contact: '{{ $reservation->renter_contact }}',
                                                        email: '{{ $reservation->email }}',
                                                        start_date: '{{ \Carbon\Carbon::parse($reservation->start_date)->format('F d, Y') }}',
                                                        end_date: '{{ \Carbon\Carbon::parse($reservation->end_date)->format('F d, Y') }}',
                                                        time_start: '{{ \Carbon\Carbon::parse($reservation->time_start)->format('g:i A') }}',
                                                        time_end: '{{ \Carbon\Carbon::parse($reservation->time_end)->format('g:i A') }}',
                                                        status: '{{ $reservation->status }}',
                                                        created_at: '{{ $reservation->created_at->format('F d, Y h:i A') }}'
                                                    })
                                                "
                                            >
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="facility-history-empty-row">
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No reservation history found.
                                        </td>
                                    </tr>
                                @endforelse
                                <tr class="facility-history-no-results hidden">
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No results found for this filter.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($historyReservations->lastPage() > 1)
                        <div class="mt-6">
                            {{ $historyReservations->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>

                <!-- Equipment Borrowed History -->
                <div class="p-6 bg-white shadow-md rounded-lg mt-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Equipment Borrowed History</h2>

                    <!-- Filter Buttons -->
                    <div class="flex flex-wrap gap-3 mb-6">
                        <button class="equipment-history-filter-btn px-6 py-2 bg-blue-600 text-white rounded-lg font-medium text-sm" data-filter="all">All</button>
                        <button class="equipment-history-filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="Returned">Returned</button>
                        <button class="equipment-history-filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="Rejected">Rejected</button>
                        <button class="equipment-history-filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="Cancelled">Cancelled</button>
                    </div>

                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Reservation ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Equipment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Equipment Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($equipmentBorrowedHistory as $equipment)
                                    <tr class="hover:bg-gray-50 equipment-history-row" data-status="{{ $equipment->equipment_status }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                            #{{ $equipment->reservation_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $equipment->resident_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $equipment->equipment_type }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $equipment->quantity_borrowed }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'Returned' => 'bg-green-200 text-green-800',
                                                    'Cancelled' => 'bg-red-200 text-red-700',
                                                    'Rejected' => 'bg-gray-200 text-gray-700',
                                                ];
                                                $statusClass = $statusColors[$equipment->equipment_status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ $equipment->equipment_status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button
                                                class="text-blue-500 hover:text-blue-700 font-medium"
                                                @click="
                                                    window.dispatchEvent(new CustomEvent('show-equipment', { detail: { id: {{ $equipment->reservation_id }} } }));
                                                    $dispatch('open-modal', 'view-equipment');
                                                "
                                            >
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="equipment-history-empty-row">
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No equipment history found.
                                        </td>
                                    </tr>
                                @endforelse
                                <tr class="equipment-history-no-results hidden">
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No results found for this filter.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($equipmentBorrowedHistory->lastPage() > 1)
                        <div class="mt-6">
                            {{ $equipmentBorrowedHistory->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
    // Facility Tab Switching
    document.querySelectorAll('.facility-tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            localStorage.setItem('activeFacilityTab', tabName);

            document.querySelectorAll('.facility-tab-button').forEach(btn => {
                btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                btn.classList.add('text-slate-600');
            });
            this.classList.remove('text-slate-600');
            this.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

            document.querySelectorAll('.facility-tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(tabName + '-content').classList.remove('hidden');
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const activeTab = localStorage.getItem('activeFacilityTab') || 'reservation-calendar';
        document.querySelectorAll('.facility-tab-button').forEach(btn => {
            btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            btn.classList.add('text-slate-600');
            if (btn.dataset.tab === activeTab) {
                btn.classList.remove('text-slate-600');
                btn.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
            }
        });
        document.querySelectorAll('.facility-tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById(activeTab + '-content').classList.remove('hidden');
    });

    function calendar() {
        return {
            currentDate: new Date(),
            dayNames: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            calendarDays: [],
            events: @json($events),
            showPicker: false,
            yearStart: Math.floor(new Date().getFullYear() / 10) * 10,
            currentMonth: '',
            currentYear: 0,
            currentDay: 0,
            
            init() {
                this.updateMonth();
                this.generateCalendar();
            },
            
            updateMonth() {
                this.currentMonth = this.currentDate.toLocaleDateString('en-US', { month: 'long' });
                this.currentYear = this.currentDate.getFullYear();
                this.currentDay = new Date().getDate();
            },
            
            formatDate(dateStr) {
                const date = new Date(dateStr + 'T00:00:00');
                return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric' });
            },
            
            getStatusColor(status) {
                const colors = {
                    'For Approval': 'bg-blue-200 text-blue-900',
                    'For Payment': 'bg-orange-200 text-orange-900',
                    'Paid': 'bg-yellow-200 text-yellow-900',
                    'Completed': 'bg-green-200 text-green-900',
                    'Cancelled': 'bg-red-200 text-red-900',
                    'Rejected': 'bg-gray-200 text-gray-900'
                };
                return colors[status] || 'bg-blue-200 text-blue-900';
            },
            
            generateCalendar() {
                const year = this.currentDate.getFullYear();
                const month = this.currentDate.getMonth();
                
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                
                const startDate = new Date(firstDay);
                startDate.setDate(startDate.getDate() - firstDay.getDay());
                
                const endDate = new Date(lastDay);
                endDate.setDate(endDate.getDate() + (6 - lastDay.getDay()));
                
                this.calendarDays = [];
                let current = new Date(startDate);
                
                while (current <= endDate) {
                    const year = current.getFullYear();
                    const month = String(current.getMonth() + 1).padStart(2, '0');
                    const day = String(current.getDate()).padStart(2, '0');
                    const dateStr = `${year}-${month}-${day}`;
                    
                    // Show events that include this date (between start and end date)
                    const dayEvents = this.events
                        .filter(e => {
                            const eventStart = new Date(e.start);
                            const eventEnd = new Date(e.end);
                            const currentDay = new Date(dateStr);
                            
                            // Check if current day is within event range
                            return currentDay >= eventStart && currentDay <= eventEnd;
                        })
                        .sort((a, b) => {
                            if (a.time_sort && b.time_sort) {
                                return a.time_sort.localeCompare(b.time_sort);
                            }
                            return 0;
                        });
                    
                    this.calendarDays.push({
                        date: current.getDate(),
                        fullDate: dateStr,
                        isCurrentMonth: current.getMonth() === this.currentDate.getMonth(),
                        isToday: this.isToday(current),
                        events: dayEvents.slice(0, 2),
                        moreCount: Math.max(0, dayEvents.length - 2)
                    });
                    
                    current.setDate(current.getDate() + 1);
                }
                
                this.$nextTick(() => {
                    this.$dispatch('calendar-rendered');
                });
            },

            isToday(date) {
                const today = new Date();
                return date.getDate() === today.getDate() &&
                       date.getMonth() === today.getMonth() &&
                       date.getFullYear() === today.getFullYear();
            },
            
            previousMonth() {
                this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                this.updateMonth();
                this.generateCalendar();
            },
            
            nextMonth() {
                this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                this.updateMonth();
                this.generateCalendar();
            }
        }
    }

    function updateEquipmentStatus(reId, status) {
        if (!confirm('Are you sure you want to update the status to "' + status + '"?')) return;

        fetch(`/equipment-reservation/${reId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: status })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to update status.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred.');
        });
    }

    function loadEquipmentTab(tab) {
        fetch(`{{ route('transaction.facility') }}?tab=${tab}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newTable = doc.querySelector('[id="equipment-reservations"]');
            const currentTable = document.querySelector('[id="equipment-reservations"]');
            
            if (newTable && currentTable) {
                currentTable.replaceWith(newTable);
            }
        });
    }

    // Facility History filter
    document.querySelectorAll('.facility-history-filter-btn').forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;

            // Reset all buttons to blue border outline
            document.querySelectorAll('.facility-history-filter-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('text-blue-600', 'border', 'border-blue-600');
            });

            // Activate clicked button - fill blue
            this.classList.remove('text-blue-600', 'border', 'border-blue-600');
            this.classList.add('bg-blue-600', 'text-white');

            // Filter rows
            const rows = document.querySelectorAll('.facility-history-row');
            const emptyRow = document.querySelector('.facility-history-empty-row');
            const noResults = document.querySelector('.facility-history-no-results');
            let visibleCount = 0;

            rows.forEach(row => {
                if (filter === 'all' || row.dataset.status === filter) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            if (emptyRow) emptyRow.classList.add('hidden');
            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        });
    });

    // Equipment History filter
    document.querySelectorAll('.equipment-history-filter-btn').forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;

            // Reset all buttons to blue border outline
            document.querySelectorAll('.equipment-history-filter-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('text-blue-600', 'border', 'border-blue-600');
            });

            // Activate clicked button - fill blue
            this.classList.remove('text-blue-600', 'border', 'border-blue-600');
            this.classList.add('bg-blue-600', 'text-white');

            // Filter rows
            const rows = document.querySelectorAll('.equipment-history-row');
            const emptyRow = document.querySelector('.equipment-history-empty-row');
            const noResults = document.querySelector('.equipment-history-no-results');
            let visibleCount = 0;

            rows.forEach(row => {
                if (filter === 'all' || row.dataset.status === filter) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            if (emptyRow) emptyRow.classList.add('hidden');
            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        });
    });
    </script>

    @include('transaction.modal.add-equipment')
    @include('transaction.modal.approve-for-payment')
    @include('transaction.modal.cancel-reservation')
    @include('transaction.modal.new-reservation')
    @include('transaction.modal.view-reservation')
    @include('transaction.modal.reservation-error')
    @include('transaction.modal.reject-reservation')
    @include('transaction.modal.mark-paid')
    @include('transaction.modal.view-equipment')
    @include('transaction.modal.complete-reservation')
    @include('transaction.modal.mark-equipment-delivered')
    @include('transaction.modal.mark-equipment-returned')

    <div x-data="{ 
        successMessage: 'Action completed successfully!'
    }" 
    @set-success-message.window="successMessage = $event.detail"
    x-cloak>
        <x-success-modal name="success-modal">
            <span x-text="successMessage"></span>
        </x-success-modal>
    </div>
</x-app-layout>