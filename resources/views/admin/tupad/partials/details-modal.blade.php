<x-modal name="view-details-{{ $resident->id }}" maxWidth="2xl" focusable>
    <div class="p-6 text-left"> {{-- Ensure text-left to counter table centering --}}
        <div class="flex justify-between items-center border-b pb-3">
            <h2 class="text-xl font-bold text-gray-800">
                Resident Profile: {{ $resident->first_name }} {{ $resident->middle_name }} {{ $resident->last_name }} {{ $resident->extension }}
            </h2>
            <button x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>

        <div class="mt-4 grid grid-cols-1 {{ Auth::user()->hasRole('super admin') ? 'md:grid-cols-2' : '' }} gap-4 text-sm">
    
            {{-- Personal Details --}}
            <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 {{ !Auth::user()->hasRole('super admin') ? 'md:col-span-2' : '' }}">
                <h3 class="font-bold text-blue-800 mb-3 uppercase tracking-wider text-xs flex items-center gap-2">
                    <x-lucide-user class="w-4 h-4" /> Personal Details
                </h3>
                <div class="space-y-2 {{ !Auth::user()->hasRole('super admin') ? 'grid grid-cols-2 gap-4 space-y-0' : '' }}">
                    <p><span class="text-gray-500">Age:</span> <span class="font-semibold">{{ \Carbon\Carbon::parse($resident->demographic->birthdate)->age }}</span></p>
                    <p><span class="text-gray-500">Gender:</span> <span class="font-semibold">{{ $resident->demographic->sex ?? 'N/A' }}</span></p>
                    <p><span class="text-gray-500">Civil Status:</span> <span class="font-semibold">{{ $resident->demographic->civil_status ?? 'N/A' }}</span></p>
                    <p><span class="text-gray-500">Occupation:</span> <span class="font-semibold">{{ $resident->demographic->occupation ?? 'N/A' }}</span></p>
                </div>
            </div>

            {{-- Health Details --}}
            @role('super admin')
                <div class="bg-red-50/50 p-4 rounded-xl border border-red-100">
                    <h3 class="font-bold text-red-800 mb-3 uppercase tracking-wider text-xs flex items-center gap-2">
                        <x-lucide-heart-pulse class="w-4 h-4" /> Health Info
                    </h3>
                    <div class="space-y-2">
                        <p><span class="text-gray-500">Sector:</span> <span class="font-semibold">{{ $resident->healthInformation->sector ?? 'N/A' }}</span></p>
                        <p><span class="text-gray-500">Vaccination:</span> <span class="font-semibold">{{ $resident->healthInformation->vaccination ?? 'None' }}</span></p>
                        <p><span class="text-gray-500">Comorbidity:</span> <span class="font-semibold">{{ $resident->healthInformation->comorbidity ?? 'None' }}</span></p>
                        <p><span class="text-gray-500">Maintenance:</span> <span class="font-semibold">{{ $resident->healthInformation->maintenance ?? 'None' }}</span></p>
                    </div>
                </div>
            @endrole
        </div>

        {{-- Work History --}}
        <div class="mt-6">
            <h3 class="font-bold text-gray-700 mb-3 uppercase tracking-wider text-xs">TUPAD History</h3>
            <div class="overflow-hidden border border-gray-100 rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Program Dates</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">Processed By</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">Dropped By</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white text-xs">
                        @forelse($resident->tupadParticipations as $history)
                            <tr>
                                <td class="px-4 py-2 font-medium">
                                    {{ \Carbon\Carbon::parse($history->start_date)->format('M d, Y') }} - 
                                    {{ \Carbon\Carbon::parse($history->end_date)->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-2 text-center">
                                    {{ $history->processor->name ?? 'System' }}
                                </td>
                                <td class="px-4 py-2 text-center">
                                    {{ $history->dropper->name ?? '-' }}   
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold 
                                        {{ $history->status === 'Completed' ? 'bg-green-100 text-green-800' : 
                                        ($history->status === 'Dropped' ? 'bg-red-100 text-red-800' : 
                                        ($history->status === 'Scheduled' ? 'bg-slate-200 text-slate-800' : 'bg-amber-100 text-amber-800')) }}">
                                        {{ strtoupper($history->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-6 text-center text-gray-400 italic">No participation history on record.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')" class="rounded-lg">Close Profile</x-secondary-button>
        </div>
    </div>
</x-modal>