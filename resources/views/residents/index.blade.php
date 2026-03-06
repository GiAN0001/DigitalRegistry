<x-app-layout>

    @if(session('success'))
            <x-success-modal name="action-success" :show="true">
                {{ session('success') }}
            </x-success-modal>
    @endif
    @if(session('error'))
        <x-error-modal name="action-error" :show="true">
            <div class="text-center">
                <p class="text-gray-600 mb-4">{{ session('error') }}</p>
            </div>
        </x-error-modal>
    @endif
    <div class="sub-content">
        <div class="filter-container">
                <div class="filters">
                    <x-search-bar placeholder="Search residents..." />
                    
                    <x-dynamic-filter
                        model="App\Models\AreaStreet"
                        column="purok_name"
                        title="Filter by Purok"
                    />
                
                   <x-dynamic-filter
                        model="App\Models\Resident"
                        column="census_cycle"
                        title="Filter by Census Cycle"
                    />
                </div>

            <div class="filters2">
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

                <div class="ml-auto flex gap-2">
                    @role('super admin|admin')
                        @if(request('archived') == 'true')
                            <a href="{{ route('residents.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 whitespace-nowrap transition duration-150 ease-in-out">
                                <x-lucide-users class="w-4 h-4 mr-2" /> Active Records
                            </a>
                        @else
                            <a href="{{ route('residents.index', ['archived' => 'true']) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 whitespace-nowrap transition duration-150 ease-in-out">
                                <x-lucide-archive class="w-4 h-4 mr-2" /> Archived Records
                            </a>
                        @endif
                    @endrole
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
                @if(count(request()->query()) > 0)
                    <a href="{{ request()->url() }}" 
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium w-48 text-slate-700 hover:text-blue-800 transition-colors duration-200"
                    title="Clear all active filters">
                        <x-lucide-rotate-ccw class="w-4 h-4" />
                        Reset Filters
                    </a>
                @endif

        </div>

        <div class="p-4 mt-4 bg-white shadow-md rounded-lg grid overflow-x-auto">
            <div class="overflow-x-auto mt-6">
                <table class="min-w-full divide-y divide-gray-200"> {{-- Modified by GIAN --}}
                    <thead class="bg-blue-200">
                        <tr>
                            <th class="px-2 py-2 w-10 rounded-l-lg"></th> 
                            <th class="px-2 py-2 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Household No</th>
                            <th class="px-2 py-2 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Head of Family</th>
                            <th class="px-2 py-2 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Address</th>
                            <th class="px-2 py-2 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Members</th>
                            <th class="px-2 py-2 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">House Structure</th>
                            <th class="px-2 py-2 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Ownership</th>
                            <th class="px-2 py-2 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Contact</th>
                            <th class="px-2 py-2 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Email</th>
                            
                            @hasanyrole('admin|super admin')
                                <th class="px-2 py-2 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Action</th>
                            @endhasanyrole
                        </tr>
                    </thead>

                    @forelse ($residents as $household)

                        @php
                            // Always pick the first ACTIVE resident as the display anchor
                            $activeResidents = $household->residents->filter(fn($r) => !$r->trashed());
                            $archivedMembers = $household->residents->filter(fn($r) => $r->trashed());
                            $anchor = $activeResidents->first(); // Could be null if all are archived
                            $headRoleId = \App\Models\householdRole::where('name', 'Head')->value('id');
                            // Try to find a non-archived head first, else use any active member
                            $headResident = $activeResidents->firstWhere('household_role_id', $headRoleId) ?? $anchor;
                            // For edit household: use any active resident's residency_type_id
                            $residencyTypeId = $activeResidents->first()?->residency_type_id;
                        @endphp

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
                                    {{ $household->household_number ?? 'N/A' }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    @if($headResident)
                                        {{ $headResident->first_name }} {{ $headResident->middle_name }} {{ $headResident->last_name }} {{ $headResident->extension }}
                                        @if($headResident->household_role_id == $headRoleId)
                                            <span class="ml-2 text-xs text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">HEAD</span>
                                        @else
                                            <span class="ml-2 text-xs text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full">{{ $headResident->householdRole->name ?? 'MEMBER' }}</span>
                                            <span class="ml-1 text-xs text-red-500 bg-red-50 px-2 py-0.5 rounded-full">Head Archived</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic text-xs">All members archived</span>
                                    @endif
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $household->house_number ?? '' }}
                                    {{ $household->areaStreet->purok_name ?? '' }}
                                    {{ $household->areaStreet->street_name ?? '' }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $activeResidents->count() }} Members
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $household->houseStructure->house_structure_type ?? 'N/A' }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $activeResidents->first()?->residencyType?->name ?? 'N/A' }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $household->contact_number ?? 'N/A' }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $household->email ?? 'N/A' }}
                                </td>
                                @hasanyrole('admin|super admin')
                                    <td class="px-2 py-4 whitespace-nowrap text-sm flex gap-2">
                                        <div>
                                            @php
                                                $editData = $household->toArray();
                                                $editData['residency_type_id'] = $residencyTypeId;
                                                $editData['area_street'] = $household->areaStreet;
                                            @endphp

                                            <button 
                                                x-data 
                                                @click="$dispatch('open-modal', 'edit-household-modal'); $dispatch('fetch-household-data', {{ $household->id }})"
                                                class="text-blue-600 hover:text-blue-900"
                                            >
                                                Edit
                                            </button>
                                        </div>
                                        @if($household->trashed())
                                            <form action="{{ route('households.restore', $household->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:underline hover:text-green-900" onclick="return confirm('Restore this household back to active?')">Restore</button>
                                            </form>
                                        @else
                                            <button 
                                                x-data
                                                @click="$dispatch('open-modal', 'delete-household-modal'); $dispatch('set-delete-household-id', {{ $household->id }})"
                                                class="text-red-800 hover:underline"
                                            >
                                                Delete
                                            </button>
                                        @endif
                                    </td>
                                @endhasanyrole
                            </tr>

                            <tr x-show="expanded" x-cloak x-transition.opacity class="bg-gray-50">
                                <td colspan="10" class="px-4 py-4">
                                    <div class="pl-10">
                                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 tracking-wider">Family Members & Details</h4>
                                        
                                        <table class="w-full text-sm text-left text-gray-600 border border-gray-200 rounded-md overflow-hidden">
                                            <thead class="bg-gray-100 text-xs uppercase font-medium text-gray-500">
                                                <tr>
                                                    <th class="px-3 py-2">Name</th>
                                                    <th class="px-3 py-2">Role</th>
                                                    <th class="px-3 py-2">Occupation</th>
                                                    @role('super admin')
                                                       <th class="px-3 py-2">Birthdate</th>
                                                    @endrole
                                                    <th class="px-3 py-2">Birthplace</th>
                                                    <th class="px-3 py-2">Age</th>
                                                    <th class="px-3 py-2">Sex</th>
                                                    <th class="px-3 py-2">Status</th>

                                                    @role('super admin')
                                                        <th class="px-3 py-2">Health Informations</th>
                                                    @endrole

                                                    @hasanyrole('admin|super admin')
                                                        <th class="px-3 py-2">Actions</th>
                                                    @endhasanyrole
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 bg-white">
                                    
                                                @php
                                                    // $activeResidents and $archivedMembers already set above in the @php block
                                                    $activeMembers   = $activeResidents;
                                                @endphp

                                                {{-- ACTIVE MEMBERS --}}
                                                @foreach($activeMembers as $member)
                                                    <tr>
                                                        <td class="px-3 py-2 font-medium text-gray-800">
                                                            {{ $member->first_name }} {{ $member->middle_name }} {{ $member->last_name }} {{ $member->extension }}
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <span @class([
                                                                'px-2 py-1 rounded-full text-xs font-semibold',
                                                                'bg-blue-100 text-blue-700' => ($member->household_role_id == $headRoleId),
                                                                'text-gray-700' => ($member->household_role_id != $headRoleId)
                                                            ])>
                                                                {{ $member->householdRole->name ?? 'Member' }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2">{{ $member->demographic->occupation ?? 'N/A' }}</td>
                                                        @role('super admin')
                                                            <td class="px-3 py-2">{{ $member->demographic->birthdate ?? '-' }}</td>
                                                        @endrole
                                                        <td class="px-3 py-2">{{ $member->demographic->birthplace ?? 'N/A' }}</td>
                                                        <td class="px-3 py-2">{{ \Carbon\Carbon::parse($member->demographic->birthdate)->age }}</td>
                                                        <td class="px-3 py-2">{{ $member->demographic->sex ?? '-' }}</td>
                                                        <td class="px-3 py-2">{{ $member->demographic->civil_status ?? '-' }}</td>
                                                        @role('super admin')
                                                            <td class="px-3 py-2">
                                                                <div class="flex flex-col text-xs gap-3">
                                                                    <span><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded w-fit">Sector:</span> {{ $member->healthInformation->sector ?? 'N/A' }}</span>
                                                                    <span><span class="bg-green-100 text-green-800 px-2 py-0.5 rounded w-fit">Vaccination:</span> {{ $member->healthInformation->vaccination ?? 'None' }}</span>
                                                                    <span><span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded w-fit">Comorbidity:</span> {{ $member->healthInformation->comorbidity ?? 'None' }}</span>
                                                                    <span><span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded w-fit">Maintenance:</span> {{ $member->healthInformation->maintenance ?? 'None'}}</span>
                                                                </div>
                                                            </td>
                                                        @endrole
                                                        @hasanyrole('admin|super admin')
                                                            <td class="px-3 py-2">
                                                                <button 
                                                                    x-data 
                                                                    @click="$dispatch('open-modal', 'edit-resident-modal'); $dispatch('fetch-resident-data', {{ $member->id }})"
                                                                    class="text-blue-600 hover:text-blue-900"
                                                                >
                                                                    Edit
                                                                </button>
                                                                <button 
                                                                    x-data
                                                                    @click="$dispatch('open-modal', 'delete-resident-modal'); $dispatch('set-delete-resident-id', {{ $member->id }})"
                                                                    class="text-red-800 hover:underline"
                                                                >
                                                                    Delete
                                                                </button>
                                                            </td>
                                                        @endhasanyrole
                                                    </tr>
                                                @endforeach

                                                {{-- ARCHIVED MEMBERS SECTION (visible only in archived view when household has deleted members) --}}
                                                @if(request('archived') == 'true' && $archivedMembers->count() > 0)
                                                    <tr class="bg-red-50">
                                                        <td colspan="10" class="px-3 py-1.5">
                                                            <span class="text-xs font-bold text-red-600 uppercase tracking-wider flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v1h16V5a2 2 0 00-2-2H4zm14 5H2v8a2 2 0 002 2h12a2 2 0 002-2V8z"/></svg>
                                                                Archived Members ({{ $archivedMembers->count() }})
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    @foreach($archivedMembers as $member)
                                                        <tr class="bg-red-50 opacity-75">
                                                            <td class="px-3 py-2 font-medium text-red-800">
                                                                {{ $member->first_name }} {{ $member->middle_name }} {{ $member->last_name }} {{ $member->extension }}
                                                                <span class="ml-2 text-xs bg-red-200 text-red-700 px-2 py-0.5 rounded-full">ARCHIVED</span>
                                                            </td>
                                                            <td class="px-3 py-2 text-red-700">
                                                                <span class="px-2 py-1 rounded-full text-xs font-semibold text-red-700">
                                                                    {{ $member->householdRole->name ?? 'Member' }}
                                                                </span>
                                                            </td>
                                                            <td class="px-3 py-2 text-red-700">{{ $member->demographic->occupation ?? 'N/A' }}</td>
                                                            @role('super admin')
                                                                <td class="px-3 py-2 text-red-700">{{ $member->demographic->birthdate ?? '-' }}</td>
                                                            @endrole
                                                            <td class="px-3 py-2 text-red-700">{{ $member->demographic->birthplace ?? 'N/A' }}</td>
                                                            <td class="px-3 py-2 text-red-700">{{ \Carbon\Carbon::parse($member->demographic->birthdate)->age }}</td>
                                                            <td class="px-3 py-2 text-red-700">{{ $member->demographic->sex ?? '-' }}</td>
                                                            <td class="px-3 py-2 text-red-700">{{ $member->demographic->civil_status ?? '-' }}</td>
                                                            @role('super admin')
                                                                <td class="px-3 py-2">
                                                                    <div class="flex flex-col text-xs gap-3 text-red-700">
                                                                        <span><span class="bg-red-100 text-red-800 px-2 py-0.5 rounded w-fit">Sector:</span> {{ $member->healthInformation->sector ?? 'N/A' }}</span>
                                                                        <span><span class="bg-red-100 text-red-800 px-2 py-0.5 rounded w-fit">Vaccination:</span> {{ $member->healthInformation->vaccination ?? 'None' }}</span>
                                                                        <span><span class="bg-red-100 text-red-800 px-2 py-0.5 rounded w-fit">Comorbidity:</span> {{ $member->healthInformation->comorbidity ?? 'None' }}</span>
                                                                        <span><span class="bg-red-100 text-red-800 px-2 py-0.5 rounded w-fit">Maintenance:</span> {{ $member->healthInformation->maintenance ?? 'None' }}</span>
                                                                    </div>
                                                                </td>
                                                            @endrole
                                                            @hasanyrole('admin|super admin')
                                                                <td class="px-3 py-2">
                                                                    <form action="{{ route('residents.restore', $member->id) }}" method="POST" class="inline">
                                                                        @csrf
                                                                        <button type="submit" class="text-green-600 hover:underline hover:text-green-900 text-sm" onclick="return confirm('Restore this archived resident?')">
                                                                            Restore
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            @endhasanyrole
                                                        </tr>
                                                    @endforeach
                                                @endif

                                                {{-- Optional: Show Pets row if exists --}}
                                                @if($household->householdPets->count() > 0)
                                                    <tr class="bg-yellow-50">
                                                        <td class="px-3 py-2 font-bold text-yellow-700">Pets</td>
                                                        <td colspan="9" class="px-3 py-2 text-yellow-700">
                                                            {{ $household->householdPets->map(fn($p) => $p->quantity . ' ' . $p->petType->name)->join(', ') }}
                                                        </td>
                                                    </tr>
                                                @endif

                                                @php $ownerRoleId = \App\Models\ResidencyType::where('name', 'Owner')->value('id'); @endphp
                                                @if(!in_array($residencyTypeId, [$ownerRoleId]) && $household->landlord_name)
                                                    <tr class="bg-orange-50">
                                                        <td class="px-3 py-2 font-bold text-orange-800">Landlord</td>
                                                        <td colspan="9" class="px-3 py-2 text-orange-800">
                                                            <span class="font-semibold uppercase tracking-wide">{{ $household->landlord_name }}</span>
                                                            @if($household->landlord_contact)
                                                                <span class="text-orange-600 text-xs ml-2 border-l border-orange-300 pl-2">
                                                                    {{ $household->landlord_contact }}
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
    @include('residents.modal.edit-household-modal')
    @include('residents.modal.edit-resident-modal')
    @include('residents.modal.delete-household-modal')
    @include('residents.modal.delete-resident-modal')
</div>

</x-app-layout>
