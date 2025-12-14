<x-app-layout>

    <div class="sub-content">

        <div class="flex flex-wrap items-center gap-3 mt-[42px]">

            <x-search-bar placeholder="Search residents..." />

            <x-dynamic-filter
                model="App\Models\AreaStreet"
                column="purok_name"
                title="Filter by Purok"
            />

            <x-dynamic-filter
                model="App\Models\Resident"
                column="created_at"
                title="Filter by year"
            />
        </div>

        <div class="flex flex-wrap items-center gap-3 mt-4">

            <x-rows-per-page />

            <x-dynamic-filter
                model="App\Models\ResidencyType"
                column="name"
                title="Filter by Ownership Status"
            />

            <x-dynamic-filter
                model="App\Models\HouseStructure"
                column="house_structure_type"
                title="Filter by House Structure"
            />

            <div class="ml-auto">
                <x-button
                    x-data
                    x-on:click.prevent="$dispatch('open-modal', 'register-resident')"
                >
                    <x-slot name="icon">
                        <x-lucide-plus class="w-4 h-4" />
                    </x-slot>
                    Register Resident
                </x-button>
            </div>
        </div>
        <div class="mt-4"></div>

        <div class="p-4 bg-white shadow-md rounded-lg grid overflow-auto">
            <div class="overflow-x-auto mt-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-200">
                        <tr>
                            <th
                                class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">
                                Household No</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Name</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Address</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                House Structure</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Ownership Status</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Birthplace</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Birthdate</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Age</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Sex</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Civil Status</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Citizenship</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Occupation</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Sector</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Vaccination</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Comorbidity</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Maintenance</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Pet Type</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                Quantity</th>

                            @role('admin')
                                <th
                                    class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">
                                    Action</th>
                            @endrole
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($residents as $resident)
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->household->household_number ?? 'N/A' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $resident->first_name }} {{ $resident->last_name }} {{ $resident->middle_name }}
                                    {{ $resident->extension }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->household->house_number ?? '' }}
                                    {{ $resident->household->areaStreet->street_name ?? '' }},
                                    {{ $resident->household->areaStreet->purok_name ?? '' }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->household->houseStructure->house_structure_type ?? 'N/A' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->residencyType->name ?? 'N/A' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->demographic->birthplace ?? 'N/A' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->demographic->birthdate ?? 'N/A' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->demographic->age ?? 'N/A' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->demographic->sex ?? 'N/A' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->demographic->civil_status ?? 'N/A' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->demographic->nationality ?? 'N/A' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->demographic->occupation ?? 'N/A' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->healthInformation->sector ?? 'N/A' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->healthInformation->vaccination ?? 'N/A' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->healthInformation->comorbidity ?? 'None' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->healthInformation->maintenance ?? 'None' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->household->householdPets->map(fn($pet) => $pet->petType->name)->join(', ') }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $resident->household->householdPets->sum('quantity') }}
                                </td>

                                @role('admin')
                                    <td class="px-2 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-blue-600 hover:text-blue-900">View</a>
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>
                                        <a href="#" class="text-red-600 hover:text-red-900 ml-4">Delete</a>
                                    </td>
                                @endrole
                            </tr>
                        @empty
                            <tr>
                                <td colspan="19" class="px-6 py-4 text-center text-gray-500">
                                    No residents found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $residents->links() }}
            </div>
        </div>
    </div>

<div>
    @include('residents.modal.register-resident')
</div>

</x-app-layout>
