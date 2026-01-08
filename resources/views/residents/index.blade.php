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

        <div class="p-4 bg-white shadow-md rounded-lg grid overflow-x-auto">
            <div class="overflow-x-auto mt-6">
                <table class="min-w-full divide-y divide-gray-200"> // Modified by GIAN whole table
                    <thead class="bg-blue-200">
                        <tr>
                            <th class="px-2 py-2 w-10"></th> 
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Household No</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Head of Family</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Address</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Members</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">House Structure</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Ownership</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Contact</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Email</th>
                        </tr>
                    </thead>

                        @forelse ($residents as $head)
    
                        <tbody class="bg-white divide-y divide-gray-200 border-b border-gray-200" x-data="{ expanded: false }">
                            
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out" :class="{ 'bg-blue-50': expanded }">
                                <td class="px-2 py-4 text-center">
                                    <button @click="expanded = !expanded" class="text-blue-600 hover:text-blue-800 focus:outline-none">
                                        <template x-if="!expanded">
                                            <x-lucide-chevron-right class="w-5 h-5" />
                                        </template>
                                        <template x-if="expanded">
                                            <x-lucide-chevron-down class="w-5 h-5" />
                                        </template>
                                    </button>
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                                    {{ $head->household->household_number ?? 'N/A' }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $head->first_name }} {{ $head->last_name }} {{ $head->extension }}
                                    <span class="ml-2 text-xs text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">HEAD</span>
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $head->household->house_number ?? '' }} 
                                    {{ $head->household->areaStreet->street_name ?? '' }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $head->household->residents->count() }} Members
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $head->household->houseStructure->house_structure_type ?? 'N/A' }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $head->residencyType->name ?? 'N/A' }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $head->household->contact_number ?? 'N/A' }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $head->household->email ?? 'N/A' }}
                                </td>
                            </tr>

                            <tr x-show="expanded" x-cloak x-transition.opacity class="bg-gray-50">
                                <td colspan="9" class="px-4 py-4">
                                    <div class="pl-10">
                                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 tracking-wider">Family Members & Details</h4>
                                        
                                        <table class="w-full text-sm text-left text-gray-600 border border-gray-200 rounded-md overflow-hidden">
                                            <thead class="bg-gray-100 text-xs uppercase font-medium text-gray-500">
                                                <tr>
                                                    <th class="px-3 py-2">Name</th>
                                                    <th class="px-3 py-2">Role</th>
                                                    <th class="px-3 py-2">Occupation</th>
                                                    <th class="px-3 py-2">Birthdate</th>
                                                    <th class="px-3 py-2">Birthplace</th>
                                                    <th class="px-3 py-2">Age</th>
                                                    <th class="px-3 py-2">Sex</th>
                                                    <th class="px-3 py-2">Status</th>
                                                    <th class="px-3 py-2">Health Informations</th>
                                                    <th class="px-3 py-2">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 bg-white">
                                                {{-- Loop through ALL residents in this household --}}
                                                @foreach($head->household->residents as $member)
                                                    <tr>
                                                        <td class="px-3 py-2 font-medium text-gray-800">
                                                            {{ $member->first_name }} {{ $member->last_name }}
                                                        
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <span @class([
                                                                'px-2 py-1 rounded-full text-xs font-semibold',
                                                                'bg-blue-100 text-blue-700' => ($member->id === $head->id),
                                                                'text-gray-700' => ($member->id !== $head->id)
                                                            ])>
                                                                {{ $member->householdRole->name ?? 'Member' }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2 ">
                                                            {{ $member->demographic->occupation ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-3 py-2">{{ $member->demographic->birthdate ?? '-' }}</td>
                                                        <td class="px-3 py-2 ">
                                                            {{ $member->demographic->birthplace ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            {{ \Carbon\Carbon::parse($member->demographic->birthdate)->age }}
                                                        </td>
                                                        <td class="px-3 py-2">{{ $member->demographic->sex ?? '-' }}</td>
                                                        <td class="px-3 py-2">{{ $member->demographic->civil_status ?? '-' }}</td>
                                                        <td class="px-3 py-2">
                                                            <div class="flex flex-col text-xs">
                                                                <span>Sector: {{ $member->healthInformation->sector ?? 'N/A' }}</span>
                                                                <span>Vaccination: {{ $member->healthInformation->vaccination ?? 'N/A' }}</span>
                                                                <span>Comorbidity: {{ $member->healthInformation->comorbidity ?? 'None' }}</span>
                                                                <span>Maintenance:: {{ $member->healthInformation->maintenance ?? 'None'}}</span>
                                                            </div>
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <a href="#" class="text-blue-800 hover:underline">Edit</a>
                                                            <a href="#" class="text-red-800 hover:underline">Delete</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                
                                                {{-- Optional: Show Pets row if exists --}}
                                                @if($head->household->householdPets->count() > 0)
                                                    <tr class="bg-yellow-50">
                                                        <td class="px-3 py-2 font-bold text-yellow-700">Pets</td>
                                                        <td colspan="9" class="px-3 py-2 text-yellow-700">
                                                            {{ $head->household->householdPets->map(fn($p) => $p->quantity . ' ' . $p->petType->name)->join(', ') }}
                                                        </td>
                                                    </tr>
                                                @endif

                                                @if(in_array($head->residency_type_id, [2, 3, 4]) && $head->household->landlord_name)
                                                    <tr class="bg-orange-50">
                                                        <td class="px-3 py-2 font-bold text-orange-800">Landlord</td>
                                                        <td colspan="9" class="px-3 py-2 text-orange-800">
                                                            <span class="font-semibold uppercase tracking-wide">{{ $head->household->landlord_name }}</span>
                                                            @if($head->household->landlord_contact)
                                                                <span class="text-orange-600 text-xs ml-2 border-l border-orange-300 pl-2">
                                                                    {{ $head->household->landlord_contact }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    @empty
                        <tbody>
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    No households found.
                                </td>
                            </tr>
                        </tbody>
                    @endforelse
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
