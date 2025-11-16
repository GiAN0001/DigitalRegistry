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
    public function index(): View
    {
        $user = Auth::user();
        $puroks = AreaStreet::select('purok_name')->distinct()->get();
        
        $residents = Resident::forUser($user)
            ->with([
                'demographic', 
                'residencyType', 
                'healthInformation', 
                'household.areaStreet', 
                'household.houseStructure', 
                'household.householdPets.petType' 
            ])
            ->latest()
            ->paginate(10);

        return view('residents.index', [
            'residents' => $residents,
            'puroks' => $puroks,
        ]);
    }
}