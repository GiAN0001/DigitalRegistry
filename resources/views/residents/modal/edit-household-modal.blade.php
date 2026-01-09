<x-modal name="edit-household-modal" maxWidth="max-w-6xl" focusable>
    <div
        x-data="{
            // 1. Initialize household: Use OLD input if validation failed, otherwise empty
            household: @if($errors->hasAny(['house_number', 'area_id', 'residency_type_id', 'contact_number'])) @js(old()) @else {} @endif,
            
            // 2. Initialize Status: Use OLD input if available
            ownershipStatus: '{{ old('residency_type_id') }}',
            
            // 3. Purok Filter
            selectedPurok: '',

            // 4. Data Sources
            allAreas: @js(App\Models\AreaStreet::all()),
            
            get availableStreets() {
                if (this.selectedPurok) {
                    return this.allAreas.filter(area => area.purok_name === this.selectedPurok);
                }
                return this.allAreas;
            },

            get distinctPuroks() {
                const puroks = this.allAreas.map(a => a.purok_name);
                return [...new Set(puroks)].sort();
            },

            updateUrlBase: '{{ url('households') }}'
        }"
        {{-- 5. AUTO-OPEN ON ERROR: Triggers only if Edit-specific errors exist --}}
        x-init="
            @if($errors->hasAny(['house_number', 'area_id', 'residency_type_id', 'contact_number', 'landlord_name']))
                $dispatch('open-modal', 'edit-household-modal');
                
                // Restore the Purok Filter based on the failed Area ID
                $nextTick(() => {
                    if (household.area_id) {
                        const area = allAreas.find(a => a.id == household.area_id);
                        if (area) selectedPurok = area.purok_name;
                    }
                });
            @endif
        "
        {{-- 6. NORMAL OPEN: Triggered by clicking 'Edit' in the table --}}
        x-on:edit-household-data.window="
            household = $event.detail;
            ownershipStatus = household.residency_type_id; 

            // Initialize Purok filter from existing data
            if (household.area_street) {
                selectedPurok = household.area_street.purok_name;
            }
        "
        class="p-8"
    >
        <div class="flex justify-between items-center bg-white z-10 pb-4 border-b mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Edit Household Information</h2>
            <button @click="$dispatch('close')" class="text-gray-500 hover:text-gray-700">
                <x-lucide-x class="w-6 h-6" />
            </button>
        </div>

        <form method="POST" x-bind:action="updateUrlBase + '/' + household.id">
            @csrf
            @method('PUT')
            
            {{-- CRITICAL: Hidden ID Input to preserve ID during validation failures --}}
            <input type="hidden" name="id" x-model="household.id">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                
                {{-- House Number --}}
                <div>
                    <x-input-label>House Number <span class="text-red-500">*</span></x-input-label>
                    <x-text-input name="house_number" class="w-full mt-1 text-sm" x-model="household.house_number" />
                    <x-input-error :messages="$errors->get('house_number')" class="mt-2" />
                </div>

                {{-- PUROK (Filter Only) --}}
                <div>
                    <x-input-label>Purok <span class="text-red-500">*</span></x-input-label>
                    <select class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm"
                            x-model="selectedPurok">
                        <option value="">All Puroks</option>
                        <template x-for="purok in distinctPuroks" :key="purok">
                            <option :value="purok" x-text="purok"></option>
                        </template>
                    </select>
                </div>

                {{-- STREET --}}
                <div>
                    <x-input-label>Street <span class="text-red-500">*</span></x-input-label>
                    <select name="area_id" 
                            class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm"
                            x-model="household.area_id">
                        <option value="" disabled>Select Street</option>
                        <template x-for="street in availableStreets" :key="street.id">
                            <option :value="street.id" 
                                    x-text="street.street_name" 
                                    :selected="street.id == household.area_id">
                            </option>
                        </template>
                    </select>
                    <x-input-error :messages="$errors->get('area_id')" class="mt-2" />
                </div>

                {{-- House Structure --}}
                <div>
                    <x-input-label>House Structure <span class="text-red-500">*</span></x-input-label>
                    <select name="house_structure_id" class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm" 
                            x-model="household.house_structure_id">
                        <option value="" disabled>Select Structure</option>
                        @foreach(App\Models\HouseStructure::all() as $struct)
                            <option value="{{ $struct->id }}">{{ $struct->house_structure_type }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('house_structure_id')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                {{-- Ownership Status --}}
                <div>
                    <x-input-label>Ownership Status <span class="text-red-500">*</span></x-input-label>
                    <select name="residency_type_id" class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm" 
                            x-model="ownershipStatus">
                        <option value="" disabled>Select Status</option>
                        @foreach(App\Models\ResidencyType::all() as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('residency_type_id')" class="mt-2" />
                </div>

                {{-- Email --}}
                <div>
                    <x-input-label>Household Email</x-input-label>
                    <x-text-input name="email" type="email" class="w-full mt-1 text-sm" x-model="household.email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- Contact --}}
                <div class="md:col-span-2">
                    <x-input-label>Household Contact No <span class="text-red-500">*</span></x-input-label>
                    <x-text-input name="contact_number" class="w-full mt-1 text-sm" x-model="household.contact_number" />
                    <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
                </div>
            </div>

            {{-- Landlord Details --}}
            <div x-show="ownershipStatus != '1'" class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                <h4 class="text-sm font-bold text-gray-600 mb-2">Landlord Details (Required if not Owner)</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label>Landlord Name <span class="text-red-500">*</span></x-input-label>
                        <x-text-input name="landlord_name" class="w-full mt-1" 
                            x-bind:required="ownershipStatus != '1'" 
                            x-model="household.landlord_name" />
                        <x-input-error :messages="$errors->get('landlord_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label>Landlord Contact <span class="text-red-500">*</span></x-input-label>
                        <x-text-input name="landlord_contact" class="w-full mt-1" 
                            x-bind:required="ownershipStatus != '1'" 
                            x-model="household.landlord_contact" />
                        <x-input-error :messages="$errors->get('landlord_contact')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')" type="button">Cancel</x-secondary-button>
                <x-primary-button class="ms-3" type="submit">Save Changes</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>