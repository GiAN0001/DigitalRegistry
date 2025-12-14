<?php

namespace App\Http\Controllers;

use App\Models\FacilityReservation;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class FacilityController extends Controller
{
    public function index(): View
    {
        $facilities = Facility::all();
        $equipments = Equipment::all();
        $residents = Resident::all();
        
        $reservations = FacilityReservation::with(['facility', 'resident', 'equipments', 'processedBy'])
            ->orderBy('start_date')
            ->orderBy('time_start')  // Add secondary sort by time
            ->get();

        // Format events for JavaScript/Alpine
        $events = $reservations->map(function($reservation) {
            return [
                'id' => $reservation->id,
                'title' => $reservation->event_name,
                'date' => \Carbon\Carbon::parse($reservation->start_date)->format('Y-m-d'),
                'time' => \Carbon\Carbon::parse($reservation->time_start)->format('g:i A'),
                'time_sort' => \Carbon\Carbon::parse($reservation->time_start)->format('H:i'), // 24hr format for sorting
                'resident_type' => $reservation->resident_type,
            ];
        })->sortBy(['date', 'time_sort'])->values();

        return view('transaction.facility', compact('facilities', 'equipments', 'residents', 'reservations', 'events'));
    }

    public function storeReservation(Request $request)
    {
        \Log::info('=== FORM SUBMISSION RECEIVED ===');
        \Log::info('All form data:', $request->all());
        \Log::info('Auth user:', ['id' => auth()->id()]);
        
        try {
            $validated = $request->validate([
                'resident_type' => 'required|in:resident,non-resident',
                'facility_id' => 'required|exists:facilities,id',
                'event_name' => 'required|string|max:255',
                'resident_id' => 'nullable|exists:residents,id',
                'renter_name' => 'nullable|string|max:255',
                'renter_contact' => 'nullable|string|max:30',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'time_start' => 'required',
                'time_end' => 'required',
                'equipment_type' => 'nullable|array',
                'equipment_type.*' => 'nullable|exists:equipments,id',
                'equipment_quantity' => 'nullable|array',
                'equipment_quantity.*' => 'nullable|numeric|min:1',
            ]);

            \Log::info('✅ VALIDATION PASSED');

            DB::beginTransaction();

            $fee = 0;
            if ($validated['resident_type'] === 'non-resident') {
                $facility = Facility::find($validated['facility_id']);
                $fee = $facility->non_resident_rate ?? 0;
            }

            $reservationData = [
                'facility_id' => $validated['facility_id'],
                'resident_type' => $validated['resident_type'],
                'event_name' => $validated['event_name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'time_start' => $validated['time_start'],
                'time_end' => $validated['time_end'],
                'fee' => $fee,
                'status' => 'Pending',  // Changed to match ENUM value
                'processed_by_user_id' => auth()->id(),
            ];

            if ($validated['resident_type'] === 'resident') {
                $reservationData['resident_id'] = $validated['resident_id'];
            } else {
                $reservationData['renter_name'] = $validated['renter_name'];
                $reservationData['renter_contact'] = $validated['renter_contact'];
            }

            \Log::info('Creating reservation with:', $reservationData);

            $reservation = FacilityReservation::create($reservationData);

            \Log::info('✅ RESERVATION CREATED:', ['id' => $reservation->id]);

            if (!empty($validated['equipment_type'])) {
                foreach ($validated['equipment_type'] as $index => $equipmentId) {
                    if (!empty($equipmentId) && isset($validated['equipment_quantity'][$index]) && !empty($validated['equipment_quantity'][$index])) {
                        DB::insert(
                            "INSERT INTO reservation_equipment (facility_reservation_id, equipment_id, quantity_borrowed, status, created_at, updated_at) VALUES (?, ?, ?, 'Pending Delivery', ?, ?)",
                            [
                                $reservation->id,
                                $equipmentId,
                                (int)$validated['equipment_quantity'][$index],
                                now(),
                                now(),
                            ]
                        );
                        \Log::info('✅ Equipment attached:', ['equipment_id' => $equipmentId]);
                    }
                }
            }

            DB::commit();
            \Log::info('=== ✅ RESERVATION SAVED SUCCESSFULLY ===');
            
            return redirect()->route('transaction.facility')->with('success', 'Reservation created successfully!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ VALIDATION FAILED:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ ERROR:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}