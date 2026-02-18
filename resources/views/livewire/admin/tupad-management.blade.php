<div>
    <div class="analytics mb-6">
        <x-analytics-widget 
        title="Eligible" 
        :value="$eligibleCount" 
        bg-color="bg-blue-600" 
        icon-name="user-check"
        />
        <x-analytics-widget 
        title="Ongoing" 
        :value="$ongoingCount" 
        bg-color="bg-yellow-500" 
        icon-name="briefcase"
        />
        <x-analytics-widget 
        title="Scheduled" 
        :value="$scheduledCount" 
        bg-color="bg-slate-700"
        icon-name="clock"
        />
        <x-analytics-widget 
        title="Ineligible" 
        :value="$ineligibleCount" 
        bg-color="bg-orange-500" 
        icon-name="user-x"
        />
        <x-analytics-widget 
        title="Dropped" 
        :value="$droppedCount" 
        bg-color="bg-red-600"
        icon-name="user-minus"
        />
    </div>

    <div class=" filters">
        <div class="relative w-full max-w-md h-10">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <x-lucide-search class="w-5 h-5 text-slate-700" />
            </div>
            <input
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="Search Resident..."
                class="block w-full h-10 pl-10 pr-4 py-2 text-sm font-normal text-slate-700 bg-slate-50 border-none rounded-lg shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-800 placeholder:text-slate-400"
            >
        </div>
    </div>

    <div class="bg-white p-6 shadow rounded-lg">

        <div class="flex border-b-2 mb-6">
            <button wire:click="$set('activeTab', 'eligible')" 
                    class="p-3 {{ $activeTab == 'eligible' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500' }}">
                Eligible
            </button>
            
            <button wire:click="$set('activeTab', 'ongoing')" 
                    class="p-3 {{ $activeTab == 'ongoing' ? 'border-b-2 border-amber-600 text-amber-600' : 'text-gray-500' }}">
                Ongoing
            </button>
            <button wire:click="$set('activeTab', 'ineligible')" 
                    class="p-3 {{ $activeTab == 'ineligible' ? 'border-b-2 border-orange-600 text-orange-600' : 'text-gray-500' }}">
                Ineligible
            </button>
            <button wire:click="$set('activeTab', 'dropped')" 
                    class="p-3 {{ $activeTab == 'dropped' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-500' }}">
                Dropped
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y">
                <div class="mb-4">
                     <h2 class="text-2xl font-bold text-gray-800 mb-6">TUPAD Program</h2>
                </div>
                <thead class="{{ $activeTab == 'eligible' ? 'bg-blue-100' : ($activeTab == 'ongoing' ? 'bg-amber-100' : ($activeTab == 'dropped' ? 'bg-red-100' : 'bg-orange-100')) }}">
                    @if($activeTab === 'eligible')
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase rounded-l-lg">Resident Info</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase">Age</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase">Address</th>
                            <th class="px-3 py-3 text-center text-xs font-bold text-slate-700 uppercase rounded-r-lg">Action</th>
                        </tr>
                    @elseif($activeTab === 'ongoing')
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase rounded-l-lg">Resident Info</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase">Date Started - End Date</th>
                            <th class="px-3 py-3 text-center text-xs font-bold text-slate-700 uppercase">Status</th>
                            <th class="px-3 py-3 text-center text-xs font-bold text-slate-700 uppercase rounded-r-lg">Action</th>
                        </tr>
                    @elseif($activeTab === 'ineligible')
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase rounded-l-lg">Resident Info</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase">Age</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase">Date Ended</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase">Reason</th>
                            <th class="px-3 py-3 text-center text-xs font-bold text-slate-700 uppercase rounded-r-lg">Action</th>
                        </tr>
                    @elseif($activeTab === 'dropped')
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase rounded-l-lg">Resident Info</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase">Age</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase">Date Dropped</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase">Reason for Dropping</th>   
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-700 uppercase">Dropped By</th>
                            <th class="px-3 py-3 text-center text-xs font-bold text-slate-700 uppercase rounded-r-lg">Action</th>
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @if($activeTab === 'eligible')
                        @include('admin.tupad.tabs.eligible')
                    @elseif($activeTab === 'ongoing')
                        @include('admin.tupad.tabs.ongoing')
                    @elseif($activeTab === 'ineligible')
                        @include('admin.tupad.tabs.ineligible')
                    @elseif($activeTab === 'dropped')
                        @include('admin.tupad.tabs.dropped')
                    @endif
                </tbody>
            </table>

            {{-- <div class="bg-gray-100 p-4 mb-4 rounded border border-dashed border-gray-400">
                <label class="text-xs font-bold text-gray-500 uppercase">Time Travel Debugger</label>
                <div class="flex items-center gap-4 mt-2">
                    <input type="date" wire:model.live="testDate" class="rounded border-gray-300 text-sm">
                    <button wire:click="$set('testDate', null)" class="text-xs text-red-600 underline">Reset to Real Time</button>
                    <span class="text-xs text-blue-600">Current System Date: {{ now()->format('M d, Y') }}</span>
                </div>
            </div> for testing only --}} 
            
        </div>
        
        <div class="mt-4">
            {{ ${$activeTab}->links() }}
        </div>
    </div>
</div>