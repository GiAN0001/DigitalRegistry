<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        $defaultCycle = Resident::getCurrentCensusCycle();
        $selectedCycle = $request->input('census_cycle', $defaultCycle);

        $isArchived = $request->filled('archived') && $request->archived == 'true';

  
        $query = Household::whereHas('residents', function ($q) use ($selectedCycle) {
            $q->withTrashed()->where('census_cycle', $selectedCycle);
        });

      
        if ($user->hasRole('staff')) {
            $query->whereHas('residents', function ($q) use ($user) {
                $q->withTrashed()->where('added_by_user_id', $user->id);
            });
        } elseif (!$user->hasRole('super admin') && !$user->hasRole('admin') && !$user->hasRole('help desk')) {
            $query->whereRaw('1 = 0'); // no results for unknown roles
        }

        if ($isArchived) {
            $query->withTrashed()
                ->where(function ($q) {
                    $q->whereNotNull('households.deleted_at')
                      ->orWhereHas('residents', fn($r) => $r->onlyTrashed());
                })
                ->with([
                    'areaStreet',
                    'houseStructure',
                    'residents' => fn($q) => $q->withTrashed()->with([
                        'demographic' => fn($q) => $q->withTrashed(),
                        'healthInformation' => fn($q) => $q->withTrashed(),
                        'residencyType',
                        'householdRole',
                    ]),
                    'householdPets.petType',
                ]);
        } else {
            
            $query->whereHas('residents', function ($q) use ($selectedCycle) {
                $q->whereNull('deleted_at')->where('census_cycle', $selectedCycle);
            })
            ->with([
                'areaStreet',
                'houseStructure',
                'residents' => fn($q) => $q->withTrashed()->with([
                    'demographic' => fn($q) => $q->withTrashed(),
                    'healthInformation' => fn($q) => $q->withTrashed(),
                    'residencyType',
                    'householdRole',
                ]),
                'householdPets.petType',
            ]);
        }

        // --- FILTERS ---

        // Search by resident name (any member)
        if ($request->filled('q')) {
            $searchTerm = trim($request->q);
            $query->whereHas('residents', function ($q) use ($searchTerm) {
                $q->withTrashed()
                  ->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('middle_name', 'like', "%{$searchTerm}%")
                  ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$searchTerm}%")
                  ->orWhere(DB::raw("CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name)"), 'like', "%{$searchTerm}%")
                  ->orWhere(DB::raw("CONCAT(last_name, ', ', first_name)"), 'like', "%{$searchTerm}%");
            });
        }

        // Filter by Purok
        if ($request->filled('purok_name')) {
            $query->whereHas('areaStreet', fn($q) => $q->where('purok_name', $request->purok_name));
        }

        // Filter by House Structure
        if ($request->filled('house_structure_type')) {
            $query->whereHas('houseStructure', fn($q) => $q->where('house_structure_type', $request->house_structure_type));
        }

        // Filter by Ownership/Residency Status (stored on residents)
        if ($request->filled('name')) {
            $query->whereHas('residents', function ($q) use ($request) {
                $q->withTrashed()->whereHas('residencyType', fn($r) => $r->where('name', $request->name));
            });
        }

        // Filter by Street
        if ($request->filled('street_name')) {
            $query->whereHas('areaStreet', fn($q) => $q->where('street_name', $request->street_name));
        }

        // Filter by Census Cycle
        if ($request->filled('census_cycle')) {
            $query->whereHas('residents', fn($q) => $q->withTrashed()->where('census_cycle', $request->census_cycle));
        }

        $perPage = $request->input('per_page', 10);
        // NOTE: variable renamed to $households for clarity in view
        $residents = $query->latest()->paginate($perPage)->withQueryString();

        return view('residents.index', compact('residents', 'selectedCycle', 'streets', 'purok'));
    }

    // ADDED BY CATH
    public function getDemographics($residentId)
    {
        try {
            $demographic = \App\Models\Demographic::where('resident_id', $residentId)->first();

            if (!$demographic) {
                return response()->json([
                    'sex' => null,
                    'birthdate' => null,
                    'civil_status' => null,
                    'citizenship' => null,
                    'annual_income' => null
                ]);
            }

            return response()->json([
                'sex' => $demographic->sex,
                'birthdate' => $demographic->birthdate,
                'civil_status' => $demographic->civil_status,
                'citizenship' => $demographic->nationality, // Map nationality to citizenship
                'annual_income' => null, // Will be stored in document_requests table
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching demographics: ' . $e->getMessage());
            return response()->json([
                'sex' => null,
                'birthdate' => null,
                'civil_status' => null,
                'citizenship' => null,
                'annual_income' => null
            ]);
        }
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

    /**
     * Returns ONLY the fields required by the Edit Household modal.
     * Keeps sensitive resident/demographic data out of the HTML source.
     */
    public function showHousehold(Household $household): \Illuminate\Http\JsonResponse
    {
        $household->load('areaStreet');

        return response()->json([
            'id'               => $household->id,
            'house_number'     => $household->house_number,
            'area_id'          => $household->area_id,
            'area_street'      => $household->areaStreet ? [
                'id'         => $household->areaStreet->id,
                'purok_name' => $household->areaStreet->purok_name,
                'street_name'=> $household->areaStreet->street_name,
            ] : null,
            'house_structure_id' => $household->house_structure_id,
            'residency_type_id'  => $household->residents()
                                        ->whereNull('deleted_at')
                                        ->value('residency_type_id'),
            'contact_number'   => $household->contact_number,
            'email'            => $household->email,
            'landlord_name'    => $household->landlord_name,
            'landlord_contact'  => $household->landlord_contact,
        ]);
    }


    public function store(ResidentRegistrationRequest $request): RedirectResponse // edited by GIAN
    {
        $validated = $request->validated();
        $headData = $validated['head'];
        $membersData = $validated['members'] ?? [];
        $householdData = $validated['household'];
        $currentCycle = Resident::getCurrentCensusCycle();

        DB::beginTransaction();
        try {
            // --- 1. IDENTITY FINGERPRINT CHECK ---
            // Validate every person in the form against the current cycle
            $people = array_merge([$headData], $membersData);
            foreach ($people as $person) {
                $duplicate = Resident::where('first_name', $person['first_name'])
                    ->where('last_name', $person['last_name'])
                    ->where('census_cycle', $currentCycle)
                    ->whereHas('demographic', function($q) use ($person) {
                        $q->where('birthdate', $person['birthdate'])
                        ->where('birthplace', $person['birthplace']);
                    })->exists();

                if ($duplicate) {
                    return back()->with('error', "Duplicate Entry: {$person['first_name']} {$person['last_name']} (Born {$person['birthdate']}) already exists in the {$currentCycle} cycle.")->withInput();
                }
            }

            // --- 2. HOUSEHOLD CREATION ---
            // We create a NEW household row for EVERY family (Family-Unit Centric)
            $householdNumber = $this->generateHouseholdNumber(
                $householdData['area_id'], 
                $householdData['house_number']
            );

            $household = Household::create([
                'household_number' => $householdNumber,
                'house_number' => strtoupper(trim($householdData['house_number'])),
                'area_id' => $householdData['area_id'],
                'house_structure_id' => $householdData['house_structure_id'], 
                'contact_number' => $householdData['contact_number'],
                'email' => $householdData['email'] ?? null,
                'landlord_name' => $householdData['landlord_name'] ?? null,
                'landlord_contact' => $householdData['landlord_contact'] ?? null,
            ]);

            // --- 3. RESIDENT ENTRIES ---
            $headRoleId = \App\Models\householdRole::where('name', 'Head')->value('id');
            $headId = $this->createResidentEntry($headData, $household->id, $householdData['residency_type_id'], $headRoleId);
            foreach ($membersData as $member) {
                $this->createResidentEntry($member, $household->id, $householdData['residency_type_id'], $member['household_role_id']);
            }

            // --- 4. PET ENTRIES ---
            if (!empty($validated['pets'])) {
                foreach ($validated['pets'] as $pet) {
                    $household->householdPets()->create($pet);
                }
            }

            // --- 5. OWNER STAMPING ---
            $ownerId = \App\Models\ResidencyType::where('name', 'Owner')->value('id');
            if ($householdData['residency_type_id'] == $ownerId) {
                $household->update(['owner_resident_id' => $headId]);
            }

            \App\Models\Log::create([
                'user_id' => Auth::id(),
                'log_type' => \App\Enums\LogAction::HOUSEHOLD_CREATED,
                'description' => Auth::user()->first_name . " registered family under " . $householdNumber,
                'date' => now(), 
            ]);

            DB::commit();
            return redirect()->route('residents.index')->with('success', 'Household registered successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Critical Error: ' . $e->getMessage())->withInput();
        }
    }

    public function updateHousehold(Request $request, Household $household) //ADDDED BY GIAN
    {
        $ownerId = \App\Models\ResidencyType::where('name', 'Owner')->value('id');

        $validated = $request->validate([
            'house_number' => ['required', 'string', 'max:50'],
            'area_id' => ['required', 'exists:area_streets,id'], // This represents the specific Street row
            'house_structure_id' => ['required', 'exists:house_structures,id'],
            'residency_type_id' => ['required', 'exists:residency_types,id'], // Input comes from form, saved to Residents
            'contact_number' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'landlord_name' => ['nullable', 'string', \Illuminate\Validation\Rule::requiredIf($request->residency_type_id != $ownerId)],
            'landlord_contact' => ['nullable', 'string', \Illuminate\Validation\Rule::requiredIf($request->residency_type_id != $ownerId)],
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

            $headRoleId = \App\Models\householdRole::where('name', 'Head')->value('id');

            if ($validated['residency_type_id'] == $ownerId) {
                $householdData['landlord_name'] = null;
                $householdData['landlord_contact'] = null;
                $householdData['owner_resident_id'] = $household->residents()->where('household_role_id', $headRoleId)->first()->id ?? null;
            }
            else {
                $householdData['owner_resident_id'] = null;
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

    public function update(Request $request, Resident $resident): RedirectResponse // edited by GIAN
    {
        // 1. Validation for the specific resident being updated
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
        // Use the cycle assigned to this specific record for temporal isolation
        $currentCycle = $resident->census_cycle; 

        DB::beginTransaction();
        try {
            // --- 2. IDENTITY FINGERPRINT CHECK (With ID Exclusion) ---
            // We look for OTHER people in the SAME cycle who match the core details
            $duplicate = Resident::where('id', '!=', $resident->id) // CRITICAL: Exclude self
                ->where('first_name', $data['first_name'])
                ->where('last_name', $data['last_name'])
                ->where('census_cycle', $currentCycle)
                ->whereHas('demographic', function($q) use ($data) {
                    $q->where('birthdate', $data['birthdate'])
                    ->where('birthplace', $data['birthplace']);
                })->exists();

            if ($duplicate) {
                // Triggers your red Error Modal
                return back()->with('error', "Update Blocked: Another resident named {$data['first_name']} {$data['last_name']} (Born {$data['birthdate']}) already exists in the {$currentCycle} cycle.")->withInput();
            }

            // --- 3. PROCEED WITH UPDATES ---
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
            return back()->with('success', 'Resident details updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Resident Update Failed (ID: {$resident->id}): " . $e->getMessage());
            return back()->with('error', 'Update Error: ' . $e->getMessage());
        }
    }

    private function createResidentEntry(array $data, $householdId, $residencyTypeId, $roleId) 
    {
        $currentYear = date('Y');
        $currentSem = (date('n') <= 6) ? 1 : 2; //edited by GIAN

        $resident = Resident::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'extension' => $data['extension'] ?? null,
            'household_role_id' => $roleId,
            'household_id' => $householdId, 
            'residency_type_id' => $residencyTypeId,
            'added_by_user_id' => Auth::id(),
            'census_cycle' => Resident::getCurrentCensusCycle(),
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

    private function generateHouseholdNumber($areaId, $houseNumber): string  // edited by GIAN
    {

        $currentStreet = AreaStreet::findOrFail($areaId);
        $purokName = $currentStreet->purok_name;
        $purokCode = $currentStreet->purok_code ?? 'NA';
        $currentCycle = Resident::getCurrentCensusCycle();
        $formattedHouseNo = strtoupper(trim($houseNumber));

        
        $count = Household::whereHas('areaStreet', function($q) use ($purokName) {
                $q->where('purok_name', $purokName);
            })
            ->whereHas('residents', function($q) use ($currentCycle) {
                $q->where('census_cycle', $currentCycle);
            })
            ->count();

        $nextSequence = $count + 1;
        $counter = str_pad($nextSequence, 3, '0', STR_PAD_LEFT); 

    
        return "NAM-{$purokCode}-{$formattedHouseNo}-{$counter}";
    }

    public function destroyHousehold(Request $request, $householdId): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->with('error', 'Authentication failed. Please check your password.');
        }

        DB::beginTransaction();
        try {
            $household = Household::findOrFail($householdId);
            $household->delete(); 
            DB::commit();
            return back()->with('success', 'Household and its members successfully deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting household: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $residentId): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->with('error', 'Authentication failed. Please check your password.');
        }

        DB::beginTransaction();
        try {
            $resident = Resident::findOrFail($residentId);
            $household = $resident->household;

            $resident->delete();

            // Auto-archive the household if no active residents remain
            $activeResidentsLeft = $household->residents()->whereNull('deleted_at')->count();
            if ($activeResidentsLeft === 0) {
                $household->delete();
                DB::commit();
                return back()->with('success', 'Resident deleted. Since this was the last active member, the household has been archived as well.');
            }

            DB::commit();
            return back()->with('success', 'Resident successfully deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting resident: ' . $e->getMessage());
        }
    }

    public function restoreHousehold(Request $request, $householdId): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $household = Household::withTrashed()->findOrFail($householdId);
            $household->restore();
            DB::commit();
            return back()->with('success', 'Household successfully restored.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error restoring household: ' . $e->getMessage());
        }
    }

    public function restore(Request $request, $residentId): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $resident = Resident::withTrashed()->findOrFail($residentId);
            $household = Household::withTrashed()->findOrFail($resident->household_id);

            $resident->restore();

            // Auto-restore the household if it was also soft-deleted (e.g. last member was deleted)
            if ($household->trashed()) {
                $household->restore();
                DB::commit();
                return back()->with('success', 'Resident restored. The household has also been automatically restored.');
            }

            DB::commit();
            return back()->with('success', 'Resident successfully restored.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error restoring resident: ' . $e->getMessage());
        }
    }
}