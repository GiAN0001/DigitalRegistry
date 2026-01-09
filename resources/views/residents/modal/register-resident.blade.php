<x-modal name="register-resident" maxWidth="max-w-[1188px]" focusable>
    
    <div class="p-8 max-h-[85vh] overflow-y-auto" 
         x-data="{ 
           ownershipStatus: '{{ old('household.residency_type_id') }}', 
            familyMembers: @js(old('members', [])), 
            pets: @js(old('pets', [])), 
            
            // Pass errors so we can display them
            serverErrors: @js($errors->toArray()),

            // --- FILTERING LOGIC ---
            allAreas: @js(App\Models\AreaStreet::all()),
            
            // Initialize Purok (Handle Old Input on Validation Fail)
            selectedPurok: '{{ old('household.area_id') ? App\Models\AreaStreet::find(old('household.area_id'))?->purok_name : '' }}',

            get distinctPuroks() {
                const puroks = this.allAreas.map(a => a.purok_name);
                return [...new Set(puroks)].sort();
            },

            get availableStreets() {
                if (this.selectedPurok) {
                    return this.allAreas.filter(area => area.purok_name === this.selectedPurok);
                }
                return this.allAreas;
            },
            // --- END FILTERING LOGIC ---

            addMember() {
                this.familyMembers.push({ 
                    id: Date.now(), 
                    last_name: '', first_name: '', middle_name: '', extension: '', 
                    birthplace: '', birthdate: '', household_role_id: '',
                    sex: '', civil_status: '', nationality: 'Filipino', occupation: '',
                    sector: 'None', vaccination: 'None', comorbidity: '', maintenance: ''
                });
            },
            removeMember(index) {
                this.familyMembers.splice(index, 1);
            },
            addPet() {
                this.pets.push({ id: Date.now(), quantity: 1, pet_type_id: '' });
            },
            removePet(index) {
                this.pets.splice(index, 1);
            },
            getErr(field) {
                return this.serverErrors[field] ? this.serverErrors[field][0] : null;
            }
         }"
         @if($errors->has('head.*') || $errors->has('household.*') || $errors->has('members.*')) 
            x-init="$dispatch('open-modal', 'register-resident')" 
         @endif
    >

        <div class="flex justify-between items-center bg-white z-10 pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Household Registration</h2>
        </div>

        <form method="POST" action="{{ route('residents.store') }}" class="mt-4"> 
            @csrf

            {{-- ---------------------------------------------------- --}}
            {{-- SECTION 1: Head of Family Information --}}
            {{-- ---------------------------------------------------- --}}
            <div class="mb-8 border-b pb-4">
                <h3 class="text-xl font-bold text-blue-700 mb-4">1. Head of Family Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Static fields can still use standard Blade x-input-error --}}
                    <div>
                        <x-input-label>Last Name <span class="text-red-500">*</span></x-input-label>
                        <x-text-input name="head[last_name]" class="w-full mt-1 text-sm" placeholder="Dela Cruz" :value="old('head.last_name')" />
                        <x-input-error :messages="$errors->get('head.last_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label>First Name <span class="text-red-500">*</span></x-input-label>
                        <x-text-input name="head[first_name]" class="w-full mt-1 text-sm" placeholder="Juan" :value="old('head.first_name')" />
                        <x-input-error :messages="$errors->get('head.first_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="Middle Name" />
                        <x-text-input name="head[middle_name]" class="w-full mt-1 text-sm" placeholder="Santos" :value="old('head.middle_name')" />
                        <x-input-error :messages="$errors->get('head.middle_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="Extension" />
                        <x-text-input name="head[extension]" class="w-full mt-1 text-sm" placeholder="Jr, Sr, III" :value="old('head.extension')" />
                        <x-input-error :messages="$errors->get('head.extension')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label>Place of Birth <span class="text-red-500">*</span></x-input-label>
                        <x-text-input name="head[birthplace]" class="w-full mt-1 text-sm" placeholder="City, Province" :value="old('head.birthplace')" />
                        <x-input-error :messages="$errors->get('head.birthplace')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label>Date of Birth <span class="text-red-500">*</span></x-input-label>
                        <x-text-input name="head[birthdate]" type="date" class="w-full mt-1 text-sm" :value="old('head.birthdate')" />
                        <x-input-error :messages="$errors->get('head.birthdate')" class="mt-2" />
                    </div>
                    
                    <div>
                        <x-input-label>Household role <span class="text-red-500">*</span></x-input-label>
                        <x-form-select name="head[household_role_id]" class="w-full h-10 mt-1 text-slate-500"
                            model="App\Models\householdRole" 
                            column="name" 
                            value-column="id"
                            placeholder="Select role"
                            :selected="old('head.household_role_id')"
                        />
                        <x-input-error :messages="$errors->get('head.household_role_id')" class="mt-2" />
                    </div>
                    
                    <div>
                        <x-input-label>Sex <span class="text-red-500">*</span></x-input-label>
                        <select name="head[sex]" class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400">
                            <option value="" selected disabled>Select</option>
                            <option value="Male" @selected(old('head.sex') == 'Male')>Male</option>
                            <option value="Female" @selected(old('head.sex') == 'Female')>Female</option>
                        </select>
                        <x-input-error :messages="$errors->get('head.sex')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label>Civil Status <span class="text-red-500">*</span></x-input-label>
                        <select name="head[civil_status]" class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400">
                            <option value="" selected disabled>Select</option>
                            <option value="Single" @selected(old('head.civil_status') == 'Single')>Single</option>
                            <option value="Married" @selected(old('head.civil_status') == 'Married')>Married</option>
                            <option value="Widowed" @selected(old('head.civil_status') == 'Widowed')>Widowed</option>
                            <option value="Separated" @selected(old('head.civil_status') == 'Separated')>Separated</option>
                        </select>
                        <x-input-error :messages="$errors->get('head.civil_status')" class="mt-2" />
                    </div>
                    
                    <div><x-input-label>Citizenship <span class="text-red-500">*</span></x-input-label><x-text-input name="head[nationality]" class="w-full h-10 mt-1 text-sm" value="Filipino" :value="old('head.nationality')" /><x-input-error :messages="$errors->get('head.nationality')" class="mt-2" /></div>
                    <div><x-input-label value="Occupation" /><x-text-input name="head[occupation]" class="w-full mt-1 text-sm" placeholder="Driver, Teacher, etc." :value="old('head.occupation')" /><x-input-error :messages="$errors->get('head.occupation')" class="mt-2" /></div>

                    <div>
                        <x-input-label value="Sector" />
                        <select name="head[sector]" class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400">
                            <option value="None" @selected(old('head.sector') == 'None')>None</option>
                            <option value="Senior Citizen" @selected(old('head.sector') == 'Senior Citizen')>Senior Citizen</option>
                            <option value="PWD" @selected(old('head.sector') == 'PWD')>PWD</option>
                            <option value="Solo Parent" @selected(old('head.sector') == 'Solo Parent')>Solo Parent</option>
                        </select>
                        <x-input-error :messages="$errors->get('head.sector')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="Vaccinations" />
                        <select name="head[vaccination]" class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400">
                            <option value="None" @selected(old('head.vaccination') == 'None')>None</option>
                            <option value="Private" @selected(old('head.vaccination') == 'Private')>Private</option>
                            <option value="Health Center" @selected(old('head.vaccination') == 'Health Center')>Health Center</option>
                        </select>
                        <x-input-error :messages="$errors->get('head.vaccination')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label value="Comorbidity" />
                        <x-text-input name="head[comorbidity]" class="w-full mt-1 text-sm" placeholder="Hypertension, Diabetes" :value="old('head.comorbidity')" />
                        <x-input-error :messages="$errors->get('head.comorbidity')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label value="Maintenance" />
                        <x-text-input name="head[maintenance]" class="w-full mt-1 text-sm" placeholder="Metformin, Losartan" :value="old('head.maintenance')" />
                        <x-input-error :messages="$errors->get('head.maintenance')" class="mt-2" />
                    </div>
                    
                </div>
            </div>


            {{-- --------------------------------------------- --}}
            {{-- SECTION 2: Household Information --}}
            {{-- --------------------------------------------- --}}
            <div class="mb-8 border-b pb-4">
                <h3 class="text-xl font-bold text-blue-700 mb-4">2. Household Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- 17. House Number --}}
                    <div>
                        <x-input-label>House Number <span class="text-red-500">*</span></x-input-label>
                        <x-text-input name="household[house_number]" class="w-full mt-1 text-sm" placeholder="123-A" :value="old('household.house_number')" />
                        <x-input-error :messages="$errors->get('household.house_number')" class="mt-2" />
                    </div>
                    

                    {{-- 18. Purok (Filter Only - Not sent to DB) --}}
                    <div>
                        <x-input-label>Purok <span class="text-red-500">*</span></x-input-label>
                        <select class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1"
                            x-model="selectedPurok"
                            @change="$refs.streetSelect.value = ''"> {{-- Reset street when purok changes --}}
                            
                            <option value="">Select Purok</option>
                            <template x-for="purok in distinctPuroks" :key="purok">
                                <option :value="purok" x-text="purok" :selected="purok == selectedPurok"></option>
                            </template>
                        </select>
                        {{-- No error message needed here; the error appears on the Street (area_id) field --}}
                    </div>

                    {{-- 19. Street (Actual Area ID sent to DB) --}}
                    <div>
                        <x-input-label>Street <span class="text-red-500">*</span></x-input-label>
                        <select name="household[area_id]" 
                            x-ref="streetSelect"
                            class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1">
                            
                            <option value="" disabled selected>Select Street</option>
                            
                            <template x-for="street in availableStreets" :key="street.id">
                                <option :value="street.id" 
                                        x-text="street.street_name" 
                                        :selected="street.id == '{{ old('household.area_id') }}'">
                                </option>
                            </template>
                        </select>
                        <x-input-error :messages="$errors->get('household.area_id')" class="mt-2" />
                    </div>
                    
                    {{-- 20. House Structure ID --}}
                    <div>
                        <x-input-label>House Structure <span class="text-red-500">*</span></x-input-label>
                        <x-form-select name="household[house_structure_id]" class="w-full h-10 mt-1 text-slate-500"
                            model="App\Models\HouseStructure" 
                            column="house_structure_type" 
                            value-column="id"
                            placeholder="Select house structure"
                            :selected="old('household.house_structure_id')"
                        />
                        <x-input-error :messages="$errors->get('household.house_structure_id')" class="mt-2" />
                    </div>

                    {{-- 21. Ownership Status (Residency Type ID) --}}
                    <div>
                        <x-input-label>Ownership Status <span class="text-red-500">*</span></x-input-label>
                        <x-form-select x-model="ownershipStatus" name="household[residency_type_id]" class="w-full h-10 mt-1 text-slate-500"
                            model="App\Models\ResidencyType" 
                            column="name" 
                            value-column="id"
                            placeholder="Select Ownership Status"
                            :selected="old('household.residency_type_id')"
                        />
                        <x-input-error :messages="$errors->get('household.residency_type_id')" class="mt-2" />
                    </div>

                    {{-- 22. Household Email --}}
                    <div>
                        <x-input-label>Household Email</x-input-label>
                        <x-text-input name="household[email]" type="email" class="w-full mt-1 text-sm" placeholder="family@example.com" :value="old('household.email')" />
                        <x-input-error :messages="$errors->get('household.email')" class="mt-2" />
                    </div>

                    {{-- 23. Household Contact No --}}
                    <div>
                        <x-input-label>Household Contact No <span class="text-red-500">*</span></x-input-label>
                        <x-text-input name="household[contact_number]" class="w-full mt-1 text-sm" placeholder="0917..." :value="old('household.contact_number')" />
                        <x-input-error :messages="$errors->get('household.contact_number')" class="mt-2" />
                    </div>

                    <div x-show="ownershipStatus != '1' && ownershipStatus != ''" class="md:col-span-3 bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                        <h4 class="text-sm font-bold text-gray-600 mb-2">Landlord Details (Required if not Owner)</h4>
                        <div class="grid grid-cols-2 gap-4">
                            {{-- 24. Landlord Name --}}
                            <div>
                                <x-input-label>Landlord Name <span class="text-red-500">*</span></x-input-label>
                                <x-text-input name="household[landlord_name]" class="w-full mt-1" 
                                    x-bind:required="ownershipStatus != '1' && ownershipStatus != ''" 
                                    :value="old('household.landlord_name')"
                                />
                                <x-input-error :messages="$errors->get('household.landlord_name')" class="mt-2" />
                            </div>
                            {{-- 25. Landlord Contact --}}
                            <div>
                                <x-input-label>Landlord Contact <span class="text-red-500">*</span></x-input-label>
                                <x-text-input name="household[landlord_contact]" class="w-full mt-1" 
                                    x-bind:required="ownershipStatus != '1' && ownershipStatus != ''" 
                                    :value="old('household.landlord_contact')"
                                />
                                <x-input-error :messages="$errors->get('household.landlord_contact')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- --------------------------------------------- --}}
            {{-- SECTION 3: Family Members --}}
            {{-- --------------------------------------------- --}}
            <div class="mb-8 border-b pb-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-blue-700">3. Family Members (If Applicable)</h3>
                    <button type="button" @click="addMember()" class="text-sm text-blue-600 font-bold hover:underline">
                        + Add Member
                    </button>
                </div>

                <div x-show="familyMembers.length === 0" class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300 text-gray-500">
                    No family members added yet. Click "+ Add Member" if you have any.
                </div>

                <div class="space-y-3">
                    <template x-for="(member, index) in familyMembers" :key="member.id">
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div class="flex gap-3 items-start">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 flex-1">
                                    
                                    <div><x-input-label>Last Name <span class="text-red-500">*</span></x-input-label><x-text-input x-bind:name="`members[${index}][last_name]`" class="w-full mt-1 text-sm" placeholder="Dela Cruz" x-bind:value="member.last_name ?? ''" /><p x-show="getErr(`members.${index}.last_name`)" x-text="getErr(`members.${index}.last_name`)" class="text-sm text-red-600 space-y-1 mt-2"></p></div>
                                    <div><x-input-label>First Name <span class="text-red-500">*</span></x-input-label><x-text-input x-bind:name="`members[${index}][first_name]`" class="w-full mt-1 text-sm" placeholder="Juan" x-bind:value="member.first_name ?? ''" /><p x-show="getErr(`members.${index}.first_name`)" x-text="getErr(`members.${index}.first_name`)" class="text-sm text-red-600 space-y-1 mt-2"></p></div>
                                    <div><x-input-label value="Middle Name" /><x-text-input x-bind:name="`members[${index}][middle_name]`" class="w-full mt-1 text-sm" placeholder="Santos" x-bind:value="member.middle_name ?? ''" /><p x-show="getErr(`members.${index}.middle_name`)" x-text="getErr(`members.${index}.middle_name`)" class="text-sm text-red-600 space-y-1 mt-2"></p></div>
                                    <div><x-input-label value="Extension" /><x-text-input x-bind:name="`members[${index}][extension]`" class="w-full mt-1 text-sm" placeholder="Jr, Sr, III" x-bind:value="member.extension ?? ''" /><p x-show="getErr(`members.${index}.extension`)" x-text="getErr(`members.${index}.extension`)" class="text-sm text-red-600 space-y-1 mt-2"></p></div>

                                    <div><x-input-label>Place of Birth <span class="text-red-500">*</span></x-input-label><x-text-input x-bind:name="`members[${index}][birthplace]`" class="w-full mt-1 text-sm" placeholder="City, Province" x-bind:value="member.birthplace ?? ''" /><p x-show="getErr(`members.${index}.birthplace`)" x-text="getErr(`members.${index}.birthplace`)" class="text-sm text-red-600 space-y-1 mt-2"></p></div>
                                    <div><x-input-label>Date of Birth <span class="text-red-500">*</span></x-input-label><x-text-input x-bind:name="`members[${index}][birthdate]`" type="date" class="w-full mt-1 text-sm" x-bind:value="member.birthdate ?? ''" /><p x-show="getErr(`members.${index}.birthdate`)" x-text="getErr(`members.${index}.birthdate`)" class="text-sm text-red-600 space-y-1 mt-2"></p></div>
                                    
                                    
                                    <div>
                                        <x-input-label>Household role <span class="text-red-500">*</span></x-input-label>
                                        <x-form-select x-bind:name="`members[${index}][household_role_id]`" class="w-full h-10 mt-1 text-slate-500"
                                            model="App\Models\householdRole" 
                                            column="name" 
                                            value-column="id"
                                            placeholder="Select role"
                                            x-bind:selected="member.household_role_id"
                                        />
                                        <p x-show="getErr(`members.${index}.household_role_id`)" x-text="getErr(`members.${index}.household_role_id`)" class="text-sm text-red-600 space-y-1 mt-2"></p>
                                    </div>
                                    
                                    <div>
                                        <x-input-label>Sex <span class="text-red-500">*</span></x-input-label>
                                        <select x-bind:name="`members[${index}][sex]`" class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400">
                                            <option value="" selected disabled>Select</option>
                                            <option value="Male" x-bind:selected="member.sex == 'Male'">Male</option>
                                            <option value="Female" x-bind:selected="member.sex == 'Female'">Female</option>
                                        </select>
                                        <p x-show="getErr(`members.${index}.sex`)" x-text="getErr(`members.${index}.sex`)" class="text-sm text-red-600 space-y-1 mt-2"></p>
                                    </div>

                                    <div>
                                        <x-input-label>Civil Status <span class="text-red-500">*</span></x-input-label>
                                        <select x-bind:name="`members[${index}][civil_status]`" class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400">
                                            <option value="" selected disabled>Select</option>
                                            <option value="Single" x-bind:selected="member.civil_status == 'Single'">Single</option>
                                            <option value="Married" x-bind:selected="member.civil_status == 'Married'">Married</option>
                                            <option value="Widowed" x-bind:selected="member.civil_status == 'Widowed'">Widowed</option>
                                            <option value="Separated" x-bind:selected="member.civil_status == 'Separated'">Separated</option>
                                        </select>
                                        <p x-show="getErr(`members.${index}.civil_status`)" x-text="getErr(`members.${index}.civil_status`)" class="text-sm text-red-600 space-y-1 mt-2"></p>
                                    </div>
                                    <div><x-input-label>Citizenship <span class="text-red-500">*</span></x-input-label><x-text-input x-bind:name="`members[${index}][nationality]`" class="w-full mt-1 text-sm" value="Filipino" x-bind:value="member.nationality ?? 'Filipino'" /><p x-show="getErr(`members.${index}.nationality`)" x-text="getErr(`members.${index}.nationality`)" class="text-sm text-red-600 space-y-1 mt-2"></p></div>
                                    <div class="md:col-span-1"><x-input-label value="Occupation" /><x-text-input x-bind:name="`members[${index}][occupation]`" class="w-full mt-1 text-sm" placeholder="Driver, Teacher, etc." x-bind:value="member.occupation ?? ''" /><p x-show="getErr(`members.${index}.occupation`)" x-text="getErr(`members.${index}.occupation`)" class="text-sm text-red-600 space-y-1 mt-2"></p></div>
                                    
                                    <div>
                                        <x-input-label value="Sector" />
                                        <select x-bind:name="`members[${index}][sector]`" class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400">
                                            <option value="None" x-bind:selected="member.sector == 'None'">None</option>
                                            <option value="Senior Citizen" x-bind:selected="member.sector == 'Senior Citizen'">Senior Citizen</option>
                                            <option value="PWD" x-bind:selected="member.sector == 'PWD'">PWD</option>
                                            <option value="Solo Parent" x-bind:selected="member.sector == 'Solo Parent'">Solo Parent</option>
                                        </select>
                                        <p x-show="getErr(`members.${index}.sector`)" x-text="getErr(`members.${index}.sector`)" class="text-sm text-red-600 space-y-1 mt-2"></p>
                                    </div>
                                    <div>
                                        <x-input-label value="Vaccinations" />
                                        <select x-bind:name="`members[${index}][vaccination]`" class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400">
                                            <option value="None" x-bind:selected="member.vaccination == 'None'">None</option>
                                            <option value="Private" x-bind:selected="member.vaccination == 'Private'">Private</option>
                                            <option value="Health Center" x-bind:selected="member.vaccination == 'Health Center'">Health Center</option>
                                        </select>
                                        <p x-show="getErr(`members.${index}.vaccination`)" x-text="getErr(`members.${index}.vaccination`)" class="text-sm text-red-600 space-y-1 mt-2"></p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <x-input-label value="Comorbidity" />
                                        <x-text-input x-bind:name="`members[${index}][comorbidity]`" class="w-full mt-1 text-sm" placeholder="Hypertension, Diabetes" x-bind:value="member.comorbidity ?? ''" />
                                        <p x-show="getErr(`members.${index}.comorbidity`)" x-text="getErr(`members.${index}.comorbidity`)" class="text-sm text-red-600 space-y-1 mt-2"></p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <x-input-label value="Maintenance" />
                                        <x-text-input x-bind:name="`members[${index}][maintenance]`" class="w-full mt-1 text-sm" placeholder="Metformin, Losartan" x-bind:value="member.maintenance ?? ''" />
                                        <p x-show="getErr(`members.${index}.maintenance`)" x-text="getErr(`members.${index}.maintenance`)" class="text-sm text-red-600 space-y-1 mt-2"></p>
                                    </div>
                                    
                                </div>
                                <button type="button" @click="removeMember(index)" class="mt-6 text-red-500 hover:text-red-700">
                                    <x-lucide-trash-2 class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>


            {{-- --------------------------------------------- --}}
            {{-- SECTION 4: Household Pets --}}
            {{-- --------------------------------------------- --}}
            <div class="mb-8 border-b pb-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-blue-700">4. Household Pets (If Applicable)</h3>
                    <button type="button" @click="addPet()" class="text-sm text-blue-600 font-bold hover:underline">
                        + Add Pet
                    </button>
                </div>

                <div x-show="pets.length === 0" class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300 text-gray-500">
                    No pets? You can skip this step or click "+ Add Pet".
                </div>

                <div class="space-y-3">
                    <template x-for="(pet, index) in pets" :key="pet.id">
                        <div class="flex gap-3 items-start bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div class="flex-1 grid grid-cols-2 gap-3">
                                <div>
                                    <x-input-label value="Pet Type"/>
                                    <x-form-select x-bind:name="`pets[${index}][pet_type_id]`" class="w-full h-10 mt-1 text-sm text-slate-500"
                                        model="App\Models\PetType" 
                                        column="name" 
                                        value-column="id"
                                        placeholder="Select pet type"
                                        x-bind:selected="pet.pet_type_id"
                                    />
                                    <p x-show="getErr(`pets.${index}.pet_type_id`)" x-text="getErr(`pets.${index}.pet_type_id`)" class="text-sm text-red-600 space-y-1 mt-2"></p>
                                </div>
                                <div>
                                    <x-input-label value="Quantity" />
                                    <x-text-input x-bind:name="`pets[${index}][quantity]`" type="number" class="w-full h-10 mt-1 text-sm text-slate-500" x-model="pet.quantity" x-bind:value="pet.quantity" />
                                    <p x-show="getErr(`pets.${index}.quantity`)" x-text="getErr(`pets.${index}.quantity`)" class="text-sm text-red-600 space-y-1 mt-2"></p>
                                </div>
                            </div>
                            <button type="button" @click="removePet(index)" class="mt-6 text-red-500 hover:text-red-700">
                                <x-lucide-trash-2 class="w-5 h-5" />
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- --------------------------------------------- --}}
            {{-- FINAL SUBMISSION BUTTON (Sticky Footer) --}}
            {{-- --------------------------------------------- --}}
            <div class="sticky bottom-0 bg-white pt-4 border-t border-gray-200 flex justify-end">
                <x-secondary-button x-on:click.prevent="$dispatch('close')">Cancel</x-secondary-button>
                
                <x-primary-button class="ms-3" type="submit">
                    {{ __('Finish & Save') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>