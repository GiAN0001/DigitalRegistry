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
        
            $householdData['household_number'] = $this->generateHouseholdNumber();
            
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

    private function generateHouseholdNumber(): string
    {
        $latest = Household::latest('id')->first();
        $nextId = $latest ? $latest->id + 1 : 1;
        return 'NAM-' . date('Y') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
    }
}