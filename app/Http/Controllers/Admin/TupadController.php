<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\TupadParticipation;

class TupadController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.tupad.index');
    }

   public function employ(Request $request)
    {
        $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Check for existing active participation (Ongoing OR Pending) [cite: 2025-12-04]
        $exists = TupadParticipation::where('resident_id', $request->resident_id)
            ->whereIn('status', ['Ongoing', 'Scheduled']) 
            ->exists();

        if ($exists) {
            return back()->with('error', 'This resident is already employed or scheduled.');
        }

        // Determine status based on start_date [cite: 2025-12-04]
        $startDate = Carbon::parse($request->start_date);
        $status = $startDate->isToday() ? 'Ongoing' : 'Scheduled';

        TupadParticipation::create([
            'resident_id' => $request->resident_id,
            'processed_by_user_id' => auth()->id(),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $status,
        ]);

        return back()->with('success', "Resident successfully scheduled as $status.");
    }

    public function drop(Request $request)
    {
        $request->validate([
            'participation_id' => 'required|exists:tupad_participations,id',
            'drop_reason' => 'required|string|min:5',
        ]);

        $participation = TupadParticipation::findOrFail($request->participation_id);

        $participation->update([
            'status' => 'Dropped',
            'drop_reason' => $request->drop_reason,
            'end_date' => now(), // CRITICAL: This closes the participation today [cite: 2025-12-04]
            'dropped_by_user_id' => auth()->id(), // NEW: Track who dropped the resident [cite: 2025-12-04]
        ]);

        return back()->with('success', 'Resident dropped and cooldown started.');
    }

    public function update(Request $request)
    {
        $participation = TupadParticipation::findOrFail($request->participation_id);
        $isOngoing = $participation->status === 'Ongoing';
        $isScheduled = $participation->status === 'Scheduled';

        // 1. DYNAMIC VALIDATION RULES [cite: 2025-12-04]
        $rules = [
            'participation_id' => 'required|exists:tupad_participations,id',
        ];

        if ($isOngoing) {
            // ONGOING: Start date is locked. End date must be >= Today AND > Start Date [cite: 2025-12-04]
            $rules['end_date'] = 'required|date|after_or_equal:today|after:' . $participation->start_date;
        } else if ($isScheduled) {
            // PENDING: Both editable. Start >= Today. End > Start AND >= Today [cite: 2025-12-04]
            $rules['start_date'] = 'required|date|after_or_equal:today';
            $rules['end_date'] = 'required|date|after_or_equal:today|after:start_date';
        }

        $messages = [
            'end_date.after' => 'The end date must be a date after ' . \Carbon\Carbon::parse($participation->start_date)->format('M d, Y') . '.',
            'end_date.after_or_equal' => 'The end date must be a date after or equal to today (' . now()->format('M d, Y') . ').',
        ];
        $request->validate($rules, $messages);

        $data = [
                'end_date' => $request->end_date,
                'updated_by_user_id' => auth()->id(),
                ];
                

        if (!$isOngoing) {
            $data['start_date'] = $request->start_date;
            
            // AUTO-TRANSITION: If they move a Pending start date to "Today" [cite: 2025-12-04]
            if (Carbon::parse($request->start_date)->isToday()) {
                $data['status'] = 'Ongoing';
            }
        }

        $participation->update($data);

        return back()->with('success', 'Project dates updated successfully.');
    }
    

}