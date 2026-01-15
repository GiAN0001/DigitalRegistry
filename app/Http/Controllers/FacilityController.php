<?php

namespace App\Http\Controllers;

use App\Models\FacilityReservation;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FacilityController extends Controller
{
    public function index(): View
    {
        $facilities = Facility::all();
        $equipments = Equipment::all();
        $residents = Resident::all();
        
        $reservations = FacilityReservation::with(['facility', 'resident', 'equipments', 'processedBy'])
            ->orderBy('start_date')
            ->orderBy('time_start')
            ->get();

        $events = $reservations->map(function($reservation) {
            return [
                'id' => $reservation->id,
                'title' => $reservation->event_name,
                'date' => \Carbon\Carbon::parse($reservation->start_date)->format('Y-m-d'),
                'time' => \Carbon\Carbon::parse($reservation->time_start)->format('g:i A'),
                'time_sort' => \Carbon\Carbon::parse($reservation->time_start)->format('H:i'),
                'resident_type' => $reservation->resident_type ?? 'resident',
            ];
        })->sortBy(['date', 'time_sort'])->values();

        return view('transaction.facility', compact('facilities', 'equipments', 'residents', 'reservations', 'events'));
    }

    public function storeReservation(Request $request)
    {
        \Log::info('=== FORM SUBMISSION RECEIVED ===');
        \Log::info('All form data:', $request->all());
        
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

            \Log::info('✅ VALIDATION PASSED', $validated);

            DB::beginTransaction();

            $fee = 0;
            if ($validated['resident_type'] === 'non-resident') {
                $facility = Facility::find($validated['facility_id']);
                $fee = $facility->non_resident_rate ?? 0;
            }

            // Build reservation data
            $reservationData = [
                'facility_id' => $validated['facility_id'],
                'resident_type' => $validated['resident_type'],
                'event_name' => $validated['event_name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'time_start' => $validated['time_start'],
                'time_end' => $validated['time_end'],
                'fee' => $fee,
                'status' => 'Pending',
                'processed_by_user_id' => Auth::id(),
            ];

            // Set renter_name and renter_contact based on resident type
            if ($validated['resident_type'] === 'resident') {
                $resident = Resident::find($validated['resident_id']);
                $reservationData['resident_id'] = $validated['resident_id'];
                $reservationData['renter_name'] = $resident->first_name . ' ' . $resident->last_name;
                $reservationData['renter_contact'] = $resident->contact_number ?? '';
            } else {
                $reservationData['renter_name'] = $validated['renter_name'];
                $reservationData['renter_contact'] = $validated['renter_contact'];
            }

            \Log::info('Creating reservation with:', $reservationData);

            $reservation = FacilityReservation::create($reservationData);

            \Log::info('✅ RESERVATION CREATED:', ['id' => $reservation->id]);

            // Save equipment to reservation_equipment table
            if (!empty($validated['equipment_type'])) {
                foreach ($validated['equipment_type'] as $index => $equipmentId) {
                    $quantity = $validated['equipment_quantity'][$index] ?? null;
                    
                    // Only insert if both equipment_id and quantity are provided
                    if (!empty($equipmentId) && !empty($quantity)) {
                        DB::table('reservation_equipment')->insert([
                            'facility_reservation_id' => $reservation->id,
                            'equipment_id' => (int) $equipmentId,
                            'quantity_borrowed' => (int) $quantity,
                            'status' => 'Pending Delivery',  // Use correct ENUM value
                        ]);
                        
                        \Log::info('✅ Equipment saved:', [
                            'equipment_id' => $equipmentId, 
                            'quantity' => $quantity
                        ]);
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
            return back()->with('error', 'Failed to create reservation: ' . $e->getMessage())->withInput();
        }
    }
}