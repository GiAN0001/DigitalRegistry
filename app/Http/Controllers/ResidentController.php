<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resident;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;
use App\Models\AreaStreet;

class ResidentController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $streets = AreaStreet::all();
        $purok = AreaStreet::all();

        $query = Resident::forUser($user)
            ->with([
                'demographic',
                'residencyType',
                'healthInformation',
                'household.areaStreet',
                'household.houseStructure',
                'household.householdPets.petType'
            ]);

        // 1. Filter by Purok (via Household -> AreaStreet)
        if ($request->filled('purok_name')) {
            $query->whereHas('household.areaStreet', function ($q) use ($request) {
                $q->where('purok_name', $request->purok_name);
            });
        }

        // 2. Filter by House Structure (via Household -> HouseStructure)
        if ($request->filled('house_structure_type')) {
            $query->whereHas('household.houseStructure', function ($q) use ($request) {
                $q->where('house_structure_type', $request->house_structure_type);
            });
        }
 
        // 3. Filter by Residency Status (Owner, Tenant, etc.)
        if ($request->filled('name')) {
            $query->whereHas('residencyType', function ($q) use ($request) {
                $q->where('name', $request->name);
            });
        }
        /*
        // 4. Filter by Sex (via Demographic)
        if ($request->filled('sex')) {
            $query->whereHas('demographic', function ($q) use ($request) {
                $q->where('sex', $request->sex);
            });
        }
            */

        // 5. Filter by Street
        if ($request->filled('street_name')) {
            $query->whereHas('household.areaStreet', function ($q) use ($request) {
                $q->where('street_name', $request->street_name);
            });
        }

        // --- PAGINATION LOGIC ---
        // Get 'per_page' from URL, default to 10 if missing
        $perPage = $request->input('per_page', 10);

        // Fetch results
        $residents = $query->latest()
            ->paginate($perPage)
            ->withQueryString(); // Important: keeps your filters active when clicking page 2

        return view('residents.index', [
            'residents' => $residents,
            'streets' => $streets,
            'purok' => $purok,
        ]);
    }
}