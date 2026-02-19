
<x-modal 
    name="employ-resident-{{ $resident->id }}" 
    :show="$errors->any() && old('resident_id') == $resident->id"
    focusable
>
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">
            Employ <span class="font-bold text-blue-600">{{ $resident->first_name }} {{ $resident->middle_name }} {{ $resident->last_name }} {{ $resident->extension }}</span>
        </h2>

        <form action="{{ route('admin.tupad.employ') }}" method="POST">
            @csrf

            <input type="hidden" name="resident_id" value="{{ $resident->id }}">

            <div class="space-y-4">
                <div>
                    <x-input-label for="start_date" value="Start Date" />
                    <x-text-input 
                        name="start_date" 
                        type="date" 
                        class="mt-1 block w-full" 
                        value="{{ old('start_date', now()->format('Y-m-d')) }}" 
                        min="{{ now()->format('Y-m-d') }}"
                    />
                    <x-input-error :messages="$errors->get('start_date')" />
                </div>

                <div>
                    <x-input-label for="end_date" value="End Date" />
                    <x-text-input 
                        name="end_date" 
                        type="date" 
                        class="mt-1 block w-full" 
                        value="{{ old('end_date') }}" 
                        min="{{ now()->format('Y-m-d') }}"
                    />
                    <x-input-error :messages="$errors->get('end_date')" />
                </div>
                
                {{-- Global employment error (e.g., already ongoing) --}}
                @if (session('error'))
                    <p class="text-sm text-red-600">{{ session('error') }}</p>
                @endif
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')" type="button">
                    Cancel
                </x-secondary-button>

                <x-primary-button class="ms-3 bg-blue-600 hover:bg-blue-700" type="submit">
                    Confirm Employment
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>