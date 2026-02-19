<x-modal 
    name="drop-resident-{{ $participation->id }}" 
    title="Drop Resident"
    :show="$errors->has('drop_reason') && old('participation_id') == $participation->id"
    focusable
>
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-2">
            Drop Resident: <span class="text-red-600">{{ $resident->first_name }} {{ $resident->middle_name }} {{ $resident->last_name }} {{$resident->extension}}</span>
        </h2>
        <p class="text-sm text-gray-500 mb-6">
            This will end their participation effective today and will be able to participate after 3 months. Please provide a reason for dropping the resident.
        </p>

        <form action="{{ route('admin.tupad.drop') }}" method="POST">
            @csrf
            <input type="hidden" name="participation_id" value="{{ $participation->id }}">

            <div>
                <x-input-label for="drop_reason" value="Reason for Dropping" />
                <textarea 
                    name="drop_reason" 
                    rows="3" 
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                    placeholder="e.g., Unexplained absences, found alternative employment..."
                >{{ old('drop_reason') }}</textarea>
                <x-input-error :messages="$errors->get('drop_reason')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')" type="button">
                    Cancel
                </x-secondary-button>

                <x-primary-button class="ms-3 bg-red-600 hover:bg-red-700" type="submit">
                    Confirm Drop
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>