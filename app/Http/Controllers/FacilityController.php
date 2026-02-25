<?php

namespace App\Http\Controllers;

use App\Models\FacilityReservation;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\Resident;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationForPaymentMail;
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
        $reservations = FacilityReservation::all();
        
        $reservations = FacilityReservation::with(['facility', 'resident', 'equipments', 'processedBy'])
            ->orderBy('start_date')
            ->orderBy('time_start')
            ->get();

        \Log::info('Reservations count:', ['count' => $reservations->count()]);

        $events = $reservations->map(function($reservation) {
            return [
                'id' => $reservation->id,
                'title' => $reservation->event_name,
                'date' => $reservation->start_date->format('Y-m-d'),
                'start' => $reservation->start_date->format('Y-m-d'),
                'end' => $reservation->end_date->format('Y-m-d'),
                'time' => \Carbon\Carbon::parse($reservation->time_start)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($reservation->time_end)->format('g:i A'),
                'time_sort' => $reservation->time_start,
                'status' => $reservation->status,
                'renter_name' => $reservation->renter_name,
                'resident_type' => $reservation->resident_type,
            ];
        })->toArray();

        \Log::info('Events for calendar:', $events);

        // === ADD THIS SECTION ===
        $jsReservations = $reservations->map(function($r) {
            return [
                'id' => $r->id,
                'facility_id' => $r->facility_id,
                'start_date' => \Carbon\Carbon::parse($r->start_date)->format('Y-m-d'),
                'end_date' => \Carbon\Carbon::parse($r->end_date)->format('Y-m-d'),
                'time_start' => DB::table('facility_reservations')->where('id', $r->id)->value('time_start'),
                'time_end' => DB::table('facility_reservations')->where('id', $r->id)->value('time_end'),
                'status' => $r->status,
            ];
        });
        // === END ADD ===

        $equipmentStats = [];

        return view('transaction.facility', compact('facilities', 'equipments', 'residents', 'reservations', 'events', 'equipmentStats', 'jsReservations'));
    }

    public function facility(): View
    {
        $facilities = Facility::all();
        $equipments = Equipment::all();
        $residents = Resident::all();
        
        $reservations = FacilityReservation::with(['facility', 'resident', 'equipments'])
            ->orderBy('start_date')
            ->get();

        $events = $reservations->map(function($reservation) {
            return [
                'id' => $reservation->id,
                'title' => $reservation->event_name,
                'start' => $reservation->start_date->format('Y-m-d'),
                'end' => $reservation->end_date->format('Y-m-d'),
                'status' => $reservation->status,
            ];
        })->toArray();

        return view('transaction.facility', compact('facilities', 'equipments', 'residents', 'reservations', 'events'));
    }

    public function show($id)
    {
        try {
            $reservation = FacilityReservation::with(['facility', 'resident', 'equipments'])->findOrFail($id);

            $getUserName = function($userId) {
                if (!$userId) return null;
                $user = \App\Models\User::find($userId);
                return $user ? trim($user->first_name . ' ' . $user->last_name) : null;
            };

            // Get raw time values directly from database
            $rawReservation = DB::table('facility_reservations')->where('id', $id)->first();

            // Log raw time values for debugging
            \Log::info('Raw time_start: ' . $rawReservation->time_start);
            \Log::info('Raw time_end: ' . $rawReservation->time_end);

            // Format time - handle both "HH:MM:SS" and "HH:MM" formats
            $formatTime = function($time) {
                if (!$time) return null;
                try {
                    return \Carbon\Carbon::createFromFormat('H:i:s', $time)->format('g:i A');
                } catch (\Exception $e) {
                    try {
                        return \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A');
                    } catch (\Exception $e2) {
                        return $time; // Return raw value if parsing fails
                    }
                }
            };

            return response()->json([
                'id' => $reservation->id,
                'event_name' => $reservation->event_name,
                'facility_name' => $reservation->facility->facility_type ?? 'N/A',
                'purpose_category' => $reservation->purpose_category,
                'resident_type' => ucfirst($reservation->resident_type),
                'renter_name' => $reservation->renter_name,
                'renter_contact' => $reservation->renter_contact,
                'email' => $reservation->email,
                'start_date' => $reservation->start_date ? \Carbon\Carbon::parse($reservation->start_date)->format('F d, Y') : null,
                'end_date' => $reservation->end_date ? \Carbon\Carbon::parse($reservation->end_date)->format('F d, Y') : null,
                'time_start' => $formatTime($rawReservation->time_start),
                'time_end' => $formatTime($rawReservation->time_end),
                'status' => $reservation->status,
                'created_at' => $reservation->created_at ? $reservation->created_at->format('F d, Y h:i A') : null,
                'for_payment_at' => $rawReservation->for_payment_at ? \Carbon\Carbon::parse($rawReservation->for_payment_at)->format('F d, Y h:i A') : null,
                'paid_at' => $rawReservation->paid_at ? \Carbon\Carbon::parse($rawReservation->paid_at)->format('F d, Y h:i A') : null,
                'date_of_cancelled' => $rawReservation->date_of_cancelled ? \Carbon\Carbon::parse($rawReservation->date_of_cancelled)->format('F d, Y h:i A') : null,
                'date_of_rejected' => $rawReservation->date_of_rejected ? \Carbon\Carbon::parse($rawReservation->date_of_rejected)->format('F d, Y h:i A') : null,
                'processed_by' => $getUserName($reservation->processed_by_user_id),
                'transferred_for_payment_by' => $getUserName($reservation->transferred_for_payment_by_user_id),
                'transferred_paid_by' => $getUserName($reservation->transferred_paid_by_user_id),
                'cancelled_by' => $getUserName($reservation->cancelled_by_user_id),
                'rejected_by' => $getUserName($reservation->rejected_by_user_id),
                'equipments' => $reservation->equipments->map(function($eq) {
                    return [
                        'id' => $eq->id,
                        'equipment_type' => $eq->equipment_type,
                        'quantity_borrowed' => $eq->pivot->quantity_borrowed ?? null,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            \Log::error('Show reservation error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load reservation'], 500);
        }
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
                'purpose_category' => 'required|in:Sports,Non-Sports',
                'resident_id' => 'nullable|exists:residents,id',
                'renter_name' => 'nullable|string|max:255',
                'renter_contact' => 'nullable|string|max:30',
                'email' => 'required|email|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'time_start' => 'required',
                'time_end' => 'required|after:time_start',  // ← ADD THIS LINE
                'equipment_type' => 'nullable|array',
                'equipment_type.*' => 'nullable|exists:equipments,id',
                'equipment_quantity' => 'nullable|array',
                'equipment_quantity.*' => 'nullable|numeric|min:1',
            ]);

            $timeStart = strtotime($request->time_start);
            $timeEnd = strtotime($request->time_end);

            if ($timeEnd <= $timeStart) {
                return back()->withErrors([
                    'time_end' => 'End time must be after start time.'
                ])->withInput();
            }

            $overlap = FacilityReservation::where('facility_id', $request->facility_id)
                ->whereIn('status', ['For Approval', 'For Payment', 'Paid'])
                ->where(function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        // Date ranges overlap
                        $q->where('start_date', '<=', $request->end_date)
                          ->where('end_date', '>=', $request->start_date);
                    });
                })
                ->where(function ($query) use ($request) {
                    // Time ranges overlap
                    $query->where('time_start', '<', $request->time_end)
                          ->where('time_end', '>', $request->time_start);
                })
                ->exists();

            if ($overlap) {
                return back()->withErrors([
                    'time_start' => 'This time slot is already booked for the selected facility and date.'
                ])->withInput();
            }

            \Log::info('✅ VALIDATION PASSED', $validated);

            DB::beginTransaction();

            $reservationData = [
                'facility_id' => $validated['facility_id'],
                'resident_type' => $validated['resident_type'],
                'event_name' => $validated['event_name'],
                'purpose_category' => $validated['purpose_category'],
                'email' => $validated['email'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'time_start' => $validated['time_start'],
                'time_end' => $validated['time_end'],
                'status' => 'For Approval',
                'processed_by_user_id' => Auth::id(),
                'resident_id' => null,
                'renter_name' => null,
                'renter_contact' => null,
                'transferred_for_payment_by_user_id' => null,
                'transferred_paid_by_user_id' => null,
                'cancelled_by_user_id' => null,
                'rejected_by_user_id' => null,
                'for_payment_at' => null,
                'paid_at' => null,
                'date_of_cancelled' => null,
                'date_of_rejected' => null,
            ];

            // Set renter_name and renter_contact based on resident type
            if ($validated['resident_type'] === 'resident') {
                $resident = Resident::find($validated['resident_id']);
                if (!$resident) {
                    \Log::error('❌ Resident not found:', ['resident_id' => $validated['resident_id']]);
                    throw new \Exception('Resident not found');
                }
                $reservationData['resident_id'] = $validated['resident_id'];
                $reservationData['renter_name'] = $resident->first_name . ' ' . $resident->last_name;
                $reservationData['renter_contact'] = $resident->contact_number ?? $validated['renter_contact'] ?? '';
            } else {
                $reservationData['renter_name'] = $validated['renter_name'] ?? '';
                $reservationData['renter_contact'] = $validated['renter_contact'] ?? '';
            }

            \Log::info('Creating reservation with:', $reservationData);

            $reservation = FacilityReservation::create($reservationData);

            \Log::info('✅ RESERVATION CREATED:', ['id' => $reservation->id]);

            // Save equipment to reservation_equipment table
            if (!empty($validated['equipment_type'])) {
                foreach ($validated['equipment_type'] as $index => $equipmentId) {
                    $quantity = $validated['equipment_quantity'][$index] ?? null;
                    
                    if (!empty($equipmentId) && !empty($quantity)) {
                        DB::table('reservation_equipment')->insert([
                            'facility_reservation_id' => $reservation->id,
                            'equipment_id' => (int) $equipmentId,
                            'quantity_borrowed' => (int) $quantity,
                            'status' => 'Pending Delivery',
                            'created_at' => now(),
                            'updated_at' => now(),
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
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Failed to create reservation: ' . $e->getMessage())->withInput();
        }
    }

    public function approveForPayment(Request $request, $id)
    {
        try {
            \Log::info('=== APPROVE FOR PAYMENT STARTED ===', ['reservation_id' => $id]);
            
            $validated = $request->validate([
                'fee' => 'required|numeric|min:0'
            ]);
            
            $reservation = FacilityReservation::findOrFail($id);
            
            \Log::info('Reservation found:', ['id' => $reservation->id, 'status' => $reservation->status]);
            
            // Update reservation status and fee
            $reservation->status = 'For Payment';
            $reservation->transferred_for_payment_by_user_id = Auth::id();
            $reservation->for_payment_at = now();
            $reservation->save();
            
            \Log::info('✅ Reservation status updated:', ['id' => $id, 'new_status' => 'For Payment']);
            
            // Send email with fee
            if ($reservation->email) {
                try {
                    Mail::to($reservation->email)->send(
                        new ReservationForPaymentMail($reservation, $validated['fee'])
                    );
                    \Log::info('✅ Payment email sent:', ['email' => $reservation->email, 'fee' => $validated['fee']]);
                } catch (\Exception $e) {
                    \Log::error('❌ Email sending failed:', ['error' => $e->getMessage()]);
                    // Continue - don't fail the approval if email fails
                }
            }
            
            \Log::info('=== ✅ APPROVAL COMPLETED ===');
            
            return response()->json(['success' => true, 'message' => 'Reservation approved for payment!']);
            
        } catch (\Exception $e) {
            \Log::error('❌ ERROR IN APPROVE FOR PAYMENT:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateReservationStatus(Request $request, $id)
    {
        try {
            $reservation = FacilityReservation::with(['facility', 'resident', 'equipments'])->findOrFail($id);
            
            $validated = $request->validate([
                'status' => 'required|in:For Approval,For Payment,Paid,Cancelled,Rejected'
            ]);
            
            $reservation->update(['status' => $validated['status']]);

            // Send email notification when approved for payment
            if ($validated['status'] === 'For Payment' && $reservation->email) {
                try {
                    Mail::to($reservation->email)->send(new ReservationForPaymentMail($reservation));
                    \Log::info('✅ Payment email sent to: ' . $reservation->email);
                } catch (\Exception $e) {
                    \Log::error('❌ Failed to send payment email:', ['error' => $e->getMessage()]);
                    // Don't fail the status update if email fails
                }
            }
            
            \Log::info('✅ Status updated for reservation:', ['id' => $id, 'status' => $validated['status']]);
            
            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            \Log::error('❌ Error updating status:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function searchResidents(Request $request)
    {
        $query = $request->get('q');
        
        $residents = Resident::where('first_name', 'like', "%$query%")
            ->orWhere('last_name', 'like', "%$query%")
            ->select('id', 'first_name', 'last_name', 'email', 'contact_number')
            ->limit(10)
            ->get()
            ->map(function($resident) {
                return [
                    'id' => $resident->id,
                    'full_name' => $resident->first_name . ' ' . $resident->last_name,
                    'email' => $resident->email,
                    'contact_number' => $resident->contact_number,
                ];
            });
        
        return response()->json($residents);
    }
}