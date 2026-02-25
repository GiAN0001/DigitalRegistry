@props(['event'])

@php
    // Status-based border colors
    $statusColors = [
        'For Approval' => 'border-blue-600',
        'For Payment' => 'border-orange-600',
        'Paid' => 'border-green-600',
        'Cancelled' => 'border-red-600',
        'Rejected' => 'border-gray-600',
    ];
    
    $borderColor = $statusColors[$event->status] ?? 'border-blue-600';
    
    // Get renter name
    $renterName = $event->renter_name ?? 'N/A';
    
    // Format time
    $timeStart = \Carbon\Carbon::parse($event->time_start)->format('g:i A');
    $timeEnd = \Carbon\Carbon::parse($event->time_end)->format('g:i A');
    
    // Check if has equipment
    $hasEquipment = $event->equipments && $event->equipments->count() > 0;
    
    // Prepare reservation data for modal
    $reservationData = [
        'id' => $event->id,
        'event_name' => $event->event_name,
        'facility_name' => $event->facility->facility_type ?? 'N/A',
        'purpose_category' => $event->purpose_category,
        'resident_type' => $event->resident_type,
        'renter_name' => $renterName,
        'renter_contact' => $event->renter_contact ?? '',
        'email' => $event->email ?? '',
        'start_date' => \Carbon\Carbon::parse($event->start_date)->format('F d, Y'),
        'end_date' => \Carbon\Carbon::parse($event->end_date)->format('F d, Y'),
        'time_start' => $timeStart,
        'time_end' => $timeEnd,
        'status' => $event->status,
        'processed_by' => $event->processedBy->name ?? 'N/A',
        'created_at' => $event->created_at->format('F d, Y g:i A'),
        'equipments' => $event->equipments->map(function($eq) {
            return [
                'id' => $eq->id,
                'equipment_type' => $eq->equipment_type,
                'quantity_borrowed' => $eq->pivot->quantity_borrowed ?? 0
            ];
        })->toArray()
    ];
@endphp

<div class="bg-white rounded-lg border-l-4 {{ $borderColor }} shadow-md p-4 mb-3">
    {{-- Event Name --}}
    <h4 class="text-base font-bold text-slate-700 mb-2">{{ $event->event_name }}</h4>
    
    {{-- Renter/Resident Name --}}
    <p class="text-sm text-slate-700 font-semibold mb-1">{{ $renterName }}</p>
    
    {{-- Time --}}
    <p class="text-sm font-medium text-slate-700 mb-2">{{ $timeStart }} - {{ $timeEnd }}</p>
    
    {{-- Equipment Badge (if applicable) --}}
    @if($hasEquipment)
        <p class="text-sm font-medium text-slate-700 mb-3">Equipment Borrowed</p>
    @endif
    
    {{-- View Details Button --}}
    <button 
        type="button"
        @click="$dispatch('open-modal', 'view-reservation'); $dispatch('show-reservation', {{ json_encode($reservationData) }})"
        class="text-xs font-semibold text-blue-600 hover:text-blue-700"
    >
        View Details
    </button>
</div>