<x-app-layout>
    <div x-data="{ successMessage: '' }"></div>
    <div class="sub-content">
        

        {{-- Analytics Widgets for Equipment Types --}}
        <div class="max-w-full">
            <div class="dashboard-grid">
                @forelse($equipments as $equipment)
                    <div class="col-span-1 md:col-span-1 lg:col-span-3">
                        <x-analytics-widget 
                            :title="$equipment->equipment_type" 
                            :value="$equipment->total_quantity" 
                            icon-name="box"
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

        <div class="flex flex-wrap items-center gap-2 mt-4 mb-4">
            <x-search-bar placeholder="Search by Name or Transaction ID" class="w-full md:flex-1" />  
        </div>

        {{-- Calendar Section --}}
        <div class="flex gap-6" x-data="{ selectedDate: '{{ \Carbon\Carbon::now()->format('Y-m-d') }}', selectedDateFormatted: '{{ \Carbon\Carbon::now()->format('F d') }}' }">
            {{-- Main Calendar --}}
            <div class="flex-1 bg-white rounded-lg shadow-sm p-6">
                <div x-data="calendar()" x-init="init()">
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
                            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
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
            <div class="w-80 bg-white rounded-lg shadow-sm p-6">
                <div class="bg-blue-100 rounded-lg p-4 text-center mb-4">
                    <h3 class="text-lg font-bold text-blue-700" x-text="selectedDateFormatted + ' Events'"></h3>
                </div>
                
                @php
                    $eventDates = $reservations->pluck('start_date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))->unique()->values()->toArray();
                @endphp
                
                <div class="max-h-[700px] overflow-y-auto space-y-3" x-data="{ eventDates: @js($eventDates) }">
                    {{-- Events for selected date --}}
                    @foreach($reservations as $reservation)
                        <div x-show="selectedDate === '{{ \Carbon\Carbon::parse($reservation->start_date)->format('Y-m-d') }}'">
                            <x-event-card :event="$reservation" />
                        </div>
                    @endforeach
                    
                    {{-- No Events Message - only shows when selected date has no events --}}
                    <div 
                        x-show="!eventDates.includes(selectedDate)"
                        class="text-center text-gray-500 py-4"
                    >
                        No Event
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
                console.log('Events loaded:', this.events);
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
                    'Paid': 'bg-green-200 text-green-900',
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
                    
                    // Filter events for this date and sort by time_sort
                    const dayEvents = this.events
                        .filter(e => e.date === dateStr)
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
    </script>

    @include('transaction.modal.approve-for-payment')
    @include('transaction.modal.success-message')
    @include('transaction.modal.new-reservation')
    @include('transaction.modal.view-reservation')
    @include('transaction.modal.reservation-error')
</x-app-layout>