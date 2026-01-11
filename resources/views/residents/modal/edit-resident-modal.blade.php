<x-modal name="edit-resident-modal" maxWidth="max-w-[1188px]" focusable>
    <div
       x-data="{
            {{-- Seed the ID from session if validation failed to keep the form action URL valid --}}
            resident: {
                id: '{{ old('resident_id', '') }}'
            },
            loading: false,
            updateUrlBase: '{{ url('residents') }}',
            
            async fetchResident(id) {
                this.loading = true;
                try {
                    const response = await fetch(`${this.updateUrlBase}/${id}`);
                    const data = await response.json();
                    
                    this.resident = {
                        ...data,
                        birthplace: data.demographic?.birthplace || '',
                        birthdate: data.demographic?.birthdate || '',
                        sex: data.demographic?.sex || '',
                        civil_status: data.demographic?.civil_status || '',
                        nationality: data.demographic?.nationality || 'Filipino',
                        occupation: data.demographic?.occupation || '',
                        sector: data.health_information?.sector || 'None',
                        vaccination: data.health_information?.vaccination || 'None',
                        comorbidity: data.health_information?.comorbidity || '',
                        maintenance: data.health_information?.maintenance || ''
                    };
                } catch (error) {
                    console.error('Failed to fetch resident:', error);
                } finally {
                    this.loading = false;
                }
            }
        }"
        {{-- AUTO-OPEN: Triggers if any 'resident' validation errors exist in the session --}}
        x-init="
            @if($errors->hasAny(['resident.*', 'resident_id']))
                $dispatch('open-modal', 'edit-resident-modal');
            @endif
        "
        x-on:fetch-resident-data.window="fetchResident($event.detail)"
        class="p-8 relative"
    >
        {{-- Loading Spinner Overlay --}}
        <div x-show="loading" class="absolute inset-0 bg-white/80 flex items-center justify-center z-50">
            <x-lucide-loader-2 class="w-10 h-10 animate-spin text-blue-600" />
        </div>
        
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Edit Resident Profile
            </h2>
            <button x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">
                <x-lucide-x class="w-6 h-6" />
            </button>
        </div>

        <form method="POST" x-bind:action="updateUrlBase + '/' + resident.id" x-show="!loading">
            @csrf
            @method('PUT')

            {{-- Store the ID in session so the form knows which URL to use after a reload --}}
            <input type="hidden" name="resident_id" x-model="resident.id">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <x-input-label>First Name <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        name="resident[first_name]" 
                        x-model="resident.first_name" 
                        value="{{ old('resident.first_name') }}"
                        x-init="if($el.value) resident.first_name = $el.value"
                        class="w-full mt-1"  
                    />
                    <x-input-error :messages="$errors->get('resident.first_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label>Middle Name</x-input-label>
                    <x-text-input 
                        name="resident[middle_name]" 
                        x-model="resident.middle_name" 
                        value="{{ old('resident.middle_name') }}"
                        x-init="if($el.value) resident.middle_name = $el.value"
                        class="w-full mt-1" 
                    />
                    <x-input-error :messages="$errors->get('resident.middle_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label>Last Name <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        name="resident[last_name]" 
                        x-model="resident.last_name" 
                        value="{{ old('resident.last_name') }}"
                        x-init="if($el.value) resident.last_name = $el.value"
                        class="w-full mt-1"  
                    />
                    <x-input-error :messages="$errors->get('resident.last_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label>Extension</x-input-label>
                    <x-text-input 
                        name="resident[extension]" 
                        x-model="resident.extension" 
                        value="{{ old('resident.extension') }}"
                        x-init="if($el.value) resident.extension = $el.value"
                        placeholder="Jr, Sr, III" class="w-full mt-1" 
                    />
                    <x-input-error :messages="$errors->get('resident.extension')" class="mt-2" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label>Place of Birth <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        name="resident[birthplace]" 
                        x-model="resident.birthplace" 
                        value="{{ old('resident.birthplace') }}"
                        x-init="if($el.value) resident.birthplace = $el.value"
                        class="w-full mt-1"  
                    />
                    <x-input-error :messages="$errors->get('resident.birthplace')" class="mt-2" />
                </div>
                <div>
                    <x-input-label>Date of Birth <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        type="date" 
                        name="resident[birthdate]" 
                        x-model="resident.birthdate" 
                        value="{{ old('resident.birthdate') }}"
                        x-init="if($el.value) resident.birthdate = $el.value"
                        class="w-full mt-1"  
                    />
                    <x-input-error :messages="$errors->get('resident.birthdate')" class="mt-2" />
                </div>
                <div>
                    <x-input-label>Sex <span class="text-red-500">*</span></x-input-label>
                    <select 
                        name="resident[sex]" 
                        x-model="resident.sex" 
                        x-init="let oldVal = '{{ old('resident.sex') }}'; if(oldVal) resident.sex = oldVal"
                        class="w-full mt-1 border-gray-300 rounded-md shadow-sm"
                    >
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                    <x-input-error :messages="$errors->get('resident.sex')" class="mt-2" />
                </div>

                <div>
                    <x-input-label>Civil Status <span class="text-red-500">*</span></x-input-label>
                    <select 
                        name="resident[civil_status]" 
                        x-model="resident.civil_status" 
                        x-init="let oldVal = '{{ old('resident.civil_status') }}'; if(oldVal) resident.civil_status = oldVal"
                        class="w-full mt-1 border-gray-300 rounded-md shadow-sm"
                    >
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Widowed">Widowed</option>
                        <option value="Separated">Separated</option>
                    </select>
                    <x-input-error :messages="$errors->get('resident.civil_status')" class="mt-2" />
                </div>
                <div>
                    <x-input-label>Household Role <span class="text-red-500">*</span></x-input-label>
                    <select 
                        name="resident[household_role_id]" 
                        x-model="resident.household_role_id" 
                        x-init="let oldVal = '{{ old('resident.household_role_id') }}'; if(oldVal) resident.household_role_id = oldVal"
                        class="w-full mt-1 border-gray-300 rounded-md shadow-sm"
                    >
                        @foreach(\App\Models\houseHoldRole::all() as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('resident.household_role_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label>Citizenship <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        name="resident[nationality]" 
                        x-model="resident.nationality" 
                        value="{{ old('resident.nationality') }}"
                        x-init="if($el.value) resident.nationality = $el.value"
                        class="w-full mt-1"  
                    />
                    <x-input-error :messages="$errors->get('resident.nationality')" class="mt-2" />
                </div>
                <div>
                    <x-input-label>Occupation</x-input-label>
                    <x-text-input 
                        name="resident[occupation]" 
                        x-model="resident.occupation" 
                        value="{{ old('resident.occupation') }}"
                        x-init="if($el.value) resident.occupation = $el.value"
                        class="w-full mt-1" 
                    />
                    <x-input-error :messages="$errors->get('resident.occupation')" class="mt-2" />
                </div>

                <div>
                    <x-input-label>Sector</x-input-label>
                    <select 
                        name="resident[sector]" 
                        x-model="resident.sector" 
                        x-init="let oldVal = '{{ old('resident.sector') }}'; if(oldVal) resident.sector = oldVal"
                        class="w-full mt-1 border-gray-300 rounded-md shadow-sm"
                    >
                        <option value="None">None</option>
                        <option value="Senior Citizen">Senior Citizen</option>
                        <option value="PWD">PWD</option>
                        <option value="Solo Parent">Solo Parent</option>
                    </select>
                    <x-input-error :messages="$errors->get('resident.sector')" class="mt-2" />
                </div>
                <div>
                    <x-input-label>Vaccinations</x-input-label>
                    <select 
                        name="resident[vaccination]" 
                        x-model="resident.vaccination" 
                        x-init="let oldVal = '{{ old('resident.vaccination') }}'; if(oldVal) resident.vaccination = oldVal"
                        class="w-full mt-1 border-gray-300 rounded-md shadow-sm"
                    >
                        <option value="None">None</option>
                        <option value="Private">Private</option>
                        <option value="Health Center">Health Center</option>
                    </select>
                    <x-input-error :messages="$errors->get('resident.vaccination')" class="mt-2" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label>Comorbidity</x-input-label>
                    <x-text-input 
                        name="resident[comorbidity]" 
                        x-model="resident.comorbidity" 
                        value="{{ old('resident.comorbidity') }}"
                        x-init="if($el.value) resident.comorbidity = $el.value"
                        placeholder="e.g. Hypertension" class="w-full mt-1" 
                    />
                    <x-input-error :messages="$errors->get('resident.comorbidity')" class="mt-2" />
                </div>
                <div class="md:col-span-4">
                    <x-input-label>Maintenance Medication</x-input-label>
                    <x-text-input 
                        name="resident[maintenance]" 
                        x-model="resident.maintenance" 
                        value="{{ old('resident.maintenance') }}"
                        x-init="if($el.value) resident.maintenance = $el.value"
                        class="w-full mt-1" 
                    />
                    <x-input-error :messages="$errors->get('resident.maintenance')" class="mt-2" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t pt-6">
                <x-secondary-button x-on:click="$dispatch('close')" type="button">Cancel</x-secondary-button>
                <x-primary-button type="submit">Save Profile Changes</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>