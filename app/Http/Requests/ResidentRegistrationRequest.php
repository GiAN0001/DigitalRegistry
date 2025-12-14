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
                Rule::requiredIf(fn() => $this->input('household.residency_type_id') != 1)
            ],
            'household.landlord_contact' => [
                'nullable', 
                'string',
                Rule::requiredIf(fn() => $this->input('household.residency_type_id') != 1)
            ],
        ];
    }
}