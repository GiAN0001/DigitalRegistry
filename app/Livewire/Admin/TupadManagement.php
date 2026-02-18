<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Resident;
use App\Models\TupadParticipation;
use Carbon\Carbon;

class TupadManagement extends Component
{
    use WithPagination;
    
    //public testDate= null; // For testing purposes only.


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
         /*  if ($this->testDate) {
            Carbon::setTestNow(Carbon::parse($this->testDate));
           } */ //fo testing purposes only.
           
        $now = now(); 
        $threeMonthsAgo = now()->subMonths(3);

        TupadParticipation::where('status', 'Scheduled')
            ->where('start_date', '<=', $now->toDateString())
            ->update(['status' => 'Ongoing']);

        TupadParticipation::where('status', 'Ongoing')
            ->where('end_date', '<', $now->toDateString())
            ->update(['status' => 'Completed']);

        // --- SHARED SEARCH SCOPE ---
        $searchQuery = function ($query) {
            $query->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('middle_name', 'like', '%' . $this->search . '%')
                        ->orWhere('extension', 'like', '%' . $this->search . '%');
                });
            });
        };

        $adultCutoffDate = Carbon::now()->subYears(18)->toDateString();
        // 1. AUTO-ELIGIBLE: 18+ AND no active work OR cooldown record
        $eligible = Resident::with(['demographic', 'household.areaStreet'])
            ->tap($searchQuery)
            ->whereHas('demographic', function ($q) use ($adultCutoffDate) {
                $q->where('birthdate', '<=', $adultCutoffDate);
            })
            ->whereDoesntHave('tupadParticipations', function ($q) use ($now, $threeMonthsAgo) {
                $q->where('end_date', '>=', $now) // Active
                ->orWhere('end_date', '>', $threeMonthsAgo); // Cooldown
            })->paginate(15);

        // 2. AUTO-ONGOING: Status is 'Ongoing' AND contract hasn't expired
        $ongoing = Resident::with(['demographic', 'tupadParticipations'])
            ->tap($searchQuery)
            ->whereHas('tupadParticipations', function ($q) use ($now) {
                $q->whereIn('status', ['Ongoing', 'Scheduled']); // Records older than $now were already updated to 'Completed' above
            })->paginate(15);

        // 5. INELIGIBLE: Residents who recently exited (Finished or Dropped)
        $ineligible = Resident::with(['demographic', 'tupadParticipations'])
            ->tap($searchQuery)
            ->whereHas('tupadParticipations', function ($q) use ($threeMonthsAgo, $now) {
                $q->whereIn('status', ['Completed', 'Dropped'])
                ->where('end_date', '>', $threeMonthsAgo)
                ->where('end_date', '<', $now);
            })->paginate(15);

        // 4. DROPPED: Everyone who has ever been dropped (Full History)
        $dropped = Resident::with(['demographic', 'tupadParticipations'])
            ->tap($searchQuery)
            ->whereHas('tupadParticipations', fn($q) => $q->where('status', 'Dropped'))
            ->paginate(15);

        // 5. UNIFIED COUNTS
        $counts = [
            'eligible' => Resident::whereHas('demographic', fn($q) => $q->where('birthdate', '<=', $adultCutoffDate))
            ->whereDoesntHave('tupadParticipations', function($q) use ($now, $threeMonthsAgo) {
                $q->where('end_date', '>=', $now)->orWhere('end_date', '>', $threeMonthsAgo);
            })->count(),
            'ongoing' => TupadParticipation::where('status', 'Ongoing')->count(),
            'ineligible' => TupadParticipation::whereIn('status', ['Completed', 'Dropped'])
                ->where('end_date', '>', $threeMonthsAgo)->count(),
            'dropped' => TupadParticipation::where('status', 'Dropped')->count(),
            'scheduled' => TupadParticipation::where('status', 'Scheduled')->count(),
        ];

        $this->dispatch('update-counts', ...$counts);

        return view('livewire.admin.tupad-management', [
            'eligible' => Resident::with(['demographic', 'household.areaStreet'])->tap($searchQuery)->whereDoesntHave('tupadParticipations', function($q) use ($now, $threeMonthsAgo) {
                $q->where('end_date', '>=', $now)->orWhere('end_date', '>', $threeMonthsAgo);
            })->paginate(15),
            'eligible' => $eligible,
            'ongoing' => $ongoing,
            'ineligible' => $ineligible,
            'dropped' => Resident::with(['demographic', 'tupadParticipations'])->tap($searchQuery)->whereHas('tupadParticipations', fn($q) => $q->where('status', 'Dropped'))->paginate(15),
            'eligibleCount' => $counts['eligible'],
            'ongoingCount' => $counts['ongoing'],
            'ineligibleCount' => $counts['ineligible'],
            'droppedCount' => $counts['dropped'],
            'scheduledCount' => $counts['scheduled'],
           
        ]);
    }
}