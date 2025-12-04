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

        <form> 
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300">
                <h3 class="text-lg font-semibold text-slate-700 mb-4">Your Information (Head of Family)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <x-input-label>
                            Last Name <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input class="w-full mt-1 text-sm" placeholder="Dela Cruz" required />
                    </div>
                    <div>
                        <x-input-label>
                            First Name <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input class="w-full mt-1 text-sm" placeholder="Juan" required />
                    </div>
                    <div>
                        <x-input-label value="Middle Name" />
                        <x-text-input class="w-full mt-1 text-sm" placeholder="Santos" />
                    </div>
                    <div>
                        <x-input-label value="Extension" />
                        <x-text-input class="w-full mt-1 text-sm" placeholder="Jr, Sr, III" />
                    </div>

                    <div>
                        <x-input-label>
                            Place of Birth <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input class="w-full mt-1 text-sm" placeholder="City, Province" required />
                    </div>
                    <div>
                        <x-input-label>
                            Date of Birth <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input type="date" class="w-full mt-1 text-sm" required />
                    </div>
                    <div>
                        <x-input-label>
                            Age <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input class="w-full mt-1 text-sm" placeholder="18" required />
                    </div>
                     <div>
                        <x-input-label>
                            Household role <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-form-select class="w-full h-10 mt-1 text-slate-500"
                            model="App\Models\houseHoldRole" 
                            column="name" 
                            value-column="id"
                            placeholder="Select role"
                            name="area_id"
                        />
                    </div>
                    <div>
                        <x-input-label>
                            Sex <span class="text-red-500">*</span>
                        </x-input-label>
                        <select class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400" required>
                            <option value="" selected disabled>Select</option>
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label>
                            Civil Status <span class="text-red-500">*</span>
                        </x-input-label>
                        <select class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400" required>
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
                        <x-text-input class="w-full h-10 mt-1 text-sm" value="Filipino" required />
                    </div>
                    <div>
                        <x-input-label value="Occupation" />
                        <x-text-input class="w-full mt-1 text-sm" placeholder="Driver, Teacher, etc." />
                    </div>

                    <div>
                        <x-input-label value="Sector" />
                        <select class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400">
                            <option>None</option>
                            <option>Senior Citizen</option>
                            <option>Pregnant</option>
                            <option>PWD</option>
                            <option>Solo Parent</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Vaccinations" />
                        <select class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400">
                            <option>None</option>
                            <option>Private Citizen</option>
                            <option>Health Center</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label value="Comorbidity" />
                        <x-text-input class="w-full mt-1 text-sm border-blue-700" placeholder="Hypertension, Diabetes" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label value="Maintenance" />
                        <x-text-input class="w-full mt-1 text-sm border-blue-700" placeholder="Metformin, Losartan" />
                    </div>
                </div>
            </div>

            <div x-show="step === 2" style="display: none;" x-transition:enter="transition ease-out duration-300">
                <h3 class="text-lg font-semibold text-slate-700 mb-4">Household Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <x-input-label>
                            House Number <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input class="w-full mt-1 text-sm" placeholder="123-A" required />
                    </div>
                    <div>
                        <x-input-label>
                            Purok <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-form-select class="w-full h-10 mt-1 text-slate-500"
                            model="App\Models\AreaStreet" 
                            column="Purok_name" 
                            value-column="id"
                            placeholder="Select purok"
                        />
                    </div>
                    <div>
                        <x-input-label>
                            Street <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-form-select class="w-full h-10 mt-1 text-slate-500"
                            model="App\Models\AreaStreet" 
                            column="street_name" 
                            value-column="id"
                            placeholder="Select street"
                        />
                    </div>
                    <div>
                        <x-input-label>
                            House Structure <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-form-select class="w-full h-10 mt-1 text-slate-500"
                            model="App\Models\houseStructure" 
                            column="house_structure_type" 
                            value-column="id"
                            placeholder="Select house structure"
                        />
                    </div>

                    <div>
                        <x-input-label>
                            Ownership Status <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-form-select x-model="ownershipStatus" class="w-full h-10 mt-1 text-slate-500"
                            model="App\Models\ResidencyType" 
                            column="name" 
                            value-column="id"
                            placeholder="Select Ownership Status"
                        />
                    </div>

                    <div>
                        <x-input-label>
                            Household Email <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input type="email" class="w-full mt-1 text-sm" placeholder="family@example.com" />
                    </div>
                    <div>
                        <x-input-label>
                            Household Contact No <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input class="w-full mt-1 text-sm" placeholder="0917..." />
                    </div>

                    <div x-show="ownershipStatus != '0' && ownershipStatus != ''" class="md:col-span-3 bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
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
                    <h3 class="text-lg font-semibold text-slate-700">Family Members</h3>
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
                                <div>
                                    <x-input-label>
                                        Last Name <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input class="w-full mt-1 text-sm" placeholder="Dela Cruz" required />
                                </div>
                                <div>
                                    <x-input-label>
                                        First Name <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input class="w-full mt-1 text-sm" placeholder="Juan" required />
                                </div>
                                <div>
                                    <x-input-label value="Middle Name" />
                                    <x-text-input class="w-full mt-1 text-sm" placeholder="Santos" />
                                </div>
                                <div>
                                    <x-input-label value="Extension" />
                                    <x-text-input class="w-full mt-1 text-sm" placeholder="Jr, Sr, III" />
                                </div>

                                <div>
                                    <x-input-label>
                                        Place of Birth <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input class="w-full mt-1 text-sm" placeholder="City, Province" required />
                                </div>
                                <div>
                                    <x-input-label>
                                        Date of Birth <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input type="date" class="w-full mt-1 text-sm" required />
                                </div>
                                <div>
                                    <x-input-label>
                                        Age <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input class="w-full mt-1 text-sm" placeholder="18" required />
                                </div>
                                <div>
                                    <x-input-label>
                                        Household role <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-form-select class="w-full h-10 mt-1 text-slate-500"
                                        model="App\Models\houseHoldRole" 
                                        column="name" 
                                        value-column="id"
                                        placeholder="Select role"
                                        name="area_id"
                                    />
                                </div>
                                <div>
                                    <x-input-label>
                                        Sex <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <select class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400" required>
                                        <option value="" selected disabled>Select</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>

                                <div>
                                    <x-input-label>
                                        Civil Status <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <select class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400" required>
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
                                    <x-text-input class="w-full mt-1 text-sm" value="Filipino" required />
                                </div>
                                <div class="md:col-span-1">
                                    <x-input-label value="Occupation" />
                                    <x-text-input class="w-full mt-1 text-sm" placeholder="Driver, Teacher, etc." />
                                </div>

                                <div>
                                    <x-input-label value="Sector" />
                                    <select class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400">
                                        <option>None</option>
                                        <option>Senior Citizen</option>
                                        <option>PWD</option>
                                        <option>Solo Parent</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label value="Vaccinations" />
                                    <select class="w-full h-10 mt-1 text-sm text-slate-500 border border-gray-300 rounded-md shadow-sm focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400">
                                        <option>None</option>
                                        <option>Private Citizen</option>
                                        <option>Health Center</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label value="Comorbidity" />
                                    <x-text-input class="w-full mt-1 text-sm" placeholder="Hypertension, Diabetes" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label value="Maintenance" />
                                    <x-text-input class="w-full mt-1 text-sm" placeholder="Metformin, Losartan" />
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
                    <h3 class="text-lg font-semibold text-slate-700">Household Pets</h3>
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
                                    <x-form-select class="w-full h-10 mt-1 text-sm text-slate-500"
                                        model="App\Models\petType" 
                                        column="name" 
                                        value-column="id"
                                        placeholder="Select pet type"
                                    />
                                </div>
                                <div>
                                    <x-input-label value="Quantity" />
                                    <x-text-input type="number" class="w-full h-10 mt-1 text-sm text-slate-500" x-model="pet.quantity" />
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