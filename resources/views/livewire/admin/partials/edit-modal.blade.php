<x-modal name="edit-resident-{{ $resident->id }}" 
        title="Edit TUPAD Dates"
        :show="($errors->has('start_date') || $errors->has('end_date')) && old('participation_id') == $currentParticipation->id"
        focusable>
    <h2 class="text-lg font-medium text-gray-900 p-6">
            Resident: <span class="text-blue-800">{{ $resident->first_name }} {{ $resident->middle_name }} {{ $resident->last_name }} {{$resident->extension}}</span>
    </h2>
    <form action="{{ route('admin.tupad.update') }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="participation_id" value="{{ $currentParticipation->id }}">

        <div class="space-y-4">
            {{-- Start Date Section --}}
            <div>
                <label class="block text-sm font-bold text-slate-700">Start Date</label>
                <input type="date" name="start_date" 
                    value="{{ $currentParticipation->start_date ? \Carbon\Carbon::parse($currentParticipation->start_date)->format('Y-m-d') : '' }}"
                    min="{{ date('Y-m-d') }}"
                    {{ $currentParticipation->status === 'Ongoing' ? 'disabled' : '' }}
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm {{ $currentParticipation->status === 'Ongoing' ? 'bg-slate-100 cursor-not-allowed' : '' }}">
                    
                @if($currentParticipation->status === 'Ongoing')
                    <p class="text-[10px] text-amber-600 mt-1 italic font-medium">
                        * Status is Ongoing. Start date cannot be edited.
                    </p>
                @endif
                 <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
            </div>
            

            {{-- End Date Section --}}
            <div>
                <label class="block text-sm font-bold text-slate-700">End Date</label>
                <input type="date" name="end_date" 
                    value="{{ $currentParticipation->end_date ? \Carbon\Carbon::parse($currentParticipation->end_date)->format('Y-m-d') : '' }}"
                    min="{{ date('Y-m-d') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
        </div>

        @if($currentParticipation->updated_by_user_id)
            <div class="mt-4 p-2 bg-slate-50 border border-slate-200 rounded text-[10px] text-slate-500 italic">
                Last updated by: <strong>{{ $currentParticipation->updater->name ?? 'System' }}</strong> 
                on {{ $currentParticipation->updated_at->format('M d, Y h:i A') }}
            </div>
        @endif

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-700">Cancel</button>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 shadow-md transition-all">
                Update Schedule
            </button>
        </div>
    </form>
</x-modal>
