<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;


use App\Models\Resident;
use App\Models\AreaStreet;
use App\Models\Household;

use App\Models\Demographic; 
use App\Models\HealthInformation;

use App\Http\Requests\ResidentRegistrationRequest;

class ResidentController extends Controller
{
    public function index(Request $request): View //WHOLE CODE BASE IS EDITED BY GIAN
    {
        $user = Auth::user();
        $streets = AreaStreet::all();
        $purok = AreaStreet::all();


        $query = Resident::forUser($user)
            ->where('household_role_id', 1) 
            ->with([
                'demographic',
                'residencyType',
                'healthInformation',
                'household.areaStreet',
                'household.houseStructure',
                'household.householdPets.petType',
  
                'household.residents.demographic', 
                'household.residents.healthInformation',
                'household.residents.residencyType'
            ]); 

        //search bar -- added by GIAN
        if ($request->filled('q')) {
            $searchTerm = $request->q;
                $query->whereHas('household.residents', function ($q) use ($searchTerm) {
                    $q->where('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('last_name', 'like', "%{$searchTerm}%")
                    ->orWhere('middle_name', 'like', "%{$searchTerm}%");
                });
        }

        // 1. Filter by Purok
        if ($request->filled('purok_name')) {
            $query->whereHas('household.areaStreet', function ($q) use ($request) {
                $q->where('purok_name', $request->purok_name);
            });
        }

        // 2. Filter by House Structure
        if ($request->filled('house_structure_type')) {
            $query->whereHas('household.houseStructure', function ($q) use ($request) {
                $q->where('house_structure_type', $request->house_structure_type);
            });
        }

        // 3. Filter by Residency Status
        if ($request->filled('name')) {
            $query->whereHas('residencyType', function ($q) use ($request) {
                $q->where('name', $request->name);
            });
        }

        // 4. Filter by Street
        if ($request->filled('street_name')) {
            $query->whereHas('household.areaStreet', function ($q) use ($request) {
                $q->where('street_name', $request->street_name);
            });
        }

        //5. Filter by Year Added added by GIAN
        if ($request->filled('created_at')) {
        $query->whereYear('created_at', $request->created_at);
    }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $residents = $query->latest()->paginate($perPage)->withQueryString();

        return view('residents.index', [
            'residents' => $residents,
            'streets' => $streets,
            'purok' => $purok,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $user = Auth::user();

        $residents = Resident::forUser($user)
            ->where(function($q) use ($query) {
                $q->where('first_name','like',"%{$query}%")
                ->orWhere('last_name','like',"%{$query}%")
                ->orWhereHas('household', function($h) use ($query) {
                    $h->where('email','like',"%{$query}%")
                        ->orWhere('contact_number','like',"%{$query}%")
                        ->orWhere('house_number','like',"%{$query}%")
                        ->orWhereHas('areaStreet', function($a) use ($query) {
                            $a->where('purok_name','like',"%{$query}%")
                            ->orWhere('street_name','like',"%{$query}%");
                        });
                });
            })
            ->with([
                'household:id,area_id,house_number,contact_number,email',
                'household.areaStreet:id,purok_name,street_name'
            ])
            ->limit(10)
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'extension', 'household_id']);

        return response()->json($residents->map(function ($resident) {
            $house = $resident->household;
            $area  = $house?->areaStreet;
            $fullName = trim("{$resident->first_name} {$resident->middle_name} {$resident->last_name} {$resident->extension}");
            $fullAddress = trim(
                ($house?->house_number ? "{$house->house_number}, " : "") .
                ($area?->street_name ? "{$area->street_name}, " : "") .
                ($area?->purok_name ? "Purok {$area->purok_name}" : ""),
                ", "
            );

            return [
                'id' => $resident->id,
                'full_name' => $fullName,
                'email' => $house?->email,
                'contact_number' => $house?->contact_number,
                'address' => $fullAddress,
                'area_id' => $house?->area_id,
            ];
        }));
    }

    public function store(ResidentRegistrationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        $headData = $validated['head'];
        $householdData = $validated['household'];

        $membersData = $validated['members'] ?? []; 
        $petsData = $validated['pets'] ?? [];

        DB::beginTransaction();
        
            $householdData['household_number'] = $this->generateHouseholdNumber(
                $householdData['area_id'], 
                $householdData['house_number']
            );
            
            $household = Household::create([
                'household_number' => $householdData['household_number'],
                'house_number' => $householdData['house_number'],
                'area_id' => $householdData['area_id'],
                'house_structure_id' => $householdData['house_structure_id'], 
                'contact_number' => $householdData['contact_number'],
                'email' => $householdData['email'] ?? null,
                'landlord_name' => $householdData['landlord_name'] ?? null,
                'landlord_contact' => $householdData['landlord_contact'] ?? null,
            ]);

            $headId = $this->createResidentEntry($headData, $household->id, $householdData['residency_type_id'], 1);


            if (!empty($membersData)) {
                foreach ($membersData as $member) {
                    $this->createResidentEntry(
                        $member, 
                        $household->id, 
                        $householdData['residency_type_id'], 
                        $member['household_role_id']
                    );
                }
            }


            if (!empty($petsData)) {
                foreach ($petsData as $petData) {
                    $household->householdPets()->create([
                        'pet_type_id' => $petData['pet_type_id'],
                        'quantity' => $petData['quantity'],
                    ]);
                }
            }

            if ($householdData['residency_type_id'] == 1) {
                $household->update(['owner_resident_id' => $headId]);
            }

            DB::commit();

            return redirect()->route('residents.index')->with('success', 'Household and Family registered successfully!');

    }
    public function updateHousehold(Request $request, Household $household) //ADDDED BY GIAN
    {
 
        $validated = $request->validate([
            'house_number' => ['required', 'string', 'max:50'],
            'area_id' => ['required', 'exists:area_streets,id'], // This represents the specific Street row
            'house_structure_id' => ['required', 'exists:house_structures,id'],
            'residency_type_id' => ['required', 'exists:residency_types,id'], // Input comes from form, saved to Residents
            'contact_number' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'landlord_name' => ['nullable', 'string', \Illuminate\Validation\Rule::requiredIf($request->residency_type_id != 1)],
            'landlord_contact' => ['nullable', 'string', \Illuminate\Validation\Rule::requiredIf($request->residency_type_id != 1)],
        ]);

        DB::beginTransaction();
        try {

            $household->residents()->update([
                'residency_type_id' => $validated['residency_type_id'],
                'updated_by_user_id' => Auth::id()
            ]);

            // 3. Update the Household Table
            // Exclude residency_type_id since it doesn't exist in the households table
            $householdData = collect($validated)->except(['residency_type_id'])->toArray();

            if ($validated['residency_type_id'] == 1) {
                $householdData['landlord_name'] = null;
                $householdData['landlord_contact'] = null;
            }
            
            $household->update($householdData);

            DB::commit();
            return back()->with('success', 'Household details updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Update Failed: ' . $e->getMessage());
        }
    }
      
    public function show(Resident $resident) // ADDED BY GIAN
    {

        return response()->json([
            'id' => $resident->id,
            'first_name' => $resident->first_name,
            'middle_name' => $resident->middle_name,
            'last_name' => $resident->last_name,
            'extension' => $resident->extension,
            'household_role_id' => $resident->household_role_id,
            'demographic' => $resident->demographic ? $resident->demographic->only([
                'birthplace', 'birthdate', 'sex', 'civil_status', 'nationality', 'occupation'
            ]) : null,
            'health_information' => $resident->healthInformation ? $resident->healthInformation->only([
                'sector', 'vaccination', 'comorbidity', 'maintenance'
            ]) : null,
        ]);
    }

    public function update(Request $request, Resident $resident): RedirectResponse
    {
        $validated = $request->validate([
            'resident.first_name' => ['required', 'string', 'max:100'],
            'resident.last_name' => ['required', 'string', 'max:100'],
            'resident.middle_name' => ['nullable', 'string', 'max:100'],
            'resident.extension' => ['nullable', 'string', 'max:10'],
            'resident.household_role_id' => ['required', 'exists:household_roles,id'],
            'resident.birthplace' => ['required', 'string', 'max:255'],
            'resident.birthdate' => ['required', 'date', 'before:today'],
            'resident.sex' => ['required', 'in:Male,Female'],
            'resident.civil_status' => ['required', 'in:Single,Married,Widowed,Separated'],
            'resident.nationality' => ['required', 'string', 'max:100'],
            'resident.occupation' => ['nullable', 'string', 'max:100'],
            'resident.sector' => ['required', 'in:None,PWD,Senior Citizen,Solo Parent'],
            'resident.vaccination' => ['nullable', 'in:None,Private,Health Center'],
            'resident.comorbidity' => ['nullable', 'string', 'max:255'],
            'resident.maintenance' => ['nullable', 'string', 'max:255'],
        ]);

        $data = $validated['resident'];

        DB::beginTransaction();
        try {
           
            $resident->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'middle_name' => $data['middle_name'],
                'extension' => $data['extension'],
                'household_role_id' => $data['household_role_id'],
                'updated_by_user_id' => Auth::id(), 
            ]);

           
            $resident->demographic()->updateOrCreate(
                ['resident_id' => $resident->id],
                [
                    'birthplace' => $data['birthplace'],
                    'birthdate' => $data['birthdate'],
                    'sex' => $data['sex'],
                    'civil_status' => $data['civil_status'],
                    'nationality' => $data['nationality'],
                    'occupation' => $data['occupation'],
                ]
            );

            $vaccination = ($data['vaccination'] === 'None') ? null : $data['vaccination'];
            $resident->healthInformation()->updateOrCreate(
                ['resident_id' => $resident->id],
                [
                    'sector' => $data['sector'] ?? 'None',
                    'vaccination' => $vaccination,
                    'comorbidity' => $data['comorbidity'],
                    'maintenance' => $data['maintenance'],
                ]
            );

            DB::commit();
            return back()->with('success', 'Resident profile updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Resident Update Failed (ID: {$resident->id}): " . $e->getMessage());
            return back()->with('error', 'Failed to update resident. Details: ' . $e->getMessage());
        }
    }

    private function createResidentEntry(array $data, $householdId, $residencyTypeId, $roleId)
    {

        $resident = Resident::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'extension' => $data['extension'] ?? null,
            'household_role_id' => $roleId,
            'household_id' => $householdId, 
            'residency_type_id' => $residencyTypeId,
            'added_by_user_id' => Auth::id(),
        ]);


        Demographic::create([
            'resident_id' => $resident->id,
            'birthplace' => $data['birthplace'],
            'birthdate' => $data['birthdate'],
            'sex' => $data['sex'],
            'civil_status' => $data['civil_status'],
            'nationality' => $data['nationality'],
            'occupation' => $data['occupation'] ?? null,
        ]);

     
        $vaccinationValue = $data['vaccination'] ?? null;
        if ($vaccinationValue === 'None') {
            $vaccinationValue = null; 
        }

        HealthInformation::create([
            'resident_id' => $resident->id,
            'sector' => $data['sector'] ?? 'None',
            'vaccination' => $vaccinationValue,
            'comorbidity' => $data['comorbidity'] ?? null,
            'maintenance' => $data['maintenance'] ?? null,
        ]);

        return $resident->id;
    }

   private function generateHouseholdNumber($areaId, $houseNumber): string // MODIFIED BY GIAN
    {
       
        $area = AreaStreet::find($areaId);
        $purokCode = $area && $area->purok_code ? $area->purok_code : 'NA';
     
        $houseNo = trim($houseNumber);
        
        $count = Household::where('area_id', $areaId)
                          ->where('house_number', $houseNumber)
                          ->count();

        $nextSequence = $count + 1;
        $counter = str_pad($nextSequence, 3, '0', STR_PAD_LEFT); 

        return "NAM-{$purokCode}-{$houseNo}-{$counter}";
    }
}