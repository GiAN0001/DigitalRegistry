<x-modal name="lookup-modal" maxWidth="max-w-lg" focusable>
    <div class="p-8" x-data="lookupModal()">
        <div class="flex justify-between items-start mb-4">
            <h2 class="text-2xl font-bold text-blue-700">Add Lookup Data</h2>
            <button type="button" @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">
                <x-lucide-x class="w-6 h-6"/>
            </button>
        </div>
        <form>
            <!-- Dropdown to select lookup type -->
            <div class="mb-6">
                <x-input-label for="lookup_type">Select Lookup Type <span class="text-red-500">*</span></x-input-label>
                <select 
                    id="lookup_type"
                    x-model="lookupType"
                    @change="resetFields()"
                    class="w-full h-10 mt-1 text-sm text-slate-700 border border-gray-300 rounded-lg"
                    required>
                    <option value="">Select</option>
                    <option value="area_street">Area Street</option>
                    <option value="barangay_role">Barangay Role</option>
                    <option value="document_purpose">Document Purpose</option>
                    <option value="equipment">Equipment</option>
                    <option value="facilities">Facilities</option>
                    <option value="household_role">Household Roles</option>
                    <option value="house_structure">House Structures</option>
                    <option value="pet_type">Pet Types</option>
                    <option value="residency_type">Residency Types</option>
                </select>
            </div>

            <!-- Dynamic Fields -->
            <template x-if="lookupType === 'area_street'">
                <div>
                    <x-input-label for="purok_name">Purok Name <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        id="purok_name"
                        name="purok_name"
                        type="text"
                        x-model="fields.purok_name"
                        class="w-full mt-1 text-sm text-slate-700"
                        placeholder="Enter purok name"
                        required
                    />
                    <x-input-label for="purok_code" class="mt-4">Purok Code <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        id="purok_code"
                        name="purok_code"
                        type="text"
                        x-model="fields.purok_code"
                        class="w-full mt-1 text-sm text-slate-700"
                        placeholder="e.g., JPRK for J.P. Rizal Street"
                        required
                    />
                    <x-input-label for="street_name" class="mt-4">Street Name <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        id="street_name"
                        name="street_name"
                        type="text"
                        x-model="fields.street_name"
                        class="w-full mt-1 text-sm text-slate-700"
                        placeholder="Enter street name"
                        required
                    />
                </div>
            </template>

            <template x-if="lookupType === 'barangay_role'">
                <div>
                    <x-input-label for="role_name">Role Name <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        id="role_name"
                        name="role_name"
                        type="text"
                        x-model="fields.role_name"
                        class="w-full mt-1 text-sm text-slate-700"
                        placeholder="Enter Role Name"
                        required
                    />
                </div>
            </template>

            <template x-if="lookupType === 'document_purpose'">
                <div>
                    <x-input-label for="purpose_name">Document Purpose <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        id="purpose_name"
                        name="purpose_name"
                        type="text"
                        x-model="fields.purpose_name"
                        class="w-full mt-1 text-sm text-slate-700"
                        placeholder="Enter Document Purpose"
                        required
                    />
                </div>
            </template>

            <template x-if="lookupType === 'document_type'">
                <div>
                    <x-input-label for="type_name">Document Type <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        id="type_name"
                        name="type_name"
                        type="text"
                        x-model="fields.type_name"
                        class="w-full mt-1 text-sm text-slate-700"
                        placeholder="Enter Document Type"
                        required
                    />
                </div>
            </template>

            <template x-if="lookupType === 'equipment'">
                <div>
                    <x-input-label for="equipment_name">Equipment Type <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        id="equipment_name"
                        name="equipment_name"
                        type="text"
                        x-model="fields.equipment_name"
                        class="w-full mt-1 text-sm text-slate-700"
                        placeholder="Enter Equipment Type"
                        required
                    />
                </div>
            </template>

            <template x-if="lookupType === 'facilities'">
                <div>
                    <x-input-label for="facility_name">Facility Type <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        id="facility_name"
                        name="facility_name"
                        type="text"
                        x-model="fields.facilities_name"
                        class="w-full mt-1 text-sm text-slate-700"
                        placeholder="Enter Facility Type"
                        required
                    />
                </div>
            </template>

            <template x-if="lookupType === 'household_role'">
                <div>
                    <x-input-label for="household_role_name">Household Role <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        id="household_role_name"
                        name="household_role_name"
                        type="text"
                        x-model="fields.household_role_name"
                        class="w-full mt-1 text-sm text-slate-700"
                        placeholder="Enter Household Role"
                        required
                    />
                </div>
            </template>

            <template x-if="lookupType === 'house_structure'">
                <div>
                    <x-input-label for="house_structure_name">House Structure <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        id="house_structure_name"
                        name="house_structure_name"
                        type="text"
                        x-model="fields.house_structure_name"
                        class="w-full mt-1 text-sm text-slate-700"
                        placeholder="Enter House Structure"
                        required
                    />
                </div>
            </template>

            <template x-if="lookupType === 'pet_type'">
                <div>
                    <x-input-label for="pet_type_name">Pet Type <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        id="pet_type_name"
                        name="pet_type_name"
                        type="text"
                        x-model="fields.pet_type_name"
                        class="w-full mt-1 text-sm text-slate-700"
                        placeholder="Enter Pet Type"
                        required
                    />
                </div>
            </template>

            <template x-if="lookupType === 'residency_type'">
                <div>
                    <x-input-label for="residency_type_name">Residency Type <span class="text-red-500">*</span></x-input-label>
                    <x-text-input 
                        id="residency_type_name"
                        name="residency_type_name"
                        type="text"
                        x-model="fields.residency_type_name"
                        class="w-full mt-1 text-sm text-slate-700"
                        placeholder="Enter Residency Type"
                        required
                    />
                </div>
            </template>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 mt-8">
                <x-secondary-button @click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button type="button" x-bind:disabled="!lookupType || !isFilled()" @click="submitForm">
                    Add
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

<script>
    function lookupModal() {
        return {
            lookupType: '',
            fields: {
                purok_name: '',
                purok_code: '',
                street_name: '',
                role_name: '',
                purpose_name: '',
                type_name: '',
                equipment_name: '',
                facilities_name: ''
            },
            resetFields() {
                this.fields = {
                    purok_name: '',
                    purok_code: '',
                    street_name: '',
                    role_name: '',
                    purpose_name: '',
                    type_name: '',
                    equipment_name: '',
                    facilities_name: ''
                };
            },
            isFilled() {
                if (this.lookupType === 'barangay_role') {
                    return this.fields.role_name.trim() !== '';
                }
                if (this.lookupType === 'area_street') {
                    return this.fields.purok_name.trim() !== '' && 
                           this.fields.purok_code.trim() !== '' && 
                           this.fields.street_name.trim() !== '';
                }
                if (this.lookupType === 'document_purpose') {
                    return this.fields.purpose_name.trim() !== '';
                }
                if (this.lookupType === 'document_type') {
                    return this.fields.type_name.trim() !== '';
                }
                if (this.lookupType === 'equipment') {
                    return this.fields.equipment_name.trim() !== '';
                }
                if (this.lookupType === 'facilities') {
                    return this.fields.facilities_name.trim() !== '';
                }
                return false;
            },
            submitForm() {
                console.log(this.lookupType, this.fields);
                // Add your submit logic here
                this.resetFields();
                this.lookupType = '';
                this.$dispatch('close');
            }
        }
    }
</script>