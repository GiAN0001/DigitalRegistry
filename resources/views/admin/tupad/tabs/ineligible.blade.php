@forelse($ineligible as $resident)
    @php 
        // 1. Fetch the latest record that is either Completed or Dropped
        $lastRecord = $resident->tupadParticipations
            ->whereIn('status', ['Completed', 'Dropped'])
            ->sortByDesc(fn($q) => $q->dropped_at ?? $q->end_date)
            ->first();

        // 2. Identify the Exit Date: Use dropped_at if it exists, otherwise use scheduled end_date
        $exitDate = $lastRecord->dropped_at ?? $lastRecord->end_date;
        
        // 3. Calculate eligibility based on that actual exit date
        $availableDate = \Carbon\Carbon::parse($exitDate)->addMonths(3);

        // 4. Dynamic Labels for UI clarity
        $isDropped = $lastRecord->status === 'Dropped';
        $columnLabel = $isDropped ? 'Date Dropped' : 'Date Ended';
        $statusBadgeClass = $isDropped ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700';
    @endphp

    <tr class="hover:bg-gray-50 transition border-b">
        <td class="px-3 py-4 text-sm font-bold text-gray-700">
           {{ $resident->first_name }} {{ $resident->middle_name }} {{ $resident->last_name }} {{ $resident->extension }}
        </td>

        <td class="px-3 py-4 text-sm text-gray-600">
            {{ \Carbon\Carbon::parse($resident->demographic?->birthdate)->age }}
        </td>

        <td class="px-3 py-4 text-sm text-gray-600">
            <div class="flex flex-col">
                <span class="text-[10px] uppercase font-bold text-slate-400">{{ $columnLabel }}</span>
                <span>{{ \Carbon\Carbon::parse($exitDate)->format('M d, Y') }}</span>
            </div>
        </td>

        <td class="px-3 py-4 text-sm">
            <div class="flex flex-col gap-1">
                {{-- Dynamic Tagging: Dropped vs Finished --}}
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusBadgeClass }} w-fit">
                    {{ $isDropped ? 'Dropped' : 'Finished' }}
                </span>
                
                @if($isDropped)
                    <span class="text-xs text-red-500 italic">
                        Reason: {{ $lastRecord->drop_reason ?? 'No reason provided' }}
                    </span>
                @endif

                <span class="text-[11px] text-slate-500 font-semibold mt-1">
                    Eligible: {{ $availableDate->format('M d, Y') }}
                </span>
            </div>
        </td>

        <td class="px-3 py-4 text-center">
            <div class="flex justify-center gap-3">
                <button @click="$dispatch('open-modal', 'view-details-{{ $resident->id }}')" class="text-green-600 text-xs flex items-center hover:underline">
                    <x-lucide-eye class="w-4 h-4 mr-1" /> View Details
                </button>
            </div>
            @include('admin.tupad.partials.details-modal', ['resident' => $resident])
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">
            No residents found in the 3-month cooldown period.
        </td>
    </tr>
@endforelse