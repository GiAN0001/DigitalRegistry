<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resident; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\View\View;
use App\Models\AreaStreet;

class ResidentController extends Controller
{
    public function index(Request $request) // Inject Request
    {
        $user = Auth::user();
        $query = Resident::forUser($user)
            ->with([
                'demographic', 
                'residencyType', 
                'healthInformation', 
                'household.areaStreet', 
                'household.houseStructure', 
                'household.householdPets.petType'
                // ... other relationships
            ]);

        // --- FILTER LOGIC ---

        // 1. Filter by Purok
        if ($request->has('purok_name') && $request->purok_name != null) {
            // Use whereHas to query the relationship
            $query->whereHas('household.areaStreet', function ($q) use ($request) {
                $q->where('purok_name', $request->purok_name);
            });
        }
        /*
        // 2. Filter by House Structure
        if ($request->has('house_structure_type') && $request->house_structure_type != null) {
            $query->whereHas('household.houseStructure', function ($q) use ($request) {
                $q->where('house_structure_type', $request->house_structure_type);
            });
        }

        // 3. Filter by Sex
        if ($request->has('sex') && $request->sex != null) {
            $query->whereHas('demographic', function ($q) use ($request) {
                $q->where('sex', $request->sex);
            });
        } */

        // --- END FILTER LOGIC ---

        $residents = $query->latest()->paginate(10)->withQueryString(); // Persist filters in pagination links

        return view('residents.index', [
            'residents' => $residents,
        ]);
    }
}