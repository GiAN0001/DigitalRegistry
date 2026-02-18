@forelse($ongoing as $resident)
    @php 
        // Forensic Check: Find the record that is either 'Ongoing' or 'Scheduled' for this resident [cite: 2025-12-04]
        $currentParticipation = $resident->tupadParticipations
            ->whereIn('status', ['Ongoing', 'Scheduled']) 
            ->first(); 
    @endphp

    <tr class="hover:bg-gray-50 border-b">
        <td class="px-3 py-4 text-sm font-bold text-gray-700">
            {{ $resident->first_name }} {{ $resident->middle_name ?? '' }} {{ $resident->last_name }} {{ $resident->extension }}
        </td>

        {{-- Display Start and End Dates --}}
        <td class="px-3 py-4 text-sm text-gray-600">
            {{ \Carbon\Carbon::parse($currentParticipation?->start_date)->format('M d, Y') }} - 
            {{ \Carbon\Carbon::parse($currentParticipation?->end_date)->format('M d, Y') }}
        </td>

        <td class="px-3 py-4 text-sm text-center">
            {{-- Forensic Update: Dynamic badge colors based on status [cite: 2025-12-04] --}}
            @if($currentParticipation?->status === 'Scheduled')
                <span class="px-2 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700 uppercase">
                    {{ $currentParticipation->status }}
                </span>
            @else
                <span class="px-2 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 uppercase">
                    {{ $currentParticipation->status }}
                </span>
            @endif
        </td>

        <td class="px-3 py-4 text-center">
            <div class="flex justify-center gap-3">
                <button @click="$dispatch('open-modal', 'view-details-{{ $resident->id }}')" class="text-green-600 text-xs flex items-center hover:underline">
                    <x-lucide-eye class="w-4 h-4 mr-1" /> View Details
                </button>

                <button @click="$dispatch('open-modal', 'edit-resident-{{ $resident->id }}')" class="text-blue-600 text-xs flex items-center hover:underline">
                    <x-lucide-edit class="w-4 h-4 mr-1" /> Edit
                </button>
    
                <button @click="$dispatch('open-modal', 'drop-resident-{{ $currentParticipation->id }}')" class="text-red-600 text-xs flex items-center hover:underline">
                    <x-lucide-user-minus class="w-4 h-4 mr-1" /> Drop
                </button>
            </div>

            {{-- Modals --}}
            @include('admin.tupad.partials.details-modal', ['resident' => $resident])
            @include('livewire.admin.partials.edit-modal', ['resident' => $resident])
            
            @if($currentParticipation)
                @include('livewire.admin.partials.drop-modal', [
                    'participation' => $currentParticipation, 
                    'resident' => $resident
                ])
            @endif
        </td>
    </tr>
@empty
    <tr><td colspan="4" class="p-10 text-center text-gray-400 italic">No ongoing or pending participations found.</td></tr>
@endforelse