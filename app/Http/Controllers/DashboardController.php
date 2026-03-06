<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\User;
use App\Models\Resident;
use App\Models\Household;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Models\AreaStreet;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index() : View
    {
        // --- EFFECTIVE CENSUS CYCLE ---
        $currentCycle = Resident::getCurrentCensusCycle();
        $hasCurrentCycle = Resident::where('census_cycle', $currentCycle)->exists();
        $cycle = $hasCurrentCycle
            ? $currentCycle
            : (Resident::max('census_cycle') ?? $currentCycle);


        // BAR CHART FOR AGE
        $referenceDate = '2025-12-08';

        $ageGroupSql = "
            CASE
                WHEN TIMESTAMPDIFF(YEAR, d.birthdate, '{$referenceDate}') BETWEEN 0 AND 5 THEN 'A. Toddler (0-5)'
                WHEN TIMESTAMPDIFF(YEAR, d.birthdate, '{$referenceDate}') BETWEEN 6 AND 12 THEN 'B. Child (6-12)'
                WHEN TIMESTAMPDIFF(YEAR, d.birthdate, '{$referenceDate}') BETWEEN 13 AND 19 THEN 'C. Teen (13-19)'
                WHEN TIMESTAMPDIFF(YEAR, d.birthdate, '{$referenceDate}') BETWEEN 20 AND 39 THEN 'D. Adult (20-39)'
                WHEN TIMESTAMPDIFF(YEAR, d.birthdate, '{$referenceDate}') BETWEEN 40 AND 59 THEN 'E. Middle-aged (40-59)'
                WHEN TIMESTAMPDIFF(YEAR, d.birthdate, '{$referenceDate}') >= 60 THEN 'F. Senior (60+)'
                ELSE 'G. Undefined Age'
            END
        ";

        $demographicsData = \App\Models\Resident::query()
            ->join('demographics as d', 'd.resident_id', '=', 'residents.id')
            ->selectRaw("{$ageGroupSql} AS age_group, COUNT(residents.id) AS population_count")
            ->where('residents.census_cycle', $cycle)
            ->whereNotNull('d.birthdate')
            ->where('d.birthdate', '<', $referenceDate)
            ->groupBy(DB::raw($ageGroupSql))
            ->orderBy('age_group')
            ->get();

        // PIE CHART COLORS
        $getColors = function($count) {
            $colors = [];
            $baseHue = 200;
            for ($i = 0; $i < $count; $i++) {
                $hue = ($baseHue + ($i * 60)) % 360;
                $colors[] = "hsl({$hue}, 70%, 50%, 0.7)";
            }
            return $colors;
        };

        $currentYearStart = Carbon::now()->startOfYear()->toDateTimeString();
        $currentYearEnd = Carbon::now()->endOfYear()->toDateTimeString();

        // --- WIDGETS (For Admin/Super Admin) ---
        $totalResidents = Resident::where('census_cycle', $cycle)->count();
        $totalHouseholds = Household::whereHas('residents', function ($q) use ($cycle) {
            $q->where('census_cycle', $cycle);
        })->count();
        $totalActiveUsers = User::where('status', 1)->count();

        // --- WIDGETS (For Staff/Helpdesk - based on their personal inputs) ---
        $myResidentsCount = Resident::where('census_cycle', $cycle)
            ->where('added_by_user_id', auth()->id())->count();
        $myHouseholdsCount = Household::whereHas('residents', function($q) use ($cycle) {
            $q->where('census_cycle', $cycle)
              ->where('added_by_user_id', auth()->id())
              ->where('household_role_id', 1); // 1 = Head of the family
        })->count();

        // TABLE
        $users = User::with(['roles', 'barangayRole'])
            ->addSelect(['households_registered_count' => \App\Models\Household::query()
                ->selectRaw('COUNT(DISTINCT households.id)')
                ->join('residents', 'residents.household_id', '=', 'households.id')
                ->whereColumn('residents.added_by_user_id', 'users.id')
                ->where('residents.household_role_id', 1)
                ->where('residents.census_cycle', $cycle)
                ->whereBetween('residents.created_at', [$currentYearStart, $currentYearEnd])
            ])
            ->get();

        // CHART QUERY
        $purokPopulationData = Resident::query()
            ->where('residents.census_cycle', $cycle)
            ->join('households', 'households.id', '=', 'residents.household_id')
            ->join('area_streets', 'area_streets.id', '=', 'households.area_id')
            ->groupBy('area_streets.purok_name')
            ->select('area_streets.purok_name', DB::raw('COUNT(residents.id) as total_population'))
            ->orderBy('area_streets.purok_name')
            ->get();

        $chartLabels = $purokPopulationData->pluck('purok_name')->all();
        $dataPoints = $purokPopulationData->pluck('total_population')->all();
        $dataCount = count($dataPoints);
        $backgroundColors = $getColors($dataCount);

        $populationChartData = [
            'labels' => $chartLabels,
            'datasets' => [[
                'label' => 'Total Population',
                'data' => $dataPoints,
                'backgroundColor' => $backgroundColors,
                'borderColor' => 'rgba(255, 255, 255, 0.5)',
                'borderWidth' => 1
            ]]
        ];

        // BAR CHART FOR AGES
        $ageChartLabels = $demographicsData->pluck('age_group')->all();
        $ageDataPoints = $demographicsData->pluck('population_count')->all();

        $demographicsChartData = [
            'labels' => $ageChartLabels,
            'datasets' => [[
                'label' => 'Total Population',
                'data' => $ageDataPoints,
                'backgroundColor' => ['#3b82f6', '#20c997', '#ffc107', '#dc3545', '#fd7e14', '#e83e8c'],
                'borderColor' => '#fff',
                'borderWidth' => 1
            ]]
        ];

        // --- FINAL RETURN ---
        return view('dashboard', [
            'totalResidents' => $totalResidents,
            'totalHousehold' => $totalHouseholds,
            'totalActiveUsers' => $totalActiveUsers,

            // Pass these variables so they are available in the Blade view
            'myResidentsCount' => $myResidentsCount,
            'myHouseholdsCount' => $myHouseholdsCount,

            'users' => $users,
            'populationChartData' => $populationChartData,
            'demographicsChartData' => $demographicsChartData,
        ]);
    }
}
