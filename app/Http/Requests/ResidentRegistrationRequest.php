<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ResidentRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $ownerId = \App\Models\ResidencyType::where('name', 'Owner')->value('id');

        return [
            // --- STEP 1: Head of Family ONLY ---
            'head.first_name' => ['required', 'string', 'max:100'],
            'head.last_name' => ['required', 'string', 'max:100'],
            'head.middle_name' => ['nullable', 'string', 'max:100'],
            'head.extension' => ['nullable', 'string', 'max:10'],
            
            // Demographics
            'head.birthplace' => ['required', 'string', 'max:255'],
            'head.birthdate' => ['required', 'date', 'before_or_equal:' . date('Y-m-d')],

            'head.household_role_id' => ['required', Rule::exists('household_roles', 'id')],
            'head.sex' => ['required', Rule::in(['Male', 'Female'])],
            'head.civil_status' => ['required', Rule::in(['Single', 'Married', 'Widowed', 'Separated'])],
            'head.nationality' => ['required', 'string', 'max:100'],
            'head.occupation' => ['nullable', 'string', 'max:100'],
            
            // Health Information
            'head.sector' => ['nullable', Rule::in(['None', 'PWD', 'Senior Citizen', 'Solo Parent'])],
            'head.vaccination' => ['nullable', Rule::in(['Private', 'Health Center', 'None'])],
            'head.comorbidity' => ['nullable', 'string', 'max:255'],
            'head.maintenance' => ['nullable', 'string', 'max:255'],

            // --- STEP 2: Household Data (NEW) ---
            'household.house_number' => ['required', 'string', 'max:50'],

            'household.area_id' => ['required', Rule::exists('area_streets', 'id')],
            'household.house_structure_id' => ['required', Rule::exists('house_structures', 'id')],
            'household.residency_type_id' => ['required', Rule::exists('residency_types', 'id')],
            
            'household.contact_number' => ['required', 'string', 'max:30'],
            'household.email' => ['nullable', 'email', 'max:255'],
            
            // CONDITIONAL: Landlord details required if Residency Type is NOT 1 (Owner)
            'household.landlord_name' => [
                'nullable', 
                'string', 
                Rule::requiredIf(fn() => $this->input('household.residency_type_id') && $this->input('household.residency_type_id') != $ownerId)
            ],
            'household.landlord_contact' => [
                'nullable', 
                'string',
                Rule::requiredIf(fn() => $this->input('household.residency_type_id') && $this->input('household.residency_type_id') != $ownerId)
            ],

            //STEP 2 added by GIAN
            'members' => ['nullable', 'array'], 
            
            // NOTICE THE '.*.' ADDED BELOW:
            'members.*.first_name' => ['required', 'string', 'max:100'],
            'members.*.last_name' => ['required', 'string', 'max:100'],
            'members.*.middle_name' => ['nullable', 'string', 'max:100'],
            'members.*.extension' => ['nullable', 'string', 'max:10'],
            'members.*.birthplace' => ['required', 'string', 'max:255'],
            'members.*.birthdate' => ['required', 'date', 'before_or_equal:' . date('Y-m-d')],
            'members.*.household_role_id' => ['required', Rule::exists('household_roles', 'id')],
            'members.*.sex' => ['required', Rule::in(['Male', 'Female'])],
            'members.*.civil_status' => ['required', Rule::in(['Single', 'Married', 'Widowed', 'Separated'])],
            'members.*.nationality' => ['required', 'string', 'max:100'],
            'members.*.occupation' => ['nullable', 'string', 'max:100'],
            'members.*.sector' => ['nullable', Rule::in(['None', 'PWD', 'Senior Citizen', 'Solo Parent'])],
            'members.*.vaccination' => ['nullable', Rule::in(['Private', 'Health Center', 'None'])],
            'members.*.comorbidity' => ['nullable', 'string', 'max:255'],
            'members.*.maintenance' => ['nullable', 'string', 'max:255'],

            //STEP 4 added by GIAN
            'pets' => ['nullable', 'array'],

            'pets.*.pet_type_id' => ['required', 'exists:pet_types,id'],
            'pets.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'head.first_name' => 'First Name',
            'head.last_name' => 'Last Name',
            'head.middle_name' => 'Middle Name',
            'head.extension' => 'Extension',
            'head.birthplace' => 'Place of Birth',
            'head.birthdate' => 'Date of Birth',
            'head.household_role_id' => 'Household Role',
            'head.sex' => 'Sex',
            'head.civil_status' => 'Civil Status',
            'head.nationality' => 'Nationality',
            'head.occupation' => 'Occupation',
            'head.sector' => 'Sector',
            'head.vaccination' => 'Vaccination',
            'head.comorbidity' => 'Comorbidity',
            'head.maintenance' => 'Maintenance',
            
            'household.house_number' => 'House Number',
            'household.area_id' => 'Street',
            'household.house_structure_id' => 'House Structure',
            'household.residency_type_id' => 'Ownership Status',
            'household.contact_number' => 'Household Contact Number',
            'household.email' => 'Household Email',
            'household.landlord_name' => 'Landlord Name',
            'household.landlord_contact' => 'Landlord Contact',

            'members.*.first_name' => 'Member First Name',
            'members.*.last_name' => 'Member Last Name',
            'members.*.middle_name' => 'Member Middle Name',
            'members.*.extension' => 'Member Extension',
            'members.*.birthplace' => 'Member Place of Birth',
            'members.*.birthdate' => 'Member Date of Birth',
            'members.*.household_role_id' => 'Member Household Role',
            'members.*.sex' => 'Member Sex',
            'members.*.civil_status' => 'Member Civil Status',
            'members.*.nationality' => 'Member Nationality',
            'members.*.occupation' => 'Member Occupation',
            'members.*.sector' => 'Member Sector',
            'members.*.vaccination' => 'Member Vaccination',
            'members.*.comorbidity' => 'Member Comorbidity',
            'members.*.maintenance' => 'Member Maintenance',
            
            'pets.*.pet_type_id' => 'Pet Type',
            'pets.*.quantity' => 'Pet Quantity',
        ];
    }
}