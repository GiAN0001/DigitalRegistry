<x-app-layout>
    @hasanyrole('admin|super admin') {{-- Only show to admin and super admin added by gian --}}
        <div>
            <div class="max-w-full">
                <div class="dashboard-grid">

                    <div class="col-span-1 md:col-span-1 lg:col-span-3">
                        <x-analytics-widget title="Total Residents" :value="$totalResidents" icon-name="users"
                            bg-color="bg-blue-500" />
                    </div>

                    <div class="col-span-1 md:col-span-1 lg:col-span-3">
                        <x-analytics-widget title="Total Households" :value="$totalHousehold" icon-name="house"
                            bg-color="bg-blue-500" />
                    </div>

                    <div class="col-span-1 md:col-span-1 lg:col-span-3">
                        <x-analytics-widget title="Total Active Users" :value="$totalActiveUsers" icon-name="square-user"
                            bg-color="bg-blue-500" />
                    </div>

                    <div class="col-span-1 md:col-span-2 lg:col-span-9 flex flex-col h-full">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="text-md font-bold text-slate-700">
                                    Barangay Staff Activity
                                </h3>
                                <a href="#" class="text-sm text-slate-500 hover:text-blue-600 font-semibold">
                                    See all staff
                                </a>
                            </div>

                            <div class="relative h-60 w-full bg-slate-50 overflow-hidden shadow-sm sm:rounded-lg px-4 py-5 ">
                                <div class="overflow-y-hidden h-full">
                                    <table class="w-full border-separate border-spacing-y-3">
                                        <thead class="text-xs text-blue-600 uppercase tracking-wider">
                                            <tr class="bg-blue-200">
                                                <th class="px-6 py-2 text-left font-semibold rounded-l-lg">Name</th>
                                                <th class="px-6 py-2 text-left font-semibold">Barangay Role</th>
                                                <th class="px-6 py-2 text-center font-semibold">System Role</th>
                                                <th class="px-6 py-2 text-center font-semibold rounded-r-lg">House Registered</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-sm font-regular text-slate-700">
                                            @forelse ($users as $user)
                                                <tr class="bg-sky-100 hover:bg-sky-200 transition-colors duration-200">
                                                    <td class="px-6 py-2 whitespace-nowrap rounded-l-lg">
                                                        {{ $user->first_name }} {{ $user->last_name }}
                                                    </td>
                                                    <td class="px-6 whitespace-nowrap">
                                                        {{ $user->barangayRole->name ?? 'N/A' }}
                                                    </td>
                                                    <td class="px-6 whitespace-nowrap text-center">
                                                        <span class="px-2 py-1 inline-flex rounded-full bg-blue-100 text-blue-800">
                                                            {{ $user->roles->first()->name ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 whitespace-nowrap text-sm text-gray-500 text-center rounded-r-lg">
                                                        {{ $user->households_registered_count ?? 0 }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                        No users found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-2 lg:col-span-4 flex flex-col">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="text-sm font-semibold text-slate-700"></h3>
                                <h3 class="text-sm font-semibold text-slate-500">Date</h3>
                            </div>
                            <div class="h-60 w-full bg-slate-50 text-slate-700 overflow-hidden shadow-sm sm:rounded-lg p-6 relative flex flex-col justify-center items-center text-center"
                                x-data="clock()" x-init="startClock()">
                                <div class="w-full flex items-center justify-between">
                                    <div class="text-sm text-slate-700 font-bold" x-text="monthName"></div>
                                    <div class="text-sm text-slate-700 font-bold" x-text="time"></div>
                                </div>
                                <div class="z-10 py-2">
                                    <h1 class="text-9xl text-blue-600 font-bold tracking-tighter leading-none"
                                        x-text="dayNumber"></h1>
                                </div>
                                <div class="w-full flex items-center justify-between">
                                    <div class="text-sm text-slate-500 font-bold" x-text="dayName"></div>
                                    <div class="text-sm text-slate-500 font-bold">Manila</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-2 lg:col-span-6 flex flex-col">
                        <div>
                            <x-chart-widget title="Population by Purok" type="pie" :data="$populationChartData" />
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-2 lg:col-span-7 flex flex-col">
                        <div>
                            <x-demographics-chart title="Population Demographics by Age Group" :data="$demographicsChartData" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function clock() {
                return {
                    monthName: '',
                    dayNumber: '',
                    dayName: '',
                    time: '',

                    startClock() {
                        this.updateTime();
                        setInterval(() => {
                            this.updateTime();
                        }, 1000);
                    },

                    updateTime() {
                        const date = new Date();
                        this.monthName = date.toLocaleDateString('en-US', { month: 'long' });
                        this.dayNumber = date.getDate();
                        this.dayName = date.toLocaleDateString('en-US', { weekday: 'long' });
                        this.time = date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                    },
                };
            }
        </script>
    @endhasanyrole
</x-app-layout>
