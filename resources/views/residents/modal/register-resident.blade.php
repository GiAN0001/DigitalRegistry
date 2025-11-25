<x-modal name="register-resident" maxWidth="max-w-[1188px]" focusable>
    
    <div class="p-8" 
         x-data="{ 
            step: 1, 
            
            // We use this to show/hide the Landlord field in Step 2
            ownershipStatus: '', 

            // Arrays to hold dynamic rows
            familyMembers: [],
            pets: [],

            // Functions to add/remove rows
            addMember() {
                this.familyMembers.push({ 
                    id: Date.now(), // Temporary ID for key
                    first_name: '', 
                    last_name: '', 
                    relationship: '' 
                });
            },
            removeMember(index) {
                this.familyMembers.splice(index, 1);
            },
            addPet() {
                this.pets.push({ 
                    id: Date.now(), 
                    type: '', 
                    quantity: 1 
                });
            },
            removePet(index) {
                this.pets.splice(index, 1);
            }
         }">

        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Household Registration</h2>
        </div>

        <div>
            <template x-if="step === 1"><x-step-progression :current-step="1" /></template>
            <template x-if="step === 2"><x-step-progression :current-step="2" /></template>
            <template x-if="step === 3"><x-step-progression :current-step="3" /></template>
            <template x-if="step === 4"><x-step-progression :current-step="4" /></template>
        </div>

        <form> <div x-show="step === 1" x-transition:enter="transition ease-out duration-300">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Your Information (Head of Family)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <x-input-label>
                            Last Name <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input class="w-full mt-1 border-blue-700" placeholder="Dela Cruz" required />
                    </div>
                    <div>
                        <x-input-label>
                            First Name <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input class="w-full mt-1 border-blue-700" placeholder="Juan" required />
                    </div>
                    <div>
                        <x-input-label value="Middle Name" />
                        <x-text-input class="w-full mt-1 border-blue-700" placeholder="Santos" />
                    </div>
                    <div>
                        <x-input-label value="Extension" />
                        <x-text-input class="w-full mt-1 border-blue-700" placeholder="Jr, Sr, III" />
                    </div>

                    <div>
                        <x-input-label>
                            Place of Birth <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input class="w-full mt-1 border-blue-700" placeholder="City, Province" required />
                    </div>
                    <div>
                        <x-input-label>
                            Date of Birth <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input type="date" class="w-full mt-1 border-blue-700" required />
                    </div>
                    <div>
                        <x-input-label>
                            Age <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input class="w-full mt-1 border-blue-700" placeholder="18" required />
                    </div>
                    <div>
                        <x-input-label>
                            Sex <span class="text-red-500">*</span>
                        </x-input-label>
                        <select class="w-full mt-1 border-blue-700 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700" required>
                            <option value="" selected disabled>Select</option>
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label>
                            Civil Status <span class="text-red-500">*</span>
                        </x-input-label>
                        <select class="w-full mt-1 border-blue-700 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700" required>
                            <option value="" selected disabled>Select</option>
                            <option>Single</option>
                            <option>Married</option>
                            <option>Widowed</option>
                            <option>Separated</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label>
                            Citizenship <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input class="w-full mt-1 border-blue-700" value="Filipino" required />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label value="Occupation" />
                        <x-text-input class="w-full mt-1 border-blue-700" placeholder="Driver, Teacher, etc." />
                    </div>

                    <div>
                        <x-input-label value="Sector" />
                        <select class="w-full mt-1 border-blue-700 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700">
                            <option>None</option>
                            <option>Senior Citizen</option>
                            <option>PWD</option>
                            <option>Solo Parent</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <x-input-label value="Health Notes (Comorbidity / Maintenance)" />
                        <x-text-input class="w-full mt-1 border-blue-700" placeholder="Hypertension, Diabetes / Metformin, Losartan" />
                    </div>
                </div>
            </div>

            <div x-show="step === 2" style="display: none;" x-transition:enter="transition ease-out duration-300">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Household Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label>
                            House Number <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input class="w-full mt-1 border-blue-700" placeholder="123-A" required />
                    </div>
                    <div>
                        <x-input-label>
                            Purok <span class="text-red-500">*</span>
                        </x-input-label>
                        <select name="purok_name" class="w-full mt-1 border-blue-700 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700">
                            <option value="" selected disabled>Select</option>
                            @foreach($purok as $purok)
                                <option value="{{ $purok->id }}" {{ request('purok_name') == $purok->id ? 'selected' : '' }}>
                                    {{ $purok->purok_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label>
                            Street <span class="text-red-500">*</span>
                        </x-input-label>
                        <select name="street_name" class="w-full mt-1 border-blue-700 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700">
                            <option value="" selected disabled>Select</option>
                            @foreach($streets as $street)
                                <option value="{{ $street->id }}" {{ request('street_name') == $street->id ? 'selected' : '' }}>
                                    {{ $street->street_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label>
                            House Structure <span class="text-red-500">*</span>
                        </x-input-label>
                        <select class="w-full mt-1 border-blue-700 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option>Concrete</option>
                            <option>Wood</option>
                            <option>Semi-Concrete</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label>
                            Ownership Status <span class="text-red-500">*</span>
                        </x-input-label>
                        <select x-model="ownershipStatus" class="w-full mt-1 border-blue-700 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="owner">Owner</option>
                            <option value="renter">Renter / Tenant</option>
                            <option value="sharer">Sharer</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label>
                            Household Email <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input type="email" class="w-full mt-1 border-blue-700" placeholder="family@example.com" />
                    </div>
                    <div>
                        <x-input-label>
                            Household Contact No <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input class="w-full mt-1 border-blue-700" placeholder="0917..." />
                    </div>

                    <div x-show="ownershipStatus === 'renter'" class="md:col-span-3 bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                        <h4 class="text-sm font-bold text-gray-600 mb-2">Landlord Details</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label>
                                    Landlord Name <span class="text-red-500">*</span>
                                </x-input-label>
                                <x-text-input class="w-full mt-1" />
                            </div>
                            <div>
                                <x-input-label>
                                    Landlord Contact <span class="text-red-500">*</span>
                                </x-input-label>
                                <x-text-input class="w-full mt-1" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="step === 3" style="display: none;" x-transition:enter="transition ease-out duration-300">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Family Members</h3>
                    <button type="button" @click="addMember()" class="text-sm text-blue-600 font-bold hover:underline">
                        + Add Member
                    </button>
                </div>

                <div x-show="familyMembers.length === 0" class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300 text-gray-500">
                    No family members added yet. Click "+ Add Member" if you have any.
                </div>

                <div class="space-y-3">
                    <template x-for="(member, index) in familyMembers" :key="member.id">
                        <div class="flex gap-3 items-start bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-1">
                                    <x-input-label>
                                        Last Name <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input class="w-full mt-1" placeholder="Dela Cruz" required />
                                </div>
                                <div class="md:col-span-1">
                                    <x-input-label>
                                        First Name <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input class="w-full mt-1" placeholder="Juan" required />
                                </div>
                                <div class="md:col-span-1">
                                    <x-input-label value="Middle Name" />
                                    <x-text-input class="w-full mt-1" placeholder="Santos" />
                                </div>
                                <div class="md:col-span-1">
                                    <x-input-label value="Extension" />
                                    <x-text-input class="w-full mt-1" placeholder="Jr, Sr, III" />
                                </div>

                                <div class="md:col-span-1">
                                    <x-input-label>
                                        Place of Birth <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input class="w-full mt-1" placeholder="City, Province" required />
                                </div>
                                <div class="md:col-span-1">
                                    <x-input-label>
                                        Date of Birth <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input type="date" class="w-full mt-1" required />
                                </div>
                                <div>
                                    <x-input-label>
                                        Age <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input class="w-full mt-1" placeholder="18" required />
                                </div>
                                <div>
                                    <x-input-label>
                                        Sex <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <select class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                        <option value="" selected disabled>Select</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>

                                <div>
                                    <x-input-label>
                                        Civil Status <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <select class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                        <option value="" selected disabled>Select</option>
                                        <option>Single</option>
                                        <option>Married</option>
                                        <option>Widowed</option>
                                        <option>Separated</option>
                                    </select>
                                </div>
                                <div class="md:col-span-1">
                                    <x-input-label>
                                        Citizenship <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input class="w-full mt-1" value="Filipino" required />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label value="Occupation" />
                                    <x-text-input class="w-full mt-1" placeholder="Driver, Teacher, etc." />
                                </div>

                                <div class="md:col-span-1">
                                    <x-input-label value="Sector" />
                                    <select class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option>None</option>
                                        <option>Senior Citizen</option>
                                        <option>PWD</option>
                                        <option>Solo Parent</option>
                                    </select>
                                </div>
                                <div class="md:col-span-3">
                                    <x-input-label value="Health Notes (Comorbidity / Maintenance)" />
                                    <x-text-input class="w-full mt-1" placeholder="Hypertension, Diabetes / Metformin, Losartan" />
                                </div>
                            </div>
                            <button type="button" @click="removeMember(index)" class="mt-6 text-red-500 hover:text-red-700">
                                <x-lucide-trash-2 class="w-5 h-5" />
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div x-show="step === 4" style="display: none;" x-transition:enter="transition ease-out duration-300">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Household Pets</h3>
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
                                    <x-input-label value="Pet Type" class="text-xs" />
                                    <select class="w-full mt-1 text-sm border-gray-300 rounded-md" x-model="pet.type">
                                        <option>Dog</option>
                                        <option>Cat</option>
                                        <option>Bird</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label value="Quantity" class="text-xs" />
                                    <x-text-input type="number" class="w-full mt-1 text-sm" x-model="pet.quantity" />
                                </div>
                            </div>
                            <button type="button" @click="removePet(index)" class="mt-6 text-red-500 hover:text-red-700">
                                <x-lucide-trash-2 class="w-5 h-5" />
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-gray-200 flex justify-between items-center">
                <button type="button" 
                        x-on:click="step > 1 ? step-- : $dispatch('close')" 
                        class="text-gray-600 hover:text-gray-900 font-medium text-sm">
                    <span x-text="step > 1 ? '← Back' : 'Cancel'"></span>
                </button>

                <div class="flex space-x-2">
                    <template x-for="i in 4">
                        <div class="w-2 h-2 rounded-full" 
                             :class="step === i ? 'bg-blue-600' : 'bg-gray-300'"></div>
                    </template>
                </div>

                <div>
                    <button type="button" x-show="step < 4" x-on:click="step++"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                        Next Step →
                    </button>
                    
                    <button type="submit" x-show="step === 4" style="display: none;"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition shadow-md">
                        Finish & Save
                    </button>
                </div>
            </div>

        </form>
    </div>
</x-modal>