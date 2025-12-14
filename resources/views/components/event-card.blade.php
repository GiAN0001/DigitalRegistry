@props(['event'])

@php
    $isResident = strtolower($event->resident_type) === 'resident';
    $borderColor = $isResident ? 'border-blue-600' : 'border-gray-600';
    
    // Get renter name
    if ($isResident && $event->resident) {
        // Try different name attributes
        $renterName = $event->resident->full_name 
            ?? ($event->resident->first_name . ' ' . $event->resident->last_name)
            ?? $event->resident->name 
            ?? 'N/A';
    } else {
        $renterName = $event->renter_name ?? 'N/A';
    }
    
    // Format time
    $timeStart = \Carbon\Carbon::parse($event->time_start)->format('g:i A');
    $timeEnd = \Carbon\Carbon::parse($event->time_end)->format('g:i A');
    
    // Check if has equipment
    $hasEquipment = $event->equipments && $event->equipments->count() > 0;
@endphp

<div class="bg-white rounded-lg border-l-4 {{ $borderColor }} shadow-md p-4 mb-3">
    {{-- Event Name --}}
    <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $event->event_name }}</h4>
    
    {{-- Renter/Resident Name --}}
    <p class="text-sm text-gray-700 font-medium mb-1">{{ $renterName }}</p>
    
    {{-- Time --}}
    <p class="text-sm text-gray-600 mb-2">{{ $timeStart }} - {{ $timeEnd }}</p>
    
    {{-- Equipment Badge (if applicable) --}}
    @if($hasEquipment)
        <p class="text-xs text-gray-500 mb-3">Equipment Borrowed</p>
    @endif
    
    {{-- View Details Button --}}
    <button 
        class="text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline"
    >
        View Details
    </button>
</div>