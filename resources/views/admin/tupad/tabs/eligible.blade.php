
@forelse($eligible as $resident)
    <tr class="hover:bg-gray-50 border-b">
        <td class="px-3 py-4 text-sm font-bold text-gray-700">
            {{ $resident->first_name }} {{ $resident->middle_name }} {{ $resident->last_name }} {{ $resident->extension }}
        </td>
        <td class="px-3 py-4 text-sm text-gray-600">
            {{ \Carbon\Carbon::parse($resident->demographic->birthdate)->age }}
        </td>
        <td class="px-3 py-4 text-xs text-gray-500">
            {{ $resident->household->house_number ?? 'N/A' }}
            {{ $resident->household->areaStreet->purok_name ?? 'N/A' }}
            {{ $resident->household->areaStreet->street_name ?? 'N/A' }}
        </td>
        <td class="px-3 py-4 text-center">
            <div class="flex justify-center gap-2">
                <button @click="$dispatch('open-modal', 'view-details-{{ $resident->id }}')" class="text-green-600 text-xs flex items-center hover:underline">
                    <x-lucide-eye class="w-4 h-4 mr-1" /> View Details
                </button>
                <button 
                    type="button"
                    @click="$dispatch('open-modal', 'employ-resident-{{ $resident->id }}')" 
                    class="text-blue-800 text-xs flex items-center hover:underline"
                >
                    <x-lucide-file-pen class="w-4 h-4 mr-1" /> Employ
                </button>
            </div>
                @include('admin.tupad.partials.details-modal', ['resident' => $resident])
                 @include('livewire.admin.partials.employ-modal')
                
        </td>
    </tr>

@empty
    <tr><td colspan="4" class="p-10 text-center text-gray-400">No records found.</td></tr>
@endforelse
