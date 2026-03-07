<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Resident;
use App\Models\TupadParticipation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TupadManagement extends Component
{
    use WithPagination;

    #[Url(history: true, except: '')]
    public $search = '';

    #[Url(history: true)]
    public $activeTab = 'eligible';

    public function updatedActiveTab()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $now = now();
        $threeMonthsAgo = now()->subMonths(3);

        TupadParticipation::where('status', 'Scheduled')
            ->where('start_date', '<=', $now->toDateString())
            ->update(['status' => 'Ongoing']);

        TupadParticipation::where('status', 'Ongoing')
            ->where('end_date', '<', $now->toDateString())
            ->update(['status' => 'Completed']);

        // --- EFFECTIVE CENSUS CYCLE (with fallback, no UI filter) ---
        $currentCycle = Resident::getCurrentCensusCycle();
        $hasCurrentCycle = Resident::where('census_cycle', $currentCycle)->exists();
        $cycle = $hasCurrentCycle
            ? $currentCycle
            : (Resident::max('census_cycle') ?? $currentCycle);

        // --- SHARED SEARCH SCOPE ---
        $searchQuery = function ($query) {
            $query->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('middle_name', 'like', '%' . $this->search . '%')
                        ->orWhere('extension', 'like', '%' . $this->search . '%')
                        ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$this->search}%")
                        ->orWhere(DB::raw("CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name)"), 'like', "%{$this->search}%")
                        ->orWhere(DB::raw("CONCAT(last_name, ', ', first_name)"), 'like', "%{$this->search}%");
                });
            });
        };

        $adultCutoffDate = Carbon::now()->subYears(18)->toDateString();

        // 1. AUTO-ELIGIBLE: 18+ AND no active work OR cooldown record
        $eligible = Resident::with(['demographic', 'household.areaStreet'])
            ->where('census_cycle', $cycle)
            ->tap($searchQuery)
            ->whereHas('demographic', function ($q) use ($adultCutoffDate) {
                $q->where('birthdate', '<=', $adultCutoffDate);
            })
            ->whereDoesntHave('allTupadParticipations', function ($q) use ($now, $threeMonthsAgo) {
                $q->where('end_date', '>=', $now)
                ->orWhere('end_date', '>', $threeMonthsAgo);
            })->paginate(15);

        // 2. AUTO-ONGOING
        $ongoing = Resident::with(['demographic', 'allTupadParticipations'])
            ->where('census_cycle', $cycle)
            ->tap($searchQuery)
            ->whereHas('allTupadParticipations', function ($q) use ($now) {
                $q->whereIn('status', ['Ongoing', 'Scheduled']);
            })->paginate(15);

        // 3. INELIGIBLE
        $ineligible = Resident::with(['demographic', 'allTupadParticipations'])
            ->where('census_cycle', $cycle)
            ->tap($searchQuery)
            ->whereHas('allTupadParticipations', function ($q) use ($threeMonthsAgo, $now) {
                $q->whereIn('status', ['Completed', 'Dropped'])
                ->where('end_date', '>', $threeMonthsAgo)
                ->where('end_date', '<', $now);
            })->paginate(15);

        // 4. DROPPED
        $dropped = Resident::with(['demographic', 'allTupadParticipations'])
            ->where('census_cycle', $cycle)
            ->tap($searchQuery)
            ->whereHas('allTupadParticipations', fn($q) => $q->where('status', 'Dropped'))
            ->paginate(15);

        // 5. UNIFIED COUNTS (scoped to effective cycle)
        // 5. UNIFIED COUNTS (scoped to effective cycle and global ID!)
        $counts = [
            'eligible' => Resident::where('census_cycle', $cycle)
                ->whereHas('demographic', fn($q) => $q->where('birthdate', '<=', $adultCutoffDate))
                ->whereDoesntHave('allTupadParticipations', function($q) use ($now, $threeMonthsAgo) {
                    $q->where('end_date', '>=', $now)->orWhere('end_date', '>', $threeMonthsAgo);
                })->count(),
                
            'ongoing' => Resident::where('census_cycle', $cycle)
                ->whereHas('allTupadParticipations', fn($q) => $q->where('status', 'Ongoing'))->count(),
                
            'ineligible' => Resident::where('census_cycle', $cycle)
                ->whereHas('allTupadParticipations', function($q) use ($threeMonthsAgo) {
                    $q->whereIn('status', ['Completed', 'Dropped'])
                      ->where('end_date', '>', $threeMonthsAgo);
                })->count(),
                
            'dropped' => Resident::where('census_cycle', $cycle)
                ->whereHas('allTupadParticipations', fn($q) => $q->where('status', 'Dropped'))->count(),
                
            'scheduled' => Resident::where('census_cycle', $cycle)
                ->whereHas('allTupadParticipations', fn($q) => $q->where('status', 'Scheduled'))->count(),
        ];

        $this->dispatch('update-counts', ...$counts);

        return view('livewire.admin.tupad-management', [
            'eligible'       => $eligible,
            'ongoing'        => $ongoing,
            'ineligible'     => $ineligible,
            'dropped'        => $dropped,
            'eligibleCount'  => $counts['eligible'],
            'ongoingCount'   => $counts['ongoing'],
            'ineligibleCount'=> $counts['ineligible'],
            'droppedCount'   => $counts['dropped'],
            'scheduledCount' => $counts['scheduled'],
        ]);
    }
}