<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Models\Household;
use App\Models\ChristmasBox;
use App\Models\AreaStreet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ChristmasBoxController extends Controller
{
    public function index(Request $request): View
    {
        $year = $request->input('year', now()->year);
        $status = $request->input('status');

        // --- CENSUS CYCLE LOGIC with fallback ---
        $currentCycle = Resident::getCurrentCensusCycle();
        $hasCurrentCycleResidents = Resident::where('census_cycle', $currentCycle)->exists();
        $effectiveCycle = $hasCurrentCycleResidents
            ? $currentCycle
            : (Resident::max('census_cycle') ?? $currentCycle);
        $selectedCycle = $request->input('census_cycle', $effectiveCycle);
        $availableCycles = Resident::select('census_cycle')
            ->distinct()->orderByDesc('census_cycle')->pluck('census_cycle');

        $totalHouseholds = Resident::where('household_role_id', 1)
            ->where('census_cycle', $selectedCycle)->count();
        $releasedCount = ChristmasBox::where('year', $year)
            ->where('status', 'Released')
            ->count();

        $onHoldCount = $totalHouseholds - $releasedCount;

        $query = Resident::where('household_role_id', 1)
            ->where('census_cycle', $selectedCycle)
            ->leftJoin('christmas_boxes', function ($join) use ($year) {
                $join->on('residents.household_id', '=', 'christmas_boxes.household_id')
                    ->where('christmas_boxes.year', '=', $year);
            })
            ->select('residents.*') 
            ->with(['household.areaStreet', 'household.christmasBoxes' => function($q) use ($year) {
                $q->where('year', $year)->with('releasedBy');
            }]);

            if ($status === 'Released') {
                $query->where('christmas_boxes.status', 'Released');
            } elseif ($status === 'On Hold') {
                $query->whereNull('christmas_boxes.status');
            }

        $query->orderByRaw("CASE 
            WHEN christmas_boxes.status = 'Released' THEN 1 
            ELSE 0 
        END ASC");

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                ->orWhere('last_name', 'like', "%{$searchTerm}%")
                ->orWhere('middle_name', 'like', "%{$searchTerm}%")
                ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$searchTerm}%")
                ->orWhere(DB::raw("CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name)"), 'like', "%{$searchTerm}%")
                ->orWhere(DB::raw("CONCAT(last_name, ', ', first_name)"), 'like', "%{$searchTerm}%");
            });
        }
        // filter by purok
        if($request->filled('purok_name')) {
            $query->whereHas('household.areaStreet', function($q) use ($request) {
                $q->where('purok_name', $request->purok_name);
            });
        }

        $heads = $query->paginate(15)->withQueryString();

        return view('admin.christmas.index', compact(
            'heads', 'year', 'releasedCount', 'onHoldCount', 'totalHouseholds',
            'selectedCycle'
        ));
    }

    public function release(Household $household): RedirectResponse
    {
        ChristmasBox::updateOrCreate(
            ['household_id' => $household->id, 'year' => now()->year],
            [
                'released_by_user_id' => Auth::id(),
                'status' => 'Released',
                'date_released' => now(),
            ]
        );

        return back()->with('success', 'Box successfully marked as Released.');
    }

    public function revert(Household $household)
    {
       
        \App\Models\ChristmasBox::where('household_id', $household->id)
            ->where('year', now()->year)
            ->update([
                'status' => 'Hold',
                'released_by_user_id' => null,
                'date_released' => null
            ]);

        return back()->with('success', 'Status successfully reverted.');
    }
}