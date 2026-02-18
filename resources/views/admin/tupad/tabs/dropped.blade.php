@forelse($dropped as $resident)
    @php 
        $lastDrop = $resident->tupadParticipations->where('status', 'Dropped')->sortByDesc('dropped_at')->first();
    @endphp
    <tr class="hover:bg-gray-50 transition">
        <td class="px-3 py-4 text-sm font-bold text-gray-700">
            {{ $resident->first_name }} {{ $resident->middle_name }} {{ $resident->last_name }} {{ $resident->extension }}
        </td>
        <td class="px-3 py-4 text-sm text-gray-600 ">
            {{ \Carbon\Carbon::parse($resident->demographic?->birthdate)->age }}
        </td>
        <td class="px-3 py-4 text-sm text-gray-600  font-semibold">
            {{ \Carbon\Carbon::parse($lastDrop?->end_date)->format('M d, Y') }}
        </td>
        <td class="px-3 py-4 text-sm text-red-600 italic">
            {{ $lastDrop?->drop_reason ?? 'No reason provided' }}
        </td>
        <td class="px-3 py-4 text-sm text-gray-600">
           {{ $lastDrop?->dropper->name ?? '-' }}   
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
    <tr><td colspan="4" class="p-10 text-center text-gray-400">No records found.</td></tr>
@endforelse