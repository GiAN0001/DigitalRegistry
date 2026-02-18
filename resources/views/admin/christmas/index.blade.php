<x-app-layout> {{-- added by gian --}}
    <div class="analytics mb-6 ">
        <x-analytics-widget 
                title="Total Households" 
                :value="$totalHouseholds" 
                icon-name="house" 
        />
        <x-analytics-widget 
            title="Released Boxes" 
            :value="$releasedCount" 
            icon-name="package-check" 
        />
        <x-analytics-widget 
            title="On Hold" 
            :value="$onHoldCount" 
            icon-name="clock" 
        />
    </div>
    <div class="filters mb-6">
        <x-dynamic-filter
            model="App\Models\ChristmasBox"
            column="year"
            title="Filter by Year"
        />
        <x-dynamic-filter
            model="App\Models\AreaStreet"
            column="purok_name"
            title="Filter by Purok"
        />
        <x-dynamic-filter
            model="App\Models\ChristmasBox"
            column="status"
            title="Filter by Status"
            :manualOptions="['On Hold', 'Released']"
        />
        <x-search-bar placeholder="Search Head of Family..." />
    </div>
    @if(count(request()->query()) > 0)
        <a href="{{ request()->url() }}" 
        class="flex items-center gap-2 px-4 py-2 text-sm font-medium w-48 text-slate-700 hover:text-blue-800 transition-colors duration-200"
        title="Clear all active filters">
            <x-lucide-rotate-ccw class="w-4 h-4" />
            Reset Filters
        </a>
    @endif

    <div class="p-6 bg-white shadow-md rounded-lg mt-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Christmas Box Tracking</h2>
            <h2 class="text-2xl text-gray-800 font-bold">{{ $year }}</h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-200 text-slate-700">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-bold uppercase rounded-l-lg">Household No.</th>
                        <th class="px-3 py-3 text-left text-xs font-bold uppercase">Head of Family</th>
                        <th class="px-3 py-3 text-left text-xs font-bold uppercase">Address</th>
                        <th class="px-3 py-3 text-left text-xs font-bold uppercase">Status</th>
                        <th class="px-3 py-3 text-left text-xs font-bold uppercase">Released On</th>
                        <th class="px-3 py-3 text-left text-xs font-bold uppercase">Released By</th>
                        <th class="px-3 py-3 text-left text-xs font-bold uppercase rounded-r-lg">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                   @forelse($heads as $head)
                        @php
                            $box = $head->household->christmasBoxes->first();
                            $isReleased = $box && $box->status === 'Released';
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-3 py-4 text-sm font-bold text-gray-700">
                                {{ $head->household->household_number ?? 'N/A' }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-900">
                                {{ $head->first_name }} {{ $head->last_name }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-600">
                                {{ $head->household->house_number ? $head->household->house_number . ', ' : '' }}
                                {{ $head->household->areaStreet->purok_name ?? 'No Purok' }}, 
                                {{ $head->household->areaStreet->street_name ?? 'No Street' }},
                                {{ $head->household->areaStreet->area->area_name ?? '' }}
                            <td class="px-3 py-4 text-xs font-bold">
                                <span class="px-2 py-1 rounded-full {{ $isReleased ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $isReleased ? 'RELEASED' : 'ON HOLD' }}
                                </span>
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-600">
                                {{ $isReleased ? $box->date_released->format('M d, Y') : '-' }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-600 ">
                                {{ $isReleased ? trim($box->releasedBy->first_name . ' ' . ($box->releasedBy->middle_name ? $box->releasedBy->middle_name . ' ' : '') . $box->releasedBy->last_name) : '-' }}
                            </td>
                            <td class="px-3 py-4 text-sm ">
                                @if($isReleased)
                                    {{-- REVERT TRIGGER --}}
                                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-revert-{{ $head->id }}')" 
                                            class="text-red-500 hover:text-red-800 text-sm flex items-center gap-1">
                                        <x-lucide-rotate-ccw class="w-3 h-3"/> Revert
                                    </button>

                                    {{-- REVERT MODAL --}}
                                    <x-modal name="confirm-revert-{{ $head->id }}" maxWidth="md" alignment="center" focusable>
                                        <form action="{{ route('admin.christmas.revert', $head->household->id) }}" method="POST" class="p-6 text-left">
                                            @csrf
                                            <h2 class="text-lg font-bold text-red-600">Revert to Hold?</h2>
                                            <p class="mt-2 text-sm text-gray-600">
                                                Are you sure you want to revert the status of <strong>{{ $head->first_name }} {{ $head->last_name }}</strong>? This will remove the release timestamp and record.
                                            </p>
                                            <div class="mt-6 flex justify-end">
                                                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                                <x-danger-button class="ml-3">Confirm Revert</x-danger-button>
                                            </div>
                                        </form>
                                    </x-modal>
                                @else
                                    {{-- RELEASE TRIGGER --}}
                                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-release-{{ $head->id }}')" 
                                            class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
                                        <x-lucide-package class="w-3 h-3"/> Release
                                    </button>

                                    {{-- RELEASE MODAL --}}
                                    <x-modal name="confirm-release-{{ $head->id }}" maxWidth="md" alignment="center" focusable>
                                        <form action="{{ route('admin.christmas.release', $head->household->id) }}" method="POST" class="p-6 text-left">
                                            @csrf
                                            <h2 class="text-lg font-bold text-gray-900">Confirm Distribution</h2>
                                            <p class="mt-2 text-sm text-gray-600">
                                                You are about to release a Christmas box to the household of <strong>{{ $head->first_name }} {{ $head->last_name }}</strong>.
                                            </p>
                                            <div class="mt-6 flex justify-end">
                                                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                                <x-primary-button class="ml-3 bg-blue-600">Confirm Release</x-primary-button>
                                            </div>
                                        </form>
                                    </x-modal>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10  text-gray-400 italic">No matching households found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $heads->links() }}
        </div>
    </div>
    @if(session('success'))
        <x-success-modal name="action-success" :show="true">
            {{ session('success') }}
        </x-success-modal>
    @endif
</x-app-layout>