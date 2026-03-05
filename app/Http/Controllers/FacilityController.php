<?php

namespace App\Http\Controllers;

use App\Models\FacilityReservation;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\Resident;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationForPaymentMail;
use App\Mail\ReservationRejectedMail;
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
        
        $threeDaysAgo = \Carbon\Carbon::now()->subDays(3);
        
        $reservations = FacilityReservation::with(['facility', 'resident', 'equipments', 'processedBy'])
            ->where(function($query) use ($threeDaysAgo) {
                $query->whereIn('status', ['For Approval', 'For Payment', 'Paid'])
                ->orWhere('status', 'Completed')
                ->orWhere(function($q) use ($threeDaysAgo) {
                    $q->whereIn('status', ['Cancelled', 'Rejected'])
                      ->where('updated_at', '>=', $threeDaysAgo);
                });
            })
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

        $tab = request()->get('tab', 'all');
        $perPage = 5;
        $page = request()->get('page', 1);
        $threeDaysAgo = \Carbon\Carbon::now()->subDays(3);

        // Get all unique reservation IDs that have equipment - filtered by tab
        $baseQuery = DB::table('facility_reservations')
            ->join('reservation_equipment', 'facility_reservations.id', '=', 'reservation_equipment.facility_reservation_id')
            ->select('facility_reservations.id');

        // Exclude old Returned/Cancelled/Rejected (older than 3 days) - they go to history
        $baseQuery->where(function($q) use ($threeDaysAgo) {
            $q->whereNotIn('reservation_equipment.status', ['Returned', 'Cancelled', 'Rejected'])
              ->orWhere(function($inner) use ($threeDaysAgo) {
                  $inner->whereIn('reservation_equipment.status', ['Returned', 'Cancelled', 'Rejected'])
                        ->where('reservation_equipment.updated_at', '>=', $threeDaysAgo);
              });
        });

        if ($tab !== 'all') {
            $baseQuery->where('reservation_equipment.status', $tab);
        }

        $uniqueReservationIds = $baseQuery
            ->distinct()
            ->orderBy('facility_reservations.created_at', 'desc')
            ->pluck('facility_reservations.id')
            ->toArray();

        $totalUniqueReservations = count($uniqueReservationIds);
        $offset = ($page - 1) * $perPage;
        $paginatedIds = array_slice($uniqueReservationIds, $offset, $perPage);

        if (!empty($paginatedIds)) {
            $query = DB::table('facility_reservations')
                ->join('reservation_equipment', 'facility_reservations.id', '=', 'reservation_equipment.facility_reservation_id')
                ->leftJoin('residents', 'facility_reservations.resident_id', '=', 'residents.id')
                ->select(
                    'facility_reservations.id as reservation_id',
                    'facility_reservations.event_name',
                    'facility_reservations.renter_name',
                    'facility_reservations.start_date',
                    'facility_reservations.created_at',
                    'reservation_equipment.status as equipment_status',
                    DB::raw("COALESCE(CONCAT(residents.first_name, ' ', residents.last_name), facility_reservations.renter_name) as resident_name")
                )
                ->whereIn('facility_reservations.id', $paginatedIds);

            // Apply same filter - exclude old Returned/Cancelled/Rejected
            $query->where(function($q) use ($threeDaysAgo) {
                $q->whereNotIn('reservation_equipment.status', ['Returned', 'Cancelled', 'Rejected'])
                  ->orWhere(function($inner) use ($threeDaysAgo) {
                      $inner->whereIn('reservation_equipment.status', ['Returned', 'Cancelled', 'Rejected'])
                            ->where('reservation_equipment.updated_at', '>=', $threeDaysAgo);
                  });
            });

            if ($tab !== 'all') {
                $query->where('reservation_equipment.status', $tab);
            }

            $equipmentReservations = $query
                ->orderBy('facility_reservations.created_at', 'desc')
                ->get()
                ->unique('reservation_id')
                ->values();
        } else {
            $equipmentReservations = collect();
        }

            $equipmentReservationsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $equipmentReservations,
            $totalUniqueReservations,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        $equipmentStats = [];

        $equipmentStatusCounts = DB::table('reservation_equipment')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Define currentMonth and threeDaysAgo for history queries
        $currentMonth = \Carbon\Carbon::now()->format('Y-m');
        $threeDaysAgo = \Carbon\Carbon::now()->subDays(3);

        $historyReservations = FacilityReservation::with(['facility'])
            ->where(function($query) use ($currentMonth, $threeDaysAgo) {
                // Completed reservations from previous months (not current month)
                $query->where(function($q) use ($currentMonth) {
                    $q->where('status', 'Completed')
                      ->whereRaw("DATE_FORMAT(updated_at, '%Y-%m') < ?", [$currentMonth]);
                })
                // Cancelled/Rejected reservations older than 3 days
                ->orWhere(function($q) use ($threeDaysAgo) {
                    $q->whereIn('status', ['Cancelled', 'Rejected'])
                      ->where('updated_at', '<', $threeDaysAgo);
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'history_page');

            $equipmentBorrowedHistory = DB::table('facility_reservations')
            ->join('reservation_equipment', 'facility_reservations.id', '=', 'reservation_equipment.facility_reservation_id')
            ->leftJoin('residents', 'facility_reservations.resident_id', '=', 'residents.id')
            ->leftJoin('equipments', 'reservation_equipment.equipment_id', '=', 'equipments.id')
            ->select(
                'reservation_equipment.id as equipment_id',
                'facility_reservations.id as reservation_id',
                'facility_reservations.event_name',
                'facility_reservations.renter_name',
                'facility_reservations.start_date',
                'reservation_equipment.status as equipment_status',
                'equipments.equipment_type',
                'reservation_equipment.quantity_borrowed',
                'reservation_equipment.updated_at',
                DB::raw("COALESCE(CONCAT(residents.first_name, ' ', residents.last_name), facility_reservations.renter_name) as resident_name")
            )
            ->where(function($query) use ($threeDaysAgo) {
                // Returned, Cancelled, Rejected older than 3 days
                $query->whereIn('reservation_equipment.status', ['Returned', 'Cancelled', 'Rejected'])
                      ->where('reservation_equipment.updated_at', '<', $threeDaysAgo);
            })
            ->orderBy('reservation_equipment.updated_at', 'desc')
            ->paginate(10, ['*'], 'equipment_history_page');

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

        return view('transaction.facility', compact('facilities', 'equipments', 'residents', 'reservations', 'events', 'equipmentStats', 'jsReservations', 'equipmentReservationsPaginated', 'tab', 'equipmentStatusCounts', 'historyReservations', 'equipmentBorrowedHistory'));
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

            // Use array instead of stdClass to safely access columns
            $rawReservation = (array) DB::table('facility_reservations')->where('id', $id)->first();

            $formatTime = function($time) {
                if (!$time) return null;
                try {
                    return \Carbon\Carbon::createFromFormat('H:i:s', $time)->format('g:i A');
                } catch (\Exception $e) {
                    try {
                        return \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A');
                    } catch (\Exception $e2) {
                        return $time;
                    }
                }
            };

            $safeGet = function($key) use ($rawReservation) {
                return array_key_exists($key, $rawReservation) ? $rawReservation[$key] : null;
            };

            $safeDate = function($key) use ($safeGet) {
                $val = $safeGet($key);
                if (!$val) return null;
                return \Carbon\Carbon::parse($val)->format('F d, Y h:i A');
            };

            // Get equipment delivery and return info
            $deliveredByName = null;
            $dateDelivered = null;
            $returnedByName = null;
            $dateReturned = null;
            $equipmentStatus = null;

            $equipmentRecord = DB::table('reservation_equipment')
                ->where('facility_reservation_id', $id)
                ->first();

            if ($equipmentRecord) {
                $eqArray = (array) $equipmentRecord;
                $equipmentStatus = $eqArray['status'] ?? null;
                $deliveredByName = $eqArray['delivered_by_name'] ?? null;
                $dateDelivered = !empty($eqArray['date_delivered'])
                    ? \Carbon\Carbon::parse($eqArray['date_delivered'])->format('F d, Y h:i A')
                    : null;
                $returnedByName = $eqArray['returned_by_name'] ?? null;
                $dateReturned = !empty($eqArray['date_returned'])
                    ? \Carbon\Carbon::parse($eqArray['date_returned'])->format('F d, Y h:i A')
                    : null;
            }

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
                'time_start' => $formatTime($safeGet('time_start')),
                'time_end' => $formatTime($safeGet('time_end')),
                'status' => $reservation->status,
                'created_at' => $reservation->created_at ? $reservation->created_at->format('F d, Y h:i A') : null,
                'for_payment_at' => $safeDate('for_payment_at'),
                'paid_at' => $safeDate('paid_at'),
                'completed_at' => $safeDate('completed_at'),
                'date_of_cancelled' => $safeDate('date_of_cancelled'),
                'date_of_rejected' => $safeDate('date_of_rejected'),
                'processed_by' => $getUserName($safeGet('processed_by_user_id')),
                'transferred_for_payment_by' => $getUserName($safeGet('transferred_for_payment_by_user_id')),
                'transferred_paid_by' => $getUserName($safeGet('transferred_paid_by_user_id')),
                'completed_by' => $getUserName($safeGet('completed_by_user_id')),
                'cancelled_by' => $getUserName($safeGet('cancelled_by_user_id')),
                'rejected_by' => $getUserName($safeGet('rejected_by_user_id')),
                'equipment_status' => $equipmentStatus,
                'delivered_by_name' => $deliveredByName,
                'date_delivered' => $dateDelivered,
                'returned_by_name' => $returnedByName,
                'date_returned' => $dateReturned,
                'equipments' => $reservation->equipments->map(function($eq) {
                    return [
                        'id' => $eq->id,
                        'equipment_type' => $eq->equipment_type,
                        'quantity_borrowed' => $eq->pivot->quantity_borrowed ?? null,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            \Log::error('Show reservation error: ' . $e->getMessage() . ' at line ' . $e->getLine() . ' in ' . $e->getFile());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function addEquipment(Request $request)
    {
        $request->validate([
            'type_id' => 'required|integer|exists:equipments,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $equipment = \App\Models\Equipment::find($request->type_id);
        if (!$equipment) {
            return response()->json(['success' => false, 'message' => 'Equipment not found.']);
        }
        $equipment->total_quantity += $request->quantity;
        $equipment->save();

        return response()->json(['success' => true]);
    }

    public function updateEquipmentReservationStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:For Approval,For Delivery,Delivered,Returned,Rejected,Cancelled',
        ]);

        $row = DB::table('reservation_equipment')->where('id', $id)->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Equipment reservation not found.'], 404);
        }

        // If status is Returned, add quantity back to total_quantity
        if ($validated['status'] === 'Returned') {
            $equipment = Equipment::find($row->equipment_id);
            if ($equipment) {
                $equipment->total_quantity += $row->quantity_borrowed;
                $equipment->save();
            }
        }

        // If status is Rejected or Cancelled, add quantity back to total_quantity
        if (in_array($validated['status'], ['Rejected', 'Cancelled'])) {
            $equipment = Equipment::find($row->equipment_id);
            if ($equipment) {
                $equipment->total_quantity += $row->quantity_borrowed;
                $equipment->save();
            }
        }

        DB::table('reservation_equipment')->where('id', $id)->update([
            'status' => $validated['status'],
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Equipment status updated successfully.']);
            } catch (\Exception $e) {
                \Log::error('❌ Error updating equipment status:', ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }

    public function checkEquipmentStatus($reservationId)
    {
        $equipments = DB::table('reservation_equipment')
            ->where('facility_reservation_id', $reservationId)
            ->get();
        
        $unreturned = $equipments->whereNotIn('status', ['Returned', 'Cancelled', 'Rejected']);
        
        $unretrurnedStatuses = $unreturned->pluck('status')->unique()->toArray();

        return response()->json([
            'hasUnreturned' => $unreturned->count() > 0,
            'unretrurnedStatuses' => $unretrurnedStatuses
        ]);
    }

    public function getEquipmentTypes()
    {
        $types = \App\Models\Equipment::select('id', 'equipment_type')->get();
        return response()->json($types);
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
                'time_end' => 'required|after:time_start',
                'equipment_type' => 'nullable|array',
                'equipment_type.*' => 'nullable|exists:equipments,id',
                'equipment_quantity' => 'nullable|array',
                'equipment_quantity.*' => 'nullable|numeric|min:1',
            ]);

            \Log::info('✅ VALIDATION PASSED', $validated);

            // Time validation
            $timeStart = strtotime($request->time_start);
            $timeEnd = strtotime($request->time_end);

            if ($timeEnd <= $timeStart) {
                return response()->json([
                    'success' => false,
                    'message' => 'End time must be after start time.'
                ], 422);
            }

            // Check for overlapping reservations
            $overlap = FacilityReservation::where('facility_id', $request->facility_id)
                ->whereIn('status', ['For Approval', 'For Payment', 'Paid'])
                ->where(function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->end_date)
                        ->where('end_date', '>=', $request->start_date);
                    });
                })
                ->where(function ($query) use ($request) {
                    $query->where('time_start', '<', $request->time_end)
                        ->where('time_end', '>', $request->time_start);
                })
                ->exists();

            if ($overlap) {
                return response()->json([
                    'success' => false,
                    'message' => 'This time slot is already booked for the selected facility and date.'
                ], 422);
            }

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

            // Handle resident vs non-resident
            if ($validated['resident_type'] === 'resident') {
                if (!$validated['resident_id']) {
                    throw new \Exception('Resident ID is required for resident type');
                }
                $resident = Resident::find($validated['resident_id']);
                if (!$resident) {
                    \Log::error('❌ Resident not found:', ['resident_id' => $validated['resident_id']]);
                    throw new \Exception('Resident not found');
                }
                $reservationData['resident_id'] = $validated['resident_id'];
                $reservationData['renter_name'] = $resident->first_name . ' ' . $resident->last_name;
                $reservationData['renter_contact'] = $resident->contact_number ?? '';
            } else {
                $reservationData['renter_name'] = $validated['renter_name'] ?? '';
                $reservationData['renter_contact'] = $validated['renter_contact'] ?? '';
            }

            \Log::info('Creating reservation with:', $reservationData);

            $reservation = FacilityReservation::create($reservationData);

            \Log::info('✅ RESERVATION CREATED:', ['id' => $reservation->id]);

            // Handle equipment
            $equipmentTypes = $request->input('equipment_type', []);
            $equipmentQuantities = $request->input('equipment_quantity', []);

            if (!empty($equipmentTypes)) {
                foreach ($equipmentTypes as $index => $equipmentId) {
                    $quantity = $equipmentQuantities[$index] ?? null;
                    
                    if (!empty($equipmentId) && !empty($quantity)) {
                        // Validate equipment exists
                        $equipment = Equipment::find($equipmentId);
                        if (!$equipment) {
                            throw new \Exception("Equipment with ID {$equipmentId} not found");
                        }

                        // Check if enough quantity available
                        if ($equipment->total_quantity < $quantity) {
                            throw new \Exception("Not enough {$equipment->equipment_type} available. Available: {$equipment->total_quantity}, Requested: {$quantity}");
                        }

                        // Insert into reservation_equipment with 'For Approval' status
                        DB::table('reservation_equipment')->insert([
                            'facility_reservation_id' => $reservation->id,
                            'equipment_id' => (int) $equipmentId,
                            'quantity_borrowed' => (int) $quantity,
                            'status' => 'For Approval',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        // Subtract from equipment total_quantity
                        $equipment->total_quantity -= $quantity;
                        $equipment->save();

                        \Log::info('✅ Equipment saved:', [
                            'equipment_id' => $equipmentId, 
                            'quantity' => $quantity,
                            'remaining' => $equipment->total_quantity
                        ]);
                    }
                }
            }

            DB::commit();
            \Log::info('=== ✅ RESERVATION SAVED SUCCESSFULLY ===');
            
            // Return JSON for fetch request
            return response()->json(['success' => true, 'message' => 'Reservation created successfully!']);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('❌ VALIDATION FAILED:', $e->errors());
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ ERROR:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getAvailableTimes(Request $request)
    {
        $facilityId = $request->input('facility_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date', $startDate);

        $bookedHours = [];

        // Only count reservations that are NOT rejected or cancelled
        $reservations = FacilityReservation::where('facility_id', $facilityId)
            ->whereNotIn('status', ['Rejected', 'Cancelled'])  // ADDED THIS LINE
            ->where(function($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $startDate);
            })
            ->get();

        foreach ($reservations as $reservation) {
            $timeStart = (int) substr($reservation->getRawOriginal('time_start'), 0, 2);
            $timeEnd = (int) substr($reservation->getRawOriginal('time_end'), 0, 2);
            
            for ($h = $timeStart; $h < $timeEnd; $h++) {
                if (!in_array($h, $bookedHours)) {
                    $bookedHours[] = $h;
                }
            }
        }

        return response()->json([
            'booked_hours' => $bookedHours
        ]);
    }

    public function cancelReservation(Request $request, $id)
    {
        try {
            $reservation = FacilityReservation::findOrFail($id);
            $reservation->status = 'Cancelled';
            $reservation->cancelled_by_user_id = auth()->id();
            $reservation->date_of_cancelled = now();
            $reservation->save();

            // Update all related equipment reservations to Cancelled
            DB::table('reservation_equipment')
                ->where('facility_reservation_id', $reservation->id)
                ->update([
                    'status' => 'Cancelled',
                    'updated_at' => now(),
                ]);

            // Return all equipment quantities back to stock
            $equipments = DB::table('reservation_equipment')
                ->where('facility_reservation_id', $reservation->id)
                ->get();

            foreach ($equipments as $equipment) {
                $eq = Equipment::find($equipment->equipment_id);
                if ($eq) {
                    $eq->total_quantity += $equipment->quantity_borrowed;
                    $eq->save();
                }
            }

            // Send email to user
            if ($reservation->email) {
                try {
                    Mail::to($reservation->email)->send(new \App\Mail\ReservationCancelledMail($reservation, $request->reason));
                } catch (\Exception $e) {
                    \Log::error('❌ Failed to send cancellation email:', ['error' => $e->getMessage()]);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('❌ Error cancelling reservation:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function rejectReservation(Request $request, $id)
    {
        try {
            $reservation = FacilityReservation::findOrFail($id);
            $reservation->status = 'Rejected';
            $reservation->rejected_by_user_id = auth()->id();
            $reservation->date_of_rejected = now();
            $reservation->save();

            // Update all related equipment reservations to Rejected
            DB::table('reservation_equipment')
                ->where('facility_reservation_id', $reservation->id)
                ->update([
                    'status' => 'Rejected',
                    'updated_at' => now(),
                ]);

            // Return all equipment quantities back to stock
            $equipments = DB::table('reservation_equipment')
                ->where('facility_reservation_id', $reservation->id)
                ->get();

            foreach ($equipments as $equipment) {
                $eq = Equipment::find($equipment->equipment_id);
                if ($eq) {
                    $eq->total_quantity += $equipment->quantity_borrowed;
                    $eq->save();
                }
            }

            // Send email to user
            if ($reservation->email) {
                try {
                    Mail::to($reservation->email)->send(new \App\Mail\ReservationRejectedMail($reservation, $request->reason));
                } catch (\Exception $e) {
                    \Log::error('❌ Failed to send rejection email:', ['error' => $e->getMessage()]);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('❌ Error rejecting reservation:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function markAsPaid(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'amount_paid' => 'required|numeric|min:0.01',
                'mode_of_payment' => 'required|string|max:100',
                'or_number' => 'required|string|max:100',
            ]);

            $reservation = FacilityReservation::findOrFail($id);
            
            $reservation->status = 'Paid';
            $reservation->amount_paid = $validated['amount_paid'];
            $reservation->mode_of_payment = $validated['mode_of_payment'];
            $reservation->or_number = $validated['or_number'];
            $reservation->transferred_paid_by_user_id = Auth::id();
            $reservation->paid_at = now();
            $reservation->save();

            DB::table('reservation_equipment')
                ->where('facility_reservation_id', $reservation->id)
                ->update([
                    'status' => 'For Delivery',
                    'updated_at' => now(),
                ]);

            // Send payment confirmation email
            if ($reservation->email) {
                try {
                    Mail::to($reservation->email)->send(
                        new \App\Mail\ReservationPaidMail(
                            $reservation,
                            $validated['amount_paid'],
                            $validated['mode_of_payment'],
                            $validated['or_number']
                        )
                    );
                    \Log::info('✅ Paid email sent:', ['email' => $reservation->email]);
                } catch (\Exception $e) {
                    \Log::error('❌ Failed to send paid email:', ['error' => $e->getMessage()]);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('❌ Error marking as paid:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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

    public function getEquipmentDetails($id)
    {
        try {
            $reservation = FacilityReservation::with(['facility', 'resident', 'equipments'])->findOrFail($id);

            $getUserName = function($userId) {
                if (!$userId) return null;
                $user = \App\Models\User::find($userId);
                return $user ? trim($user->first_name . ' ' . $user->last_name) : null;
            };

            $rawRes = (array) DB::table('facility_reservations')->where('id', $id)->first();

            $safeGet = function($key) use ($rawRes) {
                return array_key_exists($key, $rawRes) ? $rawRes[$key] : null;
            };

            $formatTime = function($time) {
                if (!$time) return null;
                try {
                    return \Carbon\Carbon::createFromFormat('H:i:s', $time)->format('g:i A');
                } catch (\Exception $e) {
                    try {
                        return \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A');
                    } catch (\Exception $e2) {
                        return $time;
                    }
                }
            };

            $eqRecord = DB::table('reservation_equipment')
                ->where('facility_reservation_id', $id)
                ->first();

            $eq = $eqRecord ? (array) $eqRecord : [];

            return response()->json([
                'id' => $reservation->id,
                'event_name' => $reservation->event_name,
                'renter_name' => $reservation->renter_name,
                'start_date' => $reservation->start_date,
                'time_start' => $formatTime($safeGet('time_start')),
                'time_end' => $formatTime($safeGet('time_end')),
                'equipment_status' => $eq['status'] ?? null,
                'equipment_id' => $eq['id'] ?? null,
                'processed_by' => $getUserName($safeGet('processed_by_user_id')),
                'transferred_for_payment_by' => $getUserName($safeGet('transferred_for_payment_by_user_id')),
                'transferred_paid_by' => $getUserName($safeGet('transferred_paid_by_user_id')),
                'created_at' => $safeGet('created_at'),
                'for_payment_at' => $safeGet('for_payment_at'),
                'paid_at' => $safeGet('paid_at'),
                'delivered_by_name' => $eq['delivered_by_name'] ?? null,
                'date_delivered' => $eq['date_delivered'] ?? null,
                'returned_by_name' => $eq['returned_by_name'] ?? null,
                'date_returned' => $eq['date_returned'] ?? null,
                'received_by' => $getUserName($eq['received_by_user_id'] ?? null),
                'equipments' => $reservation->equipments->map(function($equipment) {
                    return [
                        'id' => $equipment->id,
                        'equipment_type' => $equipment->equipment_type,
                        'quantity_borrowed' => $equipment->pivot->quantity_borrowed ?? null,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            \Log::error('Get equipment details error: ' . $e->getMessage() . ' at line ' . $e->getLine());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateReservationStatus(Request $request, $id)
    {
        try {
            $reservation = FacilityReservation::findOrFail($id);
            $status = $request->input('status');

            $reservation->status = $status;

            if ($status === 'For Payment') {
                $reservation->transferred_for_payment_by_user_id = Auth::id();
                $reservation->for_payment_at = now();
            } elseif ($status === 'Paid') {
                $reservation->transferred_paid_by_user_id = Auth::id();
                $reservation->paid_at = now();
            } elseif ($status === 'Completed') {
                $reservation->completed_by_user_id = Auth::id();
                $reservation->completed_at = now();
            } elseif ($status === 'Rejected') {
                $reservation->rejected_by_user_id = Auth::id();
                $reservation->rejected_at = now();
            }

            $reservation->save();

            return response()->json(['success' => true, 'message' => 'Reservation status updated successfully']);
        } catch (\Exception $e) {
            \Log::error('Error updating reservation status:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function markEquipmentAsDelivered($id)
    {
        try {
            $request = request();
            $delivered_by_name = $request->input('delivered_by_name');

            if (!$delivered_by_name || !trim($delivered_by_name)) {
                return response()->json(['success' => false, 'message' => 'Delivered by name is required'], 422);
            }

            $equipmentReservation = DB::table('reservation_equipment')->where('id', $id)->first();
            
            if (!$equipmentReservation) {
                return response()->json(['success' => false, 'message' => 'Equipment not found'], 404);
            }

            $reservation = FacilityReservation::with(['equipments'])->find($equipmentReservation->facility_reservation_id);

            // Update ALL equipment records for this reservation to Delivered
            DB::table('reservation_equipment')
                ->where('facility_reservation_id', $equipmentReservation->facility_reservation_id)
                ->update([
                    'status' => 'Delivered',
                    'delivered_by_name' => $delivered_by_name,
                    'date_delivered' => now(),
                    'processed_by_user_id' => Auth::id(),
                    'updated_at' => now(),
                ]);

            // Send delivery confirmation email
            if ($reservation && $reservation->email) {
                try {
                    \Illuminate\Support\Facades\Mail::to($reservation->email)->send(
                        new \App\Mail\EquipmentDeliveredMail(
                            $reservation,
                            $delivered_by_name,
                            now()->format('F d, Y h:i A')
                        )
                    );
                    \Log::info('✅ Delivery email sent:', ['email' => $reservation->email]);
                } catch (\Exception $e) {
                    \Log::error('❌ Failed to send delivery email:', ['error' => $e->getMessage()]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Equipment marked as delivered']);
        } catch (\Exception $e) {
            \Log::error('Error marking as delivered:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function markEquipmentAsReturned($id)
    {
        try {
            $request = request();
            $returned_by_name = $request->input('returned_by_name');

            if (!$returned_by_name || !trim($returned_by_name)) {
                return response()->json(['success' => false, 'message' => 'Returned by name is required'], 422);
            }

            $equipmentReservation = DB::table('reservation_equipment')->where('id', $id)->first();
            
            if (!$equipmentReservation) {
                return response()->json(['success' => false, 'message' => 'Equipment not found'], 404);
            }

            // Get all equipment records for this reservation
            $allEquipment = DB::table('reservation_equipment')
                ->where('facility_reservation_id', $equipmentReservation->facility_reservation_id)
                ->get();

            // Return all equipment quantities back to stock
            foreach ($allEquipment as $eq) {
                $equipment = Equipment::find($eq->equipment_id);
                if ($equipment) {
                    $equipment->total_quantity += $eq->quantity_borrowed;
                    $equipment->save();
                }
            }

            // Update ALL equipment records for this reservation to Returned
            DB::table('reservation_equipment')
                ->where('facility_reservation_id', $equipmentReservation->facility_reservation_id)
                ->update([
                    'status' => 'Returned',
                    'returned_by_name' => $returned_by_name,
                    'date_returned' => now(),
                    'received_by_user_id' => Auth::id(),
                    'updated_at' => now(),
                ]);

            return response()->json(['success' => true, 'message' => 'Equipment marked as returned']);
        } catch (\Exception $e) {
            \Log::error('Error marking as returned:', ['error' => $e->getMessage()]);
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