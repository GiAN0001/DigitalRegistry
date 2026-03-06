<x-app-layout>
    <div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
            <div class="p-6 text-slate-800 border-b border-slate-200 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-blue-600">Lookup Management</h2>
                    <p class="text-sm text-slate-500 mt-1">Search for existing records or add a new item to the database</p>
                </div>
                <button 
                    id="addItemBtn"
                    x-data
                    @click="$dispatch('open-modal', 'lookup-modal')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition-colors whitespace-nowrap ml-4">
                    <x-lucide-plus class="w-4 h-4 inline mr-1" />
                    Add Item
                </button>
            </div>

            {{-- Tabs Navigation --}}
            <div class="overflow-x-auto border-b border-slate-200 scrollbar-hide">
                <div class="flex min-w-min">
                    <button data-tab="area-street" class="tab-button active px-6 py-3 font-semibold text-blue-500 border-b-2 border-blue-600 hover:text-slate-700 focus:outline-none whitespace-nowrap">
                        <i class="fas fa-search"></i>Area Streets
                    </button>
                    <button data-tab="barangay-role" class="tab-button px-6 py-3 font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700 focus:outline-none whitespace-nowrap">
                        <i class="fas fa-search"></i>Barangay Roles
                    </button>
                    <button data-tab="document-purposes" class="tab-button px-6 py-3 font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700 focus:outline-none whitespace-nowrap">
                        <i class="fas fa-search"></i>Document Purposes
                    </button>
                    <button data-tab="document-types" class="tab-button px-6 py-3 font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700 focus:outline-none whitespace-nowrap">
                        <i class="fas fa-search"></i>Document Types
                    </button>
                    <button data-tab="equipments" class="tab-button px-6 py-3 font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700 focus:outline-none whitespace-nowrap">
                        <i class="fas fa-search"></i>Equipments
                    </button>
                    <button data-tab="facilities" class="tab-button px-6 py-3 font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700 focus:outline-none whitespace-nowrap">
                        <i class="fas fa-search"></i>Facilities
                    </button>
                    <button data-tab="household-roles" class="tab-button px-6 py-3 font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700 focus:outline-none whitespace-nowrap">
                        <i class="fas fa-search"></i>Household Roles
                    </button>
                    <button data-tab="household-structures" class="tab-button px-6 py-3 font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700 focus:outline-none whitespace-nowrap">
                        <i class="fas fa-search"></i>Household Structures
                    </button>
                    <button data-tab="pet-types" class="tab-button px-6 py-3 font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700 focus:outline-none whitespace-nowrap">
                        <i class="fas fa-search"></i>Pet Types
                    </button>
                    <button data-tab="residency-types" class="tab-button px-6 py-3 font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700 focus:outline-none whitespace-nowrap">
                        <i class="fas fa-search"></i>Residency Types
                    </button>
                </div>
            </div>

            <div class="p-6">
                {{-- AREA STREET TAB --}}
                <div id="area-street-tab" class="tab-content">
                    <div class="mb-4">
                        <x-search-bar placeholder="Search by street name..." />
                    </div>

                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-200">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Street ID</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Purok Name</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Purok Code</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Street Name</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Created Date</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($areaStreets as $street)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $street->id }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $street->purok_name }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $street->purok_code }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $street->street_name }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $street->created_at->format('M d, Y') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                                            <a href="#" class="text-blue-600 hover:text-blue-800">Edit</a>
                                            <a href="#" class="text-red-600 hover:text-red-800 ml-4">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-4 text-center text-gray-500">No streets found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>


                {{-- BARANGAY ROLE TAB --}}
                <div id="barangay-role-tab" class="tab-content hidden">
                    <div class="mb-4">
                        <x-search-bar placeholder="Search by role name..." />
                    </div>

                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-200">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Role ID</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Role Name</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Created Date</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($barangayRoles as $role)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $role->id }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $role->name }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $role->created_at->format('M d, Y') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                                            <a href="#" class="text-blue-600 hover:text-blue-800">Edit</a>
                                            <a href="#" class="text-red-600 hover:text-red-800 ml-4">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">No roles found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- DOCUMENT PURPOSES TAB --}}
                <div id="document-purposes-tab" class="tab-content hidden">
                    <div class="mb-4">
                        <x-search-bar placeholder="Search by document purpose..." />
                    </div>

                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-200">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Purpose ID</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Purpose Name</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Created Date</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($documentPurposes as $purpose)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $purpose->id }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $purpose->name }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $purpose->created_at->format('M d, Y') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                                            <a href="#" class="text-blue-600 hover:text-blue-800">Edit</a>
                                            <a href="#" class="text-red-600 hover:text-red-800 ml-4">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">No document purposes found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- DOCUMENT TYPES TAB --}}
                <div id="document-types-tab" class="tab-content hidden">
                    <div class="mb-4">
                        <x-search-bar placeholder="Search by document type..." />
                    </div>

                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-200">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Type ID</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Type Name</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Created Date</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($documentTypes as $type)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $type->id }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $type->name }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $type->created_at->format('M d, Y') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                                            <a href="#" class="text-blue-600 hover:text-blue-800">Edit</a>
                                            <a href="#" class="text-red-600 hover:text-red-800 ml-4">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">No document types found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- EQUIPMENTS TAB --}}
                <div id="equipments-tab" class="tab-content hidden">
                    <div class="mb-4">
                        <x-search-bar placeholder="Search by equipment..." />
                    </div>

                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-200">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Equipment ID</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Equipment Name</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Created Date</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($equipments as $equipment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $equipment->id }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $equipment->equipment_type }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $equipment->created_at->format('M d, Y') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                                            <a href="#" class="text-blue-600 hover:text-blue-800">Edit</a>
                                            <a href="#" class="text-red-600 hover:text-red-800 ml-4">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">No equipments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- FACILITIES TAB --}}
                <div id="facilities-tab" class="tab-content hidden">
                    <div class="mb-4">
                        <x-search-bar placeholder="Search by facility..." />
                    </div>

                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-200">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Facility ID</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Facility Name</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Created Date</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($facilities as $facility)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $facility->id }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $facility->facility_type }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $facility->created_at->format('M d, Y') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                                            <a href="#" class="text-blue-600 hover:text-blue-800">Edit</a>
                                            <a href="#" class="text-red-600 hover:text-red-800 ml-4">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">No facilities found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- HOUSEHOLD ROLES TAB --}}
                <div id="household-roles-tab" class="tab-content hidden">
                    <div class="mb-4">
                        <x-search-bar placeholder="Search by household role..." />
                    </div>

                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-200">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Household Role ID</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Household Role Name</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Created Date</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($householdRoles as $householdRole)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $householdRole->id }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $householdRole->name }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $householdRole->created_at->format('M d, Y') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                                            <a href="#" class="text-blue-600 hover:text-blue-800">Edit</a>
                                            <a href="#" class="text-red-600 hover:text-red-800 ml-4">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">No household roles found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- HOUSEHOLD STRUCTURES TAB --}}
                <div id="household-structures-tab" class="tab-content hidden">
                    <div class="mb-4">
                        <x-search-bar placeholder="Search by household structure..." />
                    </div>

                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-200">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Household Structure ID</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Household Structure Name</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Created Date</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($houseStructures as $householdStructure)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $householdStructure->id }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $householdStructure->house_structure_type }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $householdStructure->created_at->format('M d, Y') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                                            <a href="#" class="text-blue-600 hover:text-blue-800">Edit</a>
                                            <a href="#" class="text-red-600 hover:text-red-800 ml-4">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">No household structures found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PET TYPES TAB --}}
                <div id="pet-types-tab" class="tab-content hidden">
                    <div class="mb-4">
                        <x-search-bar placeholder="Search by pet type..." />
                    </div>

                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-200">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Pet Type ID</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Pet Type Name</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Created Date</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($petTypes as $petType)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $petType->id }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $petType->name }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $petType->created_at->format('M d, Y') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                                            <a href="#" class="text-blue-600 hover:text-blue-800">Edit</a>
                                            <a href="#" class="text-red-600 hover:text-red-800 ml-4">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">No pet types found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- RESIDENCY TYPES TAB --}}
                <div id="residency-types-tab" class="tab-content hidden">
                    <div class="mb-4">
                        <x-search-bar placeholder="Search by residency type..." />
                    </div>

                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-200">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Residency Type ID</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Residency Type Name</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Created Date</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($residencyTypes as $residencyType)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $residencyType->id }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $residencyType->name }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $residencyType->created_at->format('M d, Y') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                                            <a href="#" class="text-blue-600 hover:text-blue-800">Edit</a>
                                            <a href="#" class="text-red-600 hover:text-red-800 ml-4">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">No residency types found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.settings.lookup.modal.modal')

    <script>
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                
                // Hide all tabs
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.add('hidden');
                });
                
                // Remove active styling from all buttons
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('text-blue-600', 'border-blue-600');
                    btn.classList.add('text-slate-500', 'border-transparent');
                });
                
                // Show selected tab
                document.getElementById(tabName + '-tab').classList.remove('hidden');
                
                // Add active styling to clicked button
                this.classList.remove('text-slate-500', 'border-transparent');
                this.classList.add('text-blue-600', 'border-blue-600');
            });
        });
    </script>
</x-app-layout>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>