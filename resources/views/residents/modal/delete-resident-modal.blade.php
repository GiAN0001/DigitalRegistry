<x-modal name="delete-resident-modal" maxWidth="md" focusable>
    <div x-data="{ residentId: null, password: '' }" 
         @set-delete-resident-id.window="residentId = $event.detail"
         class="p-6">
        
        <div class="flex items-center gap-4 mb-4">
            <div class="p-3 bg-red-100 text-red-600 rounded-full">
                <x-lucide-alert-triangle class="w-6 h-6" />
            </div>
            <h2 class="text-xl font-bold text-gray-900">Delete Resident</h2>
        </div>

        <p class="text-sm text-gray-600 mb-6 font-medium">
            Are you sure you want to delete this resident? To proceed, please enter your password.
        </p>

        <form x-bind:action="`/residents/${residentId}`" method="POST">
            @csrf
            @method('DELETE')
            
            <div class="mb-4">
                <x-input-label for="password" value="{{ __('Password') }}" />
                <x-text-input 
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="{{ __('Your Password') }}"
                    required
                />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3 bg-red-600 text-white hover:bg-red-700">
                    {{ __('Delete Resident') }}
                </x-danger-button>
            </div>
        </form>
    </div>
</x-modal>
